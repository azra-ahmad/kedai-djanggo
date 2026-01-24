@props(['customer'])

<!-- Fixed Header -->
<div class="header-gradient px-5 pt-6 pb-5 fixed top-0 left-0 right-0 z-40 shadow-lg">
    <div class="flex items-center justify-between mb-3">
        <button @click="logout()" class="flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-[#EF7722] px-3 py-2 rounded-xl hover:bg-[#EBEBEB]/50">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
        </button>
        <div class="text-center">
            <h1 class="text-2xl font-display font-bold gradient-text mb-0.5">Kedai Djanggo</h1>
            <p class="text-xs text-[#EF7722] font-semibold">{{ $customer ? $customer->name : 'Guest' }}</p>
        </div>
        <img src="{{ asset('images/logo-kedai-djanggo.jpg') }}" 
            alt="Logo" 
            class="w-12 h-12 rounded-full object-cover logo-border brightness-125 contrast-110 shadow-[0_0_10px_rgba(255,255,255,0.8)]">
    </div>
</div>
