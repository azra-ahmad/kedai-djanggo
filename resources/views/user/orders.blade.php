<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kedai Djanggo - Pesanan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #F5F5F5 0%, #FAFAFA 50%, #FFFFFF 100%); }
    </style>
</head>
<body>
    <div class="p-4">
        <h1 class="text-2xl font-bold text-teal-900 mb-4">Pesanan Saya</h1>
        @forelse ($orders as $order)
            <div class="bg-f5f5f5 rounded-xl shadow-sm p-4 mb-4 border-2 border-teal-200">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-semibold text-teal-900">Pesanan #{{ $order->midtrans_order_id }}</span>
                    <span class="text-sm {{ $order->status == 'paid' ? 'text-green-600' : 'text-yellow-600' }}">{{ ucfirst($order->status) }}</span>
                </div>
                <p class="text-teal-600 text-sm">Total: Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>
                <a href="{{ route('order.status', $order->id) }}" class="text-coral-600 text-sm mt-2 inline-block">Lihat Detail</a>
            </div>
        @empty
            <div class="text-center p-8">
                <p class="text-teal-600">Belum ada pesanan.</p>
            </div>
        @endforelse
    </div>
</body>
</html>