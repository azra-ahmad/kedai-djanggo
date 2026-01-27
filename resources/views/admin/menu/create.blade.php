@extends('layouts.admin')

@section('title', 'Add Menu')

@section('content')
<div x-data="menuForm()">
    <!-- Sticky Header -->
    <div class="sticky top-0 z-20 bg-gray-50/95 backdrop-blur-sm -mx-4 lg:-mx-6 px-4 lg:px-6 py-4 mb-6 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.menu.index') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-white rounded-lg transition">
                    <i class="ri-arrow-left-line text-xl"></i>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Add New Menu</h1>
                    <p class="text-gray-500 text-sm">Create a new menu item</p>
                </div>
            </div>
            <div class="flex gap-3">
                <!-- Status Toggle -->
                <div class="flex items-center gap-3">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_available" value="0" form="menuForm">
                        <input type="checkbox" name="is_available" value="1" x-model="isAvailable" class="sr-only peer" form="menuForm" checked>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-orange-300 rounded-full peer 
                                    peer-checked:after:translate-x-full peer-checked:after:border-white 
                                    after:content-[''] after:absolute after:top-[2px] after:left-[2px] 
                                    after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all 
                                    peer-checked:bg-green-500">
                        </div>
                    </label>
                    <span class="text-sm font-medium transition-colors" 
                          :class="isAvailable ? 'text-green-600' : 'text-gray-500'" 
                          x-text="isAvailable ? 'Available' : 'Unavailable'">
                    </span>
                </div>
                
                <div class="h-8 w-px bg-gray-300"></div>

                <a href="{{ route('admin.menu.index') }}" class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg font-semibold text-sm hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" form="menuForm" class="px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-semibold text-sm transition flex items-center gap-2">
                    <i class="ri-save-line text-lg"></i>
                    Create Menu
                </button>
            </div>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
        <div class="flex items-start gap-3">
            <i class="ri-error-warning-line text-xl text-red-600 mt-0.5"></i>
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
    <form id="menuForm" action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
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
                        <input type="text" id="nama_menu" name="nama_menu" value="{{ old('nama_menu') }}" required
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
                            placeholder="Deskripsikan menu ini untuk menarik pelanggan...">{{ old('description') }}</textarea>
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
                                <input type="number" id="harga" name="harga" value="{{ old('harga') }}" required min="0" step="500"
                                    class="w-full h-[46px] pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-sm"
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
                                class="w-full h-[46px] px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-sm bg-white appearance-none">
                                <option value="">-- Pilih Kategori --</option>
                                <option value="kopi" {{ old('kategori_menu') == 'kopi' ? 'selected' : '' }}>Kopi</option>
                                <option value="minuman" {{ old('kategori_menu') == 'minuman' ? 'selected' : '' }}>Minuman</option>
                                <option value="makanan" {{ old('kategori_menu') == 'makanan' ? 'selected' : '' }}>Makanan</option>
                                <option value="cemilan" {{ old('kategori_menu') == 'cemilan' ? 'selected' : '' }}>Cemilan</option>
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
                        <input type="file" id="gambar" name="gambar" accept="image/*" required
                            class="hidden" @change="handleImageSelect($event)">
                        <label for="gambar" class="cursor-pointer block flex-1">
                            <!-- Flexible Height Preview Area -->
                            <div class="h-full min-h-[200px] rounded-lg border-2 border-dashed border-gray-300 hover:border-orange-400 transition overflow-hidden relative bg-gray-50"
                                 :class="{ 'border-solid border-orange-500': imagePreview }">
                                
                                <!-- No Image State -->
                                <div x-show="!imagePreview" class="absolute inset-0 flex flex-col items-center justify-center p-4">
                                    <i class="ri-image-add-line text-4xl text-gray-400 mb-2"></i>
                                    <p class="text-gray-600 text-sm font-medium text-center">Upload gambar</p>
                                    <p class="text-gray-400 text-xs mt-1">Max 2MB</p>
                                </div>
                                
                                <!-- Image Preview State -->
                                <template x-if="imagePreview">
                                    <div class="relative w-full h-full">
                                        <img :src="imagePreview" alt="Preview" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 hover:opacity-100 transition flex items-center justify-center">
                                            <span class="text-white text-sm font-medium">Ganti Gambar</span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </label>
                        @error('gambar')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                        @enderror
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
            imagePreview: null,
            isAvailable: true,
            
            handleImageSelect(event) {
                const file = event.target.files[0];
                if (file) {
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
