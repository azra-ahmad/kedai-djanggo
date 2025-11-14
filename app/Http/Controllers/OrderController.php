<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Midtrans\Config;
use Midtrans\Snap;

class OrderController extends Controller
{
    public function showForm()
    {
        // Clear session lama kalau ada
        if (request()->has('new_session')) {
            Session::flush();
        }
        
        if (session()->has('customer_id')) {
            return redirect()->route('menu.index');
        }
        
        return view('user.form');
    }

    public function orders()
    {
        $customer_id = Session::get('customer_id');
        if (!$customer_id) {
            return redirect()->route('user.form');
        }
        $orders = Order::where('customer_id', $customer_id)->with('orderItems.menu')->get();
        return view('user.orders', compact('orders'));
    }

    public function submitIdentity(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
        ]);

        $customer = Customer::firstOrCreate(
            ['phone' => $validated['phone']],
            ['name' => $validated['name']]
        );

        // Clear old session
        Session::flush();
        
        // Set new customer session
        session(['customer_id' => $customer->id]);
        
        return redirect()->route('menu.index');
    }

    public function logout()
    {
        Session::flush();
        return redirect()->route('user.form');
    }

    public function cart()
    {
        $cart = Session::get('cart', []);
        $cartItems = [];
        
        foreach ($cart as $menuId => $item) {
            $cartItems[] = [
                'id' => (int) $menuId,
                'name' => $item['name'],
                'price' => (float) $item['price'],
                'quantity' => (int) $item['quantity'],
                'image' => $item['image'],
                'category' => $item['category'],
            ];
        }
        
        $total = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'cart' => $cartItems,
                'total' => $total,
                'cart_count' => array_sum(array_column($cart, 'quantity'))
            ]);
        }

        return view('user.cart', compact('cart', 'total'));
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Session::get('cart', []);
        $menu = Menu::findOrFail($request->menu_id);

        // BUAT URL GAMBAR LENGKAP
        $imagePath = $menu->gambar 
            ? asset('storage/menu-images/' . $menu->gambar) 
            : asset('images/default-menu.jpg');

        if (isset($cart[$menu->id])) {
            $cart[$menu->id]['quantity'] += $request->quantity;
        } else {
            $cart[$menu->id] = [
                'id' => $menu->id,
                'name' => $menu->nama_menu,
                'price' => $menu->harga,
                'quantity' => $request->quantity,
                'image' => $imagePath,
                'category' => $menu->kategori_menu,
            ];
        }

        Session::put('cart', $cart);

        return response()->json([
            'message' => 'Added to cart',
            'cart_count' => array_sum(array_column($cart, 'quantity')),
            'cart' => array_values($cart) // Return cart sebagai indexed array
        ]);
    }

    public function checkout()
    {
        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('menu.index')->with('error', 'Keranjang kosong!');
        }

        $customer_id = Session::get('customer_id');

        $total = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));

        $order = Order::create([
            'customer_id' => $customer_id,
            'total_harga' => $total,
            'status' => 'pending',
            'midtrans_order_id' => 'KDJ-' . time(),
        ]);

        foreach ($cart as $menu_id => $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $menu_id,
                'jumlah' => $item['quantity'],
                'subtotal' => $item['price'] * $item['quantity'],
            ]);
        }

        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id' => $order->midtrans_order_id,
                'gross_amount' => $total,
            ],
            'customer_details' => [
                'first_name' => Customer::find($customer_id)->name,
                'phone' => Customer::find($customer_id)->phone,
            ],
        ];

        $snapToken = Snap::getSnapToken($params);
        $order->update(['snap_token' => $snapToken]);

        // JANGAN clear cart dulu, biarkan sampai payment sukses atau cancel
        // Session::forget('cart');

        return view('user.checkout', compact('order', 'cart', 'total'));
    }

    public function status($order_id)
    {
        $order = Order::with(['customer', 'orderItems.menu'])->findOrFail($order_id);
        
        if (request()->wantsJson()) {
            return response()->json([
                'status' => $order->status,
                'order_id' => $order->midtrans_order_id,
                'total' => $order->total_harga
            ]);
        }
        
        return view('user.status', compact('order'));
    }

    public function notificationHandler(Request $request)
    {
        try {
            \Log::info('Midtrans notification received:', $request->all());
            
            // Check if this is from frontend callback or Midtrans webhook
            $isFrontendCallback = $request->has('order_id') && !$request->has('transaction_id');
            
            if ($isFrontendCallback) {
                // Handle frontend callback
                $orderId = $request->order_id;
                $transactionStatus = $request->transaction_status;
                
                \Log::info('Frontend callback:', [
                    'order_id' => $orderId,
                    'transaction_status' => $transactionStatus
                ]);
                
                $order = Order::where('midtrans_order_id', $orderId)->firstOrFail();
                
                if ($transactionStatus == 'settlement') {
                    $order->update(['status' => 'paid']);
                    \Log::info('Order status updated to paid via frontend callback');
                }
                
                return response()->json(['status' => 'success', 'source' => 'frontend']);
            } else {
                // Handle Midtrans webhook with proper notification class
                Config::$serverKey = config('services.midtrans.server_key');
                Config::$isProduction = config('services.midtrans.is_production');
                
                $notification = new \Midtrans\Notification();
                
                $transactionStatus = $notification->transaction_status;
                $orderId = $notification->order_id;
                $fraudStatus = isset($notification->fraud_status) ? $notification->fraud_status : 'accept';
                
                \Log::info('Midtrans webhook:', [
                    'order_id' => $orderId,
                    'transaction_status' => $transactionStatus,
                    'fraud_status' => $fraudStatus
                ]);
                
                $order = Order::where('midtrans_order_id', $orderId)->firstOrFail();
                
                // Update status based on transaction status
                if ($transactionStatus == 'capture') {
                    if ($fraudStatus == 'accept') {
                        $order->update(['status' => 'paid']);
                        \Log::info('Order status updated to paid (capture)');
                    }
                } elseif ($transactionStatus == 'settlement') {
                    $order->update(['status' => 'paid']);
                    \Log::info('Order status updated to paid (settlement)');
                } elseif ($transactionStatus == 'pending') {
                    $order->update(['status' => 'pending']);
                    \Log::info('Order status kept as pending');
                } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                    $order->update(['status' => 'failed']);
                    \Log::info('Order status updated to failed');
                }
                
                return response()->json(['status' => 'success', 'source' => 'webhook']);
            }
        } catch (\Exception $e) {
            \Log::error('Midtrans notification error: ' . $e->getMessage());
            \Log::error('Request data: ' . json_encode($request->all()));
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateQuantity(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'delta' => 'required|integer',
        ]);

        $cart = Session::get('cart', []);
        $menu_id = $request->menu_id;
        $delta = $request->delta;

        if (isset($cart[$menu_id])) {
            $cart[$menu_id]['quantity'] += $delta;
            if ($cart[$menu_id]['quantity'] <= 0) {
                unset($cart[$menu_id]);
            }
        }

        Session::put('cart', $cart);

        return response()->json([
            'message' => 'Quantity updated',
            'cart_count' => array_sum(array_column($cart, 'quantity')),
            'cart' => array_values($cart) // Return cart sebagai indexed array
        ]);
    }

    public function cancelOrder($order_id)
    {
        $order = Order::findOrFail($order_id);
        
        // Only allow cancel if order is still pending
        if ($order->status === 'pending') {
            $order->update(['status' => 'failed']);
            return redirect()->route('menu.index')->with('success', 'Pesanan berhasil dibatalkan');
        }
        
        return redirect()->route('order.status', $order_id)->with('error', 'Pesanan tidak dapat dibatalkan');
    }

    public function clearCart()
    {
        Session::forget('cart');
        return response()->json(['message' => 'Cart cleared', 'cart_count' => 0]);
    }

    public function updatePaymentStatus(Request $request, $order_id)
    {
        try {
            $order = Order::findOrFail($order_id);
            
            $status = $request->input('status', 'paid');
            $order->update(['status' => $status]);
            
            \Log::info('Payment status updated manually:', [
                'order_id' => $order->midtrans_order_id,
                'status' => $status
            ]);
            
            return response()->json([
                'status' => 'success',
                'order_status' => $order->status,
                'message' => 'Payment status updated successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating payment status: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}