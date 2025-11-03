<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $order->midtrans_order_id }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 20px; }
            .struk { box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto my-8">
        <!-- Print Button -->
        <button onclick="window.print()" class="no-print w-full bg-orange-600 hover:bg-orange-700 text-white py-3 rounded-lg font-semibold mb-4">
            🖨️ Print Struk
        </button>

        <!-- Struk -->
        <div class="struk bg-white shadow-lg" style="font-family: 'Courier New', monospace;">
            <!-- Header -->
            <div class="text-center border-b-2 border-dashed border-gray-300 pb-4 px-6 pt-6">
                <h1 class="text-2xl font-bold">KEDAI DJANGGO</h1>
                <p class="text-sm text-gray-600">Jl. Contoh No. 123, Kota</p>
                <p class="text-sm text-gray-600">Telp: 0812-3456-7890</p>
            </div>

            <!-- Order Info -->
            <div class="px-6 py-4 border-b-2 border-dashed border-gray-300">
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-semibold">Order ID:</span>
                    <span class="font-mono">{{ $order->midtrans_order_id }}</span>
                </div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-semibold">Tanggal:</span>
                    <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-semibold">Customer:</span>
                    <span>{{ $order->customer->name }}</span>
                </div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-semibold">Phone:</span>
                    <span>{{ $order->customer->phone }}</span>
                </div>
                @if($order->admin_id)
                <div class="flex justify-between text-sm">
                    <span class="font-semibold">Served by:</span>
                    <span>{{ $order->admin->name }}</span>
                </div>
                @endif
            </div>

            <!-- Items -->
            <div class="px-6 py-4 border-b-2 border-dashed border-gray-300">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Item</th>
                            <th class="text-center py-2">Qty</th>
                            <th class="text-right py-2">Harga</th>
                            <th class="text-right py-2">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                            <tr class="border-b">
                                <td class="py-2">{{ $item->menu->nama_menu }}</td>
                                <td class="text-center py-2">{{ $item->jumlah }}</td>
                                <td class="text-right py-2">{{ number_format($item->menu->harga, 0) }}</td>
                                <td class="text-right py-2 font-semibold">{{ number_format($item->subtotal, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Total -->
            <div class="px-6 py-4 border-b-2 border-dashed border-gray-300">
                <div class="flex justify-between text-lg font-bold">
                    <span>TOTAL:</span>
                    <span>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm mt-2">
                    <span>Status:</span>
                    <span class="font-semibold uppercase
                        @if($order->status == 'paid') text-green-600
                        @elseif($order->status == 'pending') text-yellow-600
                        @elseif($order->status == 'done') text-blue-600
                        @else text-red-600
                        @endif">
                        {{ $order->status }}
                    </span>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center px-6 py-4">
                <p class="text-sm text-gray-600 mb-2">Terima kasih atas kunjungan Anda!</p>
                <p class="text-xs text-gray-500">Struk ini adalah bukti pembayaran yang sah</p>
                <div class="mt-4 text-xs text-gray-400">
                    <p>Powered by Kedai Djanggo POS System</p>
                    <p>{{ now()->format('d M Y H:i:s') }}</p>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <button onclick="window.close()" class="no-print w-full bg-gray-200 hover:bg-gray-300 text-gray-700 py-3 rounded-lg font-semibold mt-4">
            ← Kembali
        </button>
    </div>

    <script>
        // Auto print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>