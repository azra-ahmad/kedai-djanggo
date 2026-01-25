<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $order->midtrans_order_id }}</title>
    <style>
        /* ================================================
           THERMAL RECEIPT - Light/Classic Style
           Optimized for 58mm/80mm thermal paper
           ================================================ */
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.5;
            color: #000;
            background: #f3f4f6; /* Light gray */
            min-height: 100vh;
            padding: 24px 16px 100px 16px;
        }

        /* Receipt Container */
        .receipt-container {
            max-width: 380px;
            margin: 0 auto;
            background: #fff;
            padding: 24px 20px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border-radius: 2px;
        }

        /* Header */
        .header {
            text-align: center;
            padding-bottom: 12px;
        }

        .header h1 {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 2px;
            margin-bottom: 6px;
        }

        .header p {
            font-size: 11px;
            color: #555;
            margin: 2px 0;
        }

        /* Dashed Separator */
        .separator {
            text-align: center;
            font-size: 11px;
            letter-spacing: -0.5px;
            color: #333;
            margin: 12px 0;
            overflow: hidden;
        }

        /* Info Section */
        .info-section {
            padding: 8px 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            padding: 3px 0;
        }

        .info-row .label {
            color: #666;
        }

        .info-row .value {
            font-weight: 600;
            color: #000;
            text-align: right;
        }

        /* Items Table */
        .items-section {
            padding: 8px 0;
        }

        .items-header {
            display: flex;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }

        .items-header .col-item { flex: 1; }
        .items-header .col-qty { width: 40px; text-align: center; }
        .items-header .col-price { width: 80px; text-align: right; }

        .item-row {
            display: flex;
            font-size: 11px;
            padding: 4px 0;
            align-items: flex-start;
        }

        .item-row .col-item {
            flex: 1;
            word-break: break-word;
            padding-right: 8px;
        }

        .item-row .col-qty {
            width: 40px;
            text-align: center;
        }

        .item-row .col-price {
            width: 80px;
            text-align: right;
            font-weight: 600;
        }

        /* Total Section */
        .total-section {
            padding: 12px 0;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 16px;
            font-weight: bold;
        }

        .status-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px dashed #ccc;
        }

        .status-badge {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 2px 8px;
            border: 1px solid #333;
            letter-spacing: 0.5px;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding-top: 12px;
        }

        .footer .thanks {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .footer .tagline {
            font-size: 10px;
            color: #666;
            margin-bottom: 12px;
        }

        .footer .timestamp {
            font-size: 9px;
            color: #999;
            margin-top: 8px;
        }

        /* ================================================
           FLOATING ACTION BAR (Screen Only)
           ================================================ */
        .floating-actions {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            border-top: 1px solid #e5e7eb;
            padding: 12px 16px;
            display: flex;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.15s ease;
        }

        .btn-back {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .btn-back:hover {
            background: #e5e7eb;
        }

        .btn-print {
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: white;
        }

        .btn-print:hover {
            background: linear-gradient(135deg, #ea580c, #dc2626);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.4);
        }

        /* ================================================
           PRINT STYLING
           ================================================ */
        @media print {
            @page {
                size: 78mm auto;
                margin: 0;
            }

            html, body {
                width: 78mm;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
            }

            .receipt-container {
                width: 100%;
                max-width: none;
                margin: 0;
                padding: 3mm;
                box-shadow: none !important;
                border-radius: 0;
            }

            .no-print {
                display: none !important;
            }

            /* Force black text for thermal printers */
            * {
                color: #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .status-badge {
                border-color: #000 !important;
            }
        }
    </style>
</head>
<body>
    <!-- Receipt Paper -->
    <div class="receipt-container">
        <!-- Header -->
        <div class="header">
            <h1>KEDAI DJANGGO</h1>
            <p>Jl. Contoh No. 123, Kota</p>
            <p>Telp: 0812-3456-7890</p>
        </div>

        <div class="separator">- - - - - - - - - - - - - - - - - - - - - -</div>

        <!-- Order Info -->
        <div class="info-section">
            <div class="info-row">
                <span class="label">No. Order</span>
                <span class="value">{{ $order->midtrans_order_id }}</span>
            </div>
            <div class="info-row">
                <span class="label">Tanggal</span>
                <span class="value">{{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="label">Customer</span>
                <span class="value">{{ $order->customer->name }}</span>
            </div>
            <div class="info-row">
                <span class="label">No. HP</span>
                <span class="value">{{ $order->customer->phone }}</span>
            </div>
            @if($order->admin_id && $order->admin)
            <div class="info-row">
                <span class="label">Kasir</span>
                <span class="value">{{ $order->admin->name }}</span>
            </div>
            @endif
        </div>

        <div class="separator">- - - - - - - - - - - - - - - - - - - - - -</div>

        <!-- Items -->
        <div class="items-section">
            <div class="items-header">
                <span class="col-item">Item</span>
                <span class="col-qty">Qty</span>
                <span class="col-price">Subtotal</span>
            </div>
            @foreach($order->orderItems as $item)
            <div class="item-row">
                <span class="col-item">{{ $item->menu->nama_menu }}</span>
                <span class="col-qty">{{ $item->jumlah }}</span>
                <span class="col-price">{{ number_format($item->subtotal, 0, ',', '.') }}</span>
            </div>
            @endforeach
        </div>

        <div class="separator">- - - - - - - - - - - - - - - - - - - - - -</div>

        <!-- Total -->
        <div class="total-section">
            <div class="total-row">
                <span>TOTAL</span>
                <span>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
            </div>
            <div class="status-row">
                <span>Status Pembayaran</span>
                <span class="status-badge">{{ strtoupper($order->status) }}</span>
            </div>
        </div>

        <div class="separator">- - - - - - - - - - - - - - - - - - - - - -</div>

        <!-- Footer -->
        <div class="footer">
            <p class="thanks">Terima Kasih!</p>
            <p class="tagline">Atas Kunjungan Anda di Kedai Djanggo</p>
            <div class="separator">- - - - - - - - - - - - - - - - - - - - - -</div>
            <p class="timestamp">Dicetak: {{ now()->format('d M Y H:i:s') }}</p>
        </div>
    </div>

    <!-- Floating Action Bar -->
    <div class="floating-actions no-print" style="z-index: 50;"> 
        <a href="{{ route('admin.orders') }}" class="btn btn-back">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>

        <button class="btn btn-print" onclick="window.print()">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print Now
        </button>
    </div>

    <script>
        // Optional: Uncomment to auto-print
        // window.onload = function() { setTimeout(window.print, 300); };
    </script>
</body>
</html>