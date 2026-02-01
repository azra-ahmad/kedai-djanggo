<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Helper: Ambil Session ID Guest
     */
    private function getSessionId()
    {
        return Session::getId();
    }

    /**
     * Get cart contents
     */
    public function index()
    {
        $sessionId = $this->getSessionId();

        // 1. Ambil Data Mentah dari Database
        $cartItems = DB::table('carts')
            ->join('menus', 'carts.menu_id', '=', 'menus.id')
            ->where('carts.session_id', $sessionId)
            ->select(
                'carts.id as cart_id',
                'carts.menu_id as id',
                'carts.quantity',
                'carts.note',
                'menus.nama_menu as name',
                'menus.harga as price',
                'menus.gambar as image_file', // ✅ FIX: Pake nama kolom asli 'gambar'
                'menus.kategori_menu as category'
            )
            ->get();

        // 2. Format Data (Biar Frontend Seneng)
        $formattedCart = $cartItems->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'price' => (float) $item->price,
                'quantity' => (int) $item->quantity,
                // ✅ FIX: Manual bikin URL gambar (sesuaikan path storage lu)
                'image' => asset('storage/' . $item->image_file), 
                'category' => $item->category,
                'note' => $item->note
            ];
        });

        // 3. Hitung Total
        $total = $formattedCart->sum(function($item) {
            return $item['price'] * $item['quantity'];
        });

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'cart' => $formattedCart->values()->all(), // ✅ Ensure proper JS array format
                'total' => $total,
                'cart_count' => $cartItems->sum('quantity')
            ]);
        }

        return view('user.cart', compact('formattedCart', 'total'));
    }

    /**
     * Add item to cart
     */
    public function add(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string|max:255'
        ]);

        $sessionId = $this->getSessionId();
        $menuId = $request->menu_id;
        $qty = $request->quantity;
        $note = $request->note;

        // Cek item duplikat
        $existingItem = DB::table('carts')
            ->where('session_id', $sessionId)
            ->where('menu_id', $menuId)
            ->first();

        if ($existingItem) {
            DB::table('carts')
                ->where('id', $existingItem->id)
                ->update([
                    'quantity' => $existingItem->quantity + $qty,
                    'note' => $note ? $note : $existingItem->note, // Update note kalau ada baru
                    'updated_at' => now()
                ]);
        } else {
            DB::table('carts')->insert([
                'session_id' => $sessionId,
                'menu_id' => $menuId,
                'quantity' => $qty,
                'note' => $note,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Return data fresh
        $refreshData = $this->index()->getData();

        return response()->json([
            'message' => 'Berhasil masuk keranjang',
            'cart_count' => $refreshData->cart_count,
            'cart' => $refreshData->cart,
            'total' => $refreshData->total
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

        $sessionId = $this->getSessionId();
        
        $item = DB::table('carts')
            ->where('session_id', $sessionId)
            ->where('menu_id', $request->menu_id)
            ->first();

        if ($item) {
            $newQty = $item->quantity + $request->delta;

            if ($newQty > 0) {
                DB::table('carts')
                    ->where('id', $item->id)
                    ->update(['quantity' => $newQty, 'updated_at' => now()]);
            } else {
                DB::table('carts')->where('id', $item->id)->delete();
            }
        }

        // ✅ FIX: Build cart data directly instead of calling index()
        $sessionId = $this->getSessionId();
        $cartItems = DB::table('carts')
            ->join('menus', 'carts.menu_id', '=', 'menus.id')
            ->where('carts.session_id', $sessionId)
            ->select(
                'carts.menu_id as id',
                'menus.nama_menu as name',
                'menus.harga as price',
                'carts.quantity',
                'menus.gambar as image_file',
                'menus.kategori_menu as category',
                'carts.note'
            )
            ->get();

        $formattedCart = $cartItems->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'price' => (float) $item->price,
                'quantity' => (int) $item->quantity,
                'image' => asset('storage/' . $item->image_file),
                'category' => $item->category,
                'note' => $item->note
            ];
        });

        $total = $formattedCart->sum(function($item) {
            return $item['price'] * $item['quantity'];
        });

        return response()->json([
            'message' => 'Quantity updated',
            'cart_count' => $cartItems->sum('quantity'),
            'cart' => $formattedCart->values()->all(),
            'total' => $total
        ]);
    }

    /**
     * Clear cart
     */
    public function clear()
    {
        DB::table('carts')
            ->where('session_id', $this->getSessionId())
            ->delete();

        return response()->json(['message' => 'Cart cleared', 'cart_count' => 0]);
    }
}