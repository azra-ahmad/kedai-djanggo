<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasCustomerSession;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    use HasCustomerSession;

    /**
     * Display customer order history
     */
    public function index()
    {
        $customer = $this->getCustomer();
        if (!$customer) {
            return redirect()->route('user.form');
        }
        
        // Query orders by customer_token from session for strict privacy isolation
        $orders = Order::where('customer_token', session('customer_token'))
            ->with('orderItems.menu')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('user.orders', compact('orders'));
    }

    /**
     * Show order status page
     */
    public function status($order_id)
    {
        $order = Order::with(['customer', 'orderItems.menu'])->findOrFail($order_id);
        
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

    /**
     * Cancel pending order
     */
    public function cancel($order_id)
    {
        $order = Order::findOrFail($order_id);
        
        // Only allow cancel if order is still pending
        if ($order->status === 'pending') {
            $order->update(['status' => 'failed']);
            Session::forget('cart');
            return redirect()->route('menu.index')->with('success', 'Pesanan berhasil dibatalkan');
        }
        
        return redirect()->route('order.status', $order_id)->with('error', 'Pesanan tidak dapat dibatalkan');
    }

    /**
     * Show order receipt
     */
    public function receipt($order_id)
    {
        $order = Order::with(['customer', 'orderItems.menu'])->findOrFail($order_id);
        
        // Security: Ensure the session owner owns this order
        if (session('customer_token') !== $order->customer_token) {
             abort(403, 'Unauthorized access to receipt');
        }

        return view('user.struk', compact('order'));
    }
}
