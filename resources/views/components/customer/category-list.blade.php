@props(['categories'])

<!-- Category Filter List (Pill Layout) -->
<div class="px-5 md:px-8 py-3 md:py-4 overflow-hidden">
    <!-- Header mainly for mobile has been removed/hidden in this design request scope, 
         but keeping it minimal if needed. User didn't ask to remove, but the design implies a cleaner scroll bar. 
         Let's keep the scroll area clean. -->
    
    <div class="flex gap-4 overflow-x-auto scrollbar-hide -mx-5 px-5 md:mx-0 md:px-0">
        <!-- Static "ALL" Option -->
        <div @click="currentCategory = 'all'"
            class="group flex-shrink-0 px-5 py-2.5 cursor-pointer rounded-full transition-all duration-300 flex flex-row items-center gap-2 border border-transparent"
            :class="currentCategory === 'all' 
                ? '!bg-[#EF7722] !text-white shadow-lg shadow-[#EF7722]/30 transform scale-105' 
                : 'bg-[#EF7722]/10 text-[#EF7722] hover:bg-[#EF7722]/20'">
            
            <i class="ri-layout-grid-fill text-xl"></i>
            <span class="text-sm font-bold whitespace-nowrap">Semua</span>
        </div>

        <!-- Dynamic Categories -->
        @foreach($categories as $category)
            @php
                $catLower = strtolower($category);
                $iconClass = 'ri-layout-grid-line'; // Default

                if (str_contains($catLower, 'kopi')) {
                    $iconClass = 'ri-cup-line';
                } elseif (str_contains($catLower, 'teh') || str_contains($catLower, 'minum')) {
                    $iconClass = 'ri-goblet-line';
                } elseif (str_contains($catLower, 'makan') || str_contains($catLower, 'nasi') || str_contains($catLower, 'mie')) {
                    $iconClass = 'ri-restaurant-2-line';
                } elseif (str_contains($catLower, 'cemil') || str_contains($catLower, 'snack')) {
                    $iconClass = 'ri-cookie-2-line';
                } elseif (str_contains($catLower, 'dessert') || str_contains($catLower, 'manis')) {
                    $iconClass = 'ri-cake-3-line';
                } elseif (str_contains($catLower, 'promo') || str_contains($catLower, 'diskon')) {
                    $iconClass = 'ri-fire-line';
                }
            @endphp

            <div @click="currentCategory = '{{ $category }}'"
                class="group flex-shrink-0 px-5 py-2.5 cursor-pointer rounded-full transition-all duration-300 flex flex-row items-center gap-2 border border-transparent"
                :class="currentCategory === '{{ $category }}' 
                    ? '!bg-[#EF7722] !text-white shadow-lg shadow-[#EF7722]/30 transform scale-105' 
                    : 'bg-[#EF7722]/10 text-[#EF7722] hover:bg-[#EF7722]/20'">
                
                <i class="{{ $iconClass }} text-xl"></i>
                <span class="text-sm font-bold whitespace-nowrap">{{ ucfirst($category) }}</span>
            </div>
        @endforeach
    </div>
</div>
