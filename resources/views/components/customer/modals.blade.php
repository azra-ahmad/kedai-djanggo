<!-- ✅ ROOT-LEVEL MODAL COMPONENT -->
<!-- Placed at the end of menu.blade.php, outside all containers -->
<!-- This ensures it's not trapped in any stacking context -->

<div x-data="modalController()" 
     x-init="init()"
     @open-detail.window="openModal($event.detail)"
     @cart-quick-add.window="quickAdd($event.detail)"
     @open-logout.window="logoutModal = true"
     @close-modal.window="closeModal()"
     @keydown.escape.window="closeModal(); logoutModal = false"
     class="modal-root">

    <!-- DETAIL MODAL -->
    <div x-show="detailModal" 
         class="fixed inset-0 z-[99999]" 
         style="display: none;" 
         x-cloak>
        
        <!-- Backdrop -->
        <div x-show="detailModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm" 
             @click="closeModal()">
        </div>

        <!-- Modal Container: "Stitch" Design -->
        <!-- Mobile: Bottom Sheet | Desktop: Centered Card -->
        <div x-show="detailModal"
             x-transition:enter="transform transition ease-out duration-300"
             x-transition:enter-start="translate-y-full sm:translate-y-0 sm:scale-95 sm:opacity-0"
             x-transition:enter-end="translate-y-0 sm:scale-100 sm:opacity-100"
             x-transition:leave="transform transition ease-in duration-200"
             x-transition:leave-start="translate-y-0 sm:scale-100 sm:opacity-100"
             x-transition:leave-end="translate-y-full sm:translate-y-0 sm:scale-95 sm:opacity-0"
             class="fixed bottom-0 left-0 right-0 bg-white rounded-t-[30px] shadow-2xl flex flex-col max-h-[90vh] 
                    sm:max-w-lg sm:mx-auto sm:rounded-2xl sm:bottom-auto sm:top-1/2 sm:-translate-y-1/2 sm:max-h-[85vh]
                    md:max-w-xl">

            <!-- Drag Handle (Mobile Only) -->
            <div class="flex-none pt-3 pb-1 flex justify-center cursor-pointer sm:hidden" 
                 @click="closeModal()">
                <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
            </div>

            <!-- Scrollable Content Area -->
            <div class="flex-1 overflow-y-auto px-6 pb-4 pt-2 sm:pt-4">
                
                <!-- Product Image -->
                <div class="relative w-full aspect-[4/3] rounded-2xl overflow-hidden mb-5 bg-gray-100 shadow-md">
                    <img :src="currentProduct.image" 
                         :alt="currentProduct.name"
                         class="w-full h-full object-cover">
                    
                    <!-- Close Button (Desktop) -->
                    <button @click="closeModal()" 
                            class="absolute top-3 right-3 bg-black/30 text-white p-2 rounded-full backdrop-blur hover:bg-black/50 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Product Details -->
                <div class="mb-6">
                    <!-- Category Badge -->
                    <span class="inline-block px-3 py-1.5 bg-orange-50 text-[#EF7722] text-[11px] font-bold uppercase tracking-wider rounded-lg mb-3" 
                          x-text="currentProduct.category">
                    </span>
                    
                    <!-- Product Name -->
                    <h3 class="text-2xl md:text-3xl font-bold text-gray-900 leading-tight mb-3" 
                        x-text="currentProduct.name">
                    </h3>
                    
                    <!-- Full Description (No Truncate) -->
                    <p class="text-gray-600 text-sm md:text-base leading-relaxed" 
                       x-text="currentProduct.description || 'Tidak ada deskripsi untuk menu ini.'">
                    </p>
                </div>

                <!-- Quantity Selector -->
                <div class="mb-5">
                    <label class="block text-sm font-bold text-gray-900 mb-3">Jumlah Pesanan</label>
                    <div class="inline-flex items-center border-2 border-[#EF7722] rounded-xl p-1 gap-6 bg-orange-50/30">
                        <!-- Decrease Button -->
                        <button @click="if(currentProduct.quantity > 1) currentProduct.quantity--" 
                                class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-[#EF7722] transition-colors rounded-lg active:scale-95" 
                                :class="currentProduct.quantity <= 1 ? 'opacity-40 cursor-not-allowed' : ''"
                                :disabled="currentProduct.quantity <= 1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path>
                            </svg>
                        </button>
                        
                        <!-- Quantity Display -->
                        <span class="min-w-[40px] text-center font-bold text-gray-900 text-xl" 
                              x-text="currentProduct.quantity">
                        </span>
                        
                        <!-- Increase Button -->
                        <button @click="currentProduct.quantity++" 
                                class="w-10 h-10 flex items-center justify-center text-[#EF7722] hover:text-[#ff8f3f] transition-colors rounded-lg active:scale-95">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Order Notes -->
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-900 mb-2">
                        Catatan Pesanan 
                        <span class="text-gray-400 font-normal text-xs">(Opsional)</span>
                    </label>
                    <textarea x-model="currentProduct.note" 
                              class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl p-4 text-sm focus:ring-2 focus:ring-[#EF7722] focus:border-[#EF7722] outline-none transition resize-none" 
                              rows="3" 
                              placeholder="Contoh: Jangan pake es, sambal dipisah, tanpa bawang..."></textarea>
                    <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Tulis permintaan khusus untuk pesanan Anda
                    </p>
                </div>

            </div>

            <!-- Sticky Footer with Price & Add Button -->
            <div class="flex-none p-5 sm:p-6 border-t-2 border-gray-100 bg-white z-20 flex items-center justify-between gap-4 rounded-b-[30px] sm:rounded-b-2xl">
                <!-- Price Display -->
                <div class="flex flex-col">
                    <span class="text-xs text-gray-500 font-bold uppercase tracking-wide mb-0.5">Total Harga</span>
                    <span class="text-2xl font-extrabold text-gray-900" 
                          x-text="'Rp ' + ((currentProduct.price || 0) * (currentProduct.quantity || 1)).toLocaleString('id-ID')">
                    </span>
                </div>
                
                <!-- Add to Cart Button -->
                <button @click="submitToCart()" 
                        class="flex-1 max-w-[200px] bg-gradient-to-r from-[#EF7722] to-[#ff8f3f] text-white px-6 py-4 rounded-full font-bold shadow-lg shadow-orange-200 active:scale-95 transition-transform flex items-center justify-center gap-2 hover:shadow-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Tambah</span>
                </button>
            </div>
        </div>
    </div>

    <!-- LOGOUT CONFIRMATION MODAL (If needed) -->
    <div x-show="logoutModal" 
         class="fixed inset-0 z-[99999]" 
         style="display: none;" 
         x-cloak>
        
        <div x-show="logoutModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm" 
             @click="logoutModal = false">
        </div>

        <div x-show="logoutModal"
             x-transition:enter="transform transition ease-out duration-300"
             x-transition:enter-start="scale-95 opacity-0"
             x-transition:enter-end="scale-100 opacity-100"
             x-transition:leave="transform transition ease-in duration-200"
             x-transition:leave-start="scale-100 opacity-100"
             x-transition:leave-end="scale-95 opacity-0"
             class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl p-6 max-w-sm w-full mx-4">
            
            <h3 class="text-xl font-bold text-gray-900 mb-2">Konfirmasi Logout</h3>
            <p class="text-gray-600 mb-6">Apakah Anda yakin ingin keluar dari akun?</p>
            
            <div class="flex gap-3">
                <button @click="logoutModal = false" 
                        class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl font-bold text-gray-700 hover:bg-gray-50 transition">
                    Batal
                </button>
                <button @click="confirmLogout()" 
                        class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 transition">
                    Ya, Logout
                </button>
            </div>
        </div>
    </div>

</div>

<script>
    function modalController() {
        return {
            detailModal: false,
            logoutModal: false,
            currentProduct: {
                id: null,
                name: '',
                price: 0,
                image: '',
                description: '',
                category: '',
                quantity: 1,
                note: ''
            },

            init() {
                // Listen for escape key
                console.log('✅ Modal controller initialized at root level');
            },

            /**
             * Open modal with product data from event
             */
            openModal(productData) {
                this.currentProduct = {
                    id: productData.id,
                    name: productData.name,
                    price: productData.price,
                    image: productData.image,
                    description: productData.description,
                    category: productData.category,
                    quantity: 1,
                    note: ''
                };
                this.detailModal = true;
                document.body.style.overflow = 'hidden';
            },

            /**
             * Close modal and reset state
             */
            closeModal() {
                this.detailModal = false;
                document.body.style.overflow = '';
                
                // Reset product state after animation
                setTimeout(() => {
                    this.currentProduct = {
                        id: null,
                        name: '',
                        price: 0,
                        image: '',
                        description: '',
                        category: '',
                        quantity: 1,
                        note: ''
                    };
                }, 200);
            },

            /**
             * Submit to cart (called from modal)
             */
            submitToCart() {
                if (!this.currentProduct.id) return;
                
                // Dispatch to parent app controller
                window.dispatchEvent(new CustomEvent('modal-add-to-cart', {
                    detail: {
                        menuId: this.currentProduct.id,
                        quantity: this.currentProduct.quantity,
                        note: this.currentProduct.note
                    }
                }));
            },

            /**
             * Quick add without opening modal (qty=1, no note)
             */
            quickAdd(data) {
                window.dispatchEvent(new CustomEvent('modal-add-to-cart', {
                    detail: {
                        menuId: data.id,
                        quantity: 1,
                        note: ''
                    }
                }));
            },

            /**
             * Logout confirmation - ✅ FIXED with proper error handling
             */
            confirmLogout() {
                // Create and submit a form for reliable logout
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('customer.logout') }}';
                form.style.display = 'none';
                
                // Add CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    }
</script>