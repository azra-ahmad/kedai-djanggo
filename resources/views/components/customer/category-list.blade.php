@props(['categories'])

<!-- Category Filter List (Icon-Only Segmented Control) -->
<div class="px-5 md:px-8 overflow-hidden">
    <!-- The Track Container -->
    <div class="bg-gray-100 p-1 rounded-2xl overflow-x-auto scrollbar-hide flex items-center justify-between md:justify-start gap-1">
        <!-- Static "ALL" Option -->
        <div @click="currentCategory = 'all'"
            title="Semua"
            class="flex-1 md:flex-none w-14 h-12 flex items-center justify-center cursor-pointer transition-all duration-200 rounded-xl"
            :class="currentCategory === 'all' 
                ? 'bg-white text-[#EF7722] shadow-sm ring-1 ring-black/5' 
                : 'text-gray-400 hover:text-gray-600 hover:bg-white/50'">
            
            <i class="ri-layout-grid-fill text-2xl"></i>
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
                } elseif (str_contains($catLower, 'cemil') || str_contains($catLower, 'snack') || str_contains($catLower, 'dessert')) {
                    $iconClass = 'ri-cake-3-line';
                } elseif (str_contains($catLower, 'promo') || str_contains($catLower, 'diskon')) {
                    $iconClass = 'ri-fire-line';
                }
            @endphp

            <div @click="currentCategory = '{{ $category }}'"
                title="{{ ucfirst($category) }}"
                class="flex-1 md:flex-none w-14 h-12 flex items-center justify-center cursor-pointer transition-all duration-200 rounded-xl"
                :class="currentCategory === '{{ $category }}' 
                    ? 'bg-white text-[#EF7722] shadow-sm ring-1 ring-black/5' 
                    : 'text-gray-400 hover:text-gray-600 hover:bg-white/50'">
                
                <i class="{{ $iconClass }} text-2xl"></i>
            </div>
        @endforeach
    </div>
</div>
