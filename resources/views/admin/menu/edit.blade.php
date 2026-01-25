@extends('layouts.admin')

@section('title', 'Edit Menu')

@section('content')
<div x-data="menuForm()">
    <!-- Hidden Delete Form -->
    <form id="deleteForm" action="{{ route('admin.menu.destroy', $menu->id) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <!-- Sticky Header -->
    <div class="sticky top-0 z-20 bg-gray-50/95 backdrop-blur-sm -mx-4 lg:-mx-6 px-4 lg:px-6 py-4 mb-6 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.menu.index') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-white rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Edit Menu</h1>
                    <p class="text-gray-500 text-sm">{{ $menu->nama_menu }}</p>
                </div>
            </div>
            <div class="flex gap-3">
                <!-- Delete Button -->
                <button type="button" 
                        onclick="if(confirm('Apakah Anda yakin ingin menghapus menu ini?')) document.getElementById('deleteForm').submit();"
                        class="px-4 py-2.5 bg-white border border-red-300 text-red-600 rounded-lg font-semibold text-sm hover:bg-red-50 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    <span class="hidden sm:inline">Delete</span>
                </button>
                <!-- Cancel Button -->
                <a href="{{ route('admin.menu.index') }}" class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg font-semibold text-sm hover:bg-gray-50 transition">
                    Cancel
                </a>
                <!-- Save Button -->
                <button type="submit" form="menuForm" class="px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-semibold text-sm transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Save
                </button>
            </div>
        </div>
    </div>

    <!-- Error Messages -->
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
    <form id="menuForm" action="{{ route('admin.menu.update', $menu->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">
            <!-- Left Column: Main Details (2/3 width) -->
            <div class="lg:col-span-2 flex flex-col gap-6">
                <!-- Basic Info Card -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Basic Information</h2>
                    
                    <!-- Nama Menu -->
                    <div class="mb-5">
                        <label for="nama_menu" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Menu <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama_menu" name="nama_menu" value="{{ old('nama_menu', $menu->nama_menu) }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-sm"
                            placeholder="Contoh: Es Kopi Susu Gula Aren">
                        @error('nama_menu')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi
                        </label>
                        <textarea id="description" name="description" rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-sm resize-none"
                            placeholder="Deskripsikan menu ini untuk menarik pelanggan...">{{ old('description', $menu->description) }}</textarea>
                        @error('description')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Pricing & Category Card (2-col grid) -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Pricing & Category</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Harga -->
                        <div>
                            <label for="harga" class="block text-sm font-medium text-gray-700 mb-2">
                                Harga <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-sm">Rp</span>
                                <input type="number" id="harga" name="harga" value="{{ old('harga', $menu->harga) }}" required min="0" step="500"
                                    class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-sm"
                                    placeholder="15000">
                            </div>
                            <p class="text-gray-400 text-xs mt-1.5">Harga dalam rupiah</p>
                            @error('harga')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kategori -->
                        <div>
                            <label for="kategori_menu" class="block text-sm font-medium text-gray-700 mb-2">
                                Kategori <span class="text-red-500">*</span>
                            </label>
                            <select id="kategori_menu" name="kategori_menu" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-sm bg-white">
                                <option value="">-- Pilih Kategori --</option>
                                <option value="kopi" {{ old('kategori_menu', $menu->kategori_menu) == 'kopi' ? 'selected' : '' }}>☕ Kopi</option>
                                <option value="minuman" {{ old('kategori_menu', $menu->kategori_menu) == 'minuman' ? 'selected' : '' }}>🥤 Minuman</option>
                                <option value="makanan" {{ old('kategori_menu', $menu->kategori_menu) == 'makanan' ? 'selected' : '' }}>🍽️ Makanan</option>
                                <option value="cemilan" {{ old('kategori_menu', $menu->kategori_menu) == 'cemilan' ? 'selected' : '' }}>🍪 Cemilan</option>
                                <option value="dessert" {{ old('kategori_menu', $menu->kategori_menu) == 'dessert' ? 'selected' : '' }}>🍰 Dessert</option>
                            </select>
                            <p class="text-gray-400 text-xs mt-1.5">Pilih kategori menu</p>
                            @error('kategori_menu')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Media & Status (1/3 width) -->
            <div class="lg:col-span-1 flex flex-col gap-6">
                <!-- Image Upload Card (flex-1 to stretch) -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm flex-1 flex flex-col">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Media</h2>
                    
                    <div class="flex-1 flex flex-col">
                        <input type="file" id="gambar" name="gambar" accept="image/*"
                            class="hidden" @change="handleImageSelect($event)">
                        <label for="gambar" class="cursor-pointer block flex-1">
                            <!-- Flexible Height Preview Area -->
                            <div class="h-full min-h-[200px] rounded-lg border-2 border-gray-200 hover:border-orange-400 transition overflow-hidden relative"
                                 :class="{ 'border-orange-500': newImageSelected }">
                                
                                <!-- Current/New Image Preview -->
                                <img :src="imagePreview" alt="{{ $menu->nama_menu }}" class="w-full h-full object-cover">
                                
                                <!-- Overlay on Hover -->
                                <div class="absolute inset-0 bg-black/40 opacity-0 hover:opacity-100 transition flex flex-col items-center justify-center gap-1">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-white text-xs font-medium">Ganti Gambar</span>
                                </div>
                                
                                <!-- Badge -->
                                <div class="absolute top-2 right-2 px-2 py-0.5 rounded-full text-xs font-semibold shadow"
                                     :class="newImageSelected ? 'bg-green-500 text-white' : 'bg-white/90 text-gray-700'">
                                    <span x-text="newImageSelected ? 'New' : 'Current'"></span>
                                </div>
                            </div>
                        </label>
                        @error('gambar')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Availability Status Card -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Status</h2>
                    
                    <div class="flex items-center justify-between p-4 rounded-lg border"
                         :class="isAvailable ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'">
                        <div>
                            <p class="text-sm font-medium" :class="isAvailable ? 'text-green-800' : 'text-red-800'" x-text="isAvailable ? 'Tersedia' : 'Habis'"></p>
                            <p class="text-xs" :class="isAvailable ? 'text-green-600' : 'text-red-600'" x-text="isAvailable ? 'Menu dapat dipesan' : 'Menu tidak ditampilkan'"></p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_available" value="0">
                            <input type="checkbox" name="is_available" value="1" x-model="isAvailable" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-green-300 rounded-full peer 
                                        peer-checked:after:translate-x-full peer-checked:after:border-white 
                                        after:content-[''] after:absolute after:top-[2px] after:left-[2px] 
                                        after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all 
                                        peer-checked:bg-green-500">
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function menuForm() {
        return {
            imagePreview: '{{ $menu->image_url }}',
            newImageSelected: false,
            isAvailable: {{ $menu->is_available ? 'true' : 'false' }},
            
            handleImageSelect(event) {
                const file = event.target.files[0];
                if (file) {
                    this.newImageSelected = true;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imagePreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            }
        }
    }
</script>
@endpush