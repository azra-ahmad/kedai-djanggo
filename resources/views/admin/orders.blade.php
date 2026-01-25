@extends('layouts.admin')

@section('title', 'Kitchen Display - Orders')

@section('content')
<div x-data="orderPoller">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-4 mb-6">
        <div>
            <h1 class="text-2xl lg:text-3xl font-black text-gray-900 tracking-tight">Orders Management</h1>
            <p class="text-sm text-gray-500 font-medium mt-0.5">Kitchen Display System</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.location.reload()" class="flex items-center gap-2 bg-gradient-to-r from-orange-500 to-red-600 text-white px-4 py-2.5 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span class="hidden sm:inline">Refresh</span>
            </button>
        </div>
    </div>

    <!-- Stats Cards (Accurate counts) -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-6">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-black text-gray-900">{{ $totalOrders }}</p>
                    <p class="text-xs text-gray-500 font-semibold">Total</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-amber-400">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-black text-amber-700">{{ $pendingOrders }}</p>
                    <p class="text-xs text-amber-600 font-semibold">Pending</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-emerald-400">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-black text-emerald-700">{{ $paidOrders }}</p>
                    <p class="text-xs text-emerald-600 font-semibold">Paid</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-blue-400">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-black text-blue-700">{{ $completedOrders }}</p>
                    <p class="text-xs text-blue-600 font-semibold">Done</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-red-400 col-span-2 lg:col-span-1">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-black text-red-700">{{ $failedOrders }}</p>
                    <p class="text-xs text-red-600 font-semibold">Failed</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sticky Toolbar -->
    <div class="sticky top-0 z-20 bg-white/95 backdrop-blur-sm rounded-xl border border-gray-200 p-3 lg:p-4 mb-6 shadow-sm">
        <form method="GET" action="{{ route('admin.orders') }}" class="flex flex-col gap-3 lg:flex-row lg:items-center lg:gap-3">
            
            <!-- Search Input -->
            <div class="relative flex-1 order-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search order ID, name, phone..." 
                       class="w-full h-10 pl-9 pr-3 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
            </div>

            <!-- Date Filters -->
            <div class="flex gap-2 order-3 lg:order-2 lg:flex-none">
                <input type="date" name="start_date" value="{{ request('start_date') }}" 
                       class="h-10 px-3 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 bg-white flex-1 lg:w-[140px]"
                       title="Start Date">
                <input type="date" name="end_date" value="{{ request('end_date') }}" 
                       class="h-10 px-3 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 bg-white flex-1 lg:w-[140px]"
                       title="End Date">
                <button type="submit" class="h-10 px-4 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-sm font-semibold transition flex-shrink-0">
                    Filter
                </button>
            </div>

            <!-- Hidden status field to preserve tab selection -->
            <input type="hidden" name="status" value="{{ $status }}">

            <!-- Reset -->
            <div class="flex gap-2 order-2 lg:order-3 lg:flex-none">
                @if(request()->hasAny(['search', 'start_date', 'end_date']))
                <a href="{{ route('admin.orders', ['status' => $status]) }}" class="h-10 w-10 flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg transition" title="Reset Filters">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Status Filter Tabs -->
    <div class="flex gap-2 mb-6 overflow-x-auto pb-2 -mx-1 px-1">
        <a href="{{ route('admin.orders', ['status' => 'all'] + request()->only(['search', 'start_date', 'end_date'])) }}"
           class="flex-shrink-0 px-4 py-2.5 rounded-xl font-bold text-sm transition-all {{ $status == 'all' ? 'bg-gradient-to-r from-orange-500 to-red-600 text-white shadow-lg' : 'bg-white text-gray-600 hover:bg-orange-50 border border-gray-200' }}">
            All
        </a>
        <a href="{{ route('admin.orders', ['status' => 'pending'] + request()->only(['search', 'start_date', 'end_date'])) }}"
           class="flex-shrink-0 px-4 py-2.5 rounded-xl font-bold text-sm transition-all {{ $status == 'pending' ? 'bg-gradient-to-r from-amber-500 to-amber-600 text-white shadow-lg' : 'bg-white text-gray-600 hover:bg-amber-50 border border-gray-200' }}">
            Pending
        </a>
        <a href="{{ route('admin.orders', ['status' => 'paid'] + request()->only(['search', 'start_date', 'end_date'])) }}"
           class="flex-shrink-0 px-4 py-2.5 rounded-xl font-bold text-sm transition-all {{ $status == 'paid' ? 'bg-gradient-to-r from-emerald-500 to-emerald-600 text-white shadow-lg' : 'bg-white text-gray-600 hover:bg-emerald-50 border border-gray-200' }}">
            Paid
        </a>
        <a href="{{ route('admin.orders', ['status' => 'done'] + request()->only(['search', 'start_date', 'end_date'])) }}"
           class="flex-shrink-0 px-4 py-2.5 rounded-xl font-bold text-sm transition-all {{ $status == 'done' ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg' : 'bg-white text-gray-600 hover:bg-blue-50 border border-gray-200' }}">
            Completed
        </a>
        <a href="{{ route('admin.orders', ['status' => 'failed'] + request()->only(['search', 'start_date', 'end_date'])) }}"
           class="flex-shrink-0 px-4 py-2.5 rounded-xl font-bold text-sm transition-all {{ $status == 'failed' ? 'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg' : 'bg-white text-gray-600 hover:bg-red-50 border border-gray-200' }}">
            Failed
        </a>
    </div>

    <!-- ============================================ -->
    <!-- DESKTOP VIEW: Table with sticky actions -->
    <!-- ============================================ -->
    <div class="hidden lg:block bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left py-3 px-4 font-bold text-gray-700 text-xs uppercase tracking-wider">Order</th>
                        <th class="text-left py-3 px-4 font-bold text-gray-700 text-xs uppercase tracking-wider">Customer</th>
                        <th class="text-left py-3 px-4 font-bold text-gray-700 text-xs uppercase tracking-wider">Items</th>
                        <th class="text-left py-3 px-4 font-bold text-gray-700 text-xs uppercase tracking-wider">Total</th>
                        <th class="text-left py-3 px-4 font-bold text-gray-700 text-xs uppercase tracking-wider">Status</th>
                        <th class="text-left py-3 px-4 font-bold text-gray-700 text-xs uppercase tracking-wider sticky right-0 bg-gray-50 shadow-[-4px_0_6px_-1px_rgba(0,0,0,0.05)]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                    <tr class="hover:bg-orange-50/30 transition">
                        <!-- Order ID & Date -->
                        <td class="py-4 px-4">
                            <p class="font-mono text-sm font-bold text-gray-900">{{ $order->midtrans_order_id }}</p>
                            <p class="text-xs text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        </td>
                        <!-- Customer -->
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-gradient-to-br from-orange-400 to-red-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-xs">{{ strtoupper(substr($order->customer->name, 0, 2)) }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 text-sm">{{ $order->customer->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->customer->phone }}</p>
                                </div>
                            </div>
                        </td>
                        <!-- Items -->
                        <td class="py-4 px-4">
                            <p class="font-semibold text-gray-900 text-sm">{{ $order->orderItems->count() }} items</p>
                            <p class="text-xs text-gray-500 max-w-[200px] truncate">
                                @foreach($order->orderItems->take(2) as $item)
                                {{ $item->quantity }}x {{ $item->menu->nama_menu }}{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                                @if($order->orderItems->count() > 2)...@endif
                            </p>
                        </td>
                        <!-- Total -->
                        <td class="py-4 px-4">
                            <p class="font-bold text-gray-900">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>
                        </td>
                        <!-- Status -->
                        <td class="py-4 px-4">
                            <span class="px-3 py-1.5 rounded-lg text-xs font-bold inline-flex items-center gap-1.5
                                @if($order->status == 'pending') bg-amber-100 text-amber-700
                                @elseif($order->status == 'paid') bg-emerald-100 text-emerald-700
                                @elseif($order->status == 'done') bg-blue-100 text-blue-700
                                @else bg-red-100 text-red-700
                                @endif">
                                <span class="w-1.5 h-1.5 rounded-full
                                    @if($order->status == 'pending') bg-amber-500
                                    @elseif($order->status == 'paid') bg-emerald-500
                                    @elseif($order->status == 'done') bg-blue-500
                                    @else bg-red-500
                                    @endif"></span>
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <!-- Actions (Sticky) -->
                        <td class="py-4 px-4 sticky right-0 bg-white shadow-[-4px_0_6px_-1px_rgba(0,0,0,0.05)]">
                            <div class="flex gap-2">
                                @if($order->status == 'paid')
                                <form action="{{ route('admin.complete', $order->id) }}" method="POST">
                                    @csrf
                                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                        Complete
                                    </button>
                                </form>
                                @endif

                                @if($order->status == 'pending')
                                <form action="{{ route('admin.fail', $order->id) }}" method="POST" onsubmit="return confirm('Cancel this order?');">
                                    @csrf
                                    <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                        Cancel
                                    </button>
                                </form>
                                @endif

                                <a href="{{ route('admin.struk', $order->id) }}" target="_blank" class="bg-gray-800 hover:bg-gray-900 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                    Receipt
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-1">No Orders Found</h3>
                                <p class="text-sm text-gray-500">No orders match your current filters</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MOBILE VIEW: Kitchen Tickets -->
    <!-- ============================================ -->
    <div class="lg:hidden space-y-4">
        @forelse($orders as $order)
        <div class="bg-white rounded-xl shadow-sm border-t-4 overflow-hidden
            @if($order->status == 'pending') border-t-amber-400
            @elseif($order->status == 'paid') border-t-emerald-400
            @elseif($order->status == 'done') border-t-blue-400
            @else border-t-red-400
            @endif">
            
            <!-- Ticket Header -->
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 bg-gray-50">
                <div class="flex items-center gap-3">
                    <span class="font-mono font-bold text-gray-900">#{{ Str::afterLast($order->midtrans_order_id, '-') }}</span>
                    <span class="text-xs text-gray-500">{{ $order->created_at->format('H:i') }}</span>
                </div>
                <span class="px-2.5 py-1 rounded-lg text-xs font-bold
                    @if($order->status == 'pending') bg-amber-100 text-amber-700
                    @elseif($order->status == 'paid') bg-emerald-100 text-emerald-700
                    @elseif($order->status == 'done') bg-blue-100 text-blue-700
                    @else bg-red-100 text-red-700
                    @endif">
                    {{ ucfirst($order->status) }}
                </span>
            </div>

            <!-- Ticket Body: Items (BOLD for kitchen) -->
            <div class="px-4 py-4">
                <div class="space-y-2">
                    @foreach($order->orderItems as $item)
                    <div class="flex justify-between items-start">
                        <div class="flex gap-2">
                            <span class="font-black text-orange-600 text-lg">{{ $item->quantity }}x</span>
                            <span class="font-bold text-gray-900 text-base">{{ $item->menu->nama_menu }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Total -->
                <div class="mt-4 pt-3 border-t border-dashed border-gray-200 flex justify-between items-center">
                    <span class="text-sm text-gray-500">Total</span>
                    <span class="font-black text-gray-900 text-lg">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Ticket Footer: Customer & Actions -->
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-orange-400 to-red-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-xs">{{ strtoupper(substr($order->customer->name, 0, 2)) }}</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">{{ $order->customer->name }}</p>
                            <p class="text-xs text-gray-500">{{ $order->customer->phone }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.struk', $order->id) }}" target="_blank" class="text-gray-600 hover:text-gray-900 p-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </a>
                </div>
                
                <!-- Big Action Buttons -->
                <div class="flex gap-2">
                    @if($order->status == 'paid')
                    <form action="{{ route('admin.complete', $order->id) }}" method="POST" class="flex-1">
                        @csrf
                        <button class="w-full bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white py-3 rounded-xl font-black text-base transition shadow-lg">
                            ✓ COMPLETE
                        </button>
                    </form>
                    @elseif($order->status == 'pending')
                    <div class="flex-1 bg-amber-100 text-amber-700 py-3 rounded-xl font-bold text-center text-sm">
                        ⏳ Waiting Payment
                    </div>
                    <form action="{{ route('admin.fail', $order->id) }}" method="POST" onsubmit="return confirm('Cancel this order?');">
                        @csrf
                        <button class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-3 rounded-xl font-bold text-sm transition">
                            Cancel
                        </button>
                    </form>
                    @elseif($order->status == 'done')
                    <div class="flex-1 bg-blue-100 text-blue-700 py-3 rounded-xl font-bold text-center text-sm">
                        ✓ Completed
                    </div>
                    @else
                    <div class="flex-1 bg-red-100 text-red-700 py-3 rounded-xl font-bold text-center text-sm">
                        ✗ Failed/Cancelled
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl p-8 text-center">
            <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">No Orders</h3>
            <p class="text-sm text-gray-500">No orders match your filters</p>
        </div>
        @endforelse

        <!-- Mobile Pagination -->
        <div class="mt-4">
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
                                toast.innerHTML = '🔔 New Order! Reloading...';
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