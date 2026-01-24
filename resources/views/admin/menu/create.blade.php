@extends('layouts.admin')

@section('title', 'Add Menu')

@section('content')
<!-- Header -->
<div class="mb-8">
    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('admin.menu.index') }}" class="text-gray-500 hover:text-gray-700 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Add New Menu</h1>
    </div>
    <p class="text-gray-500 text-sm">Create a new menu item</p>
</div>

@if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h3 class="text-red-800 font-semibold mb-1">Ada masalah dengan form:</h3>
                <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

<!-- Form -->
<div class="max-w-2xl">
    <form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl border border-gray-100 p-6 space-y-6">
        @csrf

        <!-- Image Upload -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Gambar Menu <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <input type="file" id="gambar" name="gambar" accept="image/*" required
                    class="hidden" onchange="previewImage(this)">
                <label for="gambar" class="cursor-pointer block">
                    <div id="imagePreview"
                        class="w-full h-64 bg-gray-100 rounded-xl border-2 border-dashed border-gray-300
                            flex flex-col items-center justify-center gap-3 hover:border-orange-500 transition">

                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16
                                    m-2-2l1.586-1.586a2 2 0 012.828 0L20 14
                                    m-6-6h.01M6 20h12a2 2 0 002-2V6
                                    a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>

                        <div class="text-center">
                            <p class="text-gray-600 text-sm font-medium">Klik untuk upload gambar</p>
                            <p class="text-gray-400 text-xs mt-1">JPEG, JPG, PNG, WebP (Max 2MB)</p>
                        </div>
                    </div>
                </label>
            </div>
            @error('gambar')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Nama Menu -->
        <div>
            <label for="nama_menu" class="block text-sm font-semibold text-gray-700 mb-2">
                Nama Menu <span class="text-red-500">*</span>
            </label>
            <input type="text" id="nama_menu" name="nama_menu" value="{{ old('nama_menu') }}" required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition"
                placeholder="Contoh: Kopi Hitam">
            @error('nama_menu')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Kategori -->
        <div>
            <label for="kategori_menu" class="block text-sm font-semibold text-gray-700 mb-2">
                Kategori <span class="text-red-500">*</span>
            </label>
            <select id="kategori_menu" name="kategori_menu" required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                <option value="">-- Pilih Kategori --</option>
                <option value="kopi" {{ old('kategori_menu') == 'kopi' ? 'selected' : '' }}>☕ Kopi</option>
                <option value="minuman" {{ old('kategori_menu') == 'minuman' ? 'selected' : '' }}>🥤 Minuman</option>
                <option value="makanan" {{ old('kategori_menu') == 'makanan' ? 'selected' : '' }}>🍽️ Makanan</option>
                <option value="cemilan" {{ old('kategori_menu') == 'cemilan' ? 'selected' : '' }}>🍪 Cemilan</option>
                <option value="dessert" {{ old('kategori_menu') == 'dessert' ? 'selected' : '' }}>🍰 Dessert</option>
            </select>
            @error('kategori_menu')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Harga -->
        <div>
            <label for="harga" class="block text-sm font-semibold text-gray-700 mb-2">
                Harga <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-4 top-3.5 text-gray-500 font-semibold">Rp</span>
                <input type="number" id="harga" name="harga" value="{{ old('harga') }}" required min="0" step="500"
                    class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition"
                    placeholder="15000">
            </div>
            <p class="text-gray-500 text-xs mt-1">Masukkan harga dalam rupiah (tanpa titik atau koma)</p>
            @error('harga')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                Deskripsi
            </label>
            <textarea id="description" name="description" rows="4"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition"
                placeholder="Deskripsikan menu ini...">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Buttons -->
        <div class="flex gap-3 pt-4 border-t border-gray-100">
            <button type="submit" class="flex-1 bg-orange-600 hover:bg-orange-700 text-white py-3 rounded-lg font-semibold transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Create Menu
            </button>
            <a href="{{ route('admin.menu.index') }}" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold text-center transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.style.backgroundImage = `url('${e.target.result}')`;
                preview.style.backgroundSize = 'cover';
                preview.style.backgroundPosition = 'center';
                preview.innerHTML = '';
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
