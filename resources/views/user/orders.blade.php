<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kedai Djanggo - Pesanan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="p-4">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">Pesanan Saya</h1>
        @forelse ($orders as $order)
            <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-semibold text-gray-900">Pesanan #{{ $order->midtrans_order_id }}</span>
                    <span class="text-sm {{ $order->status == 'paid' ? 'text-green-600' : 'text-yellow-600' }}">{{ ucfirst($order->status) }}</span>
                </div>
                <p class="text-gray-600 text-sm">Total: Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>
                <a href="{{ route('order.status', $order->id) }}" class="text-orange-600 text-sm mt-2 inline-block">Lihat Detail</a>
            </div>
        @empty
            <div class="text-center p-8">
                <p class="text-gray-500">Belum ada pesanan.</p>
            </div>
        @endforelse
    </div>
</body>
</html>