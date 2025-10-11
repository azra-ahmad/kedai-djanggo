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
        @keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50" x-data="{
        cart: @json($cart),
        currentCategory: 'all',
        detailModal: false,
        currentProduct: {},
        cartCount: @json(array_sum(array_column($cart, 'quantity'))),
        currentTab: 'home',
        switchTab(tab) {
            this.currentTab = tab;
            document.querySelectorAll('#homeScreen, #cartScreen').forEach(screen => screen.classList.add('hidden'));
            document.getElementById(tab + 'Screen').classList.remove('hidden');
        }
    }">
    <div id="mainApp">
        <div id="homeScreen" class="pb-20">
            <div class="bg-white px-4 pt-4 pb-3 sticky top-0 z-10 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Kedai Djanggo</h1>
                            <p class="text-sm text-gray-500">
                                <span>{{ $customer->name }}</span>
                            </p>
                    </div>
                    <img src="{{ asset('images/logo-kedai-djanggo.jpg') }}" alt="Logo Kedai" class="w-12 h-12 rounded-full object-cover">
                </div>
            </div>

            <div class="px-4 py-4 bg-white">
                <div class="flex gap-3 overflow-x-auto scrollbar-hide pb-2">
                    <div @click="currentCategory = 'all'" class="category-card flex-shrink-0 bg-gradient-to-br from-orange-100 to-orange-200 rounded-2xl p-4 w-28 cursor-pointer border-2" :class="currentCategory === 'all' ? 'border-orange-600' : 'border-transparent hover:border-orange-400'">
                        <div class="text-3xl mb-2">🍽️</div>
                        <p class="text-xs font-bold text-gray-800">Semua</p>
                    </div>
                    <div @click="currentCategory = 'kopi'" class="category-card flex-shrink-0 bg-gradient-to-br from-amber-50 to-amber-100 rounded-2xl p-4 w-28 cursor-pointer border-2" :class="currentCategory === 'kopi' ? 'border-orange-600' : 'border-transparent hover:border-orange-400'">
                        <div class="text-3xl mb-2">☕</div>
                        <p class="text-xs font-bold text-gray-800">Kopi</p>
                    </div>
                    <div @click="currentCategory = 'minuman'" class="category-card flex-shrink-0 bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-4 w-28 cursor-pointer border-2" :class="currentCategory === 'minuman' ? 'border-orange-600' : 'border-transparent hover:border-orange-400'">
                        <div class="text-3xl mb-2">🥤</div>
                        <p class="text-xs font-bold text-gray-800">Minuman</p>
                    </div>
                    <div @click="currentCategory = 'makanan'" class="category-card flex-shrink-0 bg-gradient-to-br from-red-50 to-red-100 rounded-2xl p-4 w-28 cursor-pointer border-2" :class="currentCategory === 'makanan' ? 'border-orange-600' : 'border-transparent hover:border-orange-400'">
                        <div class="text-3xl mb-2">🍜</div>
                        <p class="text-xs font-bold text-gray-800">Makanan</p>
                    </div>
                    <div @click="currentCategory = 'cemilan'" class="category-card flex-shrink-0 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-2xl p-4 w-28 cursor-pointer border-2" :class="currentCategory === 'cemilan' ? 'border-orange-600' : 'border-transparent hover:border-orange-400'">
                        <div class="text-3xl mb-2">🍟</div>
                        <p class="text-xs font-bold text-gray-800">Cemilan</p>
                    </div>
                </div>
            </div>

            <div class="px-4 py-4">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Menu Populer</h2>
                <div class="grid grid-cols-2 gap-4">
                    @foreach ($menus as $menu)
                        <div @click="currentProduct = { id: {{ $menu->id }}, name: '{{ $menu->nama_menu }}', price: {{ $menu->harga }}, category: '{{ $menu->kategori_menu }}', description: '{{ $menu->description }}', calories: 'Unknown', image: '{{ $menu->gambar }}' }; detailModal = true"
                            class="menu-card bg-white rounded-2xl overflow-hidden shadow-sm cursor-pointer"
                            :class="currentCategory === 'all' || currentCategory === '{{ $menu->kategori_menu }}' ? '' : 'hidden'"
                            data-category="{{ $menu->kategori_menu }}">
                            <div class="aspect-square bg-gray-200 relative">
                                <img src="{{ $menu->gambar }}" class="w-full h-full object-cover">
                                <div class="absolute top-2 right-2 bg-white px-2 py-1 rounded-full text-xs font-semibold">Unknown</div>
                            </div>
                            <div class="p-3">
                                <h3 class="font-bold text-gray-900 text-sm mb-1">{{ $menu->nama_menu }}</h3>
                                <p class="text-xs text-gray-500 mb-2">{{ $menu->description }}</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-orange-600 font-bold">Rp {{ number_format($menu->harga, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div id="detailModal" x-show="detailModal" class="fixed inset-0 bg-black bg-opacity-50 z-50" x-cloak>
            <div class="absolute inset-0 flex items-end">
                <div class="bg-white rounded-t-3xl w-full max-h-[90vh] overflow-y-auto slide-up">
                    <div class="relative">
                        <img :src="currentProduct.image" class="w-full h-72 object-cover">
                        <button @click="detailModal = false" class="absolute top-4 left-4 bg-white rounded-full p-2 shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <div class="absolute top-4 right-4 bg-white px-3 py-1.5 rounded-full font-semibold text-sm shadow-lg" x-text="currentProduct.calories"></div>
                    </div>
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2" x-text="currentProduct.name"></h2>
                        <p class="text-gray-600 mb-6" x-text="currentProduct.description"></p>
                        <div class="mb-6">
                            <h3 class="font-semibold text-gray-900 mb-3">Tambahan (Opsional)</h3>
                            <div class="space-y-2">
                                <label class="flex items-center justify-between p-3 border border-gray-200 rounded-lg cursor-pointer hover:border-orange-400">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" class="w-4 h-4 text-orange-600 rounded">
                                        <span class="text-sm font-medium">Extra Pedas</span>
                                    </div>
                                    <span class="text-sm text-gray-600">Gratis</span>
                                </label>
                                <label class="flex items-center justify-between p-3 border border-gray-200 rounded-lg cursor-pointer hover:border-orange-400">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" class="w-4 h-4 text-orange-600 rounded">
                                        <span class="text-sm font-medium">Extra Keju</span>
                                    </div>
                                    <span class="text-sm text-gray-600">+ Rp 3.000</span>
                                </label>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 mb-6">
                            <span class="text-3xl font-bold text-orange-600" x-text="'Rp ' + currentProduct.price.toLocaleString('id-ID')"></span>
                        </div>
                        <button @click="addToCart(currentProduct.id); detailModal = false" class="w-full bg-gradient-to-r from-orange-600 to-red-600 text-white py-4 rounded-xl font-bold text-lg shadow-lg">
                            Tambah ke Keranjang
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="cartScreen" class="hidden pb-20">
            <div class="bg-white p-4 sticky top-0 z-10 shadow-sm">
                <div class="flex items-center gap-3">
                    <button @click="switchTab('home')" class="p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <h1 class="text-xl font-bold text-gray-900">Keranjang</h1>
                </div>
            </div>

            <div id="cartEmpty" :class="cartCount > 0 ? 'hidden' : ''" class="p-8 text-center">
                <div class="text-6xl mb-4">🛒</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Keranjang Kosong</h3>
                <p class="text-gray-500 mb-6">Yuk, mulai tambahkan menu favorit kamu!</p>
                <button @click="switchTab('home')" class="bg-orange-600 text-white px-6 py-3 rounded-xl font-semibold">
                    Lihat Menu
                </button>
            </div>

            <div id="cartContent" :class="cartCount > 0 ? '' : 'hidden'" class="p-4">
                <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-4">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-semibold text-gray-900">Estimasi Penyajian</span>
                    </div>
                    <p class="text-xs text-gray-600">15-20 menit • Diantar ke meja</p>
                </div>

                <div id="cartItems" class="space-y-3 mb-6">
                    <template x-for="(item, index) in cart" :key="index">
                        <div class="bg-white rounded-xl p-4 shadow-sm flex gap-4">
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

                <div class="bg-gray-50 rounded-xl p-4 space-y-3 mb-6">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-semibold" x-text="'Rp ' + cart.reduce((sum, item) => sum + (item.price * item.quantity), 0).toLocaleString('id-ID')"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Pajak (10%)</span>
                        <span class="font-semibold" x-text="'Rp ' + (cart.reduce((sum, item) => sum + (item.price * item.quantity), 0) * 0.1).toLocaleString('id-ID')"></span>
                    </div>
                    <div class="border-t border-gray-200 pt-3 flex justify-between">
                        <span class="font-bold text-gray-900">Total</span>
                        <span class="font-bold text-xl text-orange-600" x-text="'Rp ' + (cart.reduce((sum, item) => sum + (item.price * item.quantity), 0) * 1.1).toLocaleString('id-ID')"></span>
                    </div>
                </div>

                <a href="{{ route('checkout') }}" class="block w-full bg-gradient-to-r from-orange-600 to-red-600 text-white py-4 rounded-xl font-bold text-lg shadow-lg text-center">
                    Lanjut ke Pembayaran
                </a>
            </div>
        </div>

        <div class="bottom-nav fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-4 py-3 flex justify-around z-20">
            <button @click="switchTab('home')" class="nav-item flex flex-col items-center gap-1" :class="{ 'active': currentTab === 'home' }">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="text-xs text-gray-500">Home</span>
            </button>
            <button @click="switchTab('cart')" class="nav-item flex flex-col items-center gap-1 relative" :class="{ 'active': currentTab === 'cart' }">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span class="text-xs text-gray-500">Cart</span>
                <span class="absolute -top-1 right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold" x-show="cartCount > 0" x-text="cartCount"></span>
            </button>
        </div>
    </div>

    <script>
        function addToCart(menuId) {
            fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ menu_id: menuId, quantity: 1 })
            })
            .then(response => response.json())
            .then(data => {
                alpine.store('cart').cart = Object.values(alpine.store('cart').cart).concat({ id: menuId, ...alpine.store('cart').currentProduct });
                alpine.store('cart').cartCount++;
                alert(data.message);
            });
        }

        function updateQuantity(menuId, delta) {
            let cart = alpine.store('cart').cart;
            let item = cart.find(i => i.id === menuId);
            if (item) {
                item.quantity += delta;
                if (item.quantity <= 0) {
                    cart = cart.filter(i => i.id !== menuId);
                }
                alpine.store('cart').cart = cart;
                alpine.store('cart').cartCount += delta;
                fetch('{{ route('cart.add') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ menu_id: menuId, quantity: delta })
                });
            }
        }

        document.addEventListener('alpine:init', () => {
            Alpine.store('cart', {
                cart: @json($cart),
                cartCount: @json(array_sum(array_column($cart, 'quantity'))),
                currentTab: 'home',
                switchTab(tab) {
                    this.currentTab = tab;
                    document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));
                    document.querySelectorAll('.nav-item')[tab === 'home' ? 0 : tab === 'cart' ? 1 : 2].classList.add('active');
                    document.querySelectorAll('#homeScreen, #cartScreen').forEach(screen => screen.classList.add('hidden'));
                    document.getElementById(tab + 'Screen').classList.remove('hidden');
                }
            });
        });
    </script>
</body>
</html>