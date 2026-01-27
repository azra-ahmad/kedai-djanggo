@props(['menu'])

@php
    $isAvailable = $menu->is_available ?? true;
@endphp

<div class="menu-card rounded-2xl overflow-hidden group relative flex flex-col h-full bg-white border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 {{ !$isAvailable ? 'opacity-80' : '' }}"
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
    x-show="!loading && (currentCategory === 'all' || currentCategory === '{{ $menu->kategori_menu }}')"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100">

    <!-- Clickable Area for Detail Modal (only if available) -->
    @if($isAvailable)
    <div @click="currentProduct = product; detailModal = true"
        class="absolute inset-0 z-10 cursor-pointer">
    </div>
    @endif

    <!-- Image Container (Square 1:1) -->
    <div class="aspect-square bg-gray-100 relative overflow-hidden flex-shrink-0 {{ !$isAvailable ? 'grayscale' : '' }}">
        <img src="{{ $menu->image_url }}" 
            alt="{{ $menu->nama_menu }}" 
            class="w-full h-full object-cover transition duration-500 transform {{ $isAvailable ? 'group-hover:scale-110' : '' }}" 
            loading="lazy" 
            onerror="this.src='{{ asset('images/default.jpg') }}'">
        
        <!-- Gradient Overlay on Hover -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

        <!-- Rating Badge (only show if available) -->
        @if($isAvailable)
        <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-lg shadow-sm border border-white/50 z-20">
            <span class="text-[10px] md:text-xs font-bold text-gray-800 flex items-center gap-1">
                <svg class="w-3 h-3 text-[#FAA533] fill-current" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                5.0
            </span>
        </div>
        @endif

        <!-- SOLD OUT Badge -->
        @unless($isAvailable)
        <div class="absolute inset-0 bg-black/50 flex items-center justify-center backdrop-blur-[2px] z-20">
            <div class="bg-red-600 text-white px-4 py-1.5 rounded-full font-bold text-xs md:text-sm shadow-xl transform -rotate-12 border-2 border-white flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                HABIS
            </div>
        </div>
        @endunless

        <!-- FAB Add Button (Inside Image Area for visual pop, or absolute positioned relative to card) -->
        <!-- Placing it overlapping the image/content boundary is a popular modern style, 
             but here I'll place it absolute bottom-right of the image or content. 
             Let's put it in the content area for better hit target separation, 
             OR absolute floating on the image bottom-right.
             User requested: "Floating Action Button style placed inside the card (e.g., bottom-right of the image area or bottom-right of the content area)."
             Let's try bottom-right of CONTENT area to keep text readable, 
             BUT for a "FAB" look, overlapping the image slightly looks very modern.
             Let's stick to a clean layout: Bottom right of the content area for accessibility and layout stability.
        -->
    </div>

    <!-- Content -->
    <div class="p-3 md:p-4 bg-white relative flex flex-col flex-grow">
        <!-- Category Pill -->
        <span class="text-[10px] uppercase font-bold text-[#EF7722] mb-1 tracking-wider">{{ $menu->kategori_menu }}</span>
        
        <h3 class="font-bold text-sm md:text-base mb-1.5 line-clamp-1 leading-tight {{ $isAvailable ? 'text-gray-900' : 'text-gray-400' }}" title="{{ $menu->nama_menu }}">
            {{ $menu->nama_menu }}
        </h3>
        
        <p class="text-xs text-gray-500 mb-8 line-clamp-2 min-h-[32px] md:min-h-[36px] leading-relaxed">
            {{ $menu->description }}
        </p>

        <div class="mt-auto flex items-center justify-between z-20">
            <span class="text-base md:text-lg font-extrabold {{ $isAvailable ? 'text-[#EF7722]' : 'text-gray-400' }}">
                Rp {{ number_format($menu->harga, 0, ',', '.') }}
            </span>

            @if($isAvailable)
            <!-- Modern FAB Add Button -->
            <button @click.stop="addToCart(product.id)" 
                    class="w-10 h-10 rounded-full bg-[#EF7722] hover:bg-[#d96a1e] text-white shadow-lg shadow-orange-500/30 flex items-center justify-center transition-all duration-300 transform hover:scale-110 active:scale-95 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#EF7722]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v12m6-6H6"></path>
                </svg>
            </button>
            @else
            <!-- Disabled Button -->
             <button disabled class="w-10 h-10 rounded-full bg-gray-100 text-gray-300 flex items-center justify-center cursor-not-allowed">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                </svg>
            </button>
            @endif
        </div>
    </div>
</div>
