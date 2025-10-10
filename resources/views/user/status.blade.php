<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kedai Djanggo - Status Pesanan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="p-4">
        <h1 class="text-xl font-bold text-gray-900 mb-4">Status Pesanan #{{ $order->id }}</h1>
        <div class="bg-white rounded-xl p-4 shadow-sm mb-4">
            <p class="text-gray-600">Nama: {{ $order->customer->name }}</p>
            <p class="text-gray-600">Status: {{ ucfirst($order->status) }}</p>
            <p class="text-gray-600">Total: Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>
        </div>
        <div class="space-y-3">
            @foreach ($order->orderItems as $item)
                <div class="bg-white rounded-xl p-4 shadow-sm flex gap-4">
                    <img src="{{ $item->menu->gambar }}" class="w-20 h-20 rounded-lg object-cover">
                    <div>
                        <h4 class="font-bold text-gray-900 mb-1">{{ $item->menu->nama_menu }}</h4>
                        <p class="text-orange-600 font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                        <p class="text-gray-600">Jumlah: {{ $item->jumlah }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>