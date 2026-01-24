@props(['menu'])

@php
    $isAvailable = $menu->is_available ?? true;
@endphp

<div class="menu-card rounded-2xl overflow-hidden group relative flex flex-col h-full bg-white hover:shadow-lg transition-all duration-300 {{ !$isAvailable ? 'opacity-90' : '' }}"
    x-data="{ 
        product: { 
            id: {{ $menu->id }}, 
            name: '{{ $menu->nama_menu }}', 
            price: {{ $menu->harga }}, 
            category: '{{ $menu->kategori_menu }}', 
            description: '{{ $menu->description }}', 
            image: '{{ $menu->image_url }}',
            available: {{ $isAvailable ? 'true' : 'false' }}
        }
    }"
    x-show="!loading && (currentCategory === 'all' || currentCategory === '{{ $menu->kategori_menu }}')">

    <!-- Clickable Area for Detail Modal (only if available) -->
    @if($isAvailable)
    <div @click="currentProduct = product; detailModal = true"
        class="absolute inset-0 z-10 cursor-pointer">
    </div>
    @endif

    <div class="aspect-square bg-[#EBEBEB] relative overflow-hidden flex-shrink-0 {{ !$isAvailable ? 'grayscale' : '' }}">
        <img src="{{ $menu->image_url }}" 
            alt="{{ $menu->nama_menu }}" 
            class="w-full h-full object-cover transition duration-300 transform {{ $isAvailable ? 'group-hover:scale-110' : '' }}" 
            loading="lazy" 
            onerror="this.src='{{ asset('images/default.jpg') }}'">
        
        <!-- Rating Badge (only show if available) -->
        @if($isAvailable)
        <div class="absolute top-3 left-3 bg-white px-2.5 py-1 rounded-lg shadow-sm">
            <span class="text-xs font-medium text-gray-700 flex items-center gap-1">
                <svg class="w-3.5 h-3.5 fill-current text-[#FAA533]" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                4.8
            </span>
        </div>
        @endif

        <!-- SOLD OUT Badge -->
        @unless($isAvailable)
        <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
            <div class="bg-red-600 text-white px-4 py-2 rounded-lg font-bold text-sm shadow-lg transform -rotate-12 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                </svg>
                HABIS
            </div>
        </div>
        @endunless
    </div>

    <div class="p-4 bg-white relative flex flex-col flex-grow">
        <h3 class="font-semibold text-base mb-1.5 line-clamp-1 {{ $isAvailable ? 'text-gray-900' : 'text-gray-400' }}">{{ $menu->nama_menu }}</h3>
        <p class="text-xs text-gray-500 mb-4 line-clamp-2 leading-relaxed min-h-[32px]">{{ $menu->description }}</p>

        <div class="mt-auto space-y-2.5">
            <div class="flex items-baseline gap-1">
                <span class="text-lg font-bold {{ $isAvailable ? 'text-gray-900' : 'text-gray-400' }}">Rp {{ number_format($menu->harga, 0, ',', '.') }}</span>
            </div>

            @if($isAvailable)
            <!-- Add to Cart Button -->
            <button @click.stop="addToCart(product.id)" 
                    class="relative z-20 w-full bg-gradient-to-r from-[#EF7722] to-[#FAA533] hover:from-[#d96a1e] hover:to-[#e6952c] text-white px-4 py-2 rounded-lg text-xs font-semibold shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-center gap-1.5 pointer-events-auto focus:outline-none focus:ring-2 focus:ring-[#EF7722] focus:ring-offset-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v12m6-6H6"></path>
                </svg>
                Tambah
            </button>
            @else
            <!-- Sold Out Button (Disabled) -->
            <button disabled 
                    class="relative z-20 w-full bg-gray-300 text-gray-500 px-4 py-2 rounded-lg text-xs font-semibold cursor-not-allowed flex items-center justify-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Stok Habis
            </button>
            @endif
        </div>
    </div>
</div>
