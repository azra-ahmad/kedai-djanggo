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
     * Display menu list with search, filter, and sort
     */
    public function index(Request $request)
    {
        $query = Menu::query();
        
        // Search by name
        if ($request->filled('search')) {
            $query->where('nama_menu', 'like', '%' . $request->search . '%');
        }
        
        // Filter by category
        if ($request->filled('category')) {
            $query->where('kategori_menu', $request->category);
        }
        
        // Sorting
        switch ($request->get('sort', 'newest')) {
            case 'price_asc':
                $query->orderBy('harga', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('harga', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('nama_menu', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }
        
        // Get all categories for filter dropdown
        $categories = Menu::select('kategori_menu')->distinct()->pluck('kategori_menu');
        
        // Paginate and preserve query params
        $menus = $query->paginate(12)->appends($request->query());
        
        return view('admin.menu.index', compact('menus', 'categories'));
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
            'is_available' => 'boolean',
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
        
        // Handle is_available checkbox (default true if not in request)
        $validated['is_available'] = $request->boolean('is_available', true);
        
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
            'is_available' => 'boolean',
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
        
        // Handle is_available checkbox
        $validated['is_available'] = $request->boolean('is_available');
        
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
