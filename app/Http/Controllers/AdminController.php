<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Menu;
use App\Models\Customer;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        // Filter by date range
        $startDate = $request->input('start_date', Carbon::now()->subDays(6)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Parse dates properly
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Statistics - Include 'done' status as well for completed orders
        $totalRevenue = Order::whereIn('status', ['paid', 'done'])
            ->whereBetween('created_at', [$start, $end])
            ->sum('total_harga');

        $totalOrders = Order::whereBetween('created_at', [$start, $end])->count();

        $pendingOrders = Order::where('status', 'pending')->count();

        $completedOrders = Order::where('status', 'done')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        // Today's orders
        $todayOrders = Order::whereDate('created_at', Carbon::today())
            ->with(['customer', 'orderItems.menu'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Revenue chart data (last 7 days) - FIXED
        $revenueChartRaw = Order::whereIn('status', ['paid', 'done'])
            ->whereBetween('created_at', [$start, $end])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_harga) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Fill missing dates with zero revenue
        $revenueChart = [];
        $currentDate = Carbon::parse($start);
        $endDate = Carbon::parse($end);

        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $existingData = $revenueChartRaw->firstWhere('date', $dateStr);

            $revenueChart[] = [
                'date' => $dateStr,
                'revenue' => $existingData ? (float) $existingData->revenue : 0
            ];

            $currentDate->addDay();
        }

        // Top selling items
        $topItems = OrderItem::join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['paid', 'done'])
            ->whereBetween('orders.created_at', [$start, $end])
            ->select(
                'menus.nama_menu',
                DB::raw('SUM(order_items.jumlah) as total_sold'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('menus.id', 'menus.nama_menu')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        // Recent customers
        $recentCustomers = Customer::withCount('orders')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'todayOrders',
            'revenueChart',
            'topItems',
            'recentCustomers',
            'startDate',
            'endDate'
        ));
    }

    public function orders(Request $request)
    {
        $status = $request->input('status', 'all');
        $search = $request->input('search'); // Ambil input search

        $query = Order::with(['customer', 'orderItems.menu']);

        // Filter berdasarkan status
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Filter berdasarkan pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('midtrans_order_id', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.orders', compact('orders', 'status'));
    }

    public function assignToSelf($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['admin_id' => auth()->id()]);

        return back()->with('success', 'Order assigned to you');
    }

    public function completeOrder($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status === 'paid') {
            $order->update([
                'status' => 'done',
                'admin_id' => auth()->id() // Auto assign current admin
            ]);
            return back()->with('success', 'Order marked as completed');
        }

        return back()->with('error', 'Only paid orders can be completed');
    }

    public function failOrder($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status === 'pending') {
            $order->update([
                'status' => 'failed',
                'admin_id' => auth()->id()
            ]);
            return back()->with('success', 'Order marked as failed');
        }

        return back()->with('error', 'Only pending orders can be marked as failed');
    }

    public function generateStruk($id)
    {
        $order = Order::with(['customer', 'orderItems.menu'])->findOrFail($id);
        return view('admin.struk', compact('order'));
    }

    // Menu CRUD Methods
    public function menuIndex()
    {
        $menus = Menu::orderBy('kategori_menu')->orderBy('nama_menu')->get();
        return view('admin.menu.index', compact('menus'));
    }

    /**
     * Normalize uploaded image to 800x800 square (center crop)
     * Only processes local uploaded files, not external URLs
     */
    private function normalizeMenuImage($file, $filename)
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

    public function menuCreate()
    {
        return view('admin.menu.create');
    }

    public function menuStore(Request $request)
    {
        $validated = $request->validate([
            'nama_menu' => 'required|string|max:255',
            'kategori_menu' => 'required|in:makanan,minuman,dessert,kopi,cemilan',
            'harga' => 'required|numeric|min:0',
            'gambar' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048', // Max 2MB
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
            // Force rename to .webp extension
            $filename = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.webp';
            $this->normalizeMenuImage($file, $filename);
            $validated['gambar'] = $filename;
        }
        
        Menu::create($validated);
        
        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil ditambahkan! 🎉');
    }

    public function menuEdit($id)
    {
        $menu = Menu::findOrFail($id);
        return view('admin.menu.edit', compact('menu'));
    }

    public function menuUpdate(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        
        $validated = $request->validate([
            'nama_menu' => 'required|string|max:255',
            'kategori_menu' => 'required|in:makanan,minuman,dessert,kopi,cemilan',
            'harga' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048', // Nullable karena optional saat update
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
            // Force rename to .webp extension
            $filename = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.webp';
            $this->normalizeMenuImage($file, $filename);
            $validated['gambar'] = $filename;
        }
        
        $menu->update($validated);
        
        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil diupdate! ✅');
    }

    public function menuDestroy($id)
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

    // Financial Report
    public function financialReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfDay()->format('Y-m-d'));

        // Ensure dates are Carbon instances for comparison
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Daily revenue - Include both 'paid' and 'done' status
        $dailyRevenue = Order::whereIn('status', ['paid', 'done'])
            ->whereBetween('created_at', [$start, $end])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total_harga) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // Category breakdown
        $categoryRevenue = OrderItem::join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['paid', 'done'])
            ->whereBetween('orders.created_at', [$start, $end])
            ->select(
                'menus.kategori_menu',
                DB::raw('SUM(order_items.subtotal) as revenue'),
                DB::raw('COUNT(order_items.id) as items_sold')
            )
            ->groupBy('menus.kategori_menu')
            ->get();

        // Summary
        $totalRevenue = $dailyRevenue->sum('revenue');
        $totalOrders = $dailyRevenue->sum('orders');
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Top selling products
        $topProducts = OrderItem::join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['paid', 'done'])
            ->whereBetween('orders.created_at', [$start, $end])
            ->select(
                'menus.nama_menu',
                'menus.kategori_menu',
                DB::raw('SUM(order_items.jumlah) as total_sold'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('menus.id', 'menus.nama_menu', 'menus.kategori_menu')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();

        return view('admin.financial', compact(
            'dailyRevenue',
            'categoryRevenue',
            'totalRevenue',
            'totalOrders',
            'averageOrderValue',
            'topProducts',
            'startDate',
            'endDate'
        ));
    }

    public function profile()
    {
        return view('admin.profile');
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'password' => 'nullable|min:8|confirmed',
        ]);

        $user = auth()->user();
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($request->filled('password')) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }

    public function checkNewOrders()
    {
        // Check for latest PAID order specifically, as those are the ones needing attention
        $latestOrder = Order::where('status', 'paid')
            ->orderBy('id', 'desc')
            ->first();
            
        $count = Order::where('status', 'paid')
            ->whereDate('created_at', now())
            ->count();

        return response()->json([
            'latest_id' => $latestOrder ? $latestOrder->id : 0,
            'count' => $count
        ]);
    }
}
