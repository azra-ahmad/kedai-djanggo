<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\Expense;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard with analytics - "Business Command Center"
     */
    public function index(Request $request)
    {
        // ============================================
        // TODAY'S REAL-TIME STATS (Hero Row)
        // ============================================
        $today = Carbon::today();
        
        $todayRevenue = (float) Order::whereIn('status', ['paid', 'done'])
            ->whereDate('created_at', $today)
            ->sum('total_harga');
        
        $todayExpense = (float) Expense::whereDate('date', $today)->sum('amount');
        
        $todayProfit = $todayRevenue - $todayExpense;
        
        $todayOrders = Order::whereDate('created_at', $today)->count();
        
        // Kitchen Queue: Orders waiting to be prepared (paid but not done)
        $kitchenQueue = Order::where('status', 'paid')->count();
        
        // Kitchen Queue Orders (for display)
        $kitchenOrders = Order::where('status', 'paid')
            ->with(['customer', 'orderItems.menu'])
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get();

        // ============================================
        // DATE RANGE STATS (Configurable Period)
        // ============================================
        $startDate = $request->input('start_date', Carbon::now()->subDays(6)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Revenue (paid + done orders)
        $totalRevenue = (float) Order::whereIn('status', ['paid', 'done'])
            ->whereBetween('created_at', [$start, $end])
            ->sum('total_harga');

        $totalOrders = Order::whereBetween('created_at', [$start, $end])->count();

        // Expenses in date range
        $totalExpense = (float) Expense::whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->sum('amount');

        $netProfit = $totalRevenue - $totalExpense;

        $pendingOrders = Order::where('status', 'pending')->count();

        $completedOrders = Order::where('status', 'done')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        // ============================================
        // DUAL CHART DATA: Revenue vs Expenses
        // ============================================
        $revenueChartRaw = Order::whereIn('status', ['paid', 'done'])
            ->whereBetween('created_at', [$start, $end])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_harga) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy('date');

        // Get expense data - using raw date string as key
        $expenseChartRaw = Expense::whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->select(
                DB::raw('DATE(date) as date_str'),
                DB::raw('SUM(amount) as expense')
            )
            ->groupBy('date_str')
            ->orderBy('date_str', 'asc')
            ->get()
            ->keyBy('date_str');

        // Fill missing dates with zero
        $revenueChart = [];
        $currentDate = Carbon::parse($start);
        $endDateCarbon = Carbon::parse($end);

        while ($currentDate <= $endDateCarbon) {
            $dateStr = $currentDate->format('Y-m-d');
            $revenueData = $revenueChartRaw->get($dateStr);
            $expenseData = $expenseChartRaw->get($dateStr);
            
            $revenue = $revenueData ? (float) $revenueData->revenue : 0;
            $expense = $expenseData ? (float) $expenseData->expense : 0;

            $revenueChart[] = [
                'date' => $dateStr,
                'revenue' => $revenue,
                'expense' => $expense,
                'profit' => $revenue - $expense
            ];

            $currentDate->addDay();
        }

        // ============================================
        // TOP SELLING ITEMS
        // ============================================
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

        // ============================================
        // RECENT ORDERS (Today's Last 10)
        // ============================================
        $recentOrders = Order::whereDate('created_at', Carbon::today())
            ->with(['customer', 'orderItems.menu'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // ============================================
        // STOCK ALERTS (Unavailable Menus)
        // ============================================
        $lowStockMenus = Menu::where('is_available', false)->get();

        // ============================================
        // RECENT CUSTOMERS
        // ============================================
        $recentCustomers = Customer::withCount('orders')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            // Today's Stats
            'todayRevenue',
            'todayExpense',
            'todayProfit',
            'todayOrders',
            'kitchenQueue',
            'kitchenOrders',
            // Date Range Stats
            'totalRevenue',
            'totalOrders',
            'totalExpense',
            'netProfit',
            'pendingOrders',
            'completedOrders',
            // Chart & Lists
            'revenueChart',
            'topItems',
            'recentOrders',
            'lowStockMenus',
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
