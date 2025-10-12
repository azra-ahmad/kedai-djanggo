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

        <div class="p-4">
            <!-- Order Summary -->
            <div class="bg-white rounded-2xl shadow-sm p-6 mb-4">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Ringkasan Pesanan</h2>
                
                <div class="space-y-3 mb-4">
                    @foreach ($cart as $item)
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">{{ $item['name'] }}</p>
                                <p class="text-sm text-gray-500">{{ $item['quantity'] }}x Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                            </div>
                            <p class="font-semibold text-gray-900">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-gray-200 pt-4 space-y-2">
                    <div class="flex justify-between">
                        <span class="font-bold text-gray-900">Total Pembayaran</span>
                        <span class="font-bold text-xl text-orange-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-4">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-orange-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold text-gray-900 mb-1">Informasi Pembayaran</p>
                        <p class="text-sm text-gray-600">Setelah klik tombol bayar, Anda akan diarahkan ke halaman pembayaran Midtrans. Pilih metode pembayaran yang tersedia.</p>
                    </div>
                </div>
            </div>

            <!-- Order ID Info -->
            <div class="bg-white rounded-2xl shadow-sm p-4 mb-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">ID Pesanan</span>
                    <span class="font-bold text-gray-900">{{ $order->midtrans_order_id }}</span>
                </div>
            </div>
        </div>

        <!-- Fixed Payment Button -->
        <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4">
            <button id="pay-button" class="w-full bg-gradient-to-r from-orange-600 to-red-600 text-white py-4 rounded-xl font-bold text-lg shadow-lg">
                Bayar Sekarang
            </button>
        </div>
    </div>

    <script>
        document.getElementById('pay-button').addEventListener('click', function() {
            snap.pay('{{ $order->snap_token }}', {
                onSuccess: function(result) {
                    console.log('Payment success:', result);
                    // Update status langsung
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
                        console.log('Payment status updated:', data);
                        // Clear cart setelah payment berhasil
                        return fetch('{{ route('cart.clear') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                    })
                    .then(() => {
                        console.log('Cart cleared, redirecting...');
                        window.location.href = '{{ route('order.status', $order->id) }}';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        window.location.href = '{{ route('order.status', $order->id) }}';
                    });
                },
                onPending: function(result) {
                    console.log('Payment pending:', result);
                    window.location.href = '{{ route('order.status', $order->id) }}';
                },
                onError: function(result) {
                    console.error('Payment error:', result);
                    alert('Pembayaran gagal, silakan coba lagi');
                    window.location.href = '{{ route('order.status', $order->id) }}';
                },
                onClose: function() {
                    console.log('Payment popup closed');
                    // User cancel payment, redirect back to status
                    window.location.href = '{{ route('order.status', $order->id) }}';
                }
            });
        });
    </script>
</body>
</html>