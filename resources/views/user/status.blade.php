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
        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 0.875rem;
        }
        .status-pending { background: #FEF3C7; color: #92400E; }
        .status-paid { background: #D1FAE5; color: #065F46; }
        .status-failed { background: #FEE2E2; color: #991B1B; }
        .status-done { background: #DBEAFE; color: #1E40AF; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen pb-20">
        <!-- Header -->
        <div class="bg-white p-4 sticky top-0 z-10 shadow-sm">
            <div class="flex items-center gap-3">
                <a href="{{ route('menu.index') }}" class="p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-xl font-bold text-gray-900">Status Pesanan</h1>
            </div>
        </div>

        <div class="p-4">
            <!-- Status Card -->
            <div class="bg-white rounded-2xl shadow-sm p-6 mb-4">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Order ID</p>
                        <p class="text-lg font-bold text-gray-900">{{ $order->midtrans_order_id }}</p>
                    </div>
                    <span class="status-badge status-{{ $order->status }}">
                        @if($order->status == 'pending')
                            Menunggu Pembayaran
                        @elseif($order->status == 'paid')
                            Dibayar
                        @elseif($order->status == 'failed')
                            Gagal
                        @elseif($order->status == 'done')
                            Selesai
                        @endif
                    </span>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Nama</span>
                        <span class="font-semibold text-gray-900">{{ $order->customer->name }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Telepon</span>
                        <span class="font-semibold text-gray-900">{{ $order->customer->phone }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Waktu Pesan</span>
                        <span class="font-semibold text-gray-900">{{ $order->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="mb-4">
                <h2 class="text-lg font-bold text-gray-900 mb-3">Detail Pesanan</h2>
                <div class="space-y-3">
                    @foreach ($order->orderItems as $item)
                        <div class="bg-white rounded-xl p-4 shadow-sm flex gap-4">
                            <img src="{{ $item->menu->gambar }}" class="w-20 h-20 rounded-lg object-cover">
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 mb-1">{{ $item->menu->nama_menu }}</h4>
                                <p class="text-sm text-gray-500 mb-1">{{ $item->jumlah }}x Rp {{ number_format($item->menu->harga, 0, ',', '.') }}</p>
                                <p class="text-orange-600 font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Total Summary -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">Ringkasan Pembayaran</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="font-bold text-gray-900">Total Pembayaran</span>
                        <span class="font-bold text-xl text-orange-600">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Status Info -->
            @if($order->status == 'pending')
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mt-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="font-semibold text-gray-900 mb-1">Menunggu Pembayaran</p>
                            <p class="text-sm text-gray-600">Silakan selesaikan pembayaran untuk melanjutkan pesanan Anda.</p>
                        </div>
                    </div>
                </div>
            @elseif($order->status == 'paid')
                <div class="bg-green-50 border border-green-200 rounded-xl p-4 mt-4">
                    <div class="flex items-start gap-3">
                        <div class="relative">
                            <svg class="w-6 h-6 text-green-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 mb-1">Pembayaran Berhasil! 🎉</p>
                            <div class="space-y-2 text-sm text-gray-700">
                                <p class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Pesanan sedang diproses oleh dapur
                                </p>
                                <p class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Estimasi waktu: <strong>15-20 menit</strong>
                                </p>
                                <p class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Pesanan akan diantar ke meja Anda
                                </p>
                            </div>
                            <div class="mt-3 bg-white border border-green-300 rounded-lg p-3">
                                <p class="text-xs font-semibold text-green-800 mb-1">💡 Tips:</p>
                                <p class="text-xs text-gray-600">Sambil menunggu, Anda bisa pesan menu lainnya atau nikmati suasana kedai kami!</p>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($order->status == 'done')
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mt-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <div>
                            <p class="font-semibold text-gray-900 mb-1">Pesanan Selesai ✨</p>
                            <p class="text-sm text-gray-600">Pesanan Anda sudah siap dan telah diantar. Selamat menikmati! 🍜☕</p>
                            <p class="text-sm text-gray-600 mt-2">Terima kasih telah memesan di <strong>Kedai Djanggo</strong>! 🙏</p>
                        </div>
                    </div>
                </div>
            @elseif($order->status == 'failed')
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mt-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <div>
                            <p class="font-semibold text-gray-900 mb-1">Pesanan Dibatalkan</p>
                            <p class="text-sm text-gray-600">Pesanan ini telah dibatalkan. Silakan pesan lagi atau hubungi staff jika ada pertanyaan.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Fixed Bottom Button -->
        <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 space-y-2">
            @if($order->status == 'pending')
                <form action="{{ route('order.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pesanan?')">
                    @csrf
                    <button type="submit" class="block w-full bg-red-600 text-white py-3 rounded-xl font-bold shadow-lg text-center mb-2">
                        Batalkan Pesanan
                    </button>
                </form>
            @endif
            <a href="{{ route('menu.index') }}" class="block w-full bg-gradient-to-r from-orange-600 to-red-600 text-white py-4 rounded-xl font-bold text-lg shadow-lg text-center">
                Kembali ke Menu
            </a>
        </div>
    </div>

    <script>
        function simulatePayment() {
            if (confirm('Simulasi pembayaran berhasil?')) {
                fetch('{{ route('order.updatePayment', $order->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        status: 'paid'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Payment updated:', data);
                    // Clear cart setelah payment berhasil
                    return fetch('{{ route('cart.clear') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                })
                .then(() => {
                    alert('Pembayaran berhasil! Pesanan sedang diproses.');
                    location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating status');
                });
            }
        }

        // Auto refresh every 5 seconds if pending
        @if($order->status == 'pending')
            setInterval(() => {
                fetch('{{ route('order.status', $order->id) }}', {
                    headers: { 'Accept': 'application/json' }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Checking status:', data);
                    if (data.status !== 'pending') {
                        // Clear cart when payment successful
                        fetch('{{ route('cart.clear') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        }).then(() => {
                            console.log('Cart cleared, reloading page');
                            location.reload();
                        });
                    }
                })
                .catch(error => console.error('Error checking status:', error));
            }, 5000);
        @endif
    </script>
</body>
</html>