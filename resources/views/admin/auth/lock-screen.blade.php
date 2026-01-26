<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pilih Kasir - Kedai Djanggo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .avatar-ring {
            transition: all 0.3s ease;
        }
        .avatar-ring:hover {
            transform: scale(1.08);
            box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.3);
        }
        .avatar-ring.selected {
            box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.6);
            transform: scale(1.05);
        }
        .pin-input:focus {
            animation: pulse-input 1s infinite;
        }
        @keyframes pulse-input {
            0%, 100% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.4); }
            50% { box-shadow: 0 0 0 8px rgba(249, 115, 22, 0); }
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-10px); }
            40%, 80% { transform: translateX(10px); }
        }
        .shake {
            animation: shake 0.5s ease-in-out;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 min-h-screen font-sans" x-data="lockScreen()">
    
    <!-- Main Container -->
    <!-- Main Container -->
    <div class="fixed top-0 left-0 w-full h-full z-[99999] bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 flex flex-col items-center justify-center p-6 overflow-hidden">
        
        <!-- Logo & Title -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl shadow-2xl mb-4">
                <img src="{{ asset('images/logo-kedai-djanggo.jpg') }}" 
                     alt="Logo" 
                     class="w-16 h-16 rounded-xl object-cover">
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Kedai Djanggo</h1>
            <p class="text-gray-400">Pilih profil untuk memulai shift</p>
        </div>

        <!-- Clock -->
        <div class="text-center mb-8">
            <p class="text-5xl font-light text-white tracking-wider" x-text="currentTime"></p>
            <p class="text-gray-500 mt-1" x-text="currentDate"></p>
        </div>

        @if($employees->count() > 0)
        <!-- Employee Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6 max-w-4xl">
            @foreach($employees as $employee)
            <button @click="selectEmployee({{ $employee->id }}, '{{ $employee->name }}', '{{ $employee->avatar_url }}')"
                    class="flex flex-col items-center group focus:outline-none"
                    :class="{ 'opacity-50': selectedId && selectedId !== {{ $employee->id }} }">
                <div class="avatar-ring w-24 h-24 rounded-full overflow-hidden border-4 border-white/20 bg-gray-700"
                     :class="{ 'selected': selectedId === {{ $employee->id }} }">
                    <img src="{{ $employee->avatar_url }}" 
                         alt="{{ $employee->name }}"
                         class="w-full h-full object-cover">
                </div>
                <span class="mt-3 text-white font-medium text-sm group-hover:text-orange-400 transition">
                    {{ $employee->name }}
                </span>
            </button>
            @endforeach
        </div>
        @else
        <!-- No Employees State -->
        <div class="text-center bg-white/10 backdrop-blur rounded-2xl p-10 max-w-md">
            <div class="w-20 h-20 bg-orange-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Belum Ada Karyawan</h3>
            <p class="text-gray-400 mb-6">Silakan tambahkan karyawan terlebih dahulu melalui menu Kelola Karyawan</p>
            <a href="{{ route('admin.employees.index') }}" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-semibold rounded-xl shadow-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Karyawan
            </a>
        </div>
        @endif

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="fixed top-6 right-6 bg-emerald-500 text-white px-6 py-4 rounded-xl shadow-lg flex items-center gap-3"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
        @endif
    </div>

    <!-- PIN Modal -->
    <div x-show="showPinModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
         x-cloak>
        
        <div x-show="showPinModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90"
             class="w-full max-w-sm h-auto bg-gray-800 rounded-2xl p-6 shadow-2xl border border-white/10"
             :class="{ 'shake': hasError }">
            
            <!-- Header with Avatar -->
            <div class="text-center mb-6">
                <img :src="selectedAvatar" 
                     :alt="selectedName"
                     class="w-20 h-20 rounded-full object-cover mx-auto ring-4 ring-orange-500/50 shadow-xl mb-4">
                <h3 class="text-xl font-bold text-white" x-text="selectedName"></h3>
                <p class="text-gray-400 text-sm mt-1">Masukkan PIN untuk melanjutkan</p>
            </div>

            <!-- PIN Form -->
            <form @submit.prevent="unlock" class="space-y-6">
                
                <!-- PIN Display -->
                <div class="flex justify-center gap-3">
                    <template x-for="i in 6" :key="i">
                        <div class="w-12 h-14 rounded-xl border-2 flex items-center justify-center text-2xl font-bold text-white transition-colors duration-200"
                             :class="pin.length >= i ? 'border-orange-500 bg-orange-500/20' : 'border-gray-600 bg-gray-700'">
                            <span x-show="pin.length >= i">•</span>
                        </div>
                    </template>
                </div>

                <!-- Hidden PIN Input -->
                <input type="password" 
                       x-model="pin"
                       x-ref="pinInput"
                       maxlength="6"
                       inputmode="numeric"
                       pattern="[0-9]*"
                       class="sr-only"
                       @keyup.escape="closePinModal()">

                <!-- Error Message -->
                <div x-show="hasError" class="text-center text-red-500 text-sm font-semibold flex items-center justify-center gap-2 animate-bounce">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span x-text="errorMessage"></span>
                </div>

                <!-- Numpad -->
                <div class="grid grid-cols-3 gap-3">
                    <!-- Numbers 1-9 -->
                    <template x-for="num in 9" :key="num">
                        <button type="button"
                                @click="handleNumpad(num)"
                                class="h-14 rounded-xl font-bold text-xl bg-gray-700 text-white hover:bg-gray-600 transition active:scale-95 shadow-sm">
                            <span x-text="num"></span>
                        </button>
                    </template>

                    <!-- Bottom Row: Empty - 0 - Delete -->
                    <div class="h-14 rounded-xl"></div>
                    
                    <button type="button"
                            @click="handleNumpad(0)"
                            class="h-14 rounded-xl font-bold text-xl bg-gray-700 text-white hover:bg-gray-600 transition active:scale-95 shadow-sm">
                        0
                    </button>

                    <button type="button"
                            @click="handleNumpad('del')"
                            class="h-14 rounded-xl font-bold text-xl bg-red-500/20 text-red-400 hover:bg-red-500/30 transition active:scale-95 shadow-sm flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z"/>
                        </svg>
                    </button>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-4">
                    <button type="submit" 
                            :disabled="pin.length < 4 || isLoading"
                            class="w-full py-4 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 disabled:from-gray-700 disabled:to-gray-700 disabled:text-gray-500 disabled:cursor-not-allowed text-white text-lg font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 active:scale-[0.98] flex items-center justify-center">
                        <span x-show="!isLoading">Masuk</span>
                        <svg x-show="isLoading" class="animate-spin -ml-1 mr-3 h-6 w-6 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>

                    <button type="button" 
                            @click="closePinModal()"
                            :disabled="isLoading"
                            class="w-full py-3 bg-gray-700 hover:bg-gray-600 text-gray-300 font-medium rounded-xl transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function lockScreen() {
        return {
            currentTime: '',
            currentDate: '',
            showPinModal: false,
            isLoading: false,
            selectedId: null,
            selectedName: '',
            selectedAvatar: '',
            pin: '',
            hasError: false,
            errorMessage: '',

            init() {
                this.updateClock();
                setInterval(() => this.updateClock(), 1000);
            },

            updateClock() {
                const now = new Date();
                this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                this.currentDate = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            },

            selectEmployee(id, name, avatar) {
                this.selectedId = id;
                this.selectedName = name;
                this.selectedAvatar = avatar;
                this.pin = '';
                this.hasError = false;
                this.errorMessage = '';
                this.showPinModal = true;
                this.isLoading = false;
                
                this.$nextTick(() => {
                    this.$refs.pinInput.focus();
                });
            },

            closePinModal() {
                this.showPinModal = false;
                this.selectedId = null;
                this.pin = '';
                this.hasError = false;
            },

            handleNumpad(num) {
                if (this.isLoading) return;
                
                if (num === 'del') {
                    this.pin = this.pin.slice(0, -1);
                } else if (this.pin.length < 6) {
                    this.pin += num;
                }
                this.hasError = false; // Clear error on type
            },

            async unlock() {
                if (this.pin.length < 4 || this.isLoading) return;

                this.isLoading = true;
                this.hasError = false;

                try {
                    const response = await fetch('{{ route("admin.unlock") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            employee_id: this.selectedId,
                            pin: this.pin
                        })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        // Success! Force hard redirect to dashboard to prevent loop
                        window.location.href = "{{ route('admin.dashboard') }}";
                    } else {
                        // Error (401 etc)
                        throw new Error(data.message || 'PIN salah');
                    }
                } catch (error) {
                    this.hasError = true;
                    this.errorMessage = error.message;
                    this.pin = ''; // Clear PIN
                    this.isLoading = false;
                    
                    // Re-focus input
                    this.$nextTick(() => {
                        this.$refs.pinInput.focus();
                    });

                    // Remove shake class after animation play
                    setTimeout(() => {
                        this.hasError = false;
                    }, 500);
                }
            }
        }
    }
    </script>
</body>
</html>
