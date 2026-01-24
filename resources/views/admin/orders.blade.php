@extends('layouts.admin')

@section('title', 'Manage Orders')

@section('content')
<div x-data="orderPoller">
    <!-- Header -->
    <div class="mb-8 animate-slide-down">
        <div class="flex justify-between items-start mb-8">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div>
                        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Orders Management</h1>
                        <p class="text-sm text-gray-500 font-medium mt-0.5">Manage and track all customer orders</p>
                    </div>
                </div>
            </div>
            <div class="flex gap-3">
                <button onclick="window.location.reload()" class="flex items-center gap-2 bg-gradient-to-r from-orange-500 to-red-600 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-4 gap-5">
            <div class="stat-card glass-card rounded-2xl p-6 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-200 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-orange-600 bg-orange-50 px-3 py-1 rounded-lg">Total</span>
                </div>
                <h3 class="text-3xl font-black text-gray-900 mb-1">{{ $orders->total() }}</h3>
                <p class="text-sm text-gray-600 font-semibold">All Orders</p>
            </div>

            <div class="stat-card glass-card rounded-2xl p-6 shadow-md border-l-4 border-amber-400">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-100 to-amber-200 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-3xl font-black text-amber-700 mb-1">{{ $orders->where('status', 'pending')->count() }}</h3>
                <p class="text-sm text-amber-600 font-semibold">Pending Orders</p>
            </div>

            <div class="stat-card glass-card rounded-2xl p-6 shadow-md border-l-4 border-emerald-400">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-3xl font-black text-emerald-700 mb-1">{{ $orders->where('status', 'paid')->count() }}</h3>
                <p class="text-sm text-emerald-600 font-semibold">Paid Orders</p>
            </div>

            <div class="stat-card glass-card rounded-2xl p-6 shadow-md border-l-4 border-blue-400">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-3xl font-black text-blue-700 mb-1">{{ $orders->where('status', 'done')->count() }}</h3>
                <p class="text-sm text-blue-600 font-semibold">Completed</p>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="glass-card rounded-2xl p-2 mb-6 shadow-md animate-slide-down">
        <div class="flex gap-2">
            <a href="{{ route('admin.orders', ['status' => 'all'] + request()->only('search')) }}"
                class="flex-1 text-center px-4 py-3.5 rounded-xl font-bold text-sm transition-all duration-200 {{ $status == 'all' ? 'bg-gradient-to-r from-orange-500 to-red-600 text-white shadow-lg tab-active' : 'text-gray-600 hover:bg-orange-50 hover:text-orange-600' }}">
                All Orders
            </a>
            <a href="{{ route('admin.orders', ['status' => 'pending'] + request()->only('search')) }}"
                class="flex-1 text-center px-4 py-3.5 rounded-xl font-bold text-sm transition-all duration-200 {{ $status == 'pending' ? 'bg-gradient-to-r from-amber-500 to-amber-600 text-white shadow-lg tab-active' : 'text-gray-600 hover:bg-amber-50 hover:text-amber-600' }}">
                Pending
            </a>
            <a href="{{ route('admin.orders', ['status' => 'paid'] + request()->only('search')) }}"
                class="flex-1 text-center px-4 py-3.5 rounded-xl font-bold text-sm transition-all duration-200 {{ $status == 'paid' ? 'bg-gradient-to-r from-emerald-500 to-emerald-600 text-white shadow-lg tab-active' : 'text-gray-600 hover:bg-emerald-50 hover:text-emerald-600' }}">
                Paid
            </a>
            <a href="{{ route('admin.orders', ['status' => 'done'] + request()->only('search')) }}"
                class="flex-1 text-center px-4 py-3.5 rounded-xl font-bold text-sm transition-all duration-200 {{ $status == 'done' ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg tab-active' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                Completed
            </a>
            <a href="{{ route('admin.orders', ['status' => 'failed'] + request()->only('search')) }}"
                class="flex-1 text-center px-4 py-3.5 rounded-xl font-bold text-sm transition-all duration-200 {{ $status == 'failed' ? 'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg tab-active' : 'text-gray-600 hover:bg-red-50 hover:text-red-600' }}">
                Failed
            </a>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="glass-card rounded-2xl overflow-hidden shadow-md animate-slide-down">
        <div x-data="{ search: '{{ request('search') }}', debounce: null }" x-init="
            $watch('search', value => {
                clearTimeout(debounce)
                debounce = setTimeout(() => {
                    const params = new URLSearchParams()
                    if (value) params.set('search', value)
                    @if(request('status') && request('status') !== 'all')
                        params.set('status', '{{ request('status') }}')
                    @endif
                    window.location.search = params.toString()
                }, 500)
            })
        ">
            <div class="p-6 border-b border-orange-100 bg-gradient-to-r from-orange-50/30 to-transparent">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input
                                type="text"
                                x-model="search"
                                placeholder="Search by ID, customer name, phone..."
                                class="w-full pl-12 pr-4 py-3 bg-white border border-orange-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none transition-all font-medium text-sm shadow-sm">
                        </div>
                    </div>
                    <button @click="search = ''; $nextTick(() => { window.location = '{{ route('admin.orders', ['status' => $status]) }}' })"
                        class="flex items-center gap-2 px-5 py-3 bg-white hover:bg-orange-50 text-gray-700 rounded-xl font-semibold text-sm border border-orange-200 transition-all shadow-sm hover:shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Clear
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-gray-50 to-orange-50/30">
                    <tr>
                        <th class="text-left py-4 px-6 font-black text-gray-700 text-xs uppercase tracking-wider">Order ID</th>
                        <th class="text-left py-4 px-6 font-black text-gray-700 text-xs uppercase tracking-wider">Customer</th>
                        <th class="text-left py-4 px-6 font-black text-gray-700 text-xs uppercase tracking-wider">Items</th>
                        <th class="text-left py-4 px-6 font-black text-gray-700 text-xs uppercase tracking-wider">Total</th>
                        <th class="text-left py-4 px-6 font-black text-gray-700 text-xs uppercase tracking-wider">Status</th>
                        <th class="text-left py-4 px-6 font-black text-gray-700 text-xs uppercase tracking-wider">Date</th>
                        <th class="text-left py-4 px-6 font-black text-gray-700 text-xs uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-orange-100/50 bg-white">
                    @forelse($orders as $order)
                    <tr class="table-row">
                        <td class="py-5 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-orange-100 to-orange-200 rounded-xl flex items-center justify-center shadow-sm">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                </div>
                                <span class="font-mono text-sm font-bold text-gray-900">{{ $order->midtrans_order_id }}</span>
                            </div>
                        </td>
                        <td class="py-5 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-red-500 rounded-full flex items-center justify-center shadow-md">
                                    <span class="text-white font-bold text-sm">{{ strtoupper(substr($order->customer->name, 0, 2)) }}</span>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 text-sm">{{ $order->customer->name }}</p>
                                    <p class="text-xs text-gray-500 font-medium">{{ $order->customer->phone }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-5 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center shadow-sm">
                                    <span class="text-gray-700 font-black text-sm">{{ $order->orderItems->count() }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 text-sm">{{ $order->orderItems->count() }} items</p>
                                    <p class="text-xs text-gray-500 font-medium max-w-xs truncate">
                                        @foreach($order->orderItems->take(2) as $item)
                                        {{ $item->menu->nama_menu }}{{ !$loop->last ? ', ' : '' }}
                                        @endforeach
                                        @if($order->orderItems->count() > 2)
                                        +{{ $order->orderItems->count() - 2 }} more
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="py-5 px-6">
                            <div class="inline-block bg-gradient-to-br from-emerald-50 to-green-50 px-4 py-2 rounded-lg">
                                <p class="text-xs text-emerald-600 font-bold mb-0.5">Total</p>
                                <p class="font-black text-gray-900 text-base">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>
                            </div>
                        </td>
                        <td class="py-5 px-6">
                            <span class="status-badge px-4 py-2 rounded-xl text-xs font-bold inline-flex items-center gap-2 shadow-sm
                                    @if($order->status == 'pending') bg-gradient-to-r from-amber-100 to-amber-50 text-amber-700 border border-amber-300
                                    @elseif($order->status == 'paid') bg-gradient-to-r from-emerald-100 to-emerald-50 text-emerald-700 border border-emerald-300
                                    @elseif($order->status == 'done') bg-gradient-to-r from-blue-100 to-blue-50 text-blue-700 border border-blue-300
                                    @else bg-gradient-to-r from-red-100 to-red-50 text-red-700 border border-red-300
                                    @endif">
                                <span class="w-2 h-2 rounded-full
                                        @if($order->status == 'pending') bg-amber-500
                                        @elseif($order->status == 'paid') bg-emerald-500
                                        @elseif($order->status == 'done') bg-blue-500
                                        @else bg-red-500
                                        @endif">
                                </span>
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="py-5 px-6">
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $order->created_at->format('d M Y') }}</p>
                                <p class="text-xs text-gray-500 font-semibold">{{ $order->created_at->format('H:i') }} WIB</p>
                            </div>
                        </td>
                        <td class="py-5 px-6">
                            <div class="flex gap-2">
                                @if($order->status == 'paid')
                                <form action="{{ route('admin.complete', $order->id) }}" method="POST">
                                    @csrf
                                    <button class="flex items-center gap-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-2 rounded-xl text-xs font-bold hover-lift transition-all shadow-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Complete
                                    </button>
                                </form>
                                @endif

                                @if($order->status == 'pending')
                                <form action="{{ route('admin.fail', $order->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');">
                                    @csrf
                                    <button class="flex items-center gap-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-4 py-2 rounded-xl text-xs font-bold hover-lift transition-all shadow-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Batalkan
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('admin.struk', $order->id) }}" target="_blank" class="flex items-center gap-2 bg-gradient-to-r from-gray-800 to-gray-900 hover:from-gray-900 hover:to-black text-white px-4 py-2 rounded-xl text-xs font-bold hover-lift transition-all shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Receipt
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-20 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-20 h-20 bg-gradient-to-br from-orange-100 to-orange-200 rounded-2xl flex items-center justify-center mb-5 shadow-lg">
                                    <svg class="w-10 h-10 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-black text-gray-900 mb-2">No Orders Found</h3>
                                <p class="text-sm text-gray-500 font-medium">There are no orders matching your criteria</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-orange-100 bg-gradient-to-r from-gray-50 to-orange-50/30">
            {{ $orders->appends(request()->query())->links() }}
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
                                this.playNotification();
                                
                                const toast = document.createElement('div');
                                toast.className = 'fixed top-5 right-5 bg-green-500 text-white px-6 py-4 rounded-xl shadow-2xl z-50 animate-bounce font-bold flex items-center gap-3';
                                toast.innerHTML = '🔔 Orderan Baru Masuk! Memuat ulang...';
                                document.body.appendChild(toast);
                                
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1500);
                            }
                        });
                }, 5000);
            },
            playNotification() {
                const audio = new Audio('https://cdn.freesound.org/previews/536/536108_1415754-lq.mp3');
                audio.play().catch(e => console.log('Audio error:', e));
            }
        }));
    });
</script>
@endpush