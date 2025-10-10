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
        return view('user.form');
    }

    public function submitIdentity(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
        ]);

        $customer = Customer::create([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        Session::put('customer_id', $customer->id);

        return redirect()->route('menu.index');
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Session::get('cart', []);
        $menu = Menu::findOrFail($request->menu_id);

        if (isset($cart[$menu->id])) {
            $cart[$menu->id]['quantity'] += $request->quantity;
        } else {
            $cart[$menu->id] = [
                'name' => $menu->nama_menu,
                'price' => $menu->harga,
                'quantity' => $request->quantity,
                'image' => $menu->gambar,
                'category' => $menu->kategori_menu,
            ];
        }

        Session::put('cart', $cart);

        return response()->json(['message' => 'Added to cart']);
    }

    public function checkout(Request $request)
    {
        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('menu.index')->with('error', 'Keranjang kosong!');
        }

        $customer_id = Session::get('customer_id');

        $subtotal = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));
        $tax = $subtotal * 0.1;
        $total = $subtotal + $tax;

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

        return view('user.checkout', compact('order', 'cart', 'subtotal', 'tax', 'total'));
    }

    public function status($order_id)
    {
        $order = Order::with(['customer', 'orderItems.menu'])->findOrFail($order_id);
        return view('user.status', compact('order'));
    }

    public function notificationHandler(Request $request)
    {
        $order = Order::where('midtrans_order_id', $request->order_id)->firstOrFail();
        if ($request->transaction_status == 'settlement') {
            $order->update(['status' => 'paid']);
        } elseif (in_array($request->transaction_status, ['expire', 'cancel', 'deny'])) {
            $order->update(['status' => 'failed']);
        }
        return response()->json(['status' => 'success']);
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

        return response()->json(['message' => 'Quantity updated']);
    }

    public function cart()
    {
        $cart = Session::get('cart', []);
        $subtotal = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));
        $tax = $subtotal * 0.1;
        $total = $subtotal + $tax;

        return view('user.cart', compact('cart', 'subtotal', 'tax', 'total'));
    }
}