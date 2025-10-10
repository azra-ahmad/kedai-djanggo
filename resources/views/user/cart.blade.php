<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kedai Djanggo - Cart</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="p-4">
        <h1 class="text-xl font-bold text-gray-900 mb-4">Keranjang</h1>
        @if (empty($cart))
            <div class="p-8 text-center">
                <div class="text-6xl mb-4">🛒</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Keranjang Kosong</h3>
                <p class="text-gray-500 mb-6">Yuk, mulai tambahkan menu favorit kamu!</p>
                <a href="{{ route('menu.index') }}" class="bg-orange-600 text-white px-6 py-3 rounded-xl font-semibold">
                    Lihat Menu
                </a>
            </div>
        @else
            <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-4">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-semibold text-gray-900">Estimasi Penyajian</span>
                </div>
                <p class="text-xs text-gray-600">15-20 menit • Diantar ke meja</p>
            </div>
            <div class="space-y-3 mb-6">
                @foreach ($cart as $item)
                    <div class="bg-white rounded-xl p-4 shadow-sm flex gap-4">
                        <img src="{{ $item['image'] }}" class="w-20 h-20 rounded-lg object-cover">
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-900 mb-1">{{ $item['name'] }}</h4>
                            <p class="text-orange-600 font-semibold mb-2">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                            <div class="flex items-center gap-3">
                                <button onclick="updateQuantity({{ $item['id'] }}, -1)" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-700 font-bold hover:bg-gray-200">-</button>
                                <span class="font-semibold w-8 text-center">{{ $item['quantity'] }}</span>
                                <button onclick="updateQuantity({{ $item['id'] }}, 1)" class="w-8 h-8 rounded-full bg-orange-600 flex items-center justify-center text-white font-bold hover:bg-orange-700">+</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="bg-gray-50 rounded-xl p-4 space-y-3 mb-6">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Pajak (10%)</span>
                    <span class="font-semibold">Rp {{ number_format($tax, 0, ',', '.') }}</span>
                </div>
                <div class="border-t border-gray-200 pt-3 flex justify-between">
                    <span class="font-bold text-gray-900">Total</span>
                    <span class="font-bold text-xl text-orange-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>
            <a href="{{ route('checkout') }}" class="block w-full bg-gradient-to-r from-orange-600 to-red-600 text-white py-4 rounded-xl font-bold text-lg shadow-lg text-center">
                Lanjut ke Pembayaran
            </a>
        @endif
    </div>
    <script>
        function updateQuantity(menuId, delta) {
            fetch('{{ route('cart.update') }}', {
                method: 'POST',
                headers: {
                    'Content-Type' : 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ menu_id: menuId, delta: delta })
            }).then(response => response.json()).then(data => {
                // Reload page or update UI with AJAX
                location.reload();
            });
        }
    </script>
</body>
</html>