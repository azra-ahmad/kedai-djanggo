<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::all();
        $cart = session('cart', []);
        $customer_id = session('customer_id');

        // Ambil data customer kalau ada
        $customer = null;
        if ($customer_id) {
            $customer = Customer::find($customer_id);
        }

        $orders = Order::where('customer_id', $customer_id)->with('orderItems.menu')->get();

        return view('user.menu', compact('menus', 'cart', 'customer', 'orders'));
    }
}