<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - Kedai Djanggo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    @include('admin.partials.sidebar')
    
    <div class="ml-64 p-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Orders Management</h1>
                <p class="text-gray-500">Manage all customer orders</p>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <div class="flex gap-2">
                <a href="{{ route('admin.orders', ['status' => 'all']) }}" 
                   class="px-4 py-2 rounded-lg font-semibold {{ $status == 'all' ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    All Orders
                </a>
                <a href="{{ route('admin.orders', ['status' => 'pending']) }}" 
                   class="px-4 py-2 rounded-lg font-semibold {{ $status == 'pending' ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Pending
                </a>
                <a href="{{ route('admin.orders', ['status' => 'paid']) }}" 
                   class="px-4 py-2 rounded-lg font-semibold {{ $status == 'paid' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Paid
                </a>
                <a href="{{ route('admin.orders', ['status' => 'done']) }}" 
                   class="px-4 py-2 rounded-lg font-semibold {{ $status == 'done' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Completed
                </a>
                <a href="{{ route('admin.orders', ['status' => 'failed']) }}" 
                   class="px-4 py-2 rounded-lg font-semibold {{ $status == 'failed' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Failed
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Orders Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left py-4 px-6 font-semibold text-gray-700">Order ID</th>
                            <th class="text-left py-4 px-6 font-semibold text-gray-700">Customer</th>
                            <th class="text-left py-4 px-6 font-semibold text-gray-700">Items</th>
                            <th class="text-left py-4 px-6 font-semibold text-gray-700">Total</th>
                            <th class="text-left py-4 px-6 font-semibold text-gray-700">Status</th>
                            <th class="text-left py-4 px-6 font-semibold text-gray-700">Date</th>
                            <th class="text-left py-4 px-6 font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="py-4 px-6">
                                    <span class="font-mono text-sm font-semibold text-gray-900">{{ $order->midtrans_order_id }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $order->customer->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $order->customer->phone }}</p>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $order->orderItems->count() }} items</p>
                                        <p class="text-xs text-gray-500">
                                            @foreach($order->orderItems->take(2) as $item)
                                                {{ $item->menu->nama_menu }}{{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                            @if($order->orderItems->count() > 2)
                                                ...
                                            @endif
                                        </p>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="font-bold text-gray-900">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status == 'paid') bg-green-100 text-green-800
                                        @elseif($order->status == 'done') bg-blue-100 text-blue-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <p class="text-sm text-gray-900">{{ $order->created_at->format('d M Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->created_at->format('H:i') }}</p>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex gap-2">
                                        @if($order->status == 'paid')
                                            <form action="{{ route('admin.complete', $order->id) }}" method="POST">
                                                @csrf
                                                <button class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-sm font-semibold">
                                                    Complete
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.struk', $order->id) }}" target="_blank" class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1 rounded-lg text-sm font-semibold">
                                            Struk
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center">
                                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <p class="text-gray-500 font-semibold">No orders found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</body>
</html>