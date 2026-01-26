@extends('layouts.admin')

@section('title', 'Financial Report')

@push('head-scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
@endpush

@push('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        .print-full { margin-left: 0 !important; }
        body { background: white !important; }
        .print-break { page-break-after: always; }
    }
</style>
@endpush

@section('content')
<!-- Header -->
<div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 mb-6 no-print">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-1">📊 Laporan Keuangan</h1>
        <p class="text-gray-500 text-sm">Pantau omzet, pengeluaran, dan laba bersih</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <form method="GET" class="flex items-center gap-2 bg-white rounded-xl px-3 py-2 border border-gray-200">
            <input type="date" name="start_date" value="{{ $startDate }}" class="border-0 text-sm focus:ring-0 p-0 w-[120px]" required>
            <span class="text-gray-400">—</span>
            <input type="date" name="end_date" value="{{ $endDate }}" class="border-0 text-sm focus:ring-0 p-0 w-[120px]" required>
            <button type="submit" class="bg-orange-600 text-white px-3 py-1.5 rounded-lg text-sm font-medium hover:bg-orange-700">
                Filter
            </button>
        </form>
        <a href="{{ route('admin.financial.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="hidden sm:inline">Export Excel</span>
        </a>
        <button onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-700 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            <span class="hidden sm:inline">Print</span>
        </button>
    </div>
</div>

<!-- Success Message -->
@if(session('success'))
<div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
    </svg>
    {{ session('success') }}
</div>
@endif

<!-- Print Header (Only visible when printing) -->
<div class="hidden print:block mb-8 text-center border-b-2 border-gray-300 pb-4">
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Kedai Djanggo</h1>
    <h2 class="text-xl font-semibold text-gray-700 mb-2">Laporan Keuangan</h2>
    <p class="text-gray-600">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
</div>

<!-- ============================================ -->
<!-- QUICK EXPENSE INPUT FORM -->
<!-- ============================================ -->
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 no-print">
    <form method="POST" action="{{ route('admin.expense.store') }}" class="flex flex-col lg:flex-row gap-3 items-end">
        @csrf
        <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal</label>
                <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                       class="w-full h-10 px-3 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-orange-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Keterangan</label>
                <input type="text" name="description" placeholder="Contoh: Belanja Pasar" required
                       class="w-full h-10 px-3 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-orange-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Jumlah (Rp)</label>
                <input type="number" name="amount" placeholder="50000" min="0" required
                       class="w-full h-10 px-3 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-orange-500">
            </div>
        </div>
        <button type="submit" class="h-10 px-6 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold transition flex items-center gap-2 whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Catat Pengeluaran
        </button>
    </form>
</div>

<!-- ============================================ -->
<!-- EXECUTIVE SUMMARY (Revenue, Expense, Profit) -->
<!-- ============================================ -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Total Revenue -->
    <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase">Omzet</p>
                <p class="text-lg lg:text-xl font-bold text-green-700">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Total Expenses -->
    <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase">Pengeluaran</p>
                <p class="text-lg lg:text-xl font-bold text-red-700">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Net Profit -->
    <div class="bg-white rounded-xl p-4 border-2 {{ $netProfit >= 0 ? 'border-emerald-300 bg-emerald-50' : 'border-red-300 bg-red-50' }} shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 {{ $netProfit >= 0 ? 'bg-emerald-200' : 'bg-red-200' }} rounded-lg flex items-center justify-center">
                @if($netProfit >= 0)
                <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                @else
                <svg class="w-5 h-5 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                </svg>
                @endif
            </div>
            <div>
                <p class="text-xs {{ $netProfit >= 0 ? 'text-emerald-700' : 'text-red-700' }} font-bold uppercase">Laba Bersih</p>
                <p class="text-lg lg:text-xl font-black {{ $netProfit >= 0 ? 'text-emerald-800' : 'text-red-800' }}">
                    Rp {{ number_format(abs($netProfit), 0, ',', '.') }}
                    @if($netProfit < 0) <span class="text-sm">(Rugi)</span> @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase">Transaksi</p>
                <p class="text-lg lg:text-xl font-bold text-blue-700">{{ number_format($totalOrders) }}</p>
            </div>
        </div>
    </div>
</div>

@if($totalOrders > 0 || $totalExpenses > 0)
<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
    <!-- Revenue Trend Chart -->
    <div class="lg:col-span-8 bg-white rounded-xl p-5 border border-gray-100">
        <h3 class="text-sm font-bold text-gray-900 mb-4">📈 Trend Pendapatan Harian</h3>
        <canvas id="revenueTrendChart" height="100"></canvas>
    </div>

    <!-- Category Distribution -->
    <div class="lg:col-span-4 bg-white rounded-xl p-5 border border-gray-100">
        <h3 class="text-sm font-bold text-gray-900 mb-4">🍽️ Kategori Menu</h3>
        @if($categoryRevenue->count() > 0)
        <div class="flex items-center justify-center mb-4">
            <div style="width: 160px; height: 160px;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
        <div class="space-y-2">
            @foreach($categoryRevenue as $category)
                @php $percentage = $totalRevenue > 0 ? ($category->revenue / $totalRevenue) * 100 : 0; @endphp
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full" style="background-color: {{ ['#ea580c', '#3b82f6', '#ef4444', '#fbbf24', '#a855f7'][$loop->index % 5] }};"></div>
                        <span class="text-gray-700 capitalize">{{ $category->kategori_menu }}</span>
                    </div>
                    <span class="font-bold text-gray-900">{{ number_format($percentage, 0) }}%</span>
                </div>
            @endforeach
        </div>
        @else
        <p class="text-gray-500 text-sm text-center py-8">Belum ada data</p>
        @endif
    </div>
</div>

<!-- Two Column: Recent Expenses & Top Products -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Recent Expenses -->
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-sm font-bold text-gray-900">💸 Pengeluaran Terbaru</h3>
        </div>
        <div class="max-h-[300px] overflow-y-auto">
            @forelse($recentExpenses as $expense)
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-50 hover:bg-gray-50">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">{{ $expense->description }}</p>
                    <p class="text-xs text-gray-500">{{ $expense->date->format('d M Y') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-bold text-red-600">-Rp {{ number_format($expense->amount, 0, ',', '.') }}</span>
                    <form method="POST" action="{{ route('admin.expense.destroy', $expense->id) }}" onsubmit="return confirm('Hapus pengeluaran ini?');" class="no-print">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-gray-400 hover:text-red-600 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="px-4 py-8 text-center text-gray-500 text-sm">
                Belum ada pengeluaran tercatat
            </div>
            @endforelse
        </div>
    </div>

    <!-- Top Products -->
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-900">🏆 Produk Terlaris</h3>
        </div>
        <div class="max-h-[300px] overflow-y-auto">
            @forelse($topProducts->take(5) as $index => $product)
            <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-50">
                <div class="w-7 h-7 bg-gradient-to-br from-orange-500 to-red-500 rounded-lg flex items-center justify-center text-white font-bold text-xs">
                    {{ $index + 1 }}
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">{{ $product->nama_menu }}</p>
                    <p class="text-xs text-gray-500">{{ $product->total_sold }} terjual</p>
                </div>
                <span class="text-sm font-bold text-green-600">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</span>
            </div>
            @empty
            <div class="px-4 py-8 text-center text-gray-500 text-sm">
                Belum ada transaksi
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Daily Revenue Table -->
<div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
    <div class="p-4 border-b border-gray-100">
        <h3 class="text-sm font-bold text-gray-900">📅 Rincian Harian</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="dailyTable">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left py-3 px-4 font-semibold text-gray-600">Tanggal</th>
                    <th class="text-right py-3 px-4 font-semibold text-gray-600">Order</th>
                    <th class="text-right py-3 px-4 font-semibold text-gray-600">Omzet</th>
                    <th class="text-right py-3 px-4 font-semibold text-gray-600">Pengeluaran</th>
                    <th class="text-right py-3 px-4 font-semibold text-gray-600">Nett</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($dailyRevenue as $day)
                    @php
                        $dayExpense = $dailyExpenses[$day->date] ?? null;
                        $dayExpenseAmount = $dayExpense ? $dayExpense->total : 0;
                        $dayNet = $day->revenue - $dayExpenseAmount;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4">
                            <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($day->date)->format('d M Y') }}</span>
                            <span class="text-gray-500 text-xs ml-1">({{ \Carbon\Carbon::parse($day->date)->locale('id')->isoFormat('ddd') }})</span>
                        </td>
                        <td class="py-3 px-4 text-right font-medium">{{ $day->orders }}</td>
                        <td class="py-3 px-4 text-right font-bold text-green-600">Rp {{ number_format($day->revenue, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-right font-medium text-red-600">
                            @if($dayExpenseAmount > 0)
                                -Rp {{ number_format($dayExpenseAmount, 0, ',', '.') }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right font-bold {{ $dayNet >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                            Rp {{ number_format($dayNet, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-900 text-white font-bold">
                <tr>
                    <td class="py-3 px-4">TOTAL</td>
                    <td class="py-3 px-4 text-right">{{ number_format($totalOrders) }}</td>
                    <td class="py-3 px-4 text-right">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                    <td class="py-3 px-4 text-right">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</td>
                    <td class="py-3 px-4 text-right {{ $netProfit >= 0 ? 'text-emerald-400' : 'text-red-400' }}">Rp {{ number_format($netProfit, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@else
<!-- Empty State -->
<div class="bg-white rounded-xl border border-gray-100 p-12 text-center">
    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
        </svg>
    </div>
    <h3 class="text-lg font-bold text-gray-900 mb-2">Belum Ada Data</h3>
    <p class="text-gray-500 text-sm mb-6">Tidak ada transaksi pada periode ini.</p>
    <a href="{{ route('admin.orders') }}" class="bg-orange-600 text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-orange-700 transition">
        Lihat Pesanan
    </a>
</div>
@endif
@endsection

@push('scripts')
<script>
@if($totalOrders > 0)
// Revenue Trend Chart
const trendCtx = document.getElementById('revenueTrendChart').getContext('2d');
const dailyData = @json($dailyRevenue);

new Chart(trendCtx, {
    type: 'bar',
    data: {
        labels: dailyData.map(d => {
            const date = new Date(d.date);
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
        }).reverse(),
        datasets: [{
            label: 'Omzet',
            data: dailyData.map(d => d.revenue).reverse(),
            backgroundColor: 'rgba(34, 197, 94, 0.8)',
            borderColor: '#22c55e',
            borderWidth: 0,
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1f2937',
                padding: 10,
                callbacks: {
                    label: (context) => 'Rp ' + context.parsed.y.toLocaleString('id-ID')
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: '#f3f4f6' },
                ticks: {
                    callback: (value) => 'Rp ' + (value / 1000) + 'K'
                }
            },
            x: { grid: { display: false } }
        }
    }
});

// Category Chart
@if($categoryRevenue->count() > 0)
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
const categoryData = @json($categoryRevenue);

new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: categoryData.map(c => c.kategori_menu.charAt(0).toUpperCase() + c.kategori_menu.slice(1)),
        datasets: [{
            data: categoryData.map(c => c.revenue),
            backgroundColor: ['#ea580c', '#3b82f6', '#ef4444', '#fbbf24', '#a855f7'],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        cutout: '65%',
        plugins: {
            legend: { display: false }
        }
    }
});
@endif
@endif

// Export to Excel Function
function exportToExcel() {
    const wb = XLSX.utils.book_new();
    
    // Summary
    const summaryData = [
        ["Kedai Djanggo - Laporan Keuangan"],
        ["Periode", "{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}"],
        [],
        ["RINGKASAN"],
        ["Total Omzet", "Rp {{ number_format($totalRevenue, 0, ',', '.') }}"],
        ["Total Pengeluaran", "Rp {{ number_format($totalExpenses, 0, ',', '.') }}"],
        ["Laba Bersih", "Rp {{ number_format($netProfit, 0, ',', '.') }}"],
        ["Total Transaksi", "{{ $totalOrders }}"]
    ];
    const wsSummary = XLSX.utils.aoa_to_sheet(summaryData);
    XLSX.utils.book_append_sheet(wb, wsSummary, 'Ringkasan');

    // Daily Table
    const dailyTable = document.getElementById('dailyTable');
    if (dailyTable) {
        const wsDaily = XLSX.utils.table_to_sheet(dailyTable);
        XLSX.utils.book_append_sheet(wb, wsDaily, 'Harian');
    }
    
    XLSX.writeFile(wb, 'Laporan_Keuangan_{{ \Carbon\Carbon::parse($startDate)->format("Ymd") }}_{{ \Carbon\Carbon::parse($endDate)->format("Ymd") }}.xlsx');
}
</script>
@endpush