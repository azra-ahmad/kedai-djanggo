@extends('layouts.admin')

@section('title', 'Kelola Karyawan')

@section('content')
<div class="space-y-6" x-data="employeeManager()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Karyawan</h1>
            <p class="text-gray-500 mt-1">Atur siapa saja yang bisa mengakses kasir</p>
        </div>
        <button @click="openAddModal()" 
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Tambah Karyawan
        </button>
    </div>

    <!-- Employee Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($employees as $employee)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
            <!-- Avatar Section -->
            <div class="p-6 text-center border-b border-gray-100">
                <div class="relative inline-block">
                    <img src="{{ $employee->avatar_url }}" 
                         alt="{{ $employee->name }}"
                         class="w-24 h-24 rounded-full object-cover mx-auto ring-4 ring-orange-100">
                    <!-- Status Indicator -->
                    <span class="absolute bottom-1 right-1 w-5 h-5 rounded-full border-2 border-white {{ $employee->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                </div>
                <h3 class="mt-4 font-bold text-gray-900 text-lg">{{ $employee->name }}</h3>
                <span class="inline-block mt-1 px-3 py-1 text-xs font-medium rounded-full {{ $employee->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                    {{ $employee->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>

            <!-- Actions -->
            <div class="p-4 flex items-center justify-center gap-2">
                <button @click="openEditModal({{ $employee->id }}, '{{ $employee->name }}', '{{ $employee->avatar_url }}')"
                        class="flex-1 px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </button>
                <form action="{{ route('admin.employees.toggle', $employee->id) }}" method="POST" class="flex-1">
                    @csrf
                    @method('PATCH')
                    <button type="submit" 
                            class="w-full px-3 py-2 text-sm font-medium rounded-lg transition {{ $employee->is_active ? 'text-amber-700 bg-amber-100 hover:bg-amber-200' : 'text-emerald-700 bg-emerald-100 hover:bg-emerald-200' }}">
                        {{ $employee->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Karyawan</h3>
                <p class="text-gray-500 mb-6">Tambahkan karyawan pertama untuk mengaktifkan sistem shift</p>
                <button @click="openAddModal()" 
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-semibold rounded-xl shadow-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Tambah Karyawan Pertama
                </button>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Add/Edit Modal -->
    <div x-show="showModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] w-screen h-screen flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
         x-cloak>
        <div @click.away="showModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900" x-text="isEditing ? 'Edit Karyawan' : 'Tambah Karyawan'"></h3>
                <button @click="showModal = false" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form :action="isEditing ? '{{ url('admin/employees') }}/' + editingId : '{{ route('admin.employees.store') }}'" 
                  method="POST" 
                  enctype="multipart/form-data"
                  class="p-6 space-y-5">
                @csrf
                <template x-if="isEditing">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <!-- Avatar Preview -->
                <div class="text-center">
                    <div class="relative inline-block">
                        <img :src="avatarPreview" 
                             alt="Preview"
                             class="w-28 h-28 rounded-full object-cover mx-auto ring-4 ring-orange-100">
                        <label class="absolute bottom-0 right-0 w-9 h-9 bg-orange-500 hover:bg-orange-600 rounded-full flex items-center justify-center cursor-pointer shadow-lg transition">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <input type="file" name="avatar" accept="image/*" class="hidden" @change="previewImage($event)">
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Klik ikon kamera untuk upload foto</p>
                </div>

                <!-- Name Input -->
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Karyawan</label>
                    <input type="text" 
                           name="name" 
                           x-model="formName"
                           required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition"
                           placeholder="Contoh: Budi Santoso">
                </div>

                <!-- PIN Input -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        PIN (4-6 digit)
                        <span x-show="isEditing" class="text-gray-400 font-normal">- Kosongkan jika tidak ingin mengubah</span>
                    </label>
                    <input type="password" 
                           name="pin" 
                           :required="!isEditing"
                           pattern="[0-9]{4,6}"
                           inputmode="numeric"
                           maxlength="6"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition font-mono text-xl tracking-[0.5em] text-center"
                           placeholder="••••">
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-2">
                    <!-- Delete Button (only for edit) -->
                    <template x-if="isEditing">
                        <button type="button" 
                                @click="confirmDelete()"
                                class="flex-none px-4 py-3 bg-red-100 hover:bg-red-200 text-red-700 font-bold rounded-xl transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </template>

                    <!-- Cancel Button -->
                    <button type="button" 
                            @click="showModal = false"
                            class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">
                        Batal
                    </button>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="flex-1 py-3 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200">
                        <span x-text="isEditing ? 'Simpan' : 'Tambah'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Form (Hidden) -->
    <form id="deleteForm" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>

@push('scripts')
<script>
function employeeManager() {
    return {
        showModal: false,
        isEditing: false,
        editingId: null,
        formName: '',
        avatarPreview: 'https://ui-avatars.com/api/?name=New&background=f97316&color=fff&size=128&bold=true',

        openAddModal() {
            this.isEditing = false;
            this.editingId = null;
            this.formName = '';
            this.avatarPreview = 'https://ui-avatars.com/api/?name=New&background=f97316&color=fff&size=128&bold=true';
            this.showModal = true;
        },

        openEditModal(id, name, avatarUrl) {
            this.isEditing = true;
            this.editingId = id;
            this.formName = name;
            this.avatarPreview = avatarUrl;
            this.showModal = true;
        },

        previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                this.avatarPreview = URL.createObjectURL(file);
            }
        },

        confirmDelete() {
            Swal.fire({
                title: 'Hapus Karyawan?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl px-6 py-2.5 font-bold',
                    cancelButton: 'rounded-xl px-6 py-2.5 font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('deleteForm');
                    form.action = '{{ url('admin/employees') }}/' + this.editingId;
                    form.submit();
                }
            });
        }
    }
}
</script>
@endpush
@endsection
