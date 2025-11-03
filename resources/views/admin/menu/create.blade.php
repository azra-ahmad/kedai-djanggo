<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Menu - Kedai Djanggo</title>
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
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.menu.index') }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Add New Menu</h1>
            </div>
            <p class="text-gray-500">Create a new menu item</p>
        </div>

        <!-- Form -->
        <div class="max-w-2xl">
            <form action="{{ route('admin.menu.store') }}" method="POST" class="bg-white rounded-xl shadow-sm p-6 space-y-6">
                @csrf

                <!-- Nama Menu -->
                <div>
                    <label for="nama_menu" class="block text-sm font-semibold text-gray-700 mb-2">Nama Menu</label>
                    <input type="text" id="nama_menu" name="nama_menu" value="{{ old('nama_menu') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                        placeholder="e.g. Kopi Hitam">
                    @error('nama_menu')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori -->
                <div>
                    <label for="kategori_menu" class="block text-sm font-semibold text-gray-700 mb-2">Kategori</label>
                    <select id="kategori_menu" name="kategori_menu" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">Pilih Kategori</option>
                        <option value="kopi" {{ old('kategori_menu') == 'kopi' ? 'selected' : '' }}>Kopi</option>
                        <option value="minuman" {{ old('kategori_menu') == 'minuman' ? 'selected' : '' }}>Minuman</option>
                        <option value="makanan" {{ old('kategori_menu') == 'makanan' ? 'selected' : '' }}>Makanan</option>
                        <option value="cemilan" {{ old('kategori_menu') == 'cemilan' ? 'selected' : '' }}>Cemilan</option>
                        <option value="dessert" {{ old('kategori_menu') == 'dessert' ? 'selected' : '' }}>Dessert</option>
                    </select>
                    @error('kategori_menu')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harga -->
                <div>
                    <label for="harga" class="block text-sm font-semibold text-gray-700 mb-2">Harga</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3.5 text-gray-500 font-semibold">Rp</span>
                        <input type="number" id="harga" name="harga" value="{{ old('harga') }}" required min="0"
                            class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                            placeholder="15000">
                    </div>
                    @error('harga')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gambar URL -->
                <div>
                    <label for="gambar" class="block text-sm font-semibold text-gray-700 mb-2">Gambar URL</label>
                    <input type="url" id="gambar" name="gambar" value="{{ old('gambar') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                        placeholder="https://images.unsplash.com/...">
                    <p class="text-xs text-gray-500 mt-1">Gunakan URL gambar dari Unsplash, Pexels, atau hosting lainnya</p>
                    @error('gambar')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                    <textarea id="description" name="description" rows="4"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                        placeholder="Deskripsi singkat tentang menu ini...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 bg-orange-600 hover:bg-orange-700 text-white py-3 rounded-lg font-semibold">
                        Create Menu
                    </button>
                    <a href="{{ route('admin.menu.index') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 py-3 rounded-lg font-semibold text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>