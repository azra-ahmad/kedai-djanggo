<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard with analytics
     */
    public function index(Request $request)
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

        // Revenue chart data (last 7 days)
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
        $endDateCarbon = Carbon::parse($end);

        while ($currentDate <= $endDateCarbon) {
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

    /**
     * AJAX endpoint to check for new orders (polling)
     */
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
