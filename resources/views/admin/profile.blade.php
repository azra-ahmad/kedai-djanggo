@extends('layouts.admin')

@section('title', 'Pengaturan Akun')

@section('content')
<!-- Header -->
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-1">Pengaturan Akun Utama</h1>
    <p class="text-gray-500 text-sm">Kelola akses login perangkat (Device Credential)</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Account Info Card -->
    <div class="col-span-1">
        <div class="bg-white rounded-2xl border border-gray-100 p-6 text-center">
            <div class="w-24 h-24 bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl flex items-center justify-center text-white text-3xl font-bold mx-auto mb-4">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">{{ auth()->user()->name }}</h3>
            <p class="text-sm text-gray-500 mb-4">{{ auth()->user()->email }}</p>
            <span class="inline-block px-3 py-1 bg-orange-50 text-orange-600 rounded-full text-xs font-semibold">
                Main Admin
            </span>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="col-span-1 md:col-span-2">
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <h3 class="text-base font-bold text-gray-900 mb-4">Detail Pemilik</h3>
                    <div class="space-y-4">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Akun</label>
                            <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                            @error('name')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Login</label>
                            <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                            @error('email')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-6">
                    <h4 class="text-sm font-bold text-gray-900 mb-4">Ganti Password</h4>
                    
                    <div class="space-y-4">
                        <!-- New Password -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password Baru</label>
                            <input type="password" id="password" name="password"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                                placeholder="Kosongkan jika tidak ingin mengubah">
                            @error('password')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                                placeholder="Tulis ulang password baru">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white py-3 rounded-xl font-bold text-sm transition shadow-lg">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="px-6 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-xl font-bold text-sm flex items-center justify-center transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection