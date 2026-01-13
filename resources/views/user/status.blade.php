<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kedai Djanggo - Status Pesanan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .status-pending {
            background: #FEF3C7;
            color: #92400E;
        }

        .status-paid {
            background: #D1FAE5;
            color: #065F46;
        }

        .status-failed {
            background: #FEE2E2;
            color: #991B1B;
        }

        .status-done {
            background: #DBEAFE;
            color: #1E40AF;
        }
    </style>
</head>

<body class="bg-gray-50">
    <div class="min-h-screen pb-20">
        <!-- Header -->
        <div class="bg-white p-4 sticky top-0 z-10 shadow-sm">
            <div class="flex items-center justify-between">
                <!-- Tombol Back & Judul -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('menu.index') }}" class="p-2 hover:bg-gray-100 rounded-full transition">
                        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <h1 class="text-xl font-bold text-gray-900">Struk Pesanan</h1>
                </div>

                <!-- Tombol Download (di kanan) -->
                <a href="{{ route('order.receipt', $order->id) }}"
                    target="_blank"
                    class="flex items-center gap-2  text-black px-4 py-2 rounded-lg text-xs font-bold hover-lift transition-all shadow-md">

                    <!-- Ikon Download -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>

                </a>
            </div>
        </div>

        <div class="p-4">
            <!-- Status Card -->
            <div class="bg-white rounded-2xl shadow-sm p-6 mb-4">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Order ID</p>
                        <p class="text-lg font-bold text-gray-900">{{ $order->midtrans_order_id }}</p>
                    </div>
                    <span class="status-badge status-{{ $order->status }}">
                        @if($order->status == 'pending')
                        Menunggu Pembayaran
                        @elseif($order->status == 'paid')
                        Dibayar
                        @elseif($order->status == 'failed')
                        Gagal
                        @elseif($order->status == 'done')
                        Selesai
                        @endif
                    </span>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Nama</span>
                        <span class="font-semibold text-gray-900">{{ $order->customer->name }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Telepon</span>
                        <span class="font-semibold text-gray-900">{{ $order->customer->phone }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Waktu Pesan</span>
                        <span class="font-semibold text-gray-900">{{ $order->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="mb-4">
                <h2 class="text-lg font-bold text-gray-900 mb-3">Detail Pesanan</h2>
                <div class="space-y-3">
                    @foreach ($order->orderItems as $item)
                    <div class="bg-white rounded-xl p-4 shadow-sm flex gap-4">
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-900 mb-1">{{ $item->menu->nama_menu }}</h4>
                            <p class="text-sm text-gray-500 mb-1">{{ $item->jumlah }}x Rp {{ number_format($item->menu->harga, 0, ',', '.') }}</p>
                            <p class="text-orange-600 font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Total Summary -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">Ringkasan Pembayaran</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="font-bold text-gray-900">Total Pembayaran</span>
                        <span class="font-bold text-xl text-orange-600">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fixed Bottom Button -->
        <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 space-y-2">
            @if($order->status == 'pending')
            <form action="{{ route('order.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pesanan?')">
                @csrf
                <button type="submit" class="block w-full bg-red-600 text-white py-3 rounded-xl font-bold shadow-lg text-center mb-2">
                    Batalkan Pesanan
                </button>
            </form>
            @endif
            <a href="{{ route('menu.index') }}" class="block w-full  bg-orange-600 text-white py-4 rounded-xl font-bold text-lg shadow-lg text-center">
                Kembali ke Menu
            </a>
        </div>
    </div>

     <script>
        // Auto refresh every 5 seconds if pending
        @if($order->status == 'pending')
            setInterval(() => {
                fetch('{{ route('order.status', $order->id) }}', {
                    headers: { 'Accept': 'application/json' }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Checking status:', data);
                    if (data.status === 'paid') {
                         console.log('Order paid, reloading to clear cart (handled by server)');
                         location.reload();
                    } else if (data.status !== 'pending') {
                         // For failed/expired, just reload to show status
                         location.reload();
                    }
                })
                .catch(error => console.error('Error checking status:', error));
            }, 5000);
        @endif
    </script>
</body>

</html>