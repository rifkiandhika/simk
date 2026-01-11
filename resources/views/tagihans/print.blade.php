<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tagihan - {{ $tagihan->no_tagihan }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 11px;
            color: #666;
        }
        
        .invoice-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-section table {
            width: 100%;
        }
        
        .info-section td {
            padding: 3px 0;
        }
        
        .info-section td:first-child {
            width: 120px;
            font-weight: bold;
        }
        
        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .table-items th {
            background-color: #f0f0f0;
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
            font-weight: bold;
        }
        
        .table-items td {
            padding: 8px 10px;
            border: 1px solid #ddd;
        }
        
        .table-items .text-right {
            text-align: right;
        }
        
        .table-items .text-center {
            text-align: center;
        }
        
        .table-items .category-row {
            background-color: #f8f8f8;
            font-weight: bold;
        }
        
        .table-items .subtotal-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .table-items .total-row {
            background-color: #e0e0e0;
            font-weight: bold;
            font-size: 13px;
        }
        
        .payment-section {
            margin-top: 30px;
        }
        
        .summary-box {
            float: right;
            width: 300px;
            border: 2px solid #333;
            padding: 15px;
            margin-top: 20px;
        }
        
        .summary-box table {
            width: 100%;
        }
        
        .summary-box td {
            padding: 5px 0;
        }
        
        .summary-box .total-line {
            border-top: 2px solid #333;
            font-weight: bold;
            font-size: 14px;
            padding-top: 10px;
        }
        
        .footer {
            margin-top: 50px;
            clear: both;
        }
        
        .signature-box {
            display: inline-block;
            width: 45%;
            text-align: center;
            margin-top: 20px;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 11px;
        }
        
        .status-lunas {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-cicilan {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .status-belum {
            background-color: #fff3cd;
            color: #856404;
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
    <!-- Print Button -->
    <div class="no-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
            <i class="fas fa-print"></i> Cetak
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background-color: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            Tutup
        </button>
    </div>

    <!-- Header -->
    <div class="header">
        <h1>RUMAH SAKIT UMUM</h1>
        <p>Jl. Kesehatan No. 123, Jakarta | Telp: (021) 1234567 | Email: info@rs.com</p>
    </div>

    <!-- Invoice Title -->
    <div class="invoice-title">
        TAGIHAN PASIEN
    </div>

    <!-- Invoice Info -->
    <div class="info-section">
        <table>
            <tr>
                <td><strong>No. Tagihan</strong></td>
                <td>: {{ $tagihan->no_tagihan }}</td>
                <td><strong>Tanggal Tagihan</strong></td>
                <td>: {{ $tagihan->tanggal_tagihan->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td><strong>Jenis Tagihan</strong></td>
                <td>: {{ str_replace('_', ' ', $tagihan->jenis_tagihan) }}</td>
                <td><strong>Status</strong></td>
                <td>: 
                    @if($tagihan->status == 'LUNAS')
                    <span class="status-badge status-lunas">LUNAS</span>
                    @elseif($tagihan->status == 'CICILAN')
                    <span class="status-badge status-cicilan">CICILAN</span>
                    @else
                    <span class="status-badge status-belum">BELUM LUNAS</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Patient Info -->
    <div class="info-section" style="border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9;">
        <table>
            <tr>
                <td><strong>No. Rekam Medis</strong></td>
                <td>: {{ $tagihan->pasien->no_rm }}</td>
            </tr>
            <tr>
                <td><strong>Nama Pasien</strong></td>
                <td>: {{ $tagihan->pasien->nama }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Lahir</strong></td>
                <td>: {{ $tagihan->pasien->tanggal_lahir->format('d/m/Y') }} ({{ $tagihan->pasien->tanggal_lahir->age }} tahun)</td>
            </tr>
            <tr>
                <td><strong>Alamat</strong></td>
                <td>: {{ $tagihan->pasien->alamat }}</td>
            </tr>
        </table>
    </div>

    <!-- Items Table -->
    <table class="table-items">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="45%">Deskripsi</th>
                <th width="10%" class="text-center">Qty</th>
                <th width="15%" class="text-right">Harga Satuan</th>
                <th width="15%" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($tagihan->items->groupBy('kategori') as $kategori => $items)
            <tr class="category-row">
                <td colspan="5">{{ $kategori }}</td>
            </tr>
            @foreach($items as $item)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>
                    {{ $item->deskripsi }}
                    @if($item->ditanggung)
                    <br><small style="color: #007bff;">(Ditanggung)</small>
                    @endif
                </td>
                <td class="text-center">{{ $item->qty }}</td>
                <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="subtotal-row">
                <td colspan="4" class="text-right">Subtotal {{ $kategori }}:</td>
                <td class="text-right">Rp {{ number_format($items->sum('subtotal'), 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" class="text-right">TOTAL TAGIHAN:</td>
                <td class="text-right">Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Payment History (if any) -->
    @if($tagihan->pembayarans->count() > 0)
    <div class="payment-section">
        <h3 style="margin-bottom: 10px;">Riwayat Pembayaran</h3>
        <table class="table-items">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Metode</th>
                    <th>No. Referensi</th>
                    <th class="text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tagihan->pembayarans as $pembayaran)
                <tr>
                    <td>{{ $pembayaran->tanggal_bayar->format('d/m/Y') }}</td>
                    <td>{{ $pembayaran->metode }}</td>
                    <td>{{ $pembayaran->no_referensi ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3" class="text-right">TOTAL DIBAYAR:</td>
                    <td class="text-right">Rp {{ number_format($tagihan->total_dibayar, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    <!-- Summary Box -->
    <div class="summary-box">
        <table>
            <tr>
                <td>Total Tagihan</td>
                <td class="text-right">Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Dibayar</td>
                <td class="text-right">Rp {{ number_format($tagihan->total_dibayar, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-line">
                <td>SISA TAGIHAN</td>
                <td class="text-right" style="color: {{ $tagihan->sisa_tagihan > 0 ? 'red' : 'green' }}">
                    Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Footer & Signature -->
    <div class="footer">
        <p style="font-size: 11px; color: #666; margin-bottom: 30px;">
            @if($tagihan->catatan)
            <strong>Catatan:</strong> {{ $tagihan->catatan }}
            @endif
        </p>

        <div style="margin-top: 50px;">
            <div class="signature-box">
                <p>Pasien / Keluarga</p>
                <div class="signature-line">
                    ( _____________________ )
                </div>
            </div>

            <div class="signature-box" style="float: right;">
                <p>Petugas Kasir</p>
                <div class="signature-line">
                    ( {{ auth()->user()->nama ?? '_____________________' }} )
                </div>
            </div>
        </div>

        <div style="clear: both; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; font-size: 10px; color: #999;">
            <p>Dokumen ini dicetak pada {{ now()->format('d/m/Y H:i:s') }}</p>
            <p>Simpan bukti pembayaran ini sebagai bukti yang sah</p>
        </div>
    </div>
</body>
</html>