<!-- DETAIL MODAL -->
<div x-show="detailModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-md z-[70]" @click.self="detailModal = false" x-cloak>
    <div class="absolute inset-0 flex items-end">
        <div class="bg-white rounded-t-[40px] w-full max-h-[90vh] overflow-y-auto slide-up custom-scrollbar border-t-4 border-[#EF7722]">
            <div class="relative">
                <img :src="currentProduct.image" class="w-full h-80 object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                <button @click="detailModal = false" class="absolute top-4 left-4 bg-white/95 backdrop-blur-md rounded-full p-3 shadow-xl hover:bg-white">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <div class="absolute top-4 right-4 badge px-4 py-2 rounded-full">
                    <span class="text-sm text-[#EF7722] font-bold flex items-center gap-2">
                        <svg class="w-5 h-5 fill-current text-[#FAA533]" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        4.8
                    </span>
                </div>
            </div>
            <div class="p-6">
                <h2 class="text-3xl font-display font-bold text-gray-900 mb-3" x-text="currentProduct.name"></h2>
                <p class="text-gray-600 mb-6 leading-relaxed text-sm" x-text="currentProduct.description"></p>
                <div class="bg-gradient-to-r from-[#EBEBEB] via-white to-[#EBEBEB] rounded-3xl p-6 mb-6 border-2 border-[#EF7722] shadow-inner">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-600 mb-1 font-semibold">Harga</p>
                            <span class="text-4xl font-bold gradient-text" x-text="'Rp ' + (currentProduct.price || 0).toLocaleString('id-ID')"></span>
                        </div>
                        <div class="text-5xl">💰</div>
                    </div>
                </div>
                <button @click="addToCart(currentProduct.id)" class="w-full btn-primary py-5 rounded-2xl text-base font-bold flex items-center justify-center gap-3 text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah ke Keranjang
                </button>
            </div>
        </div>
    </div>
</div>

<!-- LOGOUT CONFIRMATION MODAL -->
<div x-show="logoutModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm z-[80] flex items-center justify-center p-4" x-cloak>
    <div class="bg-white rounded-3xl p-6 w-full max-w-sm shadow-2xl transform transition-all"
        @click.outside="logoutModal = false"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90">
        
        <div class="text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Konfirmasi Keluar</h3>
            <p class="text-gray-500 text-sm mb-6">Apakah Anda yakin ingin mengakhiri sesi ini?</p>
            
            <div class="flex gap-3">
                <button @click="logoutModal = false" class="flex-1 px-4 py-3 rounded-xl border-2 border-gray-100 font-bold text-gray-600 hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button @click="confirmLogout()" class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-red-500 to-red-600 text-white font-bold shadow-lg shadow-red-200 hover:shadow-red-300 transition-all transform hover:scale-105">
                    Ya, Keluar
                </button>
            </div>
        </div>
    </div>
</div>
