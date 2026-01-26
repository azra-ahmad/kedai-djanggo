@extends('layouts.admin')

@section('title', 'Dashboard')

@push('styles')
<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #d1d5db;
        border-radius: 20px;
    }
</style>
@endpush

@push('head-scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div x-data="orderPoller">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900 mb-1">Dashboard</h1>
                <p class="text-sm text-gray-500">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <!-- Quick Expense -->
                <a href="{{ route('admin.financial') }}" class="flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Catat Pengeluaran
                </a>
                <!-- Date Filter -->
                <form method="GET" class="flex items-center gap-2 bg-white rounded-xl px-3 py-2 border border-gray-200 shadow-sm">
                    <input type="date" name="start_date" value="{{ $startDate }}" class="border-0 text-xs focus:ring-0 p-0 w-[105px] text-gray-700">
                    <span class="text-gray-300">—</span>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="border-0 text-xs focus:ring-0 p-0 w-[105px] text-gray-700">
                    <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                        Filter
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- LEFT COLUMN (Stats & Content) -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- HERO STATS ROW -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Revenue -->
                    <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-emerald-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full uppercase">Hari Ini</span>
                        </div>
                        <p class="text-xs text-gray-500 font-medium">Omzet</p>
                        <p class="text-lg font-bold text-emerald-600">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
                    </div>

                    <!-- Expense -->
                    <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-8 h-8 bg-rose-50 rounded-lg flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-rose-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                                </svg>
                            </div>
                            <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full uppercase">Keluar</span>
                        </div>
                        <p class="text-xs text-gray-500 font-medium">Pengeluaran</p>
                        <p class="text-lg font-bold text-rose-600">Rp {{ number_format($todayExpense, 0, ',', '.') }}</p>
                    </div>

                    <!-- Net Profit -->
                    <div class="bg-white rounded-xl p-4 border {{ $todayProfit >= 0 ? 'border-blue-200' : 'border-rose-300' }} shadow-sm">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-8 h-8 {{ $todayProfit >= 0 ? 'bg-blue-50' : 'bg-rose-50' }} rounded-lg flex items-center justify-center">
                                @if($todayProfit >= 0)
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-blue-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                </svg>
                                @else
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-rose-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                </svg>
                                @endif
                            </div>
                            <span class="text-[10px] font-bold {{ $todayProfit >= 0 ? 'text-blue-600 bg-blue-50' : 'text-rose-600 bg-rose-50' }} px-2 py-0.5 rounded-full uppercase">Laba</span>
                        </div>
                        <p class="text-xs text-gray-500 font-medium">Laba Bersih</p>
                        <p class="text-lg font-bold {{ $todayProfit >= 0 ? 'text-blue-600' : 'text-rose-600' }}">
                            Rp {{ number_format(abs($todayProfit), 0, ',', '.') }}
                        </p>
                    </div>

                    <!-- Kitchen Status (Summary) -->
                    @php $isBusy = $kitchenQueue >= 5; @endphp
                    <div class="bg-white rounded-xl p-4 border {{ $isBusy ? 'border-amber-300 bg-amber-50' : 'border-gray-200' }} shadow-sm">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-8 h-8 {{ $isBusy ? 'bg-amber-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center">
                                @if($isBusy)
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-amber-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 0 0 .495-7.468 5.99 5.99 0 0 0-1.925 3.547 5.975 5.975 0 0 1-2.133-1.001A3.75 3.75 0 0 0 12 18Z" />
                                </svg>
                                @else
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 0 0 .495-7.468 5.99 5.99 0 0 0-1.925 3.547 5.975 5.975 0 0 1-2.133-1.001A3.75 3.75 0 0 0 12 18Z" />
                                </svg>
                                @endif
                            </div>
                            <span class="text-[10px] font-bold {{ $isBusy ? 'text-amber-700 bg-amber-100' : 'text-gray-500 bg-gray-100' }} px-2 py-0.5 rounded-full uppercase">{{ $isBusy ? 'Ramai' : 'Kitchen' }}</span>
                        </div>
                        <p class="text-xs text-gray-500 font-medium">Antrian</p>
                        <p class="text-lg font-bold {{ $isBusy ? 'text-amber-700' : 'text-gray-900' }}">
                            {{ $kitchenQueue }} Order
                        </p>
                    </div>
                </div>

                <!-- CHART -->
                <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Cash Flow Trend</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($startDate)->format('d M') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
                        </div>
                        <div class="flex gap-4 text-xs">
                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span> Omzet</span>
                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 bg-rose-500 rounded-full"></span> Pengeluaran</span>
                        </div>
                    </div>
                    <div class="relative h-80 w-full">
                        <canvas id="cashFlowChart"></canvas>
                    </div>
                </div>

                <!-- RECENT ORDERS TABLE -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-gray-900">Transaksi Terbaru</h3>
                        <a href="{{ route('admin.orders') }}" class="text-xs font-semibold text-orange-600 hover:text-orange-700">
                            Semua Order →
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500">Waktu</th>
                                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500">Customer</th>
                                    <th class="text-right py-3 px-4 text-xs font-semibold text-gray-500">Total</th>
                                    <th class="text-center py-3 px-4 text-xs font-semibold text-gray-500">Status</th>
                                    <th class="text-right py-3 px-4 text-xs font-semibold text-gray-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($recentOrders as $order)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-3 px-4">
                                        <span class="text-xs font-medium text-gray-700">{{ $order->created_at->format('H:i') }}</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="text-xs font-medium text-gray-900">{{ $order->customer->name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <span class="text-xs font-bold text-gray-900">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                            @if($order->status == 'pending') bg-yellow-100 text-yellow-700
                                            @elseif($order->status == 'paid') bg-emerald-100 text-emerald-700
                                            @elseif($order->status == 'done') bg-blue-100 text-blue-700
                                            @else bg-gray-100 text-gray-700 @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        @if($order->status == 'paid')
                                        <form action="{{ route('admin.complete', $order->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button class="bg-emerald-600 hover:bg-emerald-700 text-white px-2 py-1 rounded text-[10px] font-bold">Done</button>
                                        </form>
                                        @else
                                        <a href="{{ route('admin.struk', $order->id) }}" class="text-xs text-gray-500 hover:text-gray-700">Struk</a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-10 text-center">
                                        <p class="text-xs text-gray-500">Belum ada order hari ini</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN (Operational Lists) -->
            <div class="space-y-6">

                <!-- KITCHEN QUEUE -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm flex flex-col">
                    <div class="p-3 border-b border-gray-200 flex justify-between items-center bg-gray-50 rounded-t-xl">
                        <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                            <span class="text-amber-500">🔥</span> Kitchen Queue
                        </h3>
                        <span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full text-[10px] font-bold">{{ $kitchenQueue }}</span>
                    </div>
                    <div class="p-2 max-h-[350px] overflow-y-auto custom-scrollbar space-y-2">
                        @forelse($kitchenOrders as $order)
                        <div class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm flex flex-col gap-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-bold text-gray-800 text-sm">{{ $order->customer->name ?? 'Guest' }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ $order->orderItems->sum('jumlah') }} Item • {{ $order->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <form action="{{ route('admin.complete', $order->id) }}" method="POST">
                                    @csrf
                                    <button class="text-emerald-600 hover:bg-emerald-50 p-1.5 rounded-lg transition" title="Selesai">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                            <div class="pt-2 border-t border-gray-50">
                                <p class="text-[10px] text-gray-400 line-clamp-2">
                                    @foreach($order->orderItems as $item)
                                    {{ $item->jumlah }}x {{ $item->menu->nama_menu ?? 'Menu' }}{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-6">
                            <p class="text-xs text-gray-400">Tidak ada antrian</p>
                        </div>
                        @endforelse
                    </div>
                    <div class="p-2 border-t border-gray-200 text-center">
                        <a href="{{ route('admin.orders') }}?status=paid" class="text-xs font-semibold text-gray-500 hover:text-gray-900">
                            Lihat Semua →
                        </a>
                    </div>
                </div>

                <!-- STOCK ALERTS -->
                @if($lowStockMenus->count() > 0)
                <div class="bg-white rounded-xl border-l-4 border-rose-500 shadow-sm flex flex-col">
                    <div class="p-3 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-rose-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>
                            Stok Menipis
                        </h3>
                        <span class="bg-rose-100 text-rose-700 px-2 py-0.5 rounded-full text-[10px] font-bold">{{ $lowStockMenus->count() }}</span>
                    </div>
                    <div class="p-2 max-h-[250px] overflow-y-auto custom-scrollbar space-y-1">
                        @foreach($lowStockMenus as $menu)
                        <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded-lg">
                            <span class="text-xs text-gray-700 font-medium">{{ $menu->nama_menu }}</span>
                            <span class="text-[10px] text-rose-500 font-bold">Habis</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="p-2 border-t border-gray-200 text-center">
                        <a href="{{ route('admin.menu.index') }}" class="text-xs font-semibold text-gray-500 hover:text-gray-900">
                            Kelola Menu →
                        </a>
                    </div>
                </div>
                @endif

                <!-- TOP PRODUCTS -->
                <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-amber-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0V9.499a2.25 2.25 0 0 0-2.25-2.25H13.84a2.25 2.25 0 0 1-2.25-2.25V4.25c0-.217.215-.375.487-.375h3.653c.272 0 .487.158.487.375v.5c0 .217-.215.375-.487.375H13.84a2.25 2.25 0 0 1 2.25 2.25v5.249a2.25 2.25 0 0 0 2.25 2.25h.375c.621 0 1.125.504 1.125 1.125v3.375Z" />
                        </svg>
                        Menu Terlaris
                    </h3>
                    <div class="space-y-2.5">
                        @forelse($topItems->take(5) as $index => $item)
                        <div class="flex items-center gap-2.5">
                            <div class="w-6 h-6 bg-gradient-to-br from-orange-500 to-rose-500 rounded-lg flex items-center justify-center text-white text-[10px] font-bold">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-900 truncate">{{ $item->nama_menu }}</p>
                            </div>
                            <span class="text-xs font-bold text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $item->total_sold }}</span>
                        </div>
                        @empty
                        <p class="text-xs text-gray-500 text-center py-4">Belum ada data</p>
                        @endforelse
                    </div>
                </div>

                <!-- PERIOD SUMMARY -->
                <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                    <h3 class="text-xs font-bold text-gray-400 mb-3 uppercase tracking-wider">Periode Ini</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-xs text-gray-500">Omzet</span>
                            <span class="text-sm font-bold text-emerald-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-gray-500">Pengeluaran</span>
                            <span class="text-sm font-bold text-rose-600">Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
                        </div>
                        <div class="border-t border-gray-100 pt-2 mt-2">
                            <div class="flex justify-between">
                                <span class="text-xs font-bold text-gray-900">Laba Bersih</span>
                                <span class="text-sm font-black {{ $netProfit >= 0 ? 'text-blue-600' : 'text-rose-600' }}">
                                    Rp {{ number_format($netProfit, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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
                                new Audio('https://cdn.freesound.org/previews/536/536108_1415754-lq.mp3').play().catch(() => {});
                                const t = document.createElement('div');
                                t.className = 'fixed top-5 right-5 bg-emerald-500 text-white px-5 py-3 rounded-xl shadow-xl z-50 font-bold text-sm';
                                t.innerHTML = '🔔 Orderan Baru!';
                                document.body.appendChild(t);
                                setTimeout(() => location.reload(), 1500);
                            }
                        });
                }, 5000);
            }
        }));
    });

    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('cashFlowChart').getContext('2d');
        const data = @json($revenueChart);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(d => new Date(d.date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })),
                datasets: [
                    {
                        label: 'Omzet',
                        data: data.map(d => d.revenue),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.08)',
                        borderWidth: 2.5,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 1.5
                    },
                    {
                        label: 'Pengeluaran',
                        data: data.map(d => d.expense),
                        borderColor: '#f43f5e',
                        backgroundColor: 'rgba(244, 63, 94, 0.08)',
                        borderWidth: 2.5,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                        pointBackgroundColor: '#f43f5e',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 1.5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 10,
                        titleFont: { size: 11 },
                        bodyFont: { size: 11, weight: '600' },
                        callbacks: {
                            label: ctx => ctx.dataset.label + ': Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6', drawBorder: false },
                        ticks: { font: { size: 10 }, callback: v => 'Rp ' + (v/1000) + 'K' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
    });
</script>
@endpush