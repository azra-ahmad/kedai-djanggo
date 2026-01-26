<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display financial report with revenue, expenses, and net profit
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

        // Revenue Summary
        $totalRevenue = $dailyRevenue->sum('revenue');
        $totalOrders = $dailyRevenue->sum('orders');
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // ============================================
        // EXPENSE TRACKING (Simple Cash Flow)
        // ============================================
        $totalExpenses = Expense::whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->sum('amount');

        $netProfit = $totalRevenue - $totalExpenses;

        // Recent expenses for the period
        $recentExpenses = Expense::whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Daily expenses grouped
        $dailyExpenses = Expense::whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->select(
                'date',
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get()
            ->keyBy(function ($item) {
                return $item->date->format('Y-m-d');
            });

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
            'endDate',
            'totalExpenses',
            'netProfit',
            'recentExpenses',
            'dailyExpenses'
        ));
    }

    /**
     * Store a new expense entry
     */
    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:50',
        ]);

        Expense::create([
            'date' => $validated['date'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'category' => $validated['category'] ?? 'operasional',
        ]);

        return back()->with('success', 'Pengeluaran berhasil dicatat!');
    }

    /**
     * Delete an expense entry
     */
    public function destroyExpense($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return back()->with('success', 'Pengeluaran berhasil dihapus!');
    }

    /**
     * Export financial report to styled Excel file
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Get revenue data
        $totalRevenue = (float) Order::whereIn('status', ['paid', 'done'])
            ->whereBetween('created_at', [$start, $end])
            ->sum('total_harga');

        $totalExpenses = (float) Expense::whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->sum('amount');

        $netProfit = $totalRevenue - $totalExpenses;

        // Category revenue
        $categoryRevenue = OrderItem::join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['paid', 'done'])
            ->whereBetween('orders.created_at', [$start, $end])
            ->select(
                'menus.kategori_menu',
                DB::raw('SUM(order_items.subtotal) as revenue')
            )
            ->groupBy('menus.kategori_menu')
            ->get();

        // =====================
        // SHEET 1: RINGKASAN
        // =====================
        $summaryData = [
            ['<center><b>LAPORAN KEUANGAN KEDAI DJANGGO</b></center>'],
            ['<center>Periode: ' . Carbon::parse($startDate)->format('d M Y') . ' - ' . Carbon::parse($endDate)->format('d M Y') . '</center>'],
            [''],
            ['<style bgcolor="#EEEEEE"><b>Kategori</b></style>', '<style bgcolor="#EEEEEE"><b>Jumlah (Rp)</b></style>'],
            ['Total Omzet', 'Rp ' . number_format($totalRevenue, 0, ',', '.')],
            ['Total Pengeluaran', 'Rp ' . number_format($totalExpenses, 0, ',', '.')],
            [
                '<b>Laba Bersih</b>',
                $netProfit >= 0
                    ? '<style color="#008000"><b>Rp ' . number_format($netProfit, 0, ',', '.') . '</b></style>'
                    : '<style color="#FF0000"><b>Rp ' . number_format($netProfit, 0, ',', '.') . ' (Rugi)</b></style>'
            ],
            [''],
            ['<style bgcolor="#EEEEEE"><b>Pendapatan per Kategori</b></style>', ''],
        ];

        foreach ($categoryRevenue as $cat) {
            $summaryData[] = [ucfirst($cat->kategori_menu), 'Rp ' . number_format($cat->revenue, 0, ',', '.')];
        }

        // =====================
        // SHEET 2: MUTASI DETAIL
        // =====================
        $detailData = [
            [
                '<style bgcolor="#EEEEEE"><b>Tanggal</b></style>',
                '<style bgcolor="#EEEEEE"><b>Tipe</b></style>',
                '<style bgcolor="#EEEEEE"><b>Deskripsi</b></style>',
                '<style bgcolor="#EEEEEE"><b>Nominal</b></style>',
                '<style bgcolor="#EEEEEE"><b>Status</b></style>',
            ]
        ];

        // Get orders
        $orders = Order::whereIn('status', ['paid', 'done'])
            ->whereBetween('created_at', [$start, $end])
            ->with('customer')
            ->get()
            ->map(function ($order) {
                return [
                    'date' => $order->created_at,
                    'type' => 'income',
                    'description' => 'Order #' . $order->id . ' - ' . ($order->customer->name ?? 'Guest'),
                    'amount' => $order->total_harga,
                    'status' => ucfirst($order->status),
                ];
            });

        // Get expenses
        $expenses = Expense::whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->get()
            ->map(function ($expense) {
                return [
                    'date' => $expense->date,
                    'type' => 'expense',
                    'description' => $expense->description,
                    'amount' => $expense->amount,
                    'status' => 'Tercatat',
                ];
            });

        // Merge and sort by date
        $mutations = $orders->concat($expenses)->sortByDesc('date');

        foreach ($mutations as $mutation) {
            $typeLabel = $mutation['type'] === 'income'
                ? '<style color="#008000">Pemasukan</style>'
                : '<style color="#FF0000">Pengeluaran</style>';

            $amountFormatted = $mutation['type'] === 'income'
                ? '<style color="#008000">Rp ' . number_format($mutation['amount'], 0, ',', '.') . '</style>'
                : '<style color="#FF0000">-Rp ' . number_format($mutation['amount'], 0, ',', '.') . '</style>';

            $detailData[] = [
                $mutation['date']->format('d/m/Y H:i'),
                $typeLabel,
                $mutation['description'],
                $amountFormatted,
                $mutation['status'],
            ];
        }

        // Generate Excel
        $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($summaryData, 'Ringkasan');
        $xlsx->addSheet($detailData, 'Mutasi Detail');

        $filename = 'Laporan-Keuangan-' . Carbon::parse($startDate)->format('Ymd') . '-' . Carbon::parse($endDate)->format('Ymd') . '.xlsx';

        return $xlsx->downloadAs($filename);
    }
}
