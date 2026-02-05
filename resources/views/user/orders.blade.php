<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kedai Djanggo - Pesanan Saya</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #F5F5F5 0%, #FAFAFA 50%, #FFFFFF 100%); 
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="max-w-lg mx-auto p-4 pb-24">
        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('menu.index') }}" class="p-2 hover:bg-gray-100 rounded-full transition">
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h1 class="text-xl font-bold text-gray-900">Pesanan Saya</h1>
        </div>

        <!-- Orders List -->
        @forelse ($orders as $order)
            <div class="bg-white rounded-2xl shadow-sm p-4 mb-4 border border-gray-100">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <p class="font-bold text-gray-900">#{{ $order->midtrans_order_id }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $order->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    @php
                        $statusConfig = [
                            'pending' => ['label' => 'Menunggu', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
                            'paid' => ['label' => 'Dibayar', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-700'],
                            'done' => ['label' => 'Selesai', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
                            'failed' => ['label' => 'Gagal', 'bg' => 'bg-red-100', 'text' => 'text-red-700'],
                        ];
                        $config = $statusConfig[$order->status] ?? ['label' => ucfirst($order->status), 'bg' => 'bg-gray-100', 'text' => 'text-gray-700'];
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $config['bg'] }} {{ $config['text'] }}">
                        {{ $config['label'] }}
                    </span>
                </div>
                
                <div class="border-t border-gray-100 pt-3 flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500">Total</p>
                        <p class="font-bold text-orange-600">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>
                    </div>
                    <a href="{{ route('order.status', $order->id) }}" 
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl text-sm font-semibold transition">
                        Lihat Detail →
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center py-16">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <p class="text-gray-500 font-medium">Belum ada pesanan</p>
                <p class="text-gray-400 text-sm mt-1">Pesanan kamu akan muncul di sini</p>
            </div>
        @endforelse
    </div>

    <!-- Fixed Bottom Button -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4">
        <a href="{{ route('menu.index') }}" class="block w-full bg-gradient-to-r from-[#EF7722] to-[#FAA533] text-white py-4 rounded-xl font-bold text-lg shadow-lg text-center">
            Pesan Lagi
        </a>
    </div>
</body>
</html>