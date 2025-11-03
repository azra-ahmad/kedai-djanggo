<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management - Kedai Djanggo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    @include('admin.partials.sidebar')
    
    <div class="ml-64 p-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Menu Management</h1>
                <p class="text-gray-500">Manage your menu items</p>
            </div>
            <a href="{{ route('admin.menu.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Menu
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Menu Grid -->
        <div class="grid grid-cols-4 gap-6">
            @foreach($menus as $menu)
                <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
                    <div class="aspect-square bg-gray-200 relative">
                        <img src="{{ $menu->gambar }}" alt="{{ $menu->nama_menu }}" class="w-full h-full object-cover">
                        <div class="absolute top-2 right-2 px-2 py-1 rounded-full text-xs font-semibold
                            @if($menu->kategori_menu == 'kopi') bg-amber-100 text-amber-800
                            @elseif($menu->kategori_menu == 'minuman') bg-blue-100 text-blue-800
                            @elseif($menu->kategori_menu == 'makanan') bg-red-100 text-red-800
                            @elseif($menu->kategori_menu == 'cemilan') bg-yellow-100 text-yellow-800
                            @else bg-purple-100 text-purple-800
                            @endif">
                            {{ ucfirst($menu->kategori_menu) }}
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-gray-900 mb-1">{{ $menu->nama_menu }}</h3>
                        <p class="text-sm text-gray-500 mb-3 line-clamp-2">{{ $menu->description }}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-orange-600 font-bold text-lg">Rp {{ number_format($menu->harga, 0, ',', '.') }}</span>
                            <div class="flex gap-2">
                                <a href="{{ route('admin.menu.edit', $menu->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.menu.destroy', $menu->id) }}" method="POST" onsubmit="return confirm('Delete this menu?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>