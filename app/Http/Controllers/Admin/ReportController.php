<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display financial report
     */
    public function financial(Request $request)
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
}
