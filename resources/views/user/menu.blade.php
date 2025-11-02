<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kedai Djanggo - Menu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #F5F5F5 0%, #FAFAFA 50%, #FFFFFF 100%);
        }
        
        .font-display {
            font-family: 'Playfair Display', serif;
        }
        
        * {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Enhanced Category Cards */
        .category-card {
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            background: linear-gradient(135deg, #F5F5F5 0%, #FAFAFA 100%);
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }
        
        .category-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.8), transparent);
            transition: left 0.5s;
        }
        
        .category-card:hover::before {
            left: 100%;
        }
        
        .category-card:hover {
            transform: translateY(-8px) scale(1.05);
            box-shadow: 0 20px 40px rgba(255, 127, 80, 0.25);
            border-color: #FF7F50;
        }
        
        .category-card.active {
            background: linear-gradient(135deg, #FF7F50 0%, #FF6347 100%);
            border-color: #FF4500;
            box-shadow: 0 12px 32px rgba(255, 127, 80, 0.4);
            transform: scale(1.08);
        }
        
        .category-card.active .category-emoji {
            animation: bounce 0.6s ease infinite;
        }

        .category-card.active::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 127, 80, 0.8), transparent);
            transition: left 0.5s;
        }

        .category-card.active:hover::before {
            left: 100%;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        
        /* Enhanced Menu Cards with Staggered Grid */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .menu-card {
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            background: #F5F5F5;
            border: 2px solid #008080;
            position: relative;
            overflow: hidden;
        }
        
        .menu-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 127, 80, 0.1) 0%, rgba(0, 128, 128, 0.2) 100%);
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .menu-card:hover::after {
            opacity: 1;
        }
        
        .menu-card:hover {
            transform: translateY(-12px) rotate(1deg);
            box-shadow: 0 24px 48px rgba(255, 127, 80, 0.3);
            border-color: #FF7F50;
            z-index: 10;
        }
        
        .menu-card:nth-child(even) {
            margin-top: 20px;
        }
        
        .menu-card img {
            transition: transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        
        .menu-card:hover img {
            transform: scale(1.15) rotate(-2deg);
        }
        
        /* Add to Cart Button Animation */
        .add-to-cart-btn {
            position: absolute;
            bottom: -50px;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        
        .menu-card:hover .add-to-cart-btn {
            bottom: 12px;
            opacity: 1;
        }
        
        /* Enhanced Bottom Nav */
        .bottom-nav {
            box-shadow: 0 -8px 32px rgba(0, 0, 0, 0.08);
            background: linear-gradient(180deg, rgba(245, 245, 245, 0.98) 0%, rgba(250, 250, 250, 0.98) 100%);
            backdrop-filter: blur(20px);
            border-top: 2px solid #008080;
            position: relative;
        }
        
        .bottom-nav::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #FF7F50, transparent);
            animation: shimmer 3s infinite;
        }
        
        @keyframes shimmer {
            0%, 100% { opacity: 0; }
            50% { opacity: 1; }
        }
        
        .nav-item {
            position: relative;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        
        .nav-item:hover {
            transform: translateY(-4px);
        }
        
        .nav-item.active {
            transform: scale(1.1);
        }
        
        .nav-item.active::before {
            content: '';
            position: absolute;
            top: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 32px;
            height: 3px;
            background: linear-gradient(90deg, #FF7F50, #FF6347);
            border-radius: 0 0 3px 3px;
            box-shadow: 0 2px 8px rgba(255, 127, 80, 0.5);
        }

        .nav-item.active svg {
            color: #FF4500;
            filter: drop-shadow(0 2px 8px rgba(255, 69, 0, 0.4));
        }

        .nav-item.active span {
            color: #FF4500;
            font-weight: 700;
        }
        
        /* Floating Cart Button Enhancement */
        .floating-cart-btn {
            position: fixed;
            bottom: 88px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 30;
            box-shadow: 0 12px 40px rgba(255, 127, 80, 0.35);
            background: linear-gradient(135deg, #FF7F50 0%, #FF6347 50%, #FF4500 100%);
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50% { transform: translateX(-50%) translateY(-8px); }
        }
        
        .floating-cart-btn:hover {
            box-shadow: 0 16px 56px rgba(255, 127, 80, 0.5);
            animation: none;
            transform: translateX(-50%) scale(1.05);
        }
        
        .floating-cart-btn.hidden { 
            transform: translate(-50%, 150px); 
            opacity: 0; 
        }
        
        /* Price Tag Enhancement */
        .price-tag {
            background: linear-gradient(135deg, #FF7F50 0%, #008080 100%);
            color: #FFFFFF;
            padding: 8px 14px;
            border-radius: 12px;
            display: inline-block;
            font-weight: 800;
            border: 2px solid #FF6347;
            box-shadow: 0 4px 12px rgba(255, 127, 80, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .price-tag::before {
            content: '💰';
            position: absolute;
            left: -20px;
            opacity: 0.3;
            font-size: 24px;
        }
        
        /* Header Enhancement */
        .header-gradient {
            background: linear-gradient(180deg, #F5F5F5 0%, #FAFAFA 100%);
            border-bottom: 3px solid transparent;
            border-image: linear-gradient(90deg, transparent, #FF7F50, transparent) 1;
            box-shadow: 0 4px 16px rgba(255, 127, 80, 0.1);
        }
        
        /* About Section Styles */
        .about-hero {
            background: linear-gradient(135deg, #FF7F50 0%, #008080 100%);
            position: relative;
            overflow: hidden;
        }
        
        .about-hero::before {
            content: '☕';
            position: absolute;
            font-size: 200px;
            opacity: 0.1;
            top: -50px;
            right: -50px;
            animation: rotate 20s linear infinite;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .feature-card {
            background: #F5F5F5;
            border: 2px solid #008080;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 40px rgba(255, 127, 80, 0.2);
            border-color: #FF7F50;
        }
        
        .feature-icon {
            background: linear-gradient(135deg, #FF7F50 0%, #008080 100%);
            animation: pulse-soft 2s ease-in-out infinite;
        }
        
        @keyframes pulse-soft {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        /* Scrollbar */
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #F5F5F5;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #FF7F50, #008080);
            border-radius: 3px;
        }
        
        /* Cart Panel */
        .cart-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
            z-index: 40;
        }

        .cart-panel {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(180deg, #F5F5F5 0%, #FAFAFA 100%);
            border-radius: 32px 32px 0 0;
            max-height: 85vh;
            overflow-y: auto;
            z-index: 50;
            transform: translateY(100%);
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 -12px 48px rgba(255, 127, 80, 0.2);
            border-top: 3px solid #FF7F50;
        }
        
        .cart-panel.show { 
            transform: translateY(0); 
        }
        
        /* Animations */
        @keyframes slideUp { 
            from { transform: translateY(100%); opacity: 0; } 
            to { transform: translateY(0); opacity: 1; } 
        }
        
        .slide-up { 
            animation: slideUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); 
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* Badge */
        .badge {
            background: rgba(245, 245, 245, 0.95);
            backdrop-filter: blur(12px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 127, 80, 0.3);
        }
        
        /* Logo Enhancement */
        .logo-border {
            border: 3px solid #FF7F50;
            box-shadow: 0 4px 16px rgba(255, 127, 80, 0.3);
            animation: pulse-glow 2s ease-in-out infinite;
        }

        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 4px 16px rgba(255, 127, 80, 0.3); }
            50% { box-shadow: 0 4px 24px rgba(255, 127, 80, 0.5); }
        }
        
        /* Button Styles */
        .btn-primary {
            background: linear-gradient(135deg, #FF7F50 0%, #FF6347 100%);
            color: white;
            font-weight: 700;
            box-shadow: 0 8px 24px rgba(255, 127, 80, 0.3);
        }

        .btn-primary:hover {
            box-shadow: 0 12px 32px rgba(255, 127, 80, 0.4);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #F5F5F5;
            color: #FF4500;
            border: 2px solid #FF7F50;
            font-weight: 600;
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #FF7F50 0%, #008080 100%);
            border-color: #FF6347;
        }
        
        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #FF4500 0%, #FF7F50 50%, #FF6347 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Status Badges */
        .status-badge-paid {
            background: linear-gradient(135deg, #D1FAE5 0%, #A7F3D0 100%);
            color: #065F46;
            border: 2px solid #6EE7B7;
        }
        
        .status-badge-pending {
            background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
            color: #92400E;
            border: 2px solid #FBBF24;
        }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gradient-to-br from-amber-50 via-orange-50 to-white" x-data="{
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
        document.querySelectorAll('#homeScreen, #ordersScreen, #aboutScreen').forEach(screen => screen.classList.add('hidden'));
        document.getElementById(tab + 'Screen').classList.remove('hidden');
    }
}">
    <div id="mainApp" class="min-h-screen">
        <div id="homeScreen" class="pb-24">
            <!-- Header -->
            <div class="header-gradient px-5 pt-6 pb-5 fixed top-0 left-0 right-0 z-50 shadow-lg">
                <div class="flex items-center justify-between mb-3">
                    <button @click="logout()" class="flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-amber-600 px-3 py-2 rounded-xl hover:bg-amber-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                    <div class="text-center">
                        <h1 class="text-2xl font-display font-bold gradient-text mb-0.5">Kedai Djanggo</h1>
                        <p class="text-xs text-amber-600 font-semibold">{{ $customer ? $customer->name : 'Guest' }}</p>
                    </div>
                    <img src="{{ asset('images/logo-kedai-djanggo.jpg') }}" alt="Logo" class="w-12 h-12 rounded-full object-cover logo-border">
                </div>
            </div>
            
            <!-- Spacer for fixed header -->
            <div class="h-[88px]"></div>

            <!-- Categories -->
            <div class="px-5 py-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Kategori Menu</h2>
                    <span class="text-xs bg-amber-100 text-amber-700 px-3 py-1 rounded-full font-semibold">Populer ✨</span>
                </div>
                <div class="flex gap-3 overflow-x-auto scrollbar-hide pb-2">
                    <div @click="currentCategory = 'all'" 
                         class="category-card flex-shrink-0 p-4 w-28 cursor-pointer rounded-2xl text-center shadow-lg"
                         :class="currentCategory === 'all' ? 'active' : ''">
                        <div class="text-4xl mb-2 category-emoji">🍽️</div>
                        <p class="text-xs font-bold" :class="currentCategory === 'all' ? 'text-white' : 'text-gray-800'">Semua</p>
                    </div>
                    <div @click="currentCategory = 'kopi'" 
                         class="category-card flex-shrink-0 p-4 w-28 cursor-pointer rounded-2xl text-center shadow-lg"
                         :class="currentCategory === 'kopi' ? 'active' : ''">
                        <div class="text-4xl mb-2 category-emoji">☕</div>
                        <p class="text-xs font-bold" :class="currentCategory === 'kopi' ? 'text-white' : 'text-gray-800'">Kopi</p>
                    </div>
                    <div @click="currentCategory = 'minuman'" 
                         class="category-card flex-shrink-0 p-4 w-28 cursor-pointer rounded-2xl text-center shadow-lg"
                         :class="currentCategory === 'minuman' ? 'active' : ''">
                        <div class="text-4xl mb-2 category-emoji">🥤</div>
                        <p class="text-xs font-bold" :class="currentCategory === 'minuman' ? 'text-white' : 'text-gray-800'">Minuman</p>
                    </div>
                    <div @click="currentCategory = 'makanan'" 
                         class="category-card flex-shrink-0 p-4 w-28 cursor-pointer rounded-2xl text-center shadow-lg"
                         :class="currentCategory === 'makanan' ? 'active' : ''">
                        <div class="text-4xl mb-2 category-emoji">🍜</div>
                        <p class="text-xs font-bold" :class="currentCategory === 'makanan' ? 'text-white' : 'text-gray-800'">Makanan</p>
                    </div>
                    <div @click="currentCategory = 'cemilan'" 
                         class="category-card flex-shrink-0 p-4 w-28 cursor-pointer rounded-2xl text-center shadow-lg"
                         :class="currentCategory === 'cemilan' ? 'active' : ''">
                        <div class="text-4xl mb-2 category-emoji">🍟</div>
                        <p class="text-xs font-bold" :class="currentCategory === 'cemilan' ? 'text-white' : 'text-gray-800'">Cemilan</p>
                    </div>
                </div>
            </div>

            <!-- Menu Items -->
            <div class="px-5 py-2">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-display font-bold text-gray-900">Menu Spesial</h2>
                    <div class="flex items-center gap-1">
                        <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <span class="text-sm font-bold text-gray-700">Best Seller</span>
                    </div>
                </div>
                <div class="menu-grid">
                    @foreach ($menus as $menu)
                        <div @click="currentProduct = { id: {{ $menu->id }}, name: '{{ $menu->nama_menu }}', price: {{ $menu->harga }}, category: '{{ $menu->kategori_menu }}', description: '{{ $menu->description }}', image: '{{ $menu->gambar }}' }; detailModal = true"
                            class="menu-card rounded-3xl cursor-pointer shadow-lg relative"
                            x-show="currentCategory === 'all' || currentCategory === '{{ $menu->kategori_menu }}'">
                            <div class="aspect-square bg-gradient-to-br from-amber-100 to-orange-100 relative overflow-hidden rounded-t-3xl">
                                <img src="{{ $menu->gambar }}" class="w-full h-full object-cover">
                                <div class="absolute top-3 right-3 badge px-3 py-1.5 rounded-full">
                                    <span class="text-xs text-amber-700 font-bold flex items-center gap-1">
                                        <svg class="w-4 h-4 fill-current text-yellow-500" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        4.8
                                    </span>
                                </div>
                            </div>
                            <div class="p-4 relative z-10 bg-white">
                                <h3 class="font-bold text-gray-900 text-sm mb-2 line-clamp-1">{{ $menu->nama_menu }}</h3>
                                <p class="text-xs text-gray-500 mb-3 line-clamp-2 leading-relaxed">{{ $menu->description }}</p>
                                <span class="price-tag text-sm">Rp {{ number_format($menu->harga, 0, ',', '.') }}</span>
                            </div>
                            <button class="add-to-cart-btn w-11/12 bg-gradient-to-r from-amber-500 to-orange-500 text-white py-2.5 rounded-xl font-bold text-sm shadow-lg">
                                + Keranjang
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- ABOUT SCREEN -->
        <div id="aboutScreen" class="hidden pb-24 min-h-screen">
            <!-- Hero Section -->
            <div class="about-hero px-5 py-12 text-center relative">
                <div class="relative z-10">
                    <h1 class="text-4xl font-display font-bold text-white mb-3">Tentang Kami</h1>
                    <p class="text-amber-100 text-sm max-w-md mx-auto">Kedai modern dengan cita rasa tradisional yang autentik</p>
                </div>
            </div>

            <!-- Story Section -->
            <div class="px-5 -mt-8 relative z-10">
                <div class="bg-white rounded-3xl p-6 shadow-2xl border-2 border-amber-200">
                    <div class="text-center mb-6">
                        <div class="w-20 h-20 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full mx-auto mb-4 flex items-center justify-center text-4xl shadow-lg">
                            ☕
                        </div>
                        <h2 class="text-2xl font-display font-bold gradient-text mb-2">Kedai Djanggo</h2>
                        <p class="text-sm text-gray-600">Sejak 2020</p>
                    </div>
                    <p class="text-gray-700 text-sm leading-relaxed text-center mb-4">
                        Kedai Djanggo hadir dengan konsep modern namun tetap mempertahankan kehangatan suasana kedai tradisional. Kami berkomitmen menyajikan kopi pilihan dan hidangan berkualitas untuk menemani momen spesial Anda.
                    </p>
                </div>
            </div>

           <!-- Features Grid -->
            <div class="px-5 py-8">
                <h2 class="text-xl font-display font-bold text-gray-900 mb-6 text-center">Keunggulan Kami</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="feature-card rounded-2xl p-5 text-center shadow-lg">
                        <div class="feature-icon w-16 h-16 rounded-full mx-auto mb-3 flex items-center justify-center text-3xl">
                            ☕
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">Kopi Premium</h3>
                        <p class="text-xs text-gray-600">Biji kopi pilihan dari perkebunan terbaik</p>
                    </div>
                    <div class="feature-card rounded-2xl p-5 text-center shadow-lg">
                        <div class="feature-icon w-16 h-16 rounded-full mx-auto mb-3 flex items-center justify-center text-3xl">
                            👨‍🍳
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">Chef Profesional</h3>
                        <p class="text-xs text-gray-600">Diracik oleh barista berpengalaman</p>
                    </div>
                    <div class="feature-card rounded-2xl p-5 text-center shadow-lg">
                        <div class="feature-icon w-16 h-16 rounded-full mx-auto mb-3 flex items-center justify-center text-3xl">
                            🏠
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">Suasana Nyaman</h3>
                        <p class="text-xs text-gray-600">Tempat hangat untuk berkumpul</p>
                    </div>
                    <div class="feature-card rounded-2xl p-5 text-center shadow-lg">
                        <div class="feature-icon w-16 h-16 rounded-full mx-auto mb-3 flex items-center justify-center text-3xl">
                            ⚡
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">Pelayanan Cepat</h3>
                        <p class="text-xs text-gray-600">Pesanan diproses dengan efisien</p>
                    </div>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="px-5 pb-8">
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-3xl p-6 border-2 border-amber-200">
                    <h3 class="text-lg font-display font-bold text-gray-900 mb-4 text-center">Hubungi Kami</h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 bg-white p-3 rounded-xl">
                            <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center text-white">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Email</p>
                                <p class="text-sm font-semibold text-gray-900">info@kedaidjanggo.com</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 bg-white p-3 rounded-xl">
                            <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center text-white">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Telepon</p>
                                <p class="text-sm font-semibold text-gray-900">+62 812-3456-7890</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 bg-white p-3 rounded-xl">
                            <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center text-white">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Alamat</p>
                                <p class="text-sm font-semibold text-gray-900">Jl. Contoh No. 123, Jakarta</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Operating Hours -->
            <div class="px-5 pb-8">
                <div class="bg-white rounded-3xl p-6 border-2 border-amber-200 shadow-lg">
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full mx-auto mb-3 flex items-center justify-center text-3xl shadow-lg">
                            🕐
                        </div>
                        <h3 class="text-lg font-display font-bold text-gray-900">Jam Operasional</h3>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center py-2 border-b border-amber-100">
                            <span class="text-sm text-gray-600">Senin - Jumat</span>
                            <span class="text-sm font-bold text-gray-900">08:00 - 22:00</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-amber-100">
                            <span class="text-sm text-gray-600">Sabtu - Minggu</span>
                            <span class="text-sm font-bold text-gray-900">09:00 - 23:00</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-sm text-gray-600">Hari Libur</span>
                            <span class="text-sm font-bold text-amber-600">Tetap Buka!</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ORDERS SCREEN -->
        <div id="ordersScreen" class="hidden pb-24 min-h-screen">
            <div class="header-gradient p-5 sticky top-0 z-10">
                <h1 class="text-xl font-display font-bold text-gray-900">Pesanan Saya</h1>
            </div>
            <div class="p-5">
                @forelse ($orders as $order)
                    <div class="bg-white rounded-3xl p-5 mb-4 shadow-lg border-2 border-amber-100 hover:shadow-xl transition-all">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <span class="font-bold text-gray-900 block mb-1">Pesanan #{{ $order->midtrans_order_id }}</span>
                                <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }}</span>
                            </div>
                            <span class="text-xs px-3 py-1.5 rounded-full font-bold {{ $order->status == 'paid' ? 'status-badge-paid' : 'status-badge-pending' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <div class="border-t-2 border-amber-100 pt-3 mt-3">
                            <p class="text-gray-700 font-semibold mb-3 flex items-center gap-2">
                                <span class="text-sm">Total:</span>
                                <span class="gradient-text text-lg">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                            </p>
                            <a href="{{ route('order.status', $order->id) }}" class="inline-block btn-primary px-6 py-3 rounded-xl text-sm">
                                Lihat Detail →
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20">
                        <div class="w-32 h-32 bg-gradient-to-br from-amber-100 to-orange-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <svg class="w-16 h-16 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-display font-bold text-gray-900 mb-2">Belum Ada Pesanan</h3>
                        <p class="text-gray-500 text-sm mb-6">Pesanan yang sudah dibayar akan muncul di sini</p>
                        <button @click="switchTab('home')" class="btn-primary px-6 py-3 rounded-xl text-sm">
                            Mulai Belanja
                        </button>
                    </div>
                @endempty
            </div>
        </div>

        <!-- DETAIL MODAL -->
        <div x-show="detailModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-md z-50" @click.self="detailModal = false" x-cloak>
            <div class="absolute inset-0 flex items-end">
                <div class="bg-gradient-to-b from-f5f5f5 to-fafafa rounded-t-[40px] w-full max-h-[90vh] overflow-y-auto slide-up custom-scrollbar border-t-4 border-coral-400">
                    <div class="relative">
                        <img :src="currentProduct.image" class="w-full h-80 object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <button @click="detailModal = false" class="absolute top-4 left-4 bg-white/95 backdrop-blur-md rounded-full p-3 shadow-xl hover:bg-white">
                            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <div class="absolute top-4 right-4 badge px-4 py-2 rounded-full">
                            <span class="text-sm text-teal-700 font-bold flex items-center gap-2">
                                <svg class="w-5 h-5 fill-current text-yellow-500" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                4.8
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h2 class="text-3xl font-display font-bold text-gray-900 mb-3" x-text="currentProduct.name"></h2>
                        <p class="text-gray-600 mb-6 leading-relaxed text-sm" x-text="currentProduct.description"></p>
                        <div class="bg-gradient-to-r from-f5f5f5 via-fafafa to-f5f5f5 rounded-3xl p-6 mb-6 border-2 border-teal-300 shadow-inner">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray-600 mb-1 font-semibold">Harga</p>
                                    <span class="text-4xl font-bold gradient-text" x-text="'Rp ' + (currentProduct.price || 0).toLocaleString('id-ID')"></span>
                                </div>
                                <div class="text-5xl">💰</div>
                            </div>
                        </div>
                        <button @click="addToCart(currentProduct.id)" class="w-full btn-primary py-5 rounded-2xl text-base font-bold flex items-center justify-center gap-3 text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
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
            class="floating-cart-btn text-white px-8 py-5 rounded-3xl shadow-2xl flex items-center gap-4 border-2 border-teal-300"
            x-transition>
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <div class="flex flex-col items-start">
                <span class="text-xs font-bold opacity-90"><span x-text="cartCount"></span> item</span>
                <span class="font-bold text-xl" x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
            </div>
        </button>

        <!-- CART OVERLAY & PANEL -->
        <div x-show="showCartPanel" @click="showCartPanel = false" class="cart-overlay" x-transition x-cloak></div>
        
        <div x-show="showCartPanel" class="cart-panel custom-scrollbar" :class="{ 'show': showCartPanel }" x-cloak>
            <div class="sticky top-0 bg-gradient-to-b from-f5f5f5 to-fafafa border-b-2 border-teal-200 p-5 flex items-center justify-between z-10">
                <h2 class="text-xl font-display font-bold gradient-text">Keranjang Saya</h2>
                <button @click="showCartPanel = false" class="p-2 hover:bg-amber-100 rounded-full transition-all">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-5">
                <div class="bg-gradient-to-r from-f5f5f5 to-fafafa rounded-2xl p-4 mb-5 border-2 border-teal-300 shadow-inner">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-md">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="text-sm font-bold text-gray-900 block">Estimasi Penyajian</span>
                            <p class="text-xs text-gray-600 font-semibold">⚡ 15-20 menit • 🍽️ Diantar ke meja</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 mb-5">
                    <template x-for="item in cart" :key="item.id">
                        <div class="bg-f5f5f5 rounded-2xl p-4 flex gap-4 border-2 border-teal-200 shadow-lg hover:shadow-xl transition-all">
                            <img :src="item.image" class="w-24 h-24 rounded-xl object-cover border-2 border-teal-300 shadow-md">
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 mb-1 text-sm" x-text="item.name"></h4>
                                <p class="gradient-text font-bold mb-3 text-base" x-text="'Rp ' + (item.price * item.quantity).toLocaleString('id-ID')"></p>
                                <div class="flex items-center gap-3">
                                    <button @click="updateQuantity(item.id, -1)" class="w-9 h-9 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-700 font-bold hover:from-gray-200 hover:to-gray-300 shadow-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"></path>
                                        </svg>
                                    </button>
                                    <span class="font-bold w-10 text-center text-gray-900 text-lg" x-text="item.quantity"></span>
                                    <button @click="updateQuantity(item.id, 1)" class="w-9 h-9 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white font-bold hover:shadow-lg shadow-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <button @click="showCartPanel = false" class="w-full btn-secondary py-4 rounded-2xl mb-5 text-sm font-bold">
                    + Tambah Menu Lagi
                </button>

                <div class="bg-gradient-to-br from-f5f5f5 via-fafafa to-f5f5f5 rounded-3xl p-6 space-y-3 mb-6 border-2 border-teal-300 shadow-xl">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-gray-900 text-lg">Total Pembayaran</span>
                        <span class="font-bold text-3xl gradient-text" x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
                    </div>
                </div>

                <a href="{{ route('checkout') }}" class="block w-full btn-primary py-5 rounded-2xl text-base text-center font-bold shadow-2xl">
                    Lanjut ke Pembayaran →
                </a>
            </div>
        </div>

        <!-- ENHANCED BOTTOM NAV -->
        <div class="bottom-nav fixed bottom-0 left-0 right-0 px-6 py-4 flex justify-around z-20">
            <button @click="switchTab('home')" class="nav-item flex flex-col items-center gap-1.5" :class="{ 'active': currentTab === 'home' }">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="text-xs text-gray-500 font-semibold">Home</span>
            </button>
            <button @click="switchTab('orders')" class="nav-item flex flex-col items-center gap-1.5" :class="{ 'active': currentTab === 'orders' }">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <span class="text-xs text-gray-500 font-semibold">Pesanan</span>
            </button>
            <button @click="switchTab('about')" class="nav-item flex flex-col items-center gap-1.5" :class="{ 'active': currentTab === 'about' }">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-xs text-gray-500 font-semibold">Tentang</span>
            </button>
        </div>
    </div>

    <script>
        function addToCart(menuId) {
            if (!menuId) return;
            
            fetch('{{route('cart.add')}}', {
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
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: data }));
                showToast('✅ Berhasil ditambahkan ke keranjang!');
            })
            .catch(error => {
                console.error('Error adding to cart:', error);
                showToast('❌ Gagal menambahkan ke keranjang', 'error');
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
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: data }));
            })
            .catch(error => {
                console.error('Error updating quantity:', error);
                showToast('❌ Gagal mengupdate jumlah', 'error');
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
            toast.className = `fixed top-6 left-1/2 transform -translate-x-1/2 z-[100] px-6 py-4 rounded-2xl shadow-2xl text-sm font-bold ${type === 'success' ? 'bg-gradient-to-r from-green-400 to-emerald-500 text-white' : 'bg-gradient-to-r from-red-400 to-rose-500 text-white'}`;
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
    </script>

    </body>
</html>