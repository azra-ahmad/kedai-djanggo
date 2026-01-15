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
        .stat-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #f97316, #fb923c);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .stat-card:hover::before {
            transform: scaleX(1);
        }
        .gradient-orange {
            background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
        }
        .pulse-dot {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 via-orange-50/30 to-gray-50" x-data="orderPoller">
    @include('admin.partials.sidebar')

    <div class="ml-64 p-6">
        <!-- Header -->
        <div class="flex justify-between items-start mb-6 animate-fade-in">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2">
                    Hey {{ auth()->user()->name }}
                </h1>
                <p class="text-sm text-gray-600">Here's what's happening with your store today</p>
            </div>
            <div class="flex items-center gap-3">
                <form method="GET" class="flex items-center gap-2 bg-white rounded-xl px-4 py-2.5 border border-gray-200 shadow-sm">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="border-0 text-xs focus:ring-0 p-0 text-gray-700 font-medium">
                    <span class="text-gray-300 text-xs">—</span>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="border-0 text-xs focus:ring-0 p-0 text-gray-700 font-medium">
                    <button type="submit" class="gradient-orange text-white px-4 py-1.5 rounded-lg text-xs font-semibold hover:shadow-md transition-all">
                        Apply
                    </button>
                </form>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-4 gap-5 mb-6">
            <!-- Revenue Card -->
            <div class="stat-card bg-white rounded-xl p-5 border border-gray-100 animate-fade-in">
                <div class="flex items-start justify-between mb-3">
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-2.5 rounded-xl">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    @if($totalRevenue > 0)
                    <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded-full flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        {{ number_format((($totalRevenue - 100000) / max($totalRevenue, 1)) * 100, 1) }}%
                    </span>
                    @endif
                </div>
                <p class="text-gray-500 text-xs font-semibold mb-1 uppercase tracking-wide">Total Revenue</p>
                <h3 class="text-2xl font-bold text-gray-900 mb-1">Rp {{ number_format($totalRevenue / 1000, 0) }}K</h3>
                <div class="flex items-center gap-1 text-xs text-gray-500">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full pulse-dot"></span>
                    From {{ $totalOrders }} orders
                </div>
            </div>

            <!-- Total Orders Card -->
            <div class="stat-card bg-white rounded-xl p-5 border border-gray-100 animate-fade-in" style="animation-delay: 0.1s">
                <div class="flex items-start justify-between mb-3">
                    <div class="bg-gradient-to-br from-blue-50 to-sky-50 p-2.5 rounded-xl">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">{{ $totalOrders }}</span>
                </div>
                <p class="text-gray-500 text-xs font-semibold mb-1 uppercase tracking-wide">Total Orders</p>
                <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ number_format($totalOrders) }}</h3>
                <div class="flex items-center gap-1 text-xs text-gray-500">
                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full pulse-dot"></span>
                    All time orders
                </div>
            </div>

            <!-- Pending Orders Card -->
            <div class="stat-card bg-gradient-to-br from-yellow-50 to-amber-50 rounded-xl p-5 border border-yellow-200 animate-fade-in" style="animation-delay: 0.2s">
                <div class="flex items-start justify-between mb-3">
                    <div class="bg-yellow-100 p-2.5 rounded-xl">
                        <svg class="w-5 h-5 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    @if($pendingOrders > 0)
                    <span class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></span>
                    @endif
                </div>
                <p class="text-yellow-700 text-xs font-semibold mb-1 uppercase tracking-wide">Pending Orders</p>
                <h3 class="text-2xl font-bold text-yellow-900 mb-1">{{ number_format($pendingOrders) }}</h3>
                <div class="flex items-center gap-1 text-xs text-yellow-700">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    Awaiting payment
                </div>
            </div>

            <!-- Completed Orders Card -->
            <div class="stat-card bg-gradient-to-br from-purple-50 to-violet-50 rounded-xl p-5 border border-purple-200 animate-fade-in" style="animation-delay: 0.3s">
                <div class="flex items-start justify-between mb-3">
                    <div class="bg-purple-100 p-2.5 rounded-xl">
                        <svg class="w-5 h-5 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-purple-600 bg-purple-100 px-2 py-0.5 rounded-full">{{ $completedOrders }}</span>
                </div>
                <p class="text-purple-700 text-xs font-semibold mb-1 uppercase tracking-wide">Completed</p>
                <h3 class="text-2xl font-bold text-purple-900 mb-1">{{ number_format($completedOrders) }}</h3>
                <div class="flex items-center gap-1 text-xs text-purple-700">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Successfully delivered
                </div>
            </div>
        </div>

        <!-- Charts & Data -->
        <div class="grid grid-cols-12 gap-5 mb-6">
            <!-- Revenue Chart -->
            <div class="col-span-8 bg-white rounded-xl p-5 border border-gray-100 animate-fade-in">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Sales Report
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">Revenue overview for selected period</p>
                    </div>
                    <div class="flex gap-2">
                        <span class="px-3 py-1.5 text-xs font-semibold gradient-orange text-white rounded-lg shadow-sm">Date Range</span>
                    </div>
                </div>
                <div class="relative" style="height: 280px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Top Items -->
            <div class="col-span-4 bg-white rounded-xl p-5 border border-gray-100 animate-fade-in">
                <div class="mb-5">
                    <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        Top Selling Items
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">Best performing products</p>
                </div>
                <div class="space-y-3">
                    @forelse($topItems as $index => $item)
                        <div class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex-shrink-0 w-8 h-8 gradient-orange rounded-lg flex items-center justify-center text-white font-bold text-xs shadow-sm">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-gray-900 truncate">{{ $item->nama_menu }}</p>
                                <p class="text-xs text-gray-500 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
                                    </svg>
                                    {{ $item->total_sold }} sold
                                </p>
                            </div>
                            <span class="text-xs font-bold text-gray-900 bg-gray-100 px-2 py-1 rounded-lg">Rp {{ number_format($item->total_revenue / 1000, 0) }}K</span>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            <p class="text-xs text-gray-500 font-medium">No sales data</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-xl border border-gray-100 animate-fade-in">
            <div class="p-5 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Recent Orders
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">Latest customer orders</p>
                    </div>
                    <a href="{{ route('admin.orders') }}" class="text-xs font-semibold text-orange-600 hover:text-orange-700 flex items-center gap-1 transition-colors">
                        View all orders
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-orange-50/30 border-b border-gray-100">
                        <tr>
                            <th class="text-left py-3 px-5 text-xs font-bold text-gray-600 uppercase tracking-wider">Order ID</th>
                            <th class="text-left py-3 px-5 text-xs font-bold text-gray-600 uppercase tracking-wider">Customer</th>
                            <th class="text-left py-3 px-5 text-xs font-bold text-gray-600 uppercase tracking-wider">Items</th>
                            <th class="text-left py-3 px-5 text-xs font-bold text-gray-600 uppercase tracking-wider">Total</th>
                            <th class="text-left py-3 px-5 text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="text-left py-3 px-5 text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($todayOrders as $order)
                            <tr class="hover:bg-orange-50/30 transition-colors">
                                <td class="py-3.5 px-5">
                                    <span class="text-xs font-semibold text-gray-900 bg-gray-100 px-2 py-1 rounded-md font-mono">{{ $order->midtrans_order_id }}</span>
                                </td>
                                <td class="py-3.5 px-5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 gradient-orange rounded-full flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                            {{ substr($order->customer->name, 0, 1) }}
                                        </div>
                                        <span class="text-xs font-semibold text-gray-900">{{ $order->customer->name }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-5">
                                    <span class="text-xs text-gray-600 font-medium">{{ $order->orderItems->count() }} items</span>
                                </td>
                                <td class="py-3.5 px-5">
                                    <span class="text-xs font-bold text-gray-900">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                                </td>
                                <td class="py-3.5 px-5">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold
                                        @if($order->status == 'pending') bg-yellow-100 text-yellow-700 border border-yellow-200
                                        @elseif($order->status == 'paid') bg-green-100 text-green-700 border border-green-200
                                        @elseif($order->status == 'done') bg-blue-100 text-blue-700 border border-blue-200
                                        @else bg-red-100 text-red-700 border border-red-200
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-5">
                                    <div class="flex items-center gap-2">
                                        @if($order->status == 'paid')
                                            <form action="{{ route('admin.complete', $order->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="flex items-center gap-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-2 rounded-xl text-xs font-bold hover-lift transition-all shadow-md">Complete</button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.struk', $order->id) }}" class="flex items-center gap-2 bg-gradient-to-r from-gray-800 to-gray-900 hover:from-gray-900 hover:to-black text-white px-4 py-2 rounded-xl text-xs font-bold hover-lift transition-all shadow-md"">Receipt</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <p class="text-xs text-gray-500 font-semibold">No orders today</p>
                                    <p class="text-xs text-gray-400 mt-1">Orders will appear here when customers make purchases</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('orderPoller', () => ({
                lastId: {{ \App\Models\Order::where('status', 'paid')->orderBy('id', 'desc')->value('id') ?? 0 }},
                init() {
                    setInterval(() => {
                        fetch('{{ route('admin.check.orders') }}')
                            .then(r => r.json())
                            .then(data => {
                                if (data.latest_id > this.lastId) {
                                    this.lastId = data.latest_id;
                                    this.playNotification();
                                    
                                    // Show toast/alert before reload
                                    const toast = document.createElement('div');
                                    toast.className = 'fixed top-5 right-5 bg-green-500 text-white px-6 py-4 rounded-xl shadow-2xl z-50 animate-bounce font-bold flex items-center gap-3';
                                    toast.innerHTML = '🔔 Orderan Baru Masuk! Memuat ulang...';
                                    document.body.appendChild(toast);
                                    
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 1500);
                                }
                            });
                    }, 5000); // Check every 5 seconds
                },
                playNotification() {
                    const audio = new Audio('https://cdn.freesound.org/previews/536/536108_1415754-lq.mp3'); // Reliable short "Ding" sound
                    audio.play().catch(e => console.log('Audio error:', e));
                }
            }));
        });

        document.addEventListener('DOMContentLoaded', function () {
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
                        borderColor: '#f97316',
                        backgroundColor: (context) => {
                            const ctx = context.chart.ctx;
                            const gradient = ctx.createLinearGradient(0, 0, 0, 280);
                            gradient.addColorStop(0, 'rgba(249, 115, 22, 0.1)');
                            gradient.addColorStop(1, 'rgba(249, 115, 22, 0.01)');
                            return gradient;
                        },
                        tension: 0.4,
                        fill: true,
                        borderWidth: 2.5,
                        pointRadius: 0,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: '#f97316',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            padding: 12,
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            titleFont: { size: 11, weight: 'bold' },
                            bodyFont: { size: 13, weight: '600' },
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
                            border: { display: false },
                            grid: {
                                color: '#f3f4f6',
                                drawBorder: false,
                                drawTicks: false
                            },
                            ticks: {
                                color: '#6b7280',
                                font: { size: 11 },
                                callback: (value) => 'Rp ' + value.toLocaleString('id-ID')
                            }
                        },
                        x: {
                            border: { display: false },
                            grid: { display: false },
                            ticks: {
                                color: '#6b7280',
                                font: { size: 11 },
                                maxRotation: 0,
                                autoSkip: true,
                                maxTicksLimit: 10
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>