<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kedai Djanggo - Checkout</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .bg-orange-500 { background-color: #f97316; }
        .text-orange-500 { color: #f97316; }
        .border-t-thick { border-top: 2px solid #e5e7eb; }
        .rounded-3xl { border-radius: 1.5rem; }
        .shadow-sm { box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .hover\:bg-orange-600:hover { background-color: #ea580c; } /* Hover lebih gelap */
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen pb-24">
        <!-- Header -->
        <div class="bg-white p-4 sticky top-0 z-10 shadow-sm">
            <div class="flex items-center gap-3">
                <a href="{{ route('menu.index') }}" class="p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-xl font-bold text-gray-900">Pembayaran</h1>
            </div>
        </div>

        <div class="p-4 space-y-4">
            <!-- Order Items Card (Grouped: main item + extras dengan indent & harga per baris) -->
            <div class="bg-white rounded-3xl shadow-sm p-6">
                @foreach ($cart as $item)
                    <div class="flex gap-4 items-start mb-6 last:mb-0">
                        <!-- Item Image (gunakan placeholder atau asset nyata) -->
                        <div class="w-20 h-20 bg-orange-100 rounded-2xl flex-shrink-0 overflow-hidden">
                            <img src="{{ $item['image'] ?? asset('images/default-menu.jpg') }}" 
                                alt="{{ $item['name'] }}" 
                                class="w-full h-full object-cover">    
                        </div>
                        
                        <!-- Item Details -->
                        <div class="flex-1">
                            <h3 class="font-bold text-lg text-gray-900 mb-1">{{ $item['name'] }}</h3>
                            <div class="text-sm text-gray-500 space-y-1">
                                <p>{{ $item['quantity'] }}x {{ $item['name'] }}</p>
                                @if(!empty($item['extras']))
                                    @foreach($item['extras'] as $extra)
                                        <p class="pl-6">{{ $extra['quantity'] }}x {{ $extra['name'] }}</p>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <!-- Prices aligned per line -->
                        <div class="text-right text-sm">
                            <p class="font-bold text-gray-900">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                            @if(!empty($item['extras']))
                                @foreach($item['extras'] as $extra)
                                    <p class="text-gray-500">Rp {{ number_format($extra['price'] * $extra['quantity'], 0, ',', '.') }}</p>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>


            <!-- Order Summary -->
            <div class="bg-white rounded-3xl shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Ringkasan Pesanan</h2>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Food Total</span>
                        <span class="font-medium">Rp {{ number_format($subtotal ?? $total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Delivery</span>
                        <span class="font-medium">Rp {{ number_format($delivery ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Discount</span>
                        <span class="font-medium text-green-600">-Rp {{ number_format($discount ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-4 border-t-thick">
                    <span class="text-lg font-bold text-gray-900">Total Pembayaran</span>
                    <span class="text-2xl font-bold text-orange-500">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-4">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-orange-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold text-gray-900 mb-1">Informasi Pembayaran</p>
                        <p class="text-sm text-gray-600">Setelah klik tombol bayar, Anda akan diarahkan ke halaman pembayaran Midtrans. Pilih metode pembayaran yang tersedia.</p>
                    </div>
                </div>
            </div>

            <!-- Order ID Info -->
            <div class="bg-white rounded-3xl shadow-sm p-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">ID Pesanan</span>
                    <span class="font-bold text-gray-900">{{ $order->midtrans_order_id }}</span>
                </div>
            </div>
        </div>

        <!-- Fixed Payment Button -->
        <div class="fixed bottom-0 left-0 right-0 bg-white px-4 py-4 shadow-lg">
            <button id="pay-button" class="w-full bg-orange-500 text-white py-4 rounded-full font-bold text-base shadow-lg hover:bg-orange-600 transition">
                Process Order
            </button>
        </div>
    </div>

    <!-- Script Midtrans + Debug -->
    <script>
        window.addEventListener('load', function() {
            if (typeof snap === 'undefined') {
                console.error('Midtrans Snap.js gagal load!');
                alert('Midtrans tidak tersedia. Periksa koneksi atau konfigurasi.');
                return;
            }

            const snapToken = '{{ $order->snap_token }}';
            console.log('Snap Token:', snapToken);
            if (!snapToken || snapToken.trim() === '') {
                console.error('Snap token kosong!');
                document.getElementById('pay-button').disabled = true;
                document.getElementById('pay-button').textContent = 'Token Tidak Tersedia';
                return;
            }

            document.getElementById('pay-button').addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Tombol diklik, memanggil snap.pay...');

                snap.pay(snapToken, {
                    onSuccess: function () {
                        fetch('{{ route('order.updatePayment', $order->id) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ status: 'paid' })
                        }).then(() => {
                            window.location.href = '{{ route('order.status', $order->id) }}';
                        });
                    },
                    onPending: function () {
                        window.location.href = '{{ route('order.status', $order->id) }}';
                    },
                    onError: function () {
                        window.location.href = '{{ route('order.status', $order->id) }}';
                    },
                    onClose: function () {
                        window.location.href = '{{ route('order.status', $order->id) }}';
                    }
                });

            });
        });
    </script>
</body>
</html>