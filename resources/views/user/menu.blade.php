@extends('layouts.customer')

@section('title', 'Kedai Djanggo - Menu')

@section('content')
<div x-data="app()" x-init="init()" class="min-h-screen bg-gradient-to-br from-white via-[#EBEBEB] to-white font-sans text-gray-900">
    
    <!-- Fixed Header -->
    <x-customer.header :customer="$customer" />
    
    <!-- Spacer for fixed header -->
    <div class="h-[88px]"></div>

    <!-- MAIN CONTENT CONTAINER -->
    <div class="max-w-7xl mx-auto w-full">

        <!-- HERO BANNER SECTION -->
        <div class="px-5 pt-6 pb-2 md:px-8">
            <div class="rounded-3xl overflow-hidden shadow-lg relative aspect-[21/9] md:aspect-[3/1] bg-gray-900 group cursor-pointer">
                <!-- Background Image (Placeholder) -->
                <img src="https://images.unsplash.com/photo-1544148103-0773bf10d330?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" 
                     alt="Promo Banner" 
                     class="absolute inset-0 w-full h-full object-cover opacity-80 group-hover:scale-105 transition-transform duration-700">
                
                <!-- Content Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex flex-col justify-end p-6 md:p-10">
                    <span class="inline-block px-3 py-1 bg-[#EF7722] text-white text-xs md:text-sm font-bold rounded-full w-fit mb-2">
                        PROMO SPESIAL
                    </span>
                    <h2 class="text-2xl md:text-4xl font-bold text-white mb-1 leading-tight">
                        Diskon 50% Hari Ini!
                    </h2>
                    <p class="text-gray-200 text-sm md:text-base line-clamp-1">
                        Khusus untuk pembelian menu kopi signature Kedai Djanggo.
                    </p>
                </div>
            </div>
        </div>

        <!-- HOME SCREEN -->
        <div id="homeScreen" class="pb-32">
            
            <!-- STICKY CATEGORIES with Smooth Mask -->
            <div class="sticky top-[88px] z-40 bg-white/80 backdrop-blur-md border-b border-gray-200/50 transition-all duration-300 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] [mask-image:linear-gradient(to_bottom,black_85%,transparent)]">
                <div class="max-w-7xl mx-auto">
                    <x-customer.category-list :categories="$categories" />
                </div>
            </div>

            <!-- SKELETON LOADER -->
            <template x-if="loading">
                <div class="px-5 md:px-8 py-6 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6">
                    <template x-for="i in 8">
                        <div class="rounded-2xl overflow-hidden bg-white border border-gray-100 shadow-sm animate-pulse h-full flex flex-col">
                            <div class="aspect-square bg-gray-200"></div>
                            <div class="p-4 space-y-3 flex-1">
                                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                <div class="h-8 bg-gray-200 rounded w-full mt-auto"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- MENU GRID -->
            <div class="px-5 md:px-8 py-6" x-show="!loading">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900 flex items-center gap-2">
                        Menu Pilihan
                        <span class="text-xs font-normal text-gray-500 bg-gray-100 px-2 py-1 rounded-full hidden md:inline-block">Update Terbaru</span>
                    </h2>
                    
                    <!-- View Toggle (Optional Future Feature) or Filter Info -->
                    <div class="text-sm text-gray-500 font-medium">
                        Menampilkan <span x-text="filteredCount"></span> menu
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6">
                    @foreach ($menus as $menu)
                        <x-customer.menu-card :menu="$menu" />
                    @endforeach
                </div>
                
                <!-- Empty State -->
                <div x-show="filteredCount === 0" class="col-span-full py-20 text-center" x-cloak>
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-4xl">🔍</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Tidak ada menu ditemukan</h3>
                    <p class="text-gray-500">Coba pilih kategori lain.</p>
                </div>
            </div>
        </div>

        <!-- ORDERS SCREEN -->
        <x-customer.orders-list :orders="$orders" />

        <!-- ABOUT SCREEN -->
        <x-customer.hero-about />

    </div> <!-- End max-w-7xl -->

    <!-- Modals -->
    <x-customer.modals />

    <!-- Floating Cart Pill -->
    <div class="max-w-7xl mx-auto relative">
        <x-customer.floating-cart />
    </div>

    <!-- Bottom Navigation -->
    <x-customer.bottom-nav />
</div>

<script>
    function app() {
        return {
            cart: [],
            currentCategory: 'all',
            detailModal: false,
            logoutModal: false,
            showCartPanel: false,
            currentProduct: {},
            cartCount: 0,
            currentTab: 'home',
            total: 0,
            loading: true,
            
            // Computed property simulation for filtered count
            get filteredCount() {
                if (this.currentCategory === 'all') return {{ $menus->count() }};
                // This is a rough client-side estimate, ideally backend filtered or use DOM counting
                // Since we use x-show on cards, we can't easily count hidden DOM elements responsively without more logic
                // For now, let's just return a generic text or implement a better counter if needed.
                // Simple workaround: count based on data available in specific implementation
                return document.querySelectorAll('.menu-card[style*="display: none"]').length > 0 
                       ? document.querySelectorAll('.menu-card:not([style*="display: none"])').length 
                       : {{ $menus->count() }}; 
            },

            init() {
                this.loadCart();
                window.addEventListener('cart-updated', () => {
                    this.loadCart();
                    this.detailModal = false;
                });
                
                // Simulate loading delay for skeleton demo
                setTimeout(() => this.loading = false, 600);
            },

            loadCart() {
                fetch('{{ route('cart.index') }}', {
                    headers: { 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    this.cart = data.cart || [];
                    this.cartCount = data.cart_count || 0;
                    this.total = data.total || 0;
                })
                .catch(() => {
                    this.cart = [];
                    this.cartCount = 0;
                    this.total = 0;
                });
            },

            switchTab(tab) {
                this.currentTab = tab;
                this.detailModal = false;
                this.showCartPanel = false;
                document.querySelectorAll('#homeScreen, #ordersScreen, #aboutScreen')
                    .forEach(screen => screen.classList.add('hidden'));
                document.getElementById(tab + 'Screen').classList.remove('hidden');
                
                // Scroll to top when switching
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            addToCart(menuId) {
                fetch('{{ route('cart.add') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        menu_id: menuId,
                        quantity: 1
                    })
                })
                .then(async res => {
                    if (!res.ok) {
                        const text = await res.text();
                        throw new Error(res.status + ' ' + res.statusText);
                    }
                    return res.json();
                })
                .then(data => {
                    this.cart = data.cart || [];
                    this.cartCount = data.cart_count || 0;
                    this.total = data.total || 0;
                    window.dispatchEvent(new CustomEvent('cart-updated'));
                    this.showToast('Berhasil ditambahkan ke keranjang!');
                })
                .catch(err => {
                    console.error(err);
                    this.showToast('Gagal: ' + (err.message || 'Terjadi kesalahan'), 'error');
                });
            },

            updateQuantity(menuId, delta) {
                fetch('{{ route('cart.update') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ menu_id: menuId, delta: delta })
                })
                .then(r => r.json())
                .then(data => {
                    this.cart = data.cart || [];
                    this.cartCount = data.cart_count || 0;
                    this.total = data.total || 0;
                    window.dispatchEvent(new CustomEvent('cart-updated'));
                })
                .catch(() => this.showToast('Gagal update!', 'error'));
            },

            logout() {
                this.logoutModal = true;
            },

            confirmLogout() {
                fetch('{{ route('customer.logout') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                }).then(() => {
                    window.location.href = "{{ route('user.form') }}";
                });
            },

            showToast(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className = `fixed top-6 left-1/2 transform -translate-x-1/2 z-[100] px-6 py-4 rounded-2xl shadow-xl text-sm font-bold flex items-center gap-3 transition-all duration-300 ${type === 'success' ? 'bg-white text-gray-900 border-l-4 border-[#EF7722]' : 'bg-white text-red-600 border-l-4 border-red-500'}`;
                toast.style.minWidth = '300px';
                
                // Icon based on type
                const icon = type === 'success' 
                    ? `<svg class="w-6 h-6 text-[#EF7722]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`
                    : `<svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;
                
                toast.innerHTML = `${icon}<span>${message}</span>`;
                document.body.appendChild(toast);

                // Animate in
                requestAnimationFrame(() => {
                    toast.style.transform = 'translate(-50%, 0)';
                    toast.style.opacity = '1';
                });

                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translate(-50%, -20px)';
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }
        }
    }
</script>
@endsection