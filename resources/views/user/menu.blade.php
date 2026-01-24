@extends('layouts.customer')

@section('title', 'Kedai Djanggo - Menu')

@section('content')
<div x-data="app()" x-init="init()" class="min-h-screen bg-gradient-to-br from-white via-[#EBEBEB] to-white">
    
    <!-- Fixed Header -->
    <x-customer.header :customer="$customer" />
    
    <!-- Spacer for fixed header -->
    <div class="h-[88px]"></div>

    <!-- HOME SCREEN -->
    <div id="homeScreen" class="pb-32">
        <!-- Categories -->
        <x-customer.category-list />

        <!-- SKELETON LOADER -->
        <template x-if="loading">
            <div class="px-5 py-4 grid grid-cols-2 gap-5">
                <template x-for="i in 4">
                    <div class="rounded-2xl overflow-hidden bg-white border border-gray-100 shadow-sm animate-pulse">
                        <div class="aspect-square bg-gray-200"></div>
                        <div class="p-4 space-y-3">
                            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                            <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                            <div class="h-8 bg-gray-200 rounded w-full mt-2"></div>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        <!-- Menu Items -->
        <div class="px-5 pb-5">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-semibold text-gray-900">Menu Spesial</h2>
                <div class="flex items-center gap-1.5 bg-[#EBEBEB] px-3 py-1.5 rounded-full">
                    <svg class="w-4 h-4 text-[#EF7722]" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    <span class="text-xs font-medium text-[#EF7722]">Best Seller</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5">
                @foreach ($menus as $menu)
                    <x-customer.menu-card :menu="$menu" />
                @endforeach
            </div>
        </div>
    </div>

    <!-- ORDERS SCREEN -->
    <x-customer.orders-list :orders="$orders" />

    <!-- ABOUT SCREEN -->
    <x-customer.hero-about />

    <!-- Modals -->
    <x-customer.modals />

    <!-- Floating Cart Pill -->
    <x-customer.floating-cart />

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

            init() {
                this.loadCart();
                window.addEventListener('cart-updated', () => {
                    this.loadCart();
                    this.detailModal = false;
                });
                setTimeout(() => this.loading = false, 800);
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
                toast.className = `fixed top-6 left-1/2 transform -translate-x-1/2 z-[100] px-6 py-4 rounded-2xl shadow-2xl text-sm font-bold ${type === 'success' ? 'bg-gradient-to-r from-[#EF7722] to-[#FAA533] text-white' : 'bg-gradient-to-r from-red-400 to-rose-500 text-white'}`;
                toast.style.minWidth = '280px';
                toast.textContent = message;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translate(-50%, -20px)';
                    toast.style.transition = 'all 0.3s ease';
                    setTimeout(() => toast.remove(), 300);
                }, 2500);
            }
        }
    }
</script>
@endsection