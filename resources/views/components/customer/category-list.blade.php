@props(['categories'])

<!-- Category Filter List -->
<div class="px-5 md:px-8 py-4 md:py-6 overflow-hidden">
    <!-- Header mainly for mobile -->
    <div class="flex items-center justify-between mb-4 md:hidden">
        <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Kategori</h2>
    </div>
    
    <div class="flex gap-3 overflow-x-auto scrollbar-hide pb-4 -mx-5 px-5 md:mx-0 md:px-0">
        <!-- Static "ALL" Option -->
        <div @click="currentCategory = 'all'"
            class="category-card group flex-shrink-0 p-4 w-28 md:w-32 cursor-pointer rounded-2xl text-center transition-all duration-200 border flex flex-col items-center justify-center h-full"
            :class="currentCategory === 'all' 
                ? '!bg-[#EF7722] !text-white shadow-md transform scale-105 border-transparent ring-2 ring-[#EF7722] ring-offset-2' 
                : 'bg-white text-gray-500 border-gray-100 hover:bg-gray-50 hover:text-gray-900'">
            
            <div class="mb-2 transition-transform duration-200 transform group-hover:scale-110">
                <i class="ri-layout-grid-fill text-3xl"></i>
            </div>
            
            <p class="text-xs md:text-sm font-bold truncate w-full">
               Semua
            </p>
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
                class="category-card group flex-shrink-0 p-4 w-28 md:w-32 cursor-pointer rounded-2xl text-center transition-all duration-200 border flex flex-col items-center justify-center h-full"
                :class="currentCategory === '{{ $category }}' 
                    ? '!bg-[#EF7722] !text-white shadow-md transform scale-105 border-transparent ring-2 ring-[#EF7722] ring-offset-2' 
                    : 'bg-white text-gray-500 border-gray-100 hover:bg-gray-50 hover:text-gray-900'">
                
                <div class="mb-2 transition-transform duration-200 transform group-hover:scale-110">
                    <i class="{{ $iconClass }} text-3xl"></i>
                </div>
                
                <p class="text-xs md:text-sm font-bold truncate w-full">
                   {{ ucfirst($category) }}
                </p>
            </div>
        @endforeach
    </div>
</div>
