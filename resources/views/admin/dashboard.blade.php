<!-- resources/views/admin/dashboard.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Kedai Djanggo Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .stat-card { transition: all 0.3s ease; }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-gray-50">
    @include('admin.partials.sidebar')
    
    <div class="ml-64 p-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1">Hey {{ auth()->user()->name }} 👋</h1>
                <p class="text-gray-500 text-sm">Here's what's happening with your store today</p>
            </div>
            <div class="flex items-center gap-3">
                <form method="GET" class="flex items-center gap-2 bg-white rounded-xl px-4 py-2 border border-gray-200">
                    <input type="date" name="start_date" value="{{ $startDate }}" class="border-0 text-sm focus:ring-0 p-0">
                    <span class="text-gray-400">—</span>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="border-0 text-sm focus:ring-0 p-0">
                    <button type="submit" class="bg-orange-600 text-white px-4 py-1.5 rounded-lg text-sm font-medium hover:bg-orange-700">
                        Filter
                    </button>
                </form>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-4 gap-6 mb-8">
            <div class="stat-card bg-white rounded-2xl p-6 border border-gray-100">
                <div class="flex items-start justify-between mb-4">
                    <div class="bg-green-50 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    @if($totalRevenue > 0)
                    <span class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-full">
                        +{{ number_format((($totalRevenue - 100000) / max($totalRevenue, 1)) * 100, 1) }}%
                    </span>
                    @endif
                </div>
                <p class="text-gray-500 text-xs font-medium mb-1">TOTAL REVENUE</p>
                <h3 class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalRevenue / 1000, 0) }}K</h3>
            </div>

            <div class="stat-card bg-white rounded-2xl p-6 border border-gray-100">
                <div class="flex items-start justify-between mb-4">
                    <div class="bg-blue-50 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded-full">{{ $totalOrders }}</span>
                </div>
                <p class="text-gray-500 text-xs font-medium mb-1">TOTAL ORDERS</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ number_format($totalOrders) }}</h3>
            </div>

            <div class="stat-card bg-white rounded-2xl p-6 border border-gray-100">
                <div class="flex items-start justify-between mb-4">
                    <div class="bg-yellow-50 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-gray-500 text-xs font-medium mb-1">PENDING ORDERS</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ number_format($pendingOrders) }}</h3>
            </div>

            <div class="stat-card bg-white rounded-2xl p-6 border border-gray-100">
                <div class="flex items-start justify-between mb-4">
                    <div class="bg-purple-50 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold text-purple-600 bg-purple-50 px-2 py-1 rounded-full">{{ $completedOrders }}</span>
                </div>
                <p class="text-gray-500 text-xs font-medium mb-1">COMPLETED</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ number_format($completedOrders) }}</h3>
            </div>
        </div>

        <!-- Charts & Data -->
        <div class="grid grid-cols-12 gap-6 mb-8">
            <!-- Revenue Chart -->
            <div class="col-span-8 bg-white rounded-2xl p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-base font-bold text-gray-900">Sales Report</h3>
                    <div class="flex gap-2">
                        <button class="px-3 py-1.5 text-xs font-medium bg-orange-600 text-white rounded-lg">Date Range</button>
                    </div>
                </div>
                <canvas id="revenueChart" height="80"></canvas>
            </div>

            <!-- Top Items -->
            <div class="col-span-4 bg-white rounded-2xl p-6 border border-gray-100">
                <h3 class="text-base font-bold text-gray-900 mb-6">Top Selling</h3>
                <div class="space-y-4">
                    @forelse($topItems as $index => $item)
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-orange-500 to-red-500 rounded-xl flex items-center justify-center text-white font-bold text-sm">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $item->nama_menu }}</p>
                                <p class="text-xs text-gray-500">{{ $item->total_sold }} sold</p>
                            </div>
                            <span class="text-sm font-bold text-gray-900">Rp {{ number_format($item->total_revenue / 1000, 0) }}K</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">No sales data</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Today's Orders -->
        <div class="bg-white rounded-2xl border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-900">Recent Orders</h3>
                    <a href="{{ route('admin.orders') }}" class="text-sm font-medium text-orange-600 hover:text-orange-700">
                        See all →
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Order ID</th>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Items</th>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($todayOrders as $order)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-4 px-6">
                                    <span class="text-sm font-semibold text-gray-900">{{ $order->midtrans_order_id }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-gradient-to-br from-orange-400 to-red-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                            {{ substr($order->customer->name, 0, 1) }}
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ $order->customer->name }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-sm text-gray-600">{{ $order->orderItems->count() }} items</span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-sm font-bold text-gray-900">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        @if($order->status == 'pending') bg-yellow-50 text-yellow-700
                                        @elseif($order->status == 'paid') bg-green-50 text-green-700
                                        @elseif($order->status == 'done') bg-blue-50 text-blue-700
                                        @else bg-red-50 text-red-700
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2">
                                        @if($order->status == 'paid')
                                            <form action="{{ route('admin.complete', $order->id) }}" method="POST">
                                                @csrf
                                                <button class="text-xs font-medium text-blue-600 hover:text-blue-700">Complete</button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.struk', $order->id) }}" class="text-xs font-medium text-orange-600 hover:text-orange-700">Receipt</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-500 text-sm">No orders today</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueData = @json($revenueChart);
        
        console.log('Revenue Data:', revenueData); // Debug
        
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: revenueData.map(d => {
                    const date = new Date(d.date);
                    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
                }),
                datasets: [{
                    label: 'Revenue',
                    data: revenueData.map(d => d.revenue),
                    borderColor: '#ea580c',
                    backgroundColor: 'rgba(234, 88, 12, 0.05)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#ea580c',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2
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
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#374151',
                        borderWidth: 1,
                        displayColors: false,
                        callbacks: {
                            label: (context) => 'Rp ' + context.parsed.y.toLocaleString('id-ID')
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f3f4f6',
                            drawBorder: false
                        },
                        ticks: {
                            font: { size: 11 },
                            color: '#9ca3af',
                            callback: (value) => 'Rp ' + (value / 1000) + 'K'
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 11 },
                            color: '#9ca3af'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>