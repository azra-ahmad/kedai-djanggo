@extends('layouts.app')
@section('content')
    <div class="p-4">
        <h1 class="text-2xl font-bold mb-4">Admin Dashboard</h1>
        <p>Selamat datang, {{ Auth::user()->email }}</p>
        <h2 class="text-lg font-semibold mt-4">Daftar Pesanan</h2>
        @foreach ($orders as $order)
            <div class="border rounded p-4 mb-4">
                <p>Pesanan #{{ $order->id }} - {{ $order->customer->name }}</p>
                <p>Status: {{ ucfirst($order->status) }}</p>
                <p>Total: Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>
                @if ($order->status == 'pending' || $order->status == 'paid')
                    <form action="{{ route('admin.assign', $order->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded">Ambil Pesanan</button>
                    </form>
                @endif
                @if ($order->status == 'paid' && $order->admin_id == Auth::user()->id)
                    <form action="{{ route('admin.complete', $order->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-500 text-white px-2 py-1 rounded">Selesaikan</button>
                    </form>
                @endif
                @if ($order->status == 'done')
                    <a href="{{ route('admin.struk', $order->id) }}" class="bg-gray-500 text-white px-2 py-1 rounded">Unduh Struk</a>
                @endif
            </div>
        @endforeach
    </div>
@endsection