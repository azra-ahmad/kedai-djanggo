<!-- Floating Cart Pill - Fixed above Bottom Nav -->
<a href="{{ route('checkout') }}"
    x-show="cartCount > 0"
    x-cloak
    class="fixed bottom-[90px] left-4 right-4 z-50 bg-gradient-to-r from-[#EF7722] to-[#FAA533] text-white p-4 rounded-full shadow-xl flex items-center justify-between border border-[#EBEBEB] transition-transform active:scale-95"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="translate-y-20 opacity-0"
    x-transition:enter-end="translate-y-0 opacity-100"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="translate-y-0 opacity-100"
    x-transition:leave-end="translate-y-20 opacity-0">
    
    <div class="flex items-center gap-3">
        <div class="bg-white/20 p-2 rounded-full">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
        </div>
        <div class="flex flex-col items-start leading-none">
            <span class="text-xs font-medium text-white/90" x-text="cartCount + ' Menu'"></span>
            <span class="text-lg font-bold" x-text="'Rp ' + (total || 0).toLocaleString('id-ID')"></span>
        </div>
    </div>

    <div class="flex items-center gap-2 font-bold text-sm">
        Lanjut Bayar
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
        </svg>
    </div>
</a>
