<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasCustomerSession;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    use HasCustomerSession;

    /**
     * Show checkout page
     */
    public function show()
    {
        // STRICT RULE: Check for existing pending order first
        $customer = $this->getCustomer();
        if ($customer) {
            $existingPendingOrder = Order::where('customer_token', session('customer_token'))
                ->where('status', 'pending')
                ->first();

            if ($existingPendingOrder) {
                return redirect()->route('order.status', $existingPendingOrder->id)
                    ->with('warning', 'Kamu masih punya pesanan yang belum dibayar');
            }
        }

        // ✅ FIX: Get cart from DATABASE instead of Session
        $sessionId = Session::getId();
        $cartItems = \DB::table('carts')
            ->join('menus', 'carts.menu_id', '=', 'menus.id')
            ->where('carts.session_id', $sessionId)
            ->select(
                'carts.id as cart_id',  // Cart ID for unique identification
                'carts.menu_id as id',
                'carts.quantity',
                'carts.note',
                'menus.nama_menu as name',
                'menus.harga as price',
                'menus.gambar as image_file'
            )
            ->get();

        // Convert to array format expected by view
        // Use cart_id as key since same menu can have different notes
        $cart = [];
        foreach ($cartItems as $item) {
            $cart[] = [
                'cart_id' => $item->cart_id,  // Unique cart item ID
                'id' => $item->id,            // Menu ID
                'name' => $item->name,
                'price' => (float) $item->price,
                'quantity' => (int) $item->quantity,
                'image' => asset('storage/' . $item->image_file),
                'note' => $item->note ?? ''
            ];
        }

        if (empty($cart)) {
            return redirect()->route('menu.index')->with('error', 'Keranjang kosong!');
        }

        if (!$customer) {
            return redirect()->route('user.form')->with('error', 'Silakan masukkan identitas terlebih dahulu');
        }

        $total = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));

        return view('user.checkout', compact('cart', 'total', 'customer'));
    }

    /**
     * Process checkout and create Midtrans transaction
     */
    public function process(Request $request)
    {
        // ✅ FIX: Get cart from DATABASE instead of Session
        $sessionId = Session::getId();
        $cartItems = \DB::table('carts')
            ->join('menus', 'carts.menu_id', '=', 'menus.id')
            ->where('carts.session_id', $sessionId)
            ->select(
                'carts.menu_id as id',
                'carts.quantity',
                'carts.note',
                'menus.nama_menu as name',
                'menus.harga as price'
            )
            ->get();

        // Convert to array format
        $cart = [];
        foreach ($cartItems as $item) {
            $cart[$item->id] = [
                'id' => $item->id,
                'name' => $item->name,
                'price' => (float) $item->price,
                'quantity' => (int) $item->quantity,
                'note' => $item->note
            ];
        }
        
        // Re-validate state
        if (empty($cart)) {
             return response()->json(['message' => 'Cart is empty'], 400);
        }

        $customer = $this->getCustomer();
        if (!$customer) {
             return response()->json(['message' => 'Customer session expired'], 401);
        }

        // STRICT RULE: Prevent duplicate pending orders
        $existingPendingOrder = Order::where('customer_token', session('customer_token'))
            ->where('status', 'pending')
            ->first();

        if ($existingPendingOrder) {
             return response()->json([
                 'status' => 'pending_exists',
                 'message' => 'Anda memiliki pesanan yang belum dibayar',
                 'redirect_url' => route('order.status', $existingPendingOrder->id)
             ], 409);
        }

        try {
            // 1. Prepare Item Details First & Calculate Total from it
            $item_details = [];
            $total_calculated = 0;

            foreach ($cart as $item) {
                $price = (int) $item['price'];
                $quantity = (int) $item['quantity'];
                
                $item_details[] = [
                    'id' => substr((string) $item['id'], 0, 50),
                    'price' => $price,
                    'quantity' => $quantity,
                    'name' => substr($item['name'] ?? 'Item', 0, 50),
                ];
                
                $total_calculated += ($price * $quantity);
            }

            // SINGLE SOURCE OF TRUTH: Generate midtrans_order_id ONCE here
            $midtransOrderId = 'KDJ-' . time() . '-' . rand(100, 999);

            $order = Order::create([
                'customer_id' => $customer->id,
                'customer_token' => session('customer_token'),
                'status' => 'pending',
                'total_harga' => $total_calculated,
                'midtrans_order_id' => $midtransOrderId,
            ]);

            foreach ($cart as $menu_id => $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $menu_id,
                    'jumlah' => $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity'],
                    'note' => $item['note'] ?? null, // ✅ FIX: Transfer note from cart to order
                ]);
            }

            Config::$serverKey = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production');
            Config::$isSanitized = true;
            Config::$is3ds = true;

            if (empty(Config::$serverKey)) {
                throw new \Exception('Midtrans Server Key is missing in configuration.');
            }

            $params = [
                'transaction_details' => [
                    'order_id' => $order->midtrans_order_id,
                    'gross_amount' => $total_calculated,
                ],
                'customer_details' => [
                    'first_name' => substr($customer->name, 0, 50),
                    'phone' => substr($customer->phone, 0, 20),
                ],
                'item_details' => $item_details,
            ];

            \Log::info('Midtrans Payload:', $params);

            $snapToken = Snap::getSnapToken($params);
            $order->update(['snap_token' => $snapToken]);

            // ✅ Clear cart from database after successful order
            \DB::table('carts')->where('session_id', $sessionId)->delete();

            return response()->json([
                'status' => 'success',
                'snap_token' => $snapToken,
                'order_id' => $order->id,
                'midtrans_order_id' => $order->midtrans_order_id
            ]);

        } catch (\Exception $e) {
            \Log::error('Checkout Error: ' . $e->getMessage());
             return response()->json(['message' => 'Checkout failed: ' . $e->getMessage()], 500);
        }
    }
}
