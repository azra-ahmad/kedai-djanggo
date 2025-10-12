<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kedai Djanggo - Menu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .category-card { transition: transform 0.2s ease; }
        .category-card:active { transform: scale(0.95); }
        .menu-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .menu-card:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .bottom-nav { box-shadow: 0 -2px 10px rgba(0,0,0,0.05); }
        .nav-item.active svg { color: #c2410c; }
        .nav-item.active span { color: #c2410c; font-weight: 600; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .slide-up { animation: slideUp 0.3s ease-out; }
        .floating-cart-btn { 
            position: fixed; 
            bottom: 80px; 
            left: 50%;
            transform: translateX(-50%);
            z-index: 30; 
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        .floating-cart-btn.hidden { transform: translate(-50%, 150px); opacity: 0; }
        .cart-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 40;
            transition: opacity 0.3s ease;
        }
        .cart-panel {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-radius: 24px 24px 0 0;
            max-height: 80vh;
            overflow-y: auto;
            z-index: 50;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }
        .cart-panel.show { transform: translateY(0); }
        @keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50" x-data="{
    cart: [],
    currentCategory: 'all',
    detailModal: false,
    showCartPanel: false,
    currentProduct: {},
    cartCount: 0,
    currentTab: 'home',
    total: 0,
    
    init() {
        this.loadCart();
        
        // Listen for cart updates
        window.addEventListener('cart-updated', (e) => {
            this.loadCart();
            this.detailModal = false;
        });
    },
    
    loadCart() {
        fetch('{{ route('cart.index') }}', {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            this.cart = data.cart || [];
            this.cartCount = data.cart_count || 0;
            this.total = data.total || 0;
        })
        .catch(error => {
            console.error('Error loading cart:', error);
            this.cart = [];
            this.cartCount = 0;
        });
    },
    
    switchTab(tab) {
        this.currentTab = tab;
        this.detailModal = false;
        this.showCartPanel = false;
        document.querySelectorAll('#homeScreen, #ordersScreen').forEach(screen => screen.classList.add('hidden'));
        document.getElementById(tab + 'Screen').classList.remove('hidden');
    }
}">
    <div id="mainApp">
        <!-- HOME SCREEN -->
        <div id="homeScreen" class="pb-20">
            <!-- Header -->
            <div class="bg-white px-4 pt-4 pb-3 sticky top-0 z-10 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <button @click="logout()" class="flex items-center gap-2 text-sm font-semibold text-red-600 hover:text-red-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Keluar
                    </button>
                    <div class="text-center">
                        <h1 class="text-2xl font-bold text-gray-900">Kedai Djanggo</h1>
                        <p class="text-sm text-gray-500">{{ $customer ? $customer->name : 'Guest' }}</p>
                    </div>
                    <img src="{{ asset('images/logo-kedai-djanggo.jpg') }}" alt="Logo Kedai" class="w-12 h-12 rounded-full object-cover">
                </div>
            </div>

            <!-- Categories -->
            <div class="px-4 py-4 bg-white">
                <div class="flex gap-3 overflow-x-auto scrollbar-hide pb-2">
                    <div @click="currentCategory = 'all'" class="category-card flex-shrink-0 bg-gradient-to-br from-orange-100 to-orange-200 rounded-2xl p-4 w-28 cursor-pointer border-2" :class="currentCategory === 'all' ? 'border-orange-600' : 'border-transparent'">
                        <div class="text-3xl mb-2">🍽️</div>
                        <p class="text-xs font-bold text-gray-800">Semua</p>
                    </div>
                    <div @click="currentCategory = 'kopi'" class="category-card flex-shrink-0 bg-gradient-to-br from-amber-50 to-amber-100 rounded-2xl p-4 w-28 cursor-pointer border-2" :class="currentCategory === 'kopi' ? 'border-orange-600' : 'border-transparent'">
                        <div class="text-3xl mb-2">☕</div>
                        <p class="text-xs font-bold text-gray-800">Kopi</p>
                    </div>
                    <div @click="currentCategory = 'minuman'" class="category-card flex-shrink-0 bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-4 w-28 cursor-pointer border-2" :class="currentCategory === 'minuman' ? 'border-orange-600' : 'border-transparent'">
                        <div class="text-3xl mb-2">🥤</div>
                        <p class="text-xs font-bold text-gray-800">Minuman</p>
                    </div>
                    <div @click="currentCategory = 'makanan'" class="category-card flex-shrink-0 bg-gradient-to-br from-red-50 to-red-100 rounded-2xl p-4 w-28 cursor-pointer border-2" :class="currentCategory === 'makanan' ? 'border-orange-600' : 'border-transparent'">
                        <div class="text-3xl mb-2">🍜</div>
                        <p class="text-xs font-bold text-gray-800">Makanan</p>
                    </div>
                    <div @click="currentCategory = 'cemilan'" class="category-card flex-shrink-0 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-2xl p-4 w-28 cursor-pointer border-2" :class="currentCategory === 'cemilan' ? 'border-orange-600' : 'border-transparent'">
                        <div class="text-3xl mb-2">🍟</div>
                        <p class="text-xs font-bold text-gray-800">Cemilan</p>
                    </div>
                </div>
            </div>

            <!-- Menu Items -->
            <div class="px-4 py-4">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Menu Populer</h2>
                <div class="grid grid-cols-2 gap-4">
                    @foreach ($menus as $menu)
                        <div @click="currentProduct = { id: {{ $menu->id }}, name: '{{ $menu->nama_menu }}', price: {{ $menu->harga }}, category: '{{ $menu->kategori_menu }}', description: '{{ $menu->description }}', image: '{{ $menu->gambar }}' }; detailModal = true"
                            class="menu-card bg-white rounded-2xl overflow-hidden shadow-sm cursor-pointer"
                            x-show="currentCategory === 'all' || currentCategory === '{{ $menu->kategori_menu }}'">
                            <div class="aspect-square bg-gray-200 relative">
                                <img src="{{ $menu->gambar }}" class="w-full h-full object-cover">
                            </div>
                            <div class="p-3">
                                <h3 class="font-bold text-gray-900 text-sm mb-1">{{ $menu->nama_menu }}</h3>
                                <p class="text-xs text-gray-500 mb-2 line-clamp-2">{{ $menu->description }}</p>
                                <span class="text-orange-600 font-bold">Rp {{ number_format($menu->harga, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- ORDERS SCREEN -->
        <div id="ordersScreen" class="hidden pb-20">
            <div class="bg-white p-4 sticky top-0 z-10 shadow-sm">
                <h1 class="text-xl font-bold text-gray-900">Pesanan Saya</h1>
            </div>
            <div class="p-4">
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
                        <div class="text-6xl mb-4">📋</div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Pesanan</h3>
                        <p class="text-gray-500">Pesanan yang sudah dibayar akan muncul di sini</p>
                    </div>
                @endempty
            </div>
        </div>

        <!-- DETAIL MODAL -->
        <div x-show="detailModal" class="fixed inset-0 bg-black bg-opacity-50 z-50" @click.self="detailModal = false" x-cloak>
            <div class="absolute inset-0 flex items-end">
                <div class="bg-white rounded-t-3xl w-full max-h-[90vh] overflow-y-auto slide-up">
                    <div class="relative">
                        <img :src="currentProduct.image" class="w-full h-72 object-cover">
                        <button @click="detailModal = false" class="absolute top-4 left-4 bg-white rounded-full p-2 shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2" x-text="currentProduct.name"></h2>
                        <p class="text-gray-600 mb-6" x-text="currentProduct.description"></p>
                        <div class="flex items-center gap-4 mb-6">
                            <span class="text-3xl font-bold text-orange-600" x-text="'Rp ' + (currentProduct.price || 0).toLocaleString('id-ID')"></span>
                        </div>
                        <button @click="addToCart(currentProduct.id)" class="w-full bg-gradient-to-r from-orange-600 to-red-600 text-white py-4 rounded-xl font-bold text-lg shadow-lg">
                            Tambah ke Keranjang
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- FLOATING CART BUTTON -->
        <button 
            x-show="cartCount > 0" 
            @click="showCartPanel = true"
            class="floating-cart-btn bg-gradient-to-r from-orange-600 to-red-600 text-white px-6 py-4 rounded-full shadow-2xl flex items-center gap-3"
            x-transition>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <div class="flex flex-col items-start">
                <span class="text-xs opacity-90"><span x-text="cartCount"></span> item</span>
                <span class="font-bold text-lg" x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
            </div>
        </button>

        <!-- CART OVERLAY & PANEL -->
        <div x-show="showCartPanel" @click="showCartPanel = false" class="cart-overlay" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak></div>
        
        <div x-show="showCartPanel" class="cart-panel" :class="{ 'show': showCartPanel }" x-cloak>
            <div class="sticky top-0 bg-white border-b border-gray-200 p-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900">Keranjang Saya</h2>
                <button @click="showCartPanel = false" class="p-2 hover:bg-gray-100 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-4">
                <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-4">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-semibold text-gray-900">Estimasi Penyajian</span>
                    </div>
                    <p class="text-xs text-gray-600">15-20 menit • Diantar ke meja</p>
                </div>

                <div class="space-y-3 mb-4">
                    <template x-for="item in cart" :key="item.id">
                        <div class="bg-white rounded-xl p-4 shadow-sm flex gap-4 border border-gray-100">
                            <img :src="item.image" class="w-20 h-20 rounded-lg object-cover">
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 mb-1" x-text="item.name"></h4>
                                <p class="text-orange-600 font-semibold mb-2" x-text="'Rp ' + (item.price * item.quantity).toLocaleString('id-ID')"></p>
                                <div class="flex items-center gap-3">
                                    <button @click="updateQuantity(item.id, -1)" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-700 font-bold hover:bg-gray-200">-</button>
                                    <span class="font-semibold w-8 text-center" x-text="item.quantity"></span>
                                    <button @click="updateQuantity(item.id, 1)" class="w-8 h-8 rounded-full bg-orange-600 flex items-center justify-center text-white font-bold hover:bg-orange-700">+</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Button Tambah Menu -->
                <button @click="showCartPanel = false" class="w-full border-2 border-orange-600 text-orange-600 py-3 rounded-xl font-semibold mb-4 hover:bg-orange-50 transition">
                    + Tambah Menu Lagi
                </button>

                <div class="bg-gray-50 rounded-xl p-4 space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="font-bold text-gray-900">Total Pembayaran</span>
                        <span class="font-bold text-xl text-orange-600" x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
                    </div>
                </div>

                <a href="{{ route('checkout') }}" class="block w-full bg-gradient-to-r from-orange-600 to-red-600 text-white py-4 rounded-xl font-bold text-lg shadow-lg text-center">
                    Lanjut ke Pembayaran
                </a>
            </div>
        </div>

        <!-- BOTTOM NAV -->
        <div class="bottom-nav fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-4 py-3 flex justify-around z-20">
            <button @click="switchTab('home')" class="nav-item flex flex-col items-center gap-1" :class="{ 'active': currentTab === 'home' }">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="text-xs text-gray-500">Home</span>
            </button>
            <button @click="switchTab('orders')" class="nav-item flex flex-col items-center gap-1" :class="{ 'active': currentTab === 'orders' }">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <span class="text-xs text-gray-500">Pesanan</span>
            </button>
        </div>
    </div>

    <script>
        function addToCart(menuId) {
            if (!menuId) return;
            
            fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ menu_id: menuId, quantity: 1 })
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                console.log('Add to cart success:', data);
                
                // Dispatch event untuk trigger Alpine reload
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: data }));
                
                // Show toast notification
                showToast('Berhasil ditambahkan ke keranjang!');
            })
            .catch(error => {
                console.error('Error adding to cart:', error);
                showToast('Gagal menambahkan ke keranjang', 'error');
            });
        }

        function updateQuantity(menuId, delta) {
            if (!menuId) return;
            
            fetch('{{ route('cart.update') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ menu_id: menuId, delta: delta })
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                console.log('Update quantity success:', data);
                
                // Dispatch event untuk trigger Alpine reload
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: data }));
            })
            .catch(error => {
                console.error('Error updating quantity:', error);
                showToast('Gagal mengupdate jumlah', 'error');
            });
        }

        function logout() {
            if (confirm('Yakin ingin keluar?')) {
                fetch('{{ route('logout') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(() => window.location.href = '{{ route('user.form') }}');
            }
        }

        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 left-1/2 transform -translate-x-1/2 z-[100] px-6 py-3 rounded-full shadow-lg text-white font-semibold ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 2000);
        }
    </script>
</body>
</html>