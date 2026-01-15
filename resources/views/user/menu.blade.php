<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kedai Djanggo - Menu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #EF7722;
            --secondary: #FAA533;
            --light: #EBEBEB;
            --dark: #000000;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #FFFFFF 0%, #EBEBEB 50%, #FAFAFA 100%);
        }

        .font-display {
            font-family: 'Playfair Display', serif;
        }

        * {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Category Cards */
        .category-card {
            transition: transform 0.2s ease, background-color 0.2s;
            background: #FFFFFF;
            border: 1px solid #f3f4f6; /* Simplified border */
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); /* Optimized shadow */
        }

        /* Removed complex hover effect with ::before gradient */

        .category-card:hover {
            transform: translateY(-4px);
            border-color: var(--primary);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); /* Tailwind shadow-md */
        }

        .category-card.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-color: var(--primary);
            box-shadow: 0 4px 6px rgba(239, 119, 34, 0.3); /* Simplified shadow */
            transform: scale(1.05);
        }

        /* Removed complex active::before gradient */

        /* Menu Cards */
        .menu-card {
            transition: transform 0.2s ease; /* Faster transition */
            background: #FFFFFF;
            border: 1px solid #f3f4f6;
            position: relative;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); /* Lighter shadow */
        }

        /* Removed menu-card::after gradient overlay */

        .menu-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); /* Tailwind shadow-xl equivalent */
            border-color: var(--primary);
            z-index: 10;
        }

        .menu-card img {
            transition: transform 0.3s ease; /* Faster */
        }

        .menu-card:hover img {
            transform: scale(1.05); /* Less zoom */
        }

        /* Add to Cart Button */
        .add-to-cart-btn {
            position: absolute;
            bottom: -50px;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: all 0.2s ease;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }

        .menu-card:hover .add-to-cart-btn {
            bottom: 12px;
            opacity: 1;
        }

        /* Bottom Nav - Optimized: Removed blur & shimmer */
        .bottom-nav {
            box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.05);
            background: rgba(255, 255, 255, 0.98); /* High opacity solid color instead of blur */
            border-top: 1px solid #f3f4f6;
            position: relative;
        }

        /* Removed shimmer animation */

        .nav-item {
            position: relative;
            transition: transform 0.2s ease;
        }

        .nav-item:hover {
            transform: translateY(-2px);
        }

        .nav-item.active {
            transform: scale(1.05);
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            top: -1px;
            left: 50%;
            transform: translateX(-50%);
            width: 32px;
            height: 3px;
            background: var(--primary); /* Solid color is faster */
            border-radius: 0 0 3px 3px;
        }

        .nav-item.active svg {
            color: var(--primary);
        }

        .nav-item.active span {
            color: var(--primary);
            font-weight: 700;
        }

        /* Floating Cart - Optimized: Removed float animation */
        .floating-cart-btn {
            position: fixed;
            bottom: 88px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 30;
            box-shadow: 0 4px 6px rgba(239, 119, 34, 0.4); /* Lighter shadow */
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            /* Animation removed for performance */
        }

        .floating-cart-btn:hover {
            box-shadow: 0 10px 15px -3px rgba(239, 119, 34, 0.5);
            transform: translateX(-50%) scale(1.02);
        }

        .floating-cart-btn.hidden {
            transform: translate(-50%, 150px);
            opacity: 0;
        }

        /* Price Tag */
        .price-tag {
            background: var(--primary); /* Solid color */
            color: #FFFFFF;
            padding: 8px 14px;
            border-radius: 12px;
            display: inline-block;
            font-weight: 800;
            position: relative;
            overflow: hidden;
        }

        /* Header */
        .header-gradient {
            background: rgba(255, 255, 255, 0.98); /* High opacity */
            border-bottom: 1px solid #f3f4f6;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        /* About Hero - Optimized: Removed infinite rotation */
        .about-hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
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
            /* Animation removed */
        }

        /* Feature Cards */
        .feature-card {
            background: #FFFFFF;
            border: 1px solid #f3f4f6;
            transition: transform 0.2s ease;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-color: var(--primary);
        }

        .feature-icon {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            /* Animation pulse removed */
        }

        /* Scrollbar */
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #FFFFFF; }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #d1d5db; /* Gray-300 simple color */
            border-radius: 3px;
        }

        /* Cart Panel */
        .cart-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            /* Backdrop blur removed */
            z-index: 59;
        }

        .cart-panel {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #FFFFFF;
            border-radius: 24px 24px 0 0;
            max-height: 85vh;
            overflow-y: auto;
            z-index: 60;
            transform: translateY(100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); /* Standard eased transition */
            box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1);
            border-top: 4px solid var(--primary);
        }

        .cart-panel.show {
            transform: translateY(0);
        }

        /* Animations - Simplified */
        @keyframes slideUp {
            from { transform: translateY(100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .slide-up { animation: slideUp 0.3s ease-out; }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .fade-in { animation: fadeIn 0.3s ease-out; }

        /* Badge - Optimized: Removed blur */
        .badge {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(239, 119, 34, 0.2);
        }

        /* Logo - Optimized: Removed pulse animation */
        .logo-border {
            border: 2px solid var(--primary);
            box-shadow: 0 2px 4px rgba(239, 119, 34, 0.2);
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            font-weight: 700;
            box-shadow: 0 4px 6px -1px rgba(239, 119, 34, 0.3);
        }

        .btn-primary:hover {
            box-shadow: 0 10px 15px -3px rgba(239, 119, 34, 0.4);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--light);
            color: var(--dark);
            border: 2px solid var(--primary);
            font-weight: 600;
        }

        .btn-secondary:hover {
            background: var(--secondary);
            border-color: var(--primary);
            color: white;
        }

        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
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

<body class="bg-gradient-to-br from-white via-[#EBEBEB] to-white" x-data="app()" x-init="init()">
    <div id="mainApp">
        <div id="homeScreen" class="pb-24">
            <!-- Header -->
            <div class="header-gradient px-5 pt-6 pb-5 fixed top-0 left-0 right-0 z-50 shadow-lg">
                <div class="flex items-center justify-between mb-3">
                    <button @click="logout()" class="flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-[#EF7722] px-3 py-2 rounded-xl hover:bg-[#EBEBEB]/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                    <div class="text-center">
                        <h1 class="text-2xl font-display font-bold gradient-text mb-0.5">Kedai Djanggo</h1>
                        <p class="text-xs text-[#EF7722] font-semibold">{{ $customer ? $customer->name : 'Guest' }}</p>
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
                    <span class="text-xs bg-[#EBEBEB] text-[#EF7722] px-3 py-1 rounded-full font-semibold">Populer ✨</span>
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

            <!-- SKELETON LOADER (Visual Only) -->
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
                    <div class="menu-card rounded-2xl overflow-hidden group relative flex flex-col h-full bg-white hover:shadow-lg transition-all duration-300"
                        x-data="{ 
                            product: { 
                                id: {{ $menu->id }}, 
                                name: '{{ $menu->nama_menu }}', 
                                price: {{ $menu->harga }}, 
                                category: '{{ $menu->kategori_menu }}', 
                                description: '{{ $menu->description }}', 
                                image: '{{ $menu->image_url }}' 
                            }
                        }"
                        x-show="!loading && (currentCategory === 'all' || currentCategory === '{{ $menu->kategori_menu }}')">

                        <!-- Clickable Area untuk Detail Modal (hanya gambar + teks, bukan button) -->
                        <div @click="currentProduct = product; detailModal = true"
                            class="absolute inset-0 z-10 cursor-pointer">
                        </div>

                        <div class="aspect-square bg-[#EBEBEB] relative overflow-hidden flex-shrink-0">
                            <img src="{{ $menu->image_url }}" alt="{{ $menu->nama_menu }}" class="w-full h-full object-cover transition duration-300 transform group-hover:scale-110" loading="lazy" onerror="this.src='{{ asset('images/default.jpg') }}'" >
                            <div class="absolute top-3 left-3 bg-white px-2.5 py-1 rounded-lg shadow-sm">
                                <span class="text-xs font-medium text-gray-700 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 fill-current text-[#FAA533]" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    4.8
                                </span>
                            </div>
                        </div>

                        <div class="p-4 bg-white relative flex flex-col flex-grow"> <!-- relative agar button di atas overlay -->
                            <h3 class="font-semibold text-gray-900 text-base mb-1.5 line-clamp-1">{{ $menu->nama_menu }}</h3>
                            <p class="text-xs text-gray-500 mb-4 line-clamp-2 leading-relaxed min-h-[32px]">{{ $menu->description }}</p>

                            <div class="mt-auto space-y-2.5">
                                <div class="flex items-baseline gap-1">
                                    <span class="text-lg font-bold text-gray-900">Rp {{ number_format($menu->harga, 0, ',', '.') }}</span>
                                </div>

                                <!-- Button Tambah: z-20 + pointer-events-auto -->
                                <button @click.stop="addToCart(product.id)" 
                                        class="relative z-20 w-full bg-gradient-to-r from-[#EF7722] to-[#FAA533] hover:from-[#d96a1e] hover:to-[#e6952c] text-white px-4 py-2 rounded-lg text-xs font-semibold shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-center gap-1.5 pointer-events-auto focus:outline-none focus:ring-2 focus:ring-[#EF7722] focus:ring-offset-2">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v12m6-6H6"></path>
                                    </svg>
                                    Tambah
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        </div>

        <!-- ABOUT SCREEN -->
        <div id="aboutScreen" class="hidden pb-24 min-h-screen">
            <!-- Hero Section -->
            <div class="about-hero px-5 py-12 text-center relative">
                <div class="relative z-10">
                    <h1 class="text-4xl font-display font-bold text-white mb-3">Tentang Kami</h1>
                    <p class="text-white/90 text-sm max-w-md mx-auto">Kedai modern dengan cita rasa tradisional yang autentik</p>
                </div>
            </div>

            <!-- Story Section -->
            <div class="px-5 -mt-8 relative z-10">
                <div class="bg-white rounded-3xl p-6 shadow-2xl border-2 border-[#FAA533]">
                    <div class="text-center mb-6">
                        <div class="w-20 h-20 bg-gradient-to-br from-[#EF7722] to-[#FAA533] rounded-full mx-auto mb-4 flex items-center justify-center text-4xl shadow-lg text-white">
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
                    <div class="feature-card rounded-2xl p-5 text-center shadow-lg bg-white border-2 border-[#EBEBEB] hover:border-[#EF7722]">
                        <div class="feature-icon w-16 h-16 rounded-full mx-auto mb-3 flex items-center justify-center text-3xl text-white">
                            ☕
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">Kopi Premium</h3>
                        <p class="text-xs text-gray-600">Biji kopi pilihan dari perkebunan terbaik</p>
                    </div>
                    <div class="feature-card rounded-2xl p-5 text-center shadow-lg bg-white border-2 border-[#EBEBEB] hover:border-[#EF7722]">
                        <div class="feature-icon w-16 h-16 rounded-full mx-auto mb-3 flex items-center justify-center text-3xl text-white">
                            👨‍🍳
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">Chef Profesional</h3>
                        <p class="text-xs text-gray-600">Diracik oleh barista berpengalaman</p>
                    </div>
                    <div class="feature-card rounded-2xl p-5 text-center shadow-lg bg-white border-2 border-[#EBEBEB] hover:border-[#EF7722]">
                        <div class="feature-icon w-16 h-16 rounded-full mx-auto mb-3 flex items-center justify-center text-3xl text-white">
                            🏠
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">Suasana Nyaman</h3>
                        <p class="text-xs text-gray-600">Tempat hangat untuk berkumpul</p>
                    </div>
                    <div class="feature-card rounded-2xl p-5 text-center shadow-lg bg-white border-2 border-[#EBEBEB] hover:border-[#EF7722]">
                        <div class="feature-icon w-16 h-16 rounded-full mx-auto mb-3 flex items-center justify-center text-3xl text-white">
                            ⚡
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">Pelayanan Cepat</h3>
                        <p class="text-xs text-gray-600">Pesanan diproses dengan efisien</p>
                    </div>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="px-5 pb-8">
                <div class="bg-gradient-to-br from-[#EBEBEB] to-white rounded-3xl p-6 border-2 border-[#FAA533]">
                    <h3 class="text-lg font-display font-bold text-gray-900 mb-4 text-center">Hubungi Kami</h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 bg-white p-3 rounded-xl shadow-sm">
                            <div class="w-10 h-10 bg-gradient-to-br from-[#EF7722] to-[#FAA533] rounded-full flex items-center justify-center text-white">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Email</p>
                                <p class="text-sm font-semibold text-gray-900">kedaidjanggo@gmail.com</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 bg-white p-3 rounded-xl shadow-sm">
                            <div class="w-10 h-10 bg-gradient-to-br from-[#EF7722] to-[#FAA533] rounded-full flex items-center justify-center text-white">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Telepon</p>
                                <p class="text-sm font-semibold text-gray-900">+62 877-3048-2920</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 bg-white p-3 rounded-xl shadow-sm">
                            <div class="w-10 h-10 bg-gradient-to-br from-[#EF7722] to-[#FAA533] rounded-full flex items-center justify-center text-white">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Alamat</p>
                                <p class="text-sm font-semibold text-gray-900">Jl. Puri Menteng V Blok B5 No. 5 Rt.12 / Rw 12</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Operating Hours -->
            <div class="px-5 pb-8">
                <div class="bg-white rounded-3xl p-6 border-2 border-[#FAA533] shadow-lg">
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-[#EF7722] to-[#FAA533] rounded-full mx-auto mb-3 flex items-center justify-center text-3xl shadow-lg text-white">
                            🕐
                        </div>
                        <h3 class="text-lg font-display font-bold text-gray-900">Jam Operasional</h3>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center py-2 border-b border-[#EBEBEB]">
                            <span class="text-sm text-gray-600">Senin - Jumat</span>
                            <span class="text-sm font-bold text-gray-900">15.00 - 22:00</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-[#EBEBEB]">
                            <span class="text-sm text-gray-600">Sabtu - Minggu</span>
                            <span class="text-sm font-bold text-gray-900">15.30 - 23:00</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-sm text-gray-600">Hari Libur</span>
                            <span class="text-sm font-bold text-[#EF7722]">Tetap Buka!</span>
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
                <div class="bg-white rounded-3xl p-5 mb-4 shadow-lg border-2 border-[#EBEBEB] hover:shadow-xl transition-all">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <span class="font-bold text-gray-900 block mb-1">Pesanan #{{ $order->midtrans_order_id }}</span>
                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }}</span>
                        </div>
                        <span class="text-xs px-3 py-1.5 rounded-full font-bold {{ $order->status == 'paid' ? 'status-badge-paid' : 'status-badge-pending' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div class="border-t-2 border-[#EBEBEB] pt-3 mt-3">
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
                    <div class="w-32 h-32 bg-gradient-to-br from-[#EBEBEB] to-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <svg class="w-16 h-16 text-[#EF7722]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-display font-bold text-gray-900 mb-2">Belum Ada Pesanan</h3>
                    <p class="text-gray-500 text-sm mb-6">Pesanan yang sudah dibayar akan muncul di sini</p>
                    <button @click="switchTab('home')" class="btn-primary px-6 py-3 rounded-xl text-sm">
                        Mulai Belanja
                    </button>
                </div>
                @endforelse
            </div>
        </div>

        <!-- DETAIL MODAL -->
        <div x-show="detailModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-md z-[70]" @click.self="detailModal = false" x-cloak>
            <div class="absolute inset-0 flex items-end">
                <div class="bg-white rounded-t-[40px] w-full max-h-[90vh] overflow-y-auto slide-up custom-scrollbar border-t-4 border-[#EF7722]">
                    <div class="relative">
                        <img :src="currentProduct.image" class="w-full h-80 object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <button @click="detailModal = false" class="absolute top-4 left-4 bg-white/95 backdrop-blur-md rounded-full p-3 shadow-xl hover:bg-white">
                            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <div class="absolute top-4 right-4 badge px-4 py-2 rounded-full">
                            <span class="text-sm text-[#EF7722] font-bold flex items-center gap-2">
                                <svg class="w-5 h-5 fill-current text-[#FAA533]" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                4.8
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h2 class="text-3xl font-display font-bold text-gray-900 mb-3" x-text="currentProduct.name"></h2>
                        <p class="text-gray-600 mb-6 leading-relaxed text-sm" x-text="currentProduct.description"></p>
                        <div class="bg-gradient-to-r from-[#EBEBEB] via-white to-[#EBEBEB] rounded-3xl p-6 mb-6 border-2 border-[#EF7722] shadow-inner">
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

        <!-- LOGOUT CONFIRMATION MODAL -->
        <div x-show="logoutModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm z-[80] flex items-center justify-center p-4" x-cloak>
            <div class="bg-white rounded-3xl p-6 w-full max-w-sm shadow-2xl transform transition-all"
                @click.outside="logoutModal = false"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-90">
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Konfirmasi Keluar</h3>
                    <p class="text-gray-500 text-sm mb-6">Apakah Anda yakin ingin mengakhiri sesi ini?</p>
                    
                    <div class="flex gap-3">
                        <button @click="logoutModal = false" class="flex-1 px-4 py-3 rounded-xl border-2 border-gray-100 font-bold text-gray-600 hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button @click="confirmLogout()" class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-red-500 to-red-600 text-white font-bold shadow-lg shadow-red-200 hover:shadow-red-300 transition-all transform hover:scale-105">
                            Ya, Keluar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- FLOATING CART PILL (Fixed above Nav) -->
        <a href="{{ route('checkout') }}"
            x-show="cartCount > 0"
            class="fixed bottom-[90px] left-4 right-4 z-50 bg-gradient-to-r from-[#EF7722] to-[#FAA533] text-white p-4 rounded-full shadow-xl flex items-center justify-between border border-[#EBEBEB] transition-transform active:scale-95"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-20 opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="translate-y-0 opacity-100"
            x-transition:leave-end="translate-y-20 opacity-0">
            
            <div class="flex items-center gap-3">
                <div class="bg-white/20 p-2 rounded-full">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="flex flex-col items-start leading-none">
                    <span class="text-xs font-medium text-white/90" x-text="cartCount + ' Menu'"></span>
                    <span class="text-lg font-bold" x-text="'Rp ' + (total || 0).toLocaleString('id-ID')"></span>
                </div>
            </div>

            <div class="flex items-center gap-2 font-bold text-sm">
                Lanjut Bayar
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </div>
        </a>

        <!-- ENHANCED BOTTOM NAV -->
        <div class="bottom-nav fixed bottom-0 left-0 right-0 px-6 py-4 flex justify-around z-40">
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
                    setTimeout(() => this.loading = false, 800); // Falback skeleton fade out
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

                // TAMBAH KE KERANJANG
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

                // UPDATE JUMLAH
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
</body>
</html>