<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Penjualan - {{ $penjualan->kode_transaksi }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            padding: 20px;
            max-width: 300px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
        }
        .header h2 {
            font-size: 16px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 11px;
            margin: 2px 0;
        }
        .info {
            margin-bottom: 15px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }
        .info-row .label {
            font-weight: bold;
        }
        table {
            width: 100%;
            margin-bottom: 15px;
        }
        table th {
            text-align: left;
            border-bottom: 1px solid #000;
            padding: 5px 0;
        }
        table td {
            padding: 5px 0;
            vertical-align: top;
        }
        .item-name {
            font-weight: bold;
        }
        .right {
            text-align: right;
        }
        .summary {
            border-top: 1px dashed #000;
            padding-top: 10px;
            margin-top: 10px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        .summary-row.total {
            font-weight: bold;
            font-size: 14px;
            border-top: 2px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            border-top: 2px dashed #000;
            padding-top: 10px;
        }
        .footer p {
            margin: 3px 0;
            font-size: 11px;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h2>APOTIK SEHAT</h2>
        <p>Jl. Kesehatan No. 123, Jakarta</p>
        <p>Telp: (021) 1234567</p>
    </div>

    {{-- Info Transaksi --}}
    <div class="info">
        <div class="info-row">
            <span class="label">No. Transaksi</span>
            <span>{{ $penjualan->kode_transaksi }}</span>
        </div>
        <div class="info-row">
            <span class="label">Tanggal</span>
            <span>{{ $penjualan->tanggal_transaksi->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="label">Kasir</span>
            <span>{{ $penjualan->user->name ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="label">Pasien</span>
            <span>{{ $penjualan->nama_pasien }}</span>
        </div>
        @if($penjualan->no_rm_pasien)
        <div class="info-row">
            <span class="label">No. RM</span>
            <span>{{ $penjualan->no_rm_pasien }}</span>
        </div>
        @endif
    </div>

    {{-- Detail Items --}}
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="right">Qty</th>
                <th class="right">Harga</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penjualan->details as $detail)
            <tr>
                <td colspan="4" class="item-name">{{ $detail->nama_obat }}</td>
            </tr>
            <tr>
                <td><small>Batch: {{ $detail->no_batch }}</small></td>
                <td class="right">{{ $detail->jumlah }}</td>
                <td class="right">{{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                <td class="right">{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Summary --}}
    <div class="summary">
        <div class="summary-row">
            <span>Subtotal</span>
            <span>Rp {{ number_format($penjualan->subtotal, 0, ',', '.') }}</span>
        </div>
        @if($penjualan->diskon > 0)
        <div class="summary-row">
            <span>Diskon</span>
            <span>Rp {{ number_format($penjualan->diskon, 0, ',', '.') }}</span>
        </div>
        @endif
        @if($penjualan->pajak > 0)
        <div class="summary-row">
            <span>Pajak</span>
            <span>Rp {{ number_format($penjualan->pajak, 0, ',', '.') }}</span>
        </div>
        @endif
        <div class="summary-row total">
            <span>TOTAL</span>
            <span>Rp {{ number_format($penjualan->total, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row">
            <span>Bayar ({{ strtoupper($penjualan->metode_pembayaran) }})</span>
            <span>Rp {{ number_format($penjualan->bayar, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row">
            <span>Kembalian</span>
            <span>Rp {{ number_format($penjualan->kembalian, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>*** TERIMA KASIH ***</p>
        <p>Semoga Lekas Sembuh</p>
        <p>Barang yang sudah dibeli tidak dapat dikembalikan</p>
        <p style="margin-top: 10px;">{{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    {{-- Print Button --}}
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">
            Print Struk
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; cursor: pointer; margin-left: 10px;">
            Tutup
        </button>
    </div>

    <script>
        // Auto print when page loaded
        window.onload = function() {
            // window.print();
        };
    </script>
</body>
</html>