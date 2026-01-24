<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Get cart contents
     */
    public function index()
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

    /**
     * Add item to cart
     */
    public function add(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Session::get('cart', []);
        $menu = Menu::findOrFail($request->menu_id);

        // Get full image URL using accessor
        $imagePath = $menu->image_url;

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
            'cart' => array_values($cart)
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request)
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
            'cart' => array_values($cart)
        ]);
    }

    /**
     * Clear cart
     */
    public function clear()
    {
        Session::forget('cart');
        return response()->json(['message' => 'Cart cleared', 'cart_count' => 0]);
    }
}
