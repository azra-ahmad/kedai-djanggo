<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class MenuController extends Controller
{
    /**
     * Display menu list
     */
    public function index()
    {
        $menus = Menu::orderBy('kategori_menu')->orderBy('nama_menu')->get();
        return view('admin.menu.index', compact('menus'));
    }

    /**
     * Show create menu form
     */
    public function create()
    {
        return view('admin.menu.create');
    }

    /**
     * Store new menu item
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_menu' => 'required|string|max:255',
            'kategori_menu' => 'required|in:makanan,minuman,dessert,kopi,cemilan',
            'harga' => 'required|numeric|min:0',
            'gambar' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
            'description' => 'nullable|string',
        ], [
            'nama_menu.required' => 'Nama menu harus diisi',
            'kategori_menu.required' => 'Kategori harus dipilih',
            'kategori_menu.in' => 'Kategori tidak valid',
            'harga.required' => 'Harga harus diisi',
            'harga.numeric' => 'Harga harus berupa angka',
            'harga.min' => 'Harga tidak boleh negatif',
            'gambar.required' => 'Gambar menu harus diupload',
            'gambar.image' => 'File harus berupa gambar',
            'gambar.mimes' => 'Format gambar harus JPEG, JPG, PNG, atau WebP',
            'gambar.max' => 'Ukuran gambar maksimal 2MB',
        ]);
        
        // Upload and normalize image
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.webp';
            $this->normalizeImage($file, $filename);
            $validated['gambar'] = $filename;
        }
        
        Menu::create($validated);
        
        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil ditambahkan! 🎉');
    }

    /**
     * Show edit menu form
     */
    public function edit($id)
    {
        $menu = Menu::findOrFail($id);
        return view('admin.menu.edit', compact('menu'));
    }

    /**
     * Update menu item
     */
    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        
        $validated = $request->validate([
            'nama_menu' => 'required|string|max:255',
            'kategori_menu' => 'required|in:makanan,minuman,dessert,kopi,cemilan',
            'harga' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'description' => 'nullable|string',
        ], [
            'nama_menu.required' => 'Nama menu harus diisi',
            'kategori_menu.required' => 'Kategori harus dipilih',
            'kategori_menu.in' => 'Kategori tidak valid',
            'harga.required' => 'Harga harus diisi',
            'harga.numeric' => 'Harga harus berupa angka',
            'harga.min' => 'Harga tidak boleh negatif',
            'gambar.image' => 'File harus berupa gambar',
            'gambar.mimes' => 'Format gambar harus JPEG, JPG, PNG, atau WebP',
            'gambar.max' => 'Ukuran gambar maksimal 2MB',
        ]);
        
        // Upload file baru jika ada
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada (hanya untuk local files)
            if ($menu->gambar && !str_starts_with($menu->gambar, 'http') && Storage::disk('public')->exists('menu-images/' . $menu->gambar)) {
                Storage::disk('public')->delete('menu-images/' . $menu->gambar);
            }
            
            $file = $request->file('gambar');
            $filename = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.webp';
            $this->normalizeImage($file, $filename);
            $validated['gambar'] = $filename;
        }
        
        $menu->update($validated);
        
        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil diupdate! ✅');
    }

    /**
     * Delete menu item
     */
    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        
        // Check if menu has orders
        if ($menu->orderItems()->count() > 0) {
            return back()->with('error', 'Menu tidak bisa dihapus karena sudah ada transaksi! ⚠️');
        }
        
        // Hapus gambar dari storage (hanya untuk local files)
        if ($menu->gambar && !str_starts_with($menu->gambar, 'http') && Storage::disk('public')->exists('menu-images/' . $menu->gambar)) {
            Storage::disk('public')->delete('menu-images/' . $menu->gambar);
        }
        
        $menu->delete();
        
        return back()->with('success', 'Menu berhasil dihapus! 🗑️');
    }

    /**
     * Normalize uploaded image to 500x500 square (center crop)
     * Only processes local uploaded files, not external URLs
     */
    private function normalizeImage($file, $filename)
    {
        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());
            
            // Center crop to 500x500 (Optimized for performance)
            $image->cover(500, 500);
            
            // Save to storage as WebP (Optimized for Web)
            $path = 'menu-images/' . $filename;
            Storage::disk('public')->put($path, $image->toWebp(80)->toString());
            
            return true;
        } catch (\Exception $e) {
            // If image processing fails, fall back to original upload
            $file->storeAs('menu-images', $filename, 'public');
            return false;
        }
    }

    /**
     * Toggle menu availability (stock status)
     */
    public function toggleAvailability(Menu $menu)
    {
        $menu->update(['is_available' => !$menu->is_available]);
        
        $status = $menu->is_available ? 'tersedia' : 'tidak tersedia';
        
        // Return JSON if AJAX request
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_available' => $menu->is_available,
                'message' => "Menu {$menu->nama_menu} sekarang {$status}"
            ]);
        }
        
        return back()->with('success', "Menu {$menu->nama_menu} sekarang {$status}");
    }
}
