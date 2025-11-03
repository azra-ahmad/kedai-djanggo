<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers - Kedai Djanggo</title>
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
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Customer Management</h1>
            <p class="text-gray-500">View all registered customers</p>
        </div>

        <!-- Customers Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left py-4 px-6 font-semibold text-gray-700">Customer</th>
                            <th class="text-left py-4 px-6 font-semibold text-gray-700">Phone</th>
                            <th class="text-left py-4 px-6 font-semibold text-gray-700">Total Orders</th>
                            <th class="text-left py-4 px-6 font-semibold text-gray-700">Total Spent</th>
                            <th class="text-left py-4 px-6 font-semibold text-gray-700">Registered</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($customers as $customer)
                            <tr class="hover:bg-gray-50">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center font-bold text-orange-600">
                                            {{ substr($customer->name, 0, 1) }}
                                        </div>
                                        <span class="font-semibold text-gray-900">{{ $customer->name }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-gray-700">{{ $customer->phone }}</td>
                                <td class="py-4 px-6">
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                                        {{ $customer->orders_count }} orders
                                    </span>
                                </td>
                                <td class="py-4 px-6 font-bold text-green-600">
                                    Rp {{ number_format($customer->total_spent ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-6 text-gray-500 text-sm">
                                    {{ $customer->created_at->format('d M Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t">
                {{ $customers->links() }}
            </div>
        </div>
    </div>
</body>
</html>