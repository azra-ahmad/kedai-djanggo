@props(['orders'])

<!-- Orders Screen -->
<div id="ordersScreen" class="hidden pb-24 min-h-screen">
    <div class="header-gradient p-5 sticky top-0 z-10">
        <h1 class="text-xl font-display font-bold text-gray-900">Pesanan Saya</h1>
    </div>
    <div class="p-5">
        @forelse ($orders as $order)
        <div class="bg-white rounded-3xl p-5 mb-4 shadow-lg border-2 border-[#EBEBEB] hover:shadow-xl transition-all">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <span class="font-bold text-gray-900 block mb-1">Pesanan #{{ $order->midtrans_order_id }}</span>
                    <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }}</span>
                </div>
                <span class="text-xs px-3 py-1.5 rounded-full font-bold {{ $order->status == 'paid' ? 'status-badge-paid' : 'status-badge-pending' }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
            <div class="border-t-2 border-[#EBEBEB] pt-3 mt-3">
                <p class="text-gray-700 font-semibold mb-3 flex items-center gap-2">
                    <span class="text-sm">Total:</span>
                    <span class="gradient-text text-lg">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                </p>
                <a href="{{ route('order.status', $order->id) }}" class="inline-block btn-primary px-6 py-3 rounded-xl text-sm">
                    Lihat Detail →
                </a>
            </div>
        </div>
        @empty
        <div class="text-center py-20">
            <div class="w-32 h-32 bg-gradient-to-br from-[#EBEBEB] to-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                <svg class="w-16 h-16 text-[#EF7722]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <h3 class="text-xl font-display font-bold text-gray-900 mb-2">Belum Ada Pesanan</h3>
            <p class="text-gray-500 text-sm mb-6">Pesanan yang sudah dibayar akan muncul di sini</p>
            <button @click="switchTab('home')" class="btn-primary px-6 py-3 rounded-xl text-sm">
                Mulai Belanja
            </button>
        </div>
        @endforelse
    </div>
</div>
