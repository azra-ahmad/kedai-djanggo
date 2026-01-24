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
    <div class="min-h-screen pb-24" x-data="checkoutApp()">
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

            <!-- Order Items Card -->
            <div class="bg-white rounded-3xl shadow-sm p-6">
                <!-- Alpine Loop -->
                <template x-for="(item, index) in cart" :key="item.id">
                    <div class="flex justify-between items-start mb-4 border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-900" x-text="item.name"></h3>
                            <div class="text-xs text-gray-500 mt-1">
                                <span x-text="item.quantity + 'x @ Rp ' + (item.price).toLocaleString('id-ID')"></span>
                                <!-- Note: Extras support simplified for this UI version -->
                            </div>
                        </div>
                        
                        <div class="flex flex-col items-end gap-2">
                            <p class="font-bold text-[#EF7722]" x-text="'Rp ' + (item.price * item.quantity).toLocaleString('id-ID')"></p>
                            
                            <!-- Quantity Controls -->
                            <div class="flex items-center gap-3 bg-gray-50 rounded-full p-1">
                                <button @click="updateQuantity(item.id, -1)" class="w-7 h-7 rounded-full bg-white shadow-sm flex items-center justify-center text-gray-700 font-bold hover:bg-gray-100 border border-gray-200 text-sm disabled:opacity-50">
                                    −
                                </button>
                                <span class="font-bold w-4 text-center text-gray-900 text-sm" x-text="item.quantity"></span>
                                <button @click="updateQuantity(item.id, 1)" class="w-7 h-7 rounded-full bg-[#EF7722] shadow-sm flex items-center justify-center text-white font-bold hover:bg-[#d96a1e] text-sm">
                                    +
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
                
                <div x-show="cart.length === 0" class="text-center py-4 text-gray-500">
                    Keranjang kosong
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-white rounded-3xl shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Ringkasan Pesanan</h2>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Food Total</span>
                        <span class="font-medium" x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Delivery</span>
                        <span class="font-medium">Rp 0</span>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-2">
                    <span class="text-base font-bold text-gray-900">Total Pembayaran</span>
                    <span class="text-xl font-bold text-orange-500" x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
                </div>
            </div>
        </div>

        <!-- Sticky Footer with Payment Button -->
        <div class="fixed bottom-0 left-0 right-0 bg-white p-4 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] z-50 rounded-t-2xl border-t border-gray-100">
             <div class="flex justify-between items-center mb-3 px-1">
                <span class="text-gray-600 font-medium">Total</span>
                <span class="text-xl font-bold text-orange-500" x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
            </div>
            <button 
                @click="processOrder"
                :disabled="cart.length === 0 || processing"
                class="w-full bg-gradient-to-r from-[#EF7722] to-[#FAA533] text-white py-4 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transition-all active:scale-95 flex items-center justify-center gap-2">
                <span x-show="!processing">Bayar Sekarang</span>
                <span x-show="processing" class="animate-pulse">Memproses...</span>
                <svg x-show="!processing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Script Midtrans + Debug -->
    <script>
        function checkoutApp() {
            return {
                cart: @json(array_values($cart)),
                total: {{ $total }},
                processing: false,

                updateQuantity(menuId, delta) {
                    fetch('{{ route('cart.update') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            menu_id: menuId,
                            delta: delta
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        this.cart = data.cart || [];
                        this.calculateTotal();
                    })
                    .catch(() => alert('Gagal update keranjang'));
                },

                calculateTotal() {
                    this.total = this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                },

                processOrder() {
                    this.processing = true;
                    
                    fetch('{{ route('checkout.process') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({})
                    })
                    .then(async response => {
                        const data = await response.json();
                        if (!response.ok) {
                            // Handle specific error cases
                            if (response.status === 409 && data.redirect_url) {
                                window.location.href = data.redirect_url;
                                return; // Stop processing
                            }
                            throw new Error(data.message || 'Network response was not ok');
                        }
                        return data;
                    })
                    .then(data => {
                        if(!data) return; // Handled by redirect above
                        
                        if (data.status === 'success' && data.snap_token) {
                            snap.pay(data.snap_token, {
                                // UX Redirect ONLY - No Database Update
                                // Payment status is updated by Midtrans Webhook ONLY
                                onSuccess: function (result) {
                                    window.location.href = '/order/status/' + data.order_id;
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
                            alert('Gagal memproses pesanan: ' + (data.message || 'Unknown error'));
                            this.processing = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat memproses pesanan.');
                        this.processing = false;
                    });
                }
            }
        }
    </script>
</body>
</html>