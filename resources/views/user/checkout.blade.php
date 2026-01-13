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
            <!-- Add Menu Button -->
            <a href="{{ route('menu.index') }}" class="block w-full mb-6 bg-gray-100 text-gray-700 py-3 rounded-xl font-bold text-center border-2 border-[#EBEBEB] hover:bg-gray-200 transition-all">
                + Tambah Menu Lain
            </a>

            <!-- Order Items Card (Grouped: main item + extras dengan indent & harga per baris) -->
            <div class="bg-white rounded-3xl shadow-sm p-6">
                @foreach ($cart as $item)
                    <div class="flex justify-between items-start mb-4 border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-900">{{ $item['name'] }}</h3>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $item['quantity'] }}x @ Rp {{ number_format($item['price'], 0, ',', '.') }}
                                @if(!empty($item['extras']))
                                    @foreach($item['extras'] as $extra)
                                        <br>+ {{ $extra['quantity'] }}x {{ $extra['name'] }}
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-gray-900">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                            @if(!empty($item['extras']))
                                @foreach($item['extras'] as $extra)
                                    <p class="text-xs text-gray-500">Rp {{ number_format($extra['price'] * $extra['quantity'], 0, ',', '.') }}</p>
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

                <div class="flex justify-between items-center pt-2">
                    <span class="text-base font-bold text-gray-900">Total Pembayaran</span>
                    <span class="text-xl font-bold text-orange-500">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Removed Payment Info & Order ID (Visual Cleanup) -->
        </div>

        <!-- Sticky Footer with Payment Button -->
        <div class="fixed bottom-0 left-0 right-0 bg-white p-4 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] z-50 rounded-t-2xl border-t border-gray-100">
             <div class="flex justify-between items-center mb-3 px-1">
                <span class="text-gray-600 font-medium">Total</span>
                <span class="text-xl font-bold text-orange-500">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            <button id="pay-button" class="w-full bg-gradient-to-r from-[#EF7722] to-[#FAA533] text-white py-4 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transition-all active:scale-95">
                Bayar Sekarang
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

            const payButton = document.getElementById('pay-button');

            payButton.addEventListener('click', function(e) {
                e.preventDefault();
                payButton.disabled = true;
                payButton.textContent = 'Processing...';

                // Call Backend to Create Order & Get Snap Token
                fetch('{{ route('checkout.process') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    console.log('Order Created:', data);
                    
                    if (data.status === 'success' && data.snap_token) {
                        snap.pay(data.snap_token, {
                            onSuccess: function (result) {
                                // DEMO MODE: Call backend to confirm payment immediately
                                fetch('/order/updatePayment/' + data.order_id, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    }
                                })
                                .then(() => {
                                    window.location.href = '/order/status/' + data.order_id;
                                })
                                .catch(err => {
                                    console.error('Demo payment update failed:', err);
                                    window.location.href = '/order/status/' + data.order_id;
                                });
                            },
                            onPending: function (result) {
                                window.location.href = '/order/status/' + data.order_id;
                            },
                            onError: function (result) {
                                window.location.href = '/order/status/' + data.order_id;
                            },
                            onClose: function () {
                                window.location.href = '/order/status/' + data.order_id;
                            }
                        });
                    } else {
                        alert('Gagal membuat pesanan: ' + (data.message || 'Unknown error'));
                        payButton.disabled = false;
                        payButton.textContent = 'Process Order';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memproses pesanan.');
                    payButton.disabled = false;
                    payButton.textContent = 'Process Order';
                });
            });
        });
    </script>
</body>
</html>