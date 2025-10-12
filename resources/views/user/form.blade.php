<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kedai Djanggo - Mulai Pesan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-orange-50 to-red-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Logo & Welcome -->
        <div class="text-center mb-8">
            <img src="{{ asset('images/logo-kedai-djanggo.jpg') }}" alt="Logo Kedai Djanggo" class="w-32 h-32 mx-auto rounded-full shadow-lg mb-4 object-cover">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Kedai Djanggo</h1>
            <p class="text-gray-600">Silakan isi data Anda untuk mulai memesan</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <form action="{{ route('user.submitIdentity') }}" method="POST" class="space-y-4">
                @csrf
                
                <!-- Name Input -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}"
                        required 
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition"
                        placeholder="Masukkan nama Anda">
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone Input -->
                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Nomor Telepon</label>
                    <input 
                        type="tel" 
                        id="phone" 
                        name="phone" 
                        value="{{ old('phone') }}"
                        required 
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition"
                        placeholder="08xxxxxxxxxx">
                    @error('phone')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Box -->
                <div class="bg-orange-50 border border-orange-200 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-orange-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-gray-700">
                            Data Anda akan digunakan untuk konfirmasi pesanan dan pengiriman ke meja.
                        </p>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-gradient-to-r from-orange-600 to-red-600 text-white py-4 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transition transform hover:scale-[1.02]">
                    Mulai Pesan
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center text-gray-500 text-sm mt-6">
            © 2025 Kedai Djanggo. All rights reserved.
        </p>
    </div>
</body>
</html>