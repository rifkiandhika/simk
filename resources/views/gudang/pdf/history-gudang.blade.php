<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Gudang - {{ $gudang->nama_gudang ?? 'Export' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #000;
        }
        
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .header h2 {
            font-size: 14px;
            margin-bottom: 5px;
            color: #555;
        }
        
        .header p {
            font-size: 10px;
            margin: 3px 0;
        }
        
        .info-section {
            margin-bottom: 15px;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }
        
        .info-section table {
            width: 100%;
        }
        
        .info-section td {
            padding: 3px 5px;
            font-size: 10px;
        }
        
        .info-section td:first-child {
            width: 30%;
            font-weight: bold;
        }
        
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        table.data-table th,
        table.data-table td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: left;
        }
        
        table.data-table th {
            background-color: #4472C4;
            color: white;
            font-weight: bold;
            text-align: center;
            font-size: 9px;
        }
        
        table.data-table td {
            font-size: 9px;
        }
        
        table.data-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #000;
        }
        
        .summary-section {
            margin-top: 20px;
            page-break-inside: avoid;
        }
        
        .summary-box {
            width: 48%;
            display: inline-block;
            border: 2px solid #ddd;
            padding: 10px;
            margin-right: 2%;
            border-radius: 5px;
        }
        
        .summary-box:last-child {
            margin-right: 0;
        }
        
        .summary-box h4 {
            font-size: 11px;
            margin-bottom: 5px;
            color: #555;
        }
        
        .summary-box .value {
            font-size: 16px;
            font-weight: bold;
            color: #000;
        }
        
        .footer {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        
        .signature-section {
            width: 100%;
            margin-top: 30px;
        }
        
        .signature-box {
            width: 45%;
            display: inline-block;
            text-align: center;
            vertical-align: top;
        }
        
        .signature-box p {
            margin: 5px 0;
        }
        
        .signature-space {
            height: 60px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin: 0 30px;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        @media print {
            body {
                margin: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN HISTORY GUDANG</h1>
        <h2>{{ strtoupper($gudang->nama_gudang ?? 'Gudang') }}</h2>
        <p>Periode: {{ $tanggal_mulai }} s/d {{ $tanggal_akhir }}</p>
        <p style="font-size: 8px; color: #888;">Dicetak pada: {{ $tanggal_cetak }}</p>
    </div>

    <!-- Info Section -->
    <div class="info-section">
        <table>
            <tr>
                <td>Nama Gudang</td>
                <td>: {{ $gudang->nama_gudang ?? '-' }}</td>
                <td>Total Transaksi</td>
                <td>: {{ number_format($summary['total_transaksi'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Lokasi</td>
                <td>: {{ $gudang->lokasi ?? '-' }}</td>
                <td>Total Penerimaan</td>
                <td>: {{ number_format($summary['total_penerimaan'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kode Gudang</td>
                <td>: {{ $gudang->kode_gudang ?? '-' }}</td>
                <td>Total Pengiriman</td>
                <td>: {{ number_format($summary['total_pengiriman'], 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="12%">Waktu Proses</th>
                <th width="10%">Status</th>
                <th width="12%">Referensi</th>
                <th width="10%">No. Ref</th>
                <th width="18%">Barang</th>
                <th width="8%">Jumlah</th>
                <th width="15%">Supplier</th>
                <th width="12%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($histories as $index => $history)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $history->waktu_proses->format('d/m/Y H:i') }}</td>
                <td class="text-center">
                    @if($history->status == 'penerimaan')
                        <span class="badge badge-success">PENERIMAAN</span>
                    @else
                        <span class="badge badge-warning">PENGIRIMAN</span>
                    @endif
                </td>
                <td>{{ $history->referensi_type ?? '-' }}</td>
                <td>{{ $history->no_referensi ?? '-' }}</td>
                <td>{{ $history->barang->nama ?? '-' }}</td>
                <td class="text-right">{{ number_format($history->jumlah, 0, ',', '.') }}</td>
                <td>{{ $history->supplier->nama_supplier ?? '-' }}</td>
                <td>{{ Str::limit($history->keterangan ?? '-', 30) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center" style="padding: 20px;">
                    Tidak ada data history dalam periode ini
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Summary Section -->
    <div class="row summary-section">
        <div class="col-6 summary-box">
            <h4>📥 Total Penerimaan</h4>
            <div class="value" style="color: #28a745;">
                {{ number_format($summary['total_penerimaan'], 0, ',', '.') }}
            </div>
        </div>
        <div class="col-6 summary-box">
            <h4>📤 Total Pengiriman</h4>
            <div class="value" style="color: #ffc107;">
                {{ number_format($summary['total_pengiriman'], 0, ',', '.') }}
            </div>
        </div>
    </div>

    <!-- Footer with Signatures -->
    <div class="footer">
        <div class="signature-section">
            <div class="signature-box" style="margin-right: 10%;">
                <p>Mengetahui,</p>
                <p style="font-weight: bold;">Manager Gudang</p>
                <div class="signature-space"></div>
                <div class="signature-line"></div>
                <p style="margin-top: 5px; font-size: 9px;">Nama & Tanggal</p>
            </div>
            <div class="signature-box">
                <p>Dibuat oleh,</p>
                <p style="font-weight: bold;">Admin</p>
                <div class="signature-space"></div>
                <div class="signature-line"></div>
                <p style="margin-top: 5px; font-size: 9px;">Nama & Tanggal</p>
            </div>
        </div>
    </div>
</body>
</html>