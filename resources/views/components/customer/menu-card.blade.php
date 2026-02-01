@props(['menu'])

@php
    $isAvailable = $menu->is_available ?? true;
@endphp

<div class="menu-card rounded-2xl overflow-hidden group relative flex flex-col h-full bg-white border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 {{ !$isAvailable ? 'opacity-80' : '' }}"
    x-data="{ 
        product: { 
            id: {{ $menu->id }}, 
            name: '{{ addslashes($menu->nama_menu) }}', 
            price: {{ $menu->harga }}, 
            category: '{{ $menu->kategori_menu }}', 
            description: '{{ addslashes($menu->description ?? 'Tidak ada deskripsi.') }}', 
            image: '{{ $menu->image_url }}',
            available: {{ $isAvailable ? 'true' : 'false' }}
        }
    }">

    <!-- ✅ CLICKABLE OVERLAY: Dispatches event to open detail modal -->
    @if($isAvailable)
    <div @click="$dispatch('open-detail', product)"
        class="absolute inset-0 z-10 cursor-pointer">
    </div>
    @endif

    <!-- Product Image -->
    <div class="aspect-square bg-gray-100 relative overflow-hidden flex-shrink-0 {{ !$isAvailable ? 'grayscale' : '' }}">
        <img src="{{ $menu->image_url }}" 
             alt="{{ $menu->nama_menu }}" 
             class="w-full h-full object-cover transition duration-500 transform {{ $isAvailable ? 'group-hover:scale-110' : '' }}">
        
        <!-- Out of Stock Badge -->
        @unless($isAvailable)
        <div class="absolute inset-0 bg-black/50 flex items-center justify-center backdrop-blur-[2px] z-20">
            <div class="bg-red-600 text-white px-4 py-1.5 rounded-full font-bold text-xs shadow-xl transform -rotate-12 border-2 border-white">
                HABIS
            </div>
        </div>
        @endunless
    </div>

    <!-- Product Info -->
    <div class="p-3 md:p-4 bg-white relative flex flex-col flex-grow">
        <!-- Category Badge -->
        <span class="text-[10px] uppercase font-bold text-[#EF7722] mb-1 tracking-wider">
            {{ $menu->kategori_menu }}
        </span>
        
        <!-- Product Name -->
        <h3 class="font-bold text-sm md:text-base mb-1.5 line-clamp-1 leading-tight">
            {{ $menu->nama_menu }}
        </h3>
        
        <!-- Description -->
        <p class="text-xs text-gray-500 mb-4 line-clamp-2 min-h-[32px]">
            {{ $menu->description ?? 'Menu lezat dari Kedai Djanggo' }}
        </p>

        <!-- Price & Quick Add Button -->
        <div class="mt-auto flex items-center justify-between z-20">
            <span class="text-base font-extrabold text-[#EF7722]">
                Rp {{ number_format($menu->harga, 0, ',', '.') }}
            </span>
            
            @if($isAvailable)
            <!-- ✅ QUICK ADD: Bypasses modal for instant add with qty=1 and no note -->
            <button @click.stop="$dispatch('cart-quick-add', { id: product.id })" 
                    class="w-8 h-8 rounded-full bg-[#EF7722] text-white flex items-center justify-center shadow-md active:scale-90 transition-transform hover:bg-[#ff8f3f]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"></path>
                </svg>
            </button>
            @endif
        </div>
    </div>
</div>