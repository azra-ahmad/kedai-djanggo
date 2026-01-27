<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Kedai Djanggo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('styles')
    @stack('head-scripts')
</head>
<body class="bg-gradient-to-br from-gray-50 via-orange-50/30 to-gray-50 font-sans" x-data="{ sidebarOpen: false }">
    
    <!-- Mobile Top Navbar (visible only on mobile) -->
    <header class="lg:hidden fixed top-0 left-0 right-0 z-40 bg-white border-b border-gray-200 px-4 py-3">
        <div class="flex items-center justify-between">
            <!-- Hamburger Button -->
            <button @click="sidebarOpen = !sidebarOpen" 
                    class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-orange-500 transition">
                <i class="ri-menu-line text-2xl"></i>
            </button>
            
            <!-- Logo (Mobile) -->
            <div class="flex items-center gap-2">
                <img 
                    src="{{ asset('images/logo-kedai-djanggo.jpg') }}" 
                    alt="Logo Kedai Djanggo" 
                    class="w-8 h-8 rounded-lg object-cover shadow-sm"
                >
                <span class="font-bold text-gray-900">Kedai Djanggo</span>
            </div>
            
            <!-- User Avatar (Mobile) -->
            <a href="{{ route('admin.profile') }}" class="w-9 h-9 bg-gradient-to-br from-orange-500 to-red-500 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                {{ substr(auth()->user()->name, 0, 1) }}
            </a>
        </div>
    </header>

    <!-- Mobile Backdrop -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="lg:hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-40"
         x-cloak>
    </div>

    <!-- Sidebar -->
    @include('admin.partials.sidebar')

    <!-- Main Content Wrapper -->
    <div class="lg:ml-64 min-h-screen pt-16 lg:pt-0">
        <!-- Flash Messages -->
        @if(session('success'))
        <div class="fixed top-20 lg:top-4 right-4 z-50 glass-card border-l-4 border-emerald-500 text-emerald-800 px-6 py-4 rounded-2xl shadow-lg animate-slide-down" 
             x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 5000)"
             x-transition>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i class="ri-checkbox-circle-line text-xl text-emerald-600"></i>
                </div>
                <span class="font-semibold">{{ session('success') }}</span>
                <button @click="show = false" class="ml-2 text-emerald-600 hover:text-emerald-800">
                    <i class="ri-close-line"></i>
                </button>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="fixed top-20 lg:top-4 right-4 z-50 glass-card border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-2xl shadow-lg animate-slide-down"
             x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 5000)"
             x-transition>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="ri-error-warning-line text-xl text-red-600"></i>
                </div>
                <span class="font-semibold">{{ session('error') }}</span>
                <button @click="show = false" class="ml-2 text-red-600 hover:text-red-800">
                    <i class="ri-close-line"></i>
                </button>
            </div>
        </div>
        @endif

        <!-- Page Content -->
        <div class="p-4 lg:p-6">
            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>
</html>
