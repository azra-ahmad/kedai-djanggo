<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display list of orders with filtering
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'all');
        $search = $request->input('search');

        $query = Order::with(['customer', 'orderItems.menu']);

        // Filter berdasarkan status
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Filter berdasarkan pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('midtrans_order_id', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.orders', compact('orders', 'status'));
    }

    /**
     * Assign order to current admin
     */
    public function assign($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['admin_id' => auth()->id()]);

        return back()->with('success', 'Order assigned to you');
    }

    /**
     * Mark order as completed
     */
    public function complete($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status === 'paid') {
            $order->update([
                'status' => 'done',
                'admin_id' => auth()->id()
            ]);
            return back()->with('success', 'Order marked as completed');
        }

        return back()->with('error', 'Only paid orders can be completed');
    }

    /**
     * Mark order as failed
     */
    public function fail($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status === 'pending') {
            $order->update([
                'status' => 'failed',
                'admin_id' => auth()->id()
            ]);
            return back()->with('success', 'Order marked as failed');
        }

        return back()->with('error', 'Only pending orders can be marked as failed');
    }

    /**
     * Generate receipt/struk for order
     */
    public function receipt($id)
    {
        $order = Order::with(['customer', 'orderItems.menu'])->findOrFail($id);
        return view('admin.struk', compact('order'));
    }
}
