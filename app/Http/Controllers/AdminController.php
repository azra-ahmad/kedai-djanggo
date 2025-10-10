<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        $orders = Order::with(['customer', 'orderItems.menu'])->get();
        return view('admin.dashboard', compact('orders'));
    }

    public function assignToSelf(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['admin_id' => Auth::user()->id]);
        return redirect()->route('admin.dashboard')->with('success', 'Pesanan diambil');
    }

    public function completeOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'done']);
        return redirect()->route('admin.dashboard')->with('success', 'Pesanan selesai');
    }

    public function generateStruk($id)
    {
        $order = Order::with(['customer', 'orderItems.menu', 'admin'])->findOrFail($id);
        $pdf = Pdf::loadView('admin.struk', compact('order'));
        return $pdf->download('struk-kedai-djanggo-' . $order->id . '.pdf');
    }
}