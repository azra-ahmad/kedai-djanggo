@extends('layouts.admin')

@section('title', 'Menu Management')

@section('content')
<div x-data="menuManager()" x-init="$watch('viewMode', val => localStorage.setItem('menuViewMode', val))">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Menu Management 🍽️</h1>
            <p class="text-gray-500 text-sm">{{ $menus->total() }} menu items</p>
        </div>
        <a href="{{ route('admin.menu.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center justify-center gap-2 transition">
            <i class="ri-add-line text-xl"></i>
            Add Menu
        </a>
    </div>

    <!-- Sticky Toolbar (Responsive: Mobile 2-row, Desktop single-row) -->
    <div class="sticky top-0 z-20 bg-white/95 backdrop-blur-sm rounded-xl border border-gray-200 p-3 lg:p-4 mb-6 shadow-sm">
        <form method="GET" action="{{ route('admin.menu.index') }}" class="flex flex-col gap-2 lg:flex-row lg:items-center lg:gap-3">
            
            <!-- Search Input (Grows on desktop) -->
            <div class="relative flex-1 order-1">
                <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari menu..." 
                       class="w-full h-10 pl-9 pr-3 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
            </div>

            <!-- Filters Group (Category + Sort) -->
            <div class="flex gap-2 order-3 lg:order-2 lg:flex-none">
                <!-- Category Filter -->
                <select name="category" onchange="this.form.submit()" 
                        class="h-10 px-3 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-white flex-1 lg:flex-none lg:w-[150px]">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        @continue($cat === 'dessert')
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                            {{ ucfirst($cat) }}
                        </option>
                    @endforeach
                </select>

                <!-- Sort Dropdown -->
                <select name="sort" onchange="this.form.submit()" 
                        class="h-10 px-3 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-white flex-1 lg:flex-none lg:w-[130px]">
                    <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama A-Z</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Termurah</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Termahal</option>
                </select>
            </div>

            <!-- Actions Group (Reset + View Toggles) - Hidden by default on mobile, shown after search -->
            <div class="flex gap-2 order-2 lg:order-3 lg:flex-none absolute right-3 top-3 lg:relative lg:right-auto lg:top-auto">
                <!-- Reset Button (if filters active) -->
                @if(request()->hasAny(['search', 'category', 'sort']))
                <a href="{{ route('admin.menu.index') }}" class="h-10 w-10 flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg transition" title="Reset Filter">
                    <i class="ri-refresh-line"></i>
                </a>
                @endif

                <!-- View Mode Toggles -->
                <div class="flex border border-gray-200 rounded-lg overflow-hidden">
                    <button type="button" @click="viewMode = 'grid'" 
                            :class="viewMode === 'grid' ? 'bg-orange-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                            class="h-10 w-10 flex items-center justify-center transition">
                        <i class="ri-layout-grid-line"></i>
                    </button>
                    <button type="button" @click="viewMode = 'list'" 
                            :class="viewMode === 'list' ? 'bg-orange-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                            class="h-10 w-10 flex items-center justify-center transition border-l border-gray-200">
                        <i class="ri-list-check"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- GRID VIEW -->
    <div x-show="viewMode === 'grid'" x-transition class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($menus as $menu)
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-lg transition group {{ !$menu->is_available ? 'ring-2 ring-red-200' : '' }}">
                <div class="aspect-square bg-gray-100 relative overflow-hidden {{ !$menu->is_available ? 'grayscale opacity-60' : '' }}">
                    <img 
                        src="{{ $menu->image_url }}"
                        alt="{{ $menu->nama_menu }}"
                        class="w-full h-full object-cover group-hover:scale-110 transition duration-300"
                        onerror="this.src='{{ asset('images/default.jpg') }}'"
                    >
                    <!-- Category Badge -->
                    <div class="absolute top-2 right-2 px-3 py-1 rounded-full text-xs font-semibold
                        @if($menu->kategori_menu == 'kopi') bg-amber-100 text-amber-800
                        @elseif($menu->kategori_menu == 'minuman') bg-blue-100 text-blue-800
                        @elseif($menu->kategori_menu == 'makanan') bg-red-100 text-red-800
                        @elseif($menu->kategori_menu == 'cemilan') bg-yellow-100 text-yellow-800
                        @else bg-purple-100 text-purple-800
                        @endif shadow">
                        {{ ucfirst($menu->kategori_menu) }}
                    </div>
                    <!-- Sold Out Badge -->
                    @unless($menu->is_available)
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-lg transform -rotate-12">
                            HABIS
                        </span>
                    </div>
                    @endunless
                </div>
                <div class="p-4">
                    <div class="flex items-start justify-between mb-1">
                        <h3 class="font-bold text-gray-900 truncate flex-1 {{ !$menu->is_available ? 'text-gray-400' : '' }}">{{ $menu->nama_menu }}</h3>
                        <!-- Availability Toggle Switch -->
                        <label class="relative inline-flex items-center cursor-pointer ml-2 flex-shrink-0"
                               x-data="{ available: {{ $menu->is_available ? 'true' : 'false' }}, loading: false }"
                               @click.prevent="toggleAvailability({{ $menu->id }}, $el)">
                            <input type="checkbox" class="sr-only peer" :checked="available" :disabled="loading">
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-green-300 rounded-full peer 
                                        peer-checked:after:translate-x-full peer-checked:after:border-white 
                                        after:content-[''] after:absolute after:top-[2px] after:left-[2px] 
                                        after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all 
                                        peer-checked:bg-green-500"
                                 :class="{ 'opacity-50': loading }">
                            </div>
                        </label>
                    </div>
                    <p class="text-sm text-gray-500 mb-3 line-clamp-2 h-10">{{ $menu->description }}</p>
                    <div class="flex items-center justify-between">
                        <span class="text-orange-600 font-bold text-lg {{ !$menu->is_available ? 'text-gray-400' : '' }}">Rp {{ number_format($menu->harga, 0, ',', '.') }}</span>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.menu.edit', $menu->id) }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-lg transition">
                                <i class="ri-pencil-line"></i>
                            </a>
                            <button @click="openDeleteModal('Hapus {{ $menu->nama_menu }}?', 'Menu ini akan dihapus permanen.', {{ $menu->id }})" 
                                    class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-lg transition transform active:scale-95">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-gray-100 p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ri-search-2-line text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Tidak ada menu ditemukan</h3>
                <p class="text-gray-500 text-sm mb-4">Coba ubah filter atau kata kunci pencarian</p>
                <a href="{{ route('admin.menu.index') }}" class="inline-block bg-orange-600 hover:bg-orange-700 text-white px-6 py-2.5 rounded-lg font-semibold transition">
                    Reset Filter
                </a>
            </div>
        @endforelse
    </div>

    <!-- LIST VIEW -->
    <div x-show="viewMode === 'list'" x-transition x-cloak class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left py-4 px-4 text-xs font-bold text-gray-600 uppercase tracking-wider">Menu</th>
                        <th class="text-left py-4 px-4 text-xs font-bold text-gray-600 uppercase tracking-wider">Kategori</th>
                        <th class="text-right py-4 px-4 text-xs font-bold text-gray-600 uppercase tracking-wider">Harga</th>
                        <th class="text-center py-4 px-4 text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="text-center py-4 px-4 text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($menus as $menu)
                    <tr class="hover:bg-orange-50/50 transition {{ !$menu->is_available ? 'bg-red-50/30' : '' }}">
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $menu->image_url }}" alt="{{ $menu->nama_menu }}" 
                                     class="w-12 h-12 rounded-lg object-cover border border-gray-200 {{ !$menu->is_available ? 'grayscale opacity-60' : '' }}"
                                     onerror="this.src='{{ asset('images/default.jpg') }}'">
                                <div class="min-w-0">
                                    <h4 class="font-semibold text-gray-900 truncate {{ !$menu->is_available ? 'text-gray-400' : '' }}">{{ $menu->nama_menu }}</h4>
                                    <p class="text-xs text-gray-500 truncate max-w-[200px]">{{ $menu->description }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                @if($menu->kategori_menu == 'kopi') bg-amber-100 text-amber-800
                                @elseif($menu->kategori_menu == 'minuman') bg-blue-100 text-blue-800
                                @elseif($menu->kategori_menu == 'makanan') bg-red-100 text-red-800
                                @elseif($menu->kategori_menu == 'cemilan') bg-yellow-100 text-yellow-800
                                @else bg-purple-100 text-purple-800
                                @endif">
                                {{ ucfirst($menu->kategori_menu) }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right">
                            <span class="font-bold text-gray-900 {{ !$menu->is_available ? 'text-gray-400' : '' }}">
                                Rp {{ number_format($menu->harga, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex justify-center">
                                <label class="relative inline-flex items-center cursor-pointer"
                                       x-data="{ available: {{ $menu->is_available ? 'true' : 'false' }}, loading: false }"
                                       @click.prevent="toggleAvailability({{ $menu->id }}, $el)">
                                    <input type="checkbox" class="sr-only peer" :checked="available" :disabled="loading">
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-green-300 rounded-full peer 
                                                peer-checked:after:translate-x-full peer-checked:after:border-white 
                                                after:content-[''] after:absolute after:top-[2px] after:left-[2px] 
                                                after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all 
                                                peer-checked:bg-green-500"
                                         :class="{ 'opacity-50': loading }">
                                    </div>
                                </label>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('admin.menu.edit', $menu->id) }}" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-lg transition">
                                    <i class="ri-pencil-line"></i>
                                </a>
                                <button @click="openDeleteModal('Hapus {{ $menu->nama_menu }}?', 'Menu ini akan dihapus permanen.', {{ $menu->id }})" 
                                        class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-lg transition transform active:scale-95">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="ri-search-2-line text-2xl text-gray-400"></i>
                            </div>
                            <h3 class="text-base font-bold text-gray-900 mb-1">Tidak ada menu ditemukan</h3>
                            <p class="text-gray-500 text-sm">Coba ubah filter atau kata kunci pencarian</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($menus->hasPages())
    <div class="mt-6">
        {{ $menus->links() }}
    </div>
    @endif

    <!-- Hidden Delete Forms -->
    @foreach($menus as $menu)
    <form id="delete-form-{{ $menu->id }}" action="{{ route('admin.menu.destroy', $menu->id) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
    @endforeach

    <!-- Modal Delete Confirm -->
    <div x-show="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 flex items-center justify-center p-4" 
        @click.self="deleteModal = false" x-cloak>
        <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl border border-red-100 transform transition-all"
            x-transition>
            <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-rose-600 rounded-full mx-auto mb-4 flex items-center justify-center text-white shadow-lg">
                <i class="ri-delete-bin-line text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2" x-text="deleteTitle"></h3>
            <p class="text-sm text-gray-600 text-center mb-6" x-text="deleteMessage"></p>
            <div class="flex gap-3">
                <button @click="confirmDelete()" 
                        class="flex-1 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white py-3 rounded-xl font-bold shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2">
                    <i class="ri-delete-bin-line"></i>
                    Hapus
                </button>
                <button @click="deleteModal = false" 
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-xl font-bold transition">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function menuManager() {
        return {
            viewMode: localStorage.getItem('menuViewMode') || 'grid',
            deleteModal: false,
            deleteTitle: '',
            deleteMessage: '',
            deleteMenuId: null,

            openDeleteModal(title, message, menuId) {
                this.deleteTitle = title;
                this.deleteMessage = message;
                this.deleteMenuId = menuId;
                this.deleteModal = true;
            },

            confirmDelete() {
                if (this.deleteMenuId) {
                    document.getElementById('delete-form-' + this.deleteMenuId).submit();
                }
                this.deleteModal = false;
            },

            toggleAvailability(menuId, el) {
                const alpine = Alpine.$data(el);
                alpine.loading = true;
                
                fetch(`/admin/menu/${menuId}/toggle`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    alpine.available = data.is_available;
                    alpine.loading = false;
                    // Reload to update UI fully
                    window.location.reload();
                })
                .catch(() => {
                    alpine.loading = false;
                });
            }
        }
    }
</script>
@endpush