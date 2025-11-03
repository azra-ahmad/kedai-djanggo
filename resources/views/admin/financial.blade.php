<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Report - Kedai Djanggo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            .print-full { margin-left: 0 !important; }
            body { background: white !important; }
            .print-break { page-break-after: always; }
        }
        .stat-card { transition: all 0.3s ease; }
        .stat-card:hover { transform: translateY(-2px); }
    </style>
</head>
<body class="bg-gray-50">
    <div class="no-print">
        @include('admin.partials.sidebar')
    </div>
    
    <div class="ml-64 print-full p-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8 no-print">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1">📊 Financial Report</h1>
                <p class="text-gray-500 text-sm">Comprehensive revenue and sales analytics</p>
            </div>
            <div class="flex gap-3">
                <form method="GET" class="flex items-center gap-2 bg-white rounded-xl px-4 py-2 border border-gray-200">
                    <input type="date" name="start_date" value="{{ $startDate }}" class="border-0 text-sm focus:ring-0 p-0" required>
                    <span class="text-gray-400">—</span>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="border-0 text-sm focus:ring-0 p-0" required>
                    <button type="submit" class="bg-orange-600 text-white px-4 py-1.5 rounded-lg text-sm font-medium hover:bg-orange-700">
                        Filter
                    </button>
                </form>
                <button onclick="exportToExcel()" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Excel
                </button>
                <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print PDF
                </button>
            </div>
        </div>

        <!-- Print Header (Only visible when printing) -->
        <div class="hidden print:block mb-8 text-center border-b-2 border-gray-300 pb-4">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Kedai Djanggo</h1>
            <h2 class="text-xl font-semibold text-gray-700 mb-2">Financial Report</h2>
            <p class="text-gray-600">Period: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
            <p class="text-sm text-gray-500">Generated: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</p>
        </div>

        @if($totalOrders > 0)
        <!-- Executive Summary -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Executive Summary
            </h3>
            <div class="grid grid-cols-4 gap-6">
                <div class="stat-card bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 border border-green-200">
                    <p class="text-green-700 text-xs font-semibold mb-1 uppercase">Total Revenue</p>
                    <h3 class="text-2xl font-bold text-green-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                    <p class="text-green-600 text-xs mt-1">{{ \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1 }} days period</p>
                </div>
                <div class="stat-card bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200">
                    <p class="text-blue-700 text-xs font-semibold mb-1 uppercase">Total Orders</p>
                    <h3 class="text-2xl font-bold text-blue-900">{{ number_format($totalOrders) }}</h3>
                    <p class="text-blue-600 text-xs mt-1">Completed transactions</p>
                </div>
                <div class="stat-card bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 border border-purple-200">
                    <p class="text-purple-700 text-xs font-semibold mb-1 uppercase">Avg Order Value</p>
                    <h3 class="text-2xl font-bold text-purple-900">Rp {{ number_format($averageOrderValue, 0, ',', '.') }}</h3>
                    <p class="text-purple-600 text-xs mt-1">Per transaction</p>
                </div>
                <div class="stat-card bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-4 border border-orange-200">
                    <p class="text-orange-700 text-xs font-semibold mb-1 uppercase">Daily Avg Revenue</p>
                    <h3 class="text-2xl font-bold text-orange-900">Rp {{ number_format($totalRevenue / max(\Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1, 1), 0, ',', '.') }}</h3>
                    <p class="text-orange-600 text-xs mt-1">Per day average</p>
                </div>
            </div>
        </div>

        <!-- Revenue Analysis -->
        <div class="grid grid-cols-12 gap-6 mb-8">
            <!-- Daily Trend Chart -->
            <div class="col-span-8 bg-white rounded-2xl p-6 border border-gray-100">
                <h3 class="text-base font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                    </svg>
                    Revenue Trend Analysis
                </h3>
                <canvas id="revenueTrendChart" height="80"></canvas>
            </div>

            <!-- Category Distribution -->
            <div class="col-span-4 bg-white rounded-2xl p-6 border border-gray-100">
                <h3 class="text-base font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                    </svg>
                    Category Mix
                </h3>
                @if($categoryRevenue->count() > 0)
                <div class="flex items-center justify-center mb-4">
                    <div style="width: 200px; height: 200px;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
                <div class="space-y-2 mt-4">
                    @foreach($categoryRevenue as $category)
                        @php
                            $percentage = ($category->revenue / $totalRevenue) * 100;
                        @endphp
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <div class="flex items-center gap-2 flex-1">
                                <div class="w-3 h-3 rounded-full" style="background-color: {{ ['#ea580c', '#3b82f6', '#ef4444', '#fbbf24', '#a855f7'][$loop->index % 5] }};"></div>
                                <span class="text-xs font-medium text-gray-700 capitalize">{{ $category->kategori_menu }}</span>
                            </div>
                            <span class="text-xs font-bold text-gray-900">{{ number_format($percentage, 1) }}%</span>
                        </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- Product Performance -->
        <div class="bg-white rounded-2xl border border-gray-100 mb-8">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Product Performance Analysis
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full" id="productTable">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Rank</th>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Product</th>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Category</th>
                            <th class="text-right py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Units Sold</th>
                            <th class="text-right py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Revenue</th>
                            <th class="text-right py-3 px-6 text-xs font-semibold text-gray-500 uppercase">% of Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($topProducts as $index => $product)
                            @php
                                $percentage = ($product->total_revenue / $totalRevenue) * 100;
                            @endphp
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-4 px-6">
                                    <div class="w-8 h-8 bg-gradient-to-br from-orange-500 to-red-500 rounded-lg flex items-center justify-center text-white font-bold text-xs">
                                        {{ $index + 1 }}
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-sm font-semibold text-gray-900">{{ $product->nama_menu }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 capitalize">
                                        {{ $product->kategori_menu }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <span class="text-sm font-semibold text-gray-900">{{ number_format($product->total_sold) }}</span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <span class="text-sm font-bold text-green-600">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <div class="w-16 h-2 bg-gray-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-orange-500 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-700">{{ number_format($percentage, 1) }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Daily Breakdown -->
        <div class="bg-white rounded-2xl border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Daily Revenue Breakdown
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full" id="dailyTable">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Date</th>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Day</th>
                            <th class="text-right py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Orders</th>
                            <th class="text-right py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Revenue</th>
                            <th class="text-right py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Avg/Order</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($dailyRevenue as $day)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-4 px-6">
                                    <span class="text-sm font-semibold text-gray-900">
                                        {{ \Carbon\Carbon::parse($day->date)->format('d M Y') }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($day->date)->locale('id')->isoFormat('dddd') }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <span class="text-sm font-semibold text-gray-900">{{ $day->orders }}</span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <span class="text-sm font-bold text-green-600">
                                        Rp {{ number_format($day->revenue, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <span class="text-sm text-gray-600">
                                        Rp {{ number_format($day->revenue / $day->orders, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-900 text-white font-bold">
                        <tr>
                            <td colspan="2" class="py-4 px-6 text-sm">GRAND TOTAL</td>
                            <td class="py-4 px-6 text-right text-sm">{{ number_format($totalOrders) }}</td>
                            <td class="py-4 px-6 text-right text-sm">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                            <td class="py-4 px-6 text-right text-sm">Rp {{ number_format($averageOrderValue, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @else
        <!-- Empty State -->
        <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">No Financial Data Available</h3>
            <p class="text-gray-500 text-sm mb-6">Belum ada transaksi yang selesai pada periode ini.</p>
            <div class="flex gap-3 justify-center">
                <a href="{{ route('admin.orders') }}" class="bg-orange-600 text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-orange-700 transition">
                    View All Orders
                </a>
                <form method="GET" class="inline">
                    <input type="hidden" name="start_date" value="{{ \Carbon\Carbon::now()->subMonths(3)->format('Y-m-d') }}">
                    <input type="hidden" name="end_date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                    <button class="bg-gray-100 text-gray-700 px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-gray-200 transition">
                        Show Last 3 Months
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

    <script>
        // Revenue Trend Chart (tetep sama, gak ubah)
        @if($totalOrders > 0)
        const trendCtx = document.getElementById('revenueTrendChart').getContext('2d');
        const dailyData = @json($dailyRevenue);
        
        new Chart(trendCtx, {
            type: 'bar',
            data: {
                labels: dailyData.map(d => {
                    const date = new Date(d.date);
                    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
                }),
                datasets: [{
                    label: 'Revenue',
                    data: dailyData.map(d => d.revenue),
                    backgroundColor: 'rgba(234, 88, 12, 0.8)',
                    borderColor: '#ea580c',
                    borderWidth: 0,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 12,
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

        // Category Chart (tetep sama)
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
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + percentage + '%';
                            }
                        }
                    }
                }
            }
        });
        @endif

        // Export to Excel Function (UPDATED: Lebih bagus dengan formatting, charts, sheet tambahan)
        function exportToExcel() {
            const wb = XLSX.utils.book_new();
            
            // Workbook Properties (metadata)
            wb.Props = {
                Title: "Kedai Djanggo Financial Report",
                Author: "Admin Kedai Djanggo",
                CreatedDate: new Date()
            };

            // Summary Sheet (dengan bold, colors, alignment)
            const summaryData = [
                ["Kedai Djanggo - Financial Report", null, null, null],
                ["Period", "{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}", null, null],
                ["Generated", "{{ \Carbon\Carbon::now()->format('d M Y H:i') }}", null, null],
                [],
                ["EXECUTIVE SUMMARY"],
                ["Total Revenue", "Rp {{ number_format($totalRevenue, 0, ',', '.') }}"],
                ["Total Orders", "{{ $totalOrders }}"],
                ["Average Order Value", "Rp {{ number_format($averageOrderValue, 0, ',', '.') }}"],
                ["Daily Average Revenue", "Rp {{ number_format($totalRevenue / max(\Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1, 1), 0, ',', '.') }}"]
            ];
            const wsSummary = XLSX.utils.aoa_to_sheet(summaryData);
            
            // Styling untuk Summary
            const merge = [{ s: { r: 0, c: 0 }, e: { r: 0, c: 3 } }, { s: { r: 4, c: 0 }, e: { r: 4, c: 3 } }]; // Merge cells buat title
            wsSummary["!merges"] = merge;
            // Bold & font size buat headers
            ['A1', 'A5'].forEach(cell => {
                wsSummary[cell].s = { font: { bold: true, sz: 14, color: { rgb: "EA580C" } }, alignment: { horizontal: 'center' } };
            });
            // Green color buat values
            ['B6', 'B7', 'B8', 'B9'].forEach(cell => {
                wsSummary[cell].s = { font: { bold: true, color: { rgb: "16A34A" } }, alignment: { horizontal: 'right' } };
            });
            // Auto width columns
            wsSummary["!cols"] = [{ wch: 25 }, { wch: 20 }, { wch: 15 }, { wch: 15 }];
            
            XLSX.utils.book_append_sheet(wb, wsSummary, 'Summary');

            // Category Revenue Sheet (NEW: Tambah sheet ini dengan pie chart)
            @if($categoryRevenue->count() > 0)
            const categorySheetData = [
                ['Category', 'Revenue', '% of Total']
            ];
            categoryData.forEach(c => {
                const percentage = ((c.revenue / {{ $totalRevenue }}) * 100).toFixed(1);
                categorySheetData.push([c.kategori_menu.charAt(0).toUpperCase() + c.kategori_menu.slice(1), c.revenue, percentage]);
            });
            // Tambah total row
            categorySheetData.push(['TOTAL', {{ $totalRevenue }}, '100.0']);
            
            const wsCategory = XLSX.utils.aoa_to_sheet(categorySheetData);
            
            // Styling: Header bold orange, revenue green, % alignment right
            wsCategory['A1'].s = wsCategory['B1'].s = wsCategory['C1'].s = { font: { bold: true, color: { rgb: "EA580C" } }, fill: { fgColor: { rgb: "F3F4F6" } } };
            for (let i = 2; i <= categorySheetData.length; i++) {
                wsCategory[`B${i}`].s = { font: { color: { rgb: "16A34A" } }, alignment: { horizontal: 'right' } };
                wsCategory[`C${i}`].s = { alignment: { horizontal: 'right' } };
            }
            // Total row bold
            const totalRow = categorySheetData.length;
            wsCategory[`A${totalRow}`].s = wsCategory[`B${totalRow}`].s = wsCategory[`C${totalRow}`].s = { font: { bold: true } };
            
            // Tambah Pie Chart di sheet (SheetJS support)
            wsCategory['!charts'] = [{
                type: 'pie',
                range: `A1:C${categorySheetData.length - 1}`,  // Exclude total
                title: 'Category Revenue Distribution',
                position: { x: 4, y: 1, w: 10, h: 10 }  // Posisi chart di sheet
            }];
            
            wsCategory["!cols"] = [{ wch: 20 }, { wch: 15 }, { wch: 10 }];
            XLSX.utils.book_append_sheet(wb, wsCategory, 'Category Revenue');
            @endif

            // Daily Revenue Sheet (dari table, tapi dengan style tambahan)
            const dailyTable = document.getElementById('dailyTable');
            const wsDaily = XLSX.utils.table_to_sheet(dailyTable);
            
            // Styling: Header bold, revenue green, alignment
            const dailyRange = XLSX.utils.decode_range(wsDaily['!ref']);
            for (let C = dailyRange.s.c; C <= dailyRange.e.c; ++C) {
                const headerCell = XLSX.utils.encode_cell({ r: 0, c: C });
                if (wsDaily[headerCell]) wsDaily[headerCell].s = { font: { bold: true, color: { rgb: "EA580C" } }, fill: { fgColor: { rgb: "F3F4F6" } }, alignment: { horizontal: 'center' } };
            }
            // Revenue column (asumsi column D)
            for (let R = 1; R <= dailyRange.e.r; ++R) {
                const revenueCell = XLSX.utils.encode_cell({ r: R, c: 3 });
                if (wsDaily[revenueCell]) wsDaily[revenueCell].s = { font: { color: { rgb: "16A34A" } }, alignment: { horizontal: 'right' } };
            }
            // Footer grand total bold
            const footerRow = dailyRange.e.r;
            for (let C = dailyRange.s.c; C <= dailyRange.e.c; ++C) {
                const footerCell = XLSX.utils.encode_cell({ r: footerRow, c: C });
                if (wsDaily[footerCell]) wsDaily[footerCell].s = { font: { bold: true, color: { rgb: "FFFFFF" } }, fill: { fgColor: { rgb: "1F2937" } } };
            }
            
            // Tambah Bar Chart buat revenue trend
            wsDaily['!charts'] = [{
                type: 'bar',
                range: `A1:D${dailyRange.e.r}`,  // Adjust based on columns
                title: 'Daily Revenue Trend',
                position: { x: 6, y: 1, w: 10, h: 10 }
            }];
            
            wsDaily["!cols"] = [{ wch: 15 }, { wch: 15 }, { wch: 10 }, { wch: 15 }, { wch: 15 }];
            XLSX.utils.book_append_sheet(wb, wsDaily, 'Daily Revenue');

            // Product Performance Sheet (mirip daily, dengan style)
            const productTable = document.getElementById('productTable');
            const wsProduct = XLSX.utils.table_to_sheet(productTable);
            
            // Styling serupa
            const productRange = XLSX.utils.decode_range(wsProduct['!ref']);
            for (let C = productRange.s.c; C <= productRange.e.c; ++C) {
                const headerCell = XLSX.utils.encode_cell({ r: 0, c: C });
                if (wsProduct[headerCell]) wsProduct[headerCell].s = { font: { bold: true, color: { rgb: "EA580C" } }, fill: { fgColor: { rgb: "F3F4F6" } }, alignment: { horizontal: 'center' } };
            }
            // Revenue column (asumsi column E)
            for (let R = 1; R <= productRange.e.r; ++R) {
                const revenueCell = XLSX.utils.encode_cell({ r: R, c: 4 });
                if (wsProduct[revenueCell]) wsProduct[revenueCell].s = { font: { color: { rgb: "16A34A" } }, alignment: { horizontal: 'right' } };
            }
            
            wsProduct["!cols"] = [{ wch: 10 }, { wch: 20 }, { wch: 15 }, { wch: 10 }, { wch: 15 }, { wch: 10 }];
            XLSX.utils.book_append_sheet(wb, wsProduct, 'Product Performance');
            
            // Download file
            const fileName = 'Financial_Report_{{ \Carbon\Carbon::parse($startDate)->format("Y-m-d") }}_to_{{ \Carbon\Carbon::parse($endDate)->format("Y-m-d") }}.xlsx';
            XLSX.writeFile(wb, fileName, { bookType: 'xlsx', compression: true });
        }
        @endif
    </script>
</body>
</html>