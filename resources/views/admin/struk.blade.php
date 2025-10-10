<div style="font-family: Arial, sans-serif; padding: 20px;">
    <h1 style="text-align: center;">Kedai Djanggo</h1>
    <p style="text-align: center;">Struk Pesanan #{{ $order->id }}</p>
    <hr>
    <p><strong>Nama:</strong> {{ $order->customer->name }}</p>
    <p><strong>Telepon:</strong> {{ $order->customer->phone }}</p>
    <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
    <p><strong>Tanggal:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
    <h3>Detail Pesanan</h3>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <th style="border: 1px solid #000; padding: 5px;">Menu</th>
            <th style="border: 1px solid #000; padding: 5px;">Jumlah</th>
            <th style="border: 1px solid #000; padding: 5px;">Subtotal</th>
        </tr>
        @foreach ($order->orderItems as $item)
            <tr>
                <td style="border: 1px solid #000; padding: 5px;">{{ $item->menu->nama_menu }}</td>
                <td style="border: 1px solid #000; padding: 5px;">{{ $item->jumlah }}</td>
                <td style="border: 1px solid #000; padding: 5px;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>
    <p><strong>Total:</strong> Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>
</div>