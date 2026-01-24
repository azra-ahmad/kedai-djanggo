<!-- Category Filter List -->
<div class="px-5 py-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Kategori Menu</h2>
        <span class="text-xs bg-[#EBEBEB] text-[#EF7722] px-3 py-1 rounded-full font-semibold">Populer ✨</span>
    </div>
    <div class="flex gap-3 overflow-x-auto scrollbar-hide pb-2">
        <div @click="currentCategory = 'all'"
            class="category-card flex-shrink-0 p-4 w-28 cursor-pointer rounded-2xl text-center shadow-lg"
            :class="currentCategory === 'all' ? 'active' : ''">
            <div class="text-4xl mb-2 category-emoji">🍽️</div>
            <p class="text-xs font-bold" :class="currentCategory === 'all' ? 'text-white' : 'text-gray-800'">Semua</p>
        </div>
        <div @click="currentCategory = 'kopi'"
            class="category-card flex-shrink-0 p-4 w-28 cursor-pointer rounded-2xl text-center shadow-lg"
            :class="currentCategory === 'kopi' ? 'active' : ''">
            <div class="text-4xl mb-2 category-emoji">☕</div>
            <p class="text-xs font-bold" :class="currentCategory === 'kopi' ? 'text-white' : 'text-gray-800'">Kopi</p>
        </div>
        <div @click="currentCategory = 'minuman'"
            class="category-card flex-shrink-0 p-4 w-28 cursor-pointer rounded-2xl text-center shadow-lg"
            :class="currentCategory === 'minuman' ? 'active' : ''">
            <div class="text-4xl mb-2 category-emoji">🥤</div>
            <p class="text-xs font-bold" :class="currentCategory === 'minuman' ? 'text-white' : 'text-gray-800'">Minuman</p>
        </div>
        <div @click="currentCategory = 'makanan'"
            class="category-card flex-shrink-0 p-4 w-28 cursor-pointer rounded-2xl text-center shadow-lg"
            :class="currentCategory === 'makanan' ? 'active' : ''">
            <div class="text-4xl mb-2 category-emoji">🍜</div>
            <p class="text-xs font-bold" :class="currentCategory === 'makanan' ? 'text-white' : 'text-gray-800'">Makanan</p>
        </div>
        <div @click="currentCategory = 'cemilan'"
            class="category-card flex-shrink-0 p-4 w-28 cursor-pointer rounded-2xl text-center shadow-lg"
            :class="currentCategory === 'cemilan' ? 'active' : ''">
            <div class="text-4xl mb-2 category-emoji">🍟</div>
            <p class="text-xs font-bold" :class="currentCategory === 'cemilan' ? 'text-white' : 'text-gray-800'">Cemilan</p>
        </div>
    </div>
</div>
