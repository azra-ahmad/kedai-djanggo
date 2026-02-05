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
                'menus.gambar as image_file', 
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
        $note = $request->note ?? ''; // Normalize null to empty string

        // Cek item duplikat berdasarkan menu_id DAN note
        // Same menu dengan note berbeda = item terpisah
        $existingItem = DB::table('carts')
            ->where('session_id', $sessionId)
            ->where('menu_id', $menuId)
            ->where('note', $note)  // Note is part of unique key now
            ->first();

        if ($existingItem) {
            // Same menu + same note = merge quantity
            DB::table('carts')
                ->where('id', $existingItem->id)
                ->update([
                    'quantity' => $existingItem->quantity + $qty,
                    'updated_at' => now()
                ]);
        } else {
            // New item (different menu OR different note)
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
            'cart_id' => 'required|integer',
            'delta' => 'required|integer',
        ]);

        $sessionId = $this->getSessionId();
        
        // Find by cart_id (unique) instead of menu_id
        $item = DB::table('carts')
            ->where('session_id', $sessionId)
            ->where('id', $request->cart_id)
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

    /**
     * Update note for a specific cart item
     */
    public function updateNote(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|integer',
            'note' => 'nullable|string|max:255'
        ]);

        $updated = DB::table('carts')
            ->where('id', $request->cart_id)
            ->where('session_id', $this->getSessionId())
            ->update([
                'note' => $request->note ?? '',
                'updated_at' => now()
            ]);

        if (!$updated) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }

        return response()->json([
            'message' => 'Note updated',
            'note' => $request->note ?? ''
        ]);
    }
}