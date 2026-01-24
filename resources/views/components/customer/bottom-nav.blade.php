<!-- Bottom Navigation - FIXED to viewport bottom -->
<div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-6 py-4 flex justify-around z-50 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
    <button @click="switchTab('home')" class="nav-item flex flex-col items-center gap-1.5" :class="{ 'active': currentTab === 'home' }">
        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        <span class="text-xs text-gray-500 font-semibold">Home</span>
    </button>
    <button @click="switchTab('orders')" class="nav-item flex flex-col items-center gap-1.5" :class="{ 'active': currentTab === 'orders' }">
        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
        </svg>
        <span class="text-xs text-gray-500 font-semibold">Pesanan</span>
    </button>
    <button @click="switchTab('about')" class="nav-item flex flex-col items-center gap-1.5" :class="{ 'active': currentTab === 'about' }">
        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="text-xs text-gray-500 font-semibold">Tentang</span>
    </button>
</div>
