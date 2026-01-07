<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Resep - {{ $penjualan->no_resep }}</title>
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
            max-width: 350px;
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
        .resep-info {
            background: #f5f5f5;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #000;
        }
        .info-row {
            display: flex;
            margin: 5px 0;
        }
        .info-row .label {
            font-weight: bold;
            width: 120px;
        }
        .divider {
            border-bottom: 1px dashed #000;
            margin: 15px 0;
        }
        .obat-section {
            margin-bottom: 15px;
        }
        .obat-item {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            background: #fff;
        }
        .obat-name {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 5px;
        }
        .obat-detail {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
            font-size: 11px;
        }
        .aturan-pakai {
            background: #fffacd;
            padding: 8px;
            margin-top: 5px;
            border-left: 3px solid #ffd700;
            font-weight: bold;
        }
        table {
            width: 100%;
            margin-bottom: 15px;
        }
        table td {
            padding: 3px 0;
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
        .note {
            background: #fff3cd;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ffc107;
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
        <p style="margin-top: 5px; font-weight: bold;">RESEP DOKTER</p>
    </div>

    {{-- Info Resep --}}
    <div class="resep-info">
        <div class="info-row">
            <span class="label">No. Resep</span>
            <span>: {{ $penjualan->no_resep }}</span>
        </div>
        <div class="info-row">
            <span class="label">Tanggal Resep</span>
            <span>: {{ \Carbon\Carbon::parse($penjualan->tanggal_resep)->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span class="label">Nama Pasien</span>
            <span>: {{ $penjualan->nama_pasien }}</span>
        </div>
        <div class="info-row">
            <span class="label">No. RM</span>
            <span>: {{ $penjualan->no_rm_pasien }}</span>
        </div>
        <div class="info-row">
            <span class="label">Dokter</span>
            <span>: {{ $penjualan->nama_dokter }}</span>
        </div>
        @if($penjualan->diagnosa)
        <div class="info-row">
            <span class="label">Diagnosa</span>
            <span>: {{ $penjualan->diagnosa }}</span>
        </div>
        @endif
    </div>

    <div class="divider"></div>

    {{-- Detail Obat --}}
    <div class="obat-section">
        <h3 style="margin-bottom: 10px; font-size: 13px;">DETAIL OBAT RESEP:</h3>
        
        @foreach($penjualan->details as $index => $detail)
        <div class="obat-item">
            <div class="obat-name">{{ $index + 1 }}. {{ $detail->nama_obat }}</div>
            
            <table style="margin-top: 5px;">
                <tr>
                    <td style="width: 80px;">Batch</td>
                    <td>: {{ $detail->no_batch }}</td>
                </tr>
                <tr>
                    <td>Jumlah</td>
                    <td>: {{ $detail->jumlah }} {{ $detail->satuan }}</td>
                </tr>
                <tr>
                    <td>Harga</td>
                    <td>: Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Subtotal</td>
                    <td>: Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                </tr>
            </table>

            <div class="aturan-pakai">
                <div style="font-size: 10px; margin-bottom: 3px;">📋 ATURAN PAKAI:</div>
                {{ $detail->aturan_pakai }}
            </div>
        </div>
        @endforeach
    </div>

    <div class="divider"></div>

    {{-- Summary Pembayaran --}}
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
            <span>TOTAL BAYAR</span>
            <span>Rp {{ number_format($penjualan->total, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row">
            <span>Metode Bayar</span>
            <span>{{ strtoupper($penjualan->metode_pembayaran) }}</span>
        </div>
        <div class="summary-row">
            <span>Bayar</span>
            <span>Rp {{ number_format($penjualan->bayar, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row">
            <span>Kembalian</span>
            <span>Rp {{ number_format($penjualan->kembalian, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Catatan Penting --}}
    <div class="note">
        <strong>CATATAN PENTING:</strong><br>
        • Perhatikan aturan pakai obat dengan benar<br>
        • Simpan obat di tempat yang sejuk dan kering<br>
        • Jauhkan dari jangkauan anak-anak<br>
        • Jika ada efek samping, segera hubungi dokter
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>Petugas: {{ $penjualan->user->name ?? '-' }}</p>
        <p>Tanggal: {{ $penjualan->tanggal_transaksi->format('d/m/Y H:i') }}</p>
        <p style="margin-top: 10px; font-weight: bold;">*** TERIMA KASIH ***</p>
        <p>Semoga Lekas Sembuh</p>
        <p>Status: <strong>{{ strtoupper($penjualan->status_resep) }}</strong></p>
    </div>

    {{-- Print Button --}}
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer; background: #28a745; color: white; border: none; border-radius: 4px;">
            Print Resep
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; cursor: pointer; background: #6c757d; color: white; border: none; border-radius: 4px; margin-left: 10px;">
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