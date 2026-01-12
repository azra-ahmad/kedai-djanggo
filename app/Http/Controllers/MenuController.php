<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Get customer from session token (helper method)
     */
    protected function getCustomerFromSession()
    {
        $token = session('customer_token');
        if (!$token) {
            return null;
        }
        return Customer::findByToken($token);
    }

    public function index()
    {
        $menus = Menu::all();
        $cart = session('cart', []);

        // Get customer from session token
        $customer = $this->getCustomerFromSession();

        // SAFETY: kalau ga ada token → balik ke form
        $token = session('customer_token');
        if (!$token || !$customer) {
            return redirect()->route('user.form');
        }

        $orders = Order::where('customer_token', $token)
            ->with('orderItems.menu')
            ->get();

        return view('user.menu', compact('menus', 'cart', 'customer', 'orders'));
    }
}