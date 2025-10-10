<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Customer;
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
            $customer = \App\Models\Customer::find($customer_id);
        }

        // Ambil total order hari ini
        $orderCount = 0;
        if ($customer_id) {
            $orderCount = \App\Models\Order::where('customer_id', $customer_id)
                ->whereDate('created_at', today())
                ->count();
        }

        return view('user.menu', compact('menus', 'cart', 'customer', 'orderCount'));
    }
}