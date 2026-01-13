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
    /**
     * Get customer from session token (helper method)
     * Returns null if token invalid or customer not found
     */
    protected function getCustomerFromSession()
    {
        $token = session('customer_token');
        if (!$token) {
            return null;
        }
        return Customer::where('customer_token', $token)->first();
    }

    public function showForm()
    {
        // Clear customer session if requested (preserve admin auth)
        if (request()->has('new_session')) {
            Session::forget('customer_token');
            Session::forget('cart');
        }
        
        // Check if customer token exists and is valid
        if ($this->getCustomerFromSession()) {
            return redirect()->route('menu.index');
        }
        
        return view('user.form');
    }

    public function orders()
    {
        $customer = $this->getCustomerFromSession();
        if (!$customer) {
            return redirect()->route('user.form');
        }
        
        // Query orders by customer_token from session for strict privacy isolation
        // This ensures users can't see orders from previous sessions even if they used the same phone number
        $orders = Order::where('customer_token', session('customer_token'))
            ->with('orderItems.menu')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('user.orders', compact('orders'));
    }

    public function submitIdentity(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|numeric|digits_between:10,15',
        ], [
            'phone.numeric' => 'Nomor telepon harus berupa angka',
            'phone.digits_between' => 'Nomor telepon minimal 10 digit dan maksimal 15 digit'
        ]);

        // ALWAYS create a new customer record for every session
        // This ensures strict separation of identities even if phone number is reused
        $customer = Customer::create([
            'phone' => $validated['phone'],
            'name' => $validated['name'],
            'customer_token' => \Illuminate\Support\Str::uuid()->toString(),
        ]);

        // Clear only customer-related session data (preserve admin auth)
        Session::forget('customer_token');
        Session::forget('cart');
        
        // Set customer token in session
        session(['customer_token' => $customer->customer_token]);
        
        return redirect()->route('menu.index');
    }

    public function logout()
    {
        // Explicit customer logout
        Session::forget('customer_token');
        Session::forget('cart');
        
        // Do NOT use flush() or Auth::logout() to protect admin session
        
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

        $customer = $this->getCustomerFromSession();
        if (!$customer) {
            return redirect()->route('user.form')->with('error', 'Silakan masukkan identitas terlebih dahulu');
        }

        $total = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));

        // Note: Order is NOT created here anymore. 
        // usage: processCheckout will handle creation.
        
        return view('user.checkout', compact('cart', 'total', 'customer'));
    }

    public function processCheckout(Request $request)
    {
        $cart = Session::get('cart', []);
        
        // Re-validate state
        if (empty($cart)) {
             return response()->json(['message' => 'Cart is empty'], 400);
        }

        $customer = $this->getCustomerFromSession();
        if (!$customer) {
             return response()->json(['message' => 'Customer session expired'], 401);
        }

        $total = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));

        try {
            // SINGLE SOURCE OF TRUTH: Generate midtrans_order_id ONCE here
            // Format: KDJ-{timestamp} as required
            $midtransOrderId = 'KDJ-' . time();

            $order = Order::create([
                'customer_id' => $customer->id,
                'customer_token' => session('customer_token'),
                'status' => 'pending',
                'total_harga' => $total,
                'midtrans_order_id' => $midtransOrderId,
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
                    'first_name' => $customer->name,
                    'phone' => $customer->phone,
                ],
            ];

            $snapToken = Snap::getSnapToken($params);
            $order->update(['snap_token' => $snapToken]);

            return response()->json([
                'status' => 'success',
                'snap_token' => $snapToken,
                'order_id' => $order->id,
                'midtrans_order_id' => $order->midtrans_order_id
            ]);

        } catch (\Exception $e) {
            \Log::error('Checkout Error: ' . $e->getMessage());
             return response()->json(['message' => 'Checkout failed'], 500);
        }
    }

    public function status($order_id)
    {
        $order = Order::with(['customer', 'orderItems.menu'])->findOrFail($order_id);
        
        // STEP 4 FIX: Cart Reset Logic
        // Cart must be cleared ONLY when order.status === 'paid'
        if ($order->status === 'paid') {
            Session::forget('cart');
        }

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
        \Log::info('MIDTRANS WEBHOOK HIT', $request->all());
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
                    // UX ONLY: Redirect or show success, but DO NOT update DB here
                    // DB update must happen via webhook for security & reliability
                    \Log::info('Frontend callback received for order: ' . $orderId);
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
                
                \Log::info('Order found for webhook:', ['db_id' => $order->id, 'midtrans_id' => $order->midtrans_order_id]);

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
                    // Do not revert to pending if already paid (idempotency)
                    if ($order->status !== 'paid') {
                        $order->update(['status' => 'pending']);
                    }
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

    // DEMO MODE ONLY - REMOVE AFTER PRESENTATION
    public function updatePaymentStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if ($order->status !== 'paid') {
            $order->update(['status' => 'paid']);
        }

        Session::forget('cart');

        return response()->json([
            'success' => true
        ]);
    }


    public function showReceipt($order_id)
    {
        $order = Order::with(['customer', 'orderItems.menu'])->findOrFail($order_id);
        
        // Security: Ensure the session owner owns this order
        if (session('customer_token') !== $order->customer_token) {
             abort(403, 'Unauthorized access to receipt');
        }

        return view('user.struk', compact('order'));
    }
}