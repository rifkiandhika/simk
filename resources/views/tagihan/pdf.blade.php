<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Tagihan {{ ucfirst($tab) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 10px;
        }
        
        .header h2 {
            margin: 0;
            font-size: 20px;
            color: #333;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-section p {
            margin: 5px 0;
        }
        
        .info-section strong {
            display: inline-block;
            width: 150px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        table thead {
            background-color: #4472C4;
            color: white;
        }
        
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        table th {
            font-weight: bold;
            text-align: center;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        table tbody tr:hover {
            background-color: #f5f5f5;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .status {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }
        
        .status-draft {
            background-color: #6c757d;
            color: white;
        }
        
        .status-menunggu {
            background-color: #ffc107;
            color: #000;
        }
        
        .status-sebagian {
            background-color: #17a2b8;
            color: white;
        }
        
        .status-lunas {
            background-color: #28a745;
            color: white;
        }
        
        .status-dibatalkan {
            background-color: #dc3545;
            color: white;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #4472C4;
        }
        
        .summary h4 {
            margin-top: 0;
            color: #333;
        }
        
        @page {
            margin: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN TAGIHAN {{ strtoupper($tab) }}</h2>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y H:i:s') }}</p>
    </div>

    <div class="info-section">
        @if(isset($filters['supplier_id']) && $filters['supplier_id'])
            @php
                $supplier = \App\Models\Supplier::find($filters['supplier_id']);
            @endphp
            <p><strong>Supplier:</strong> {{ $supplier->nama ?? '-' }}</p>
        @endif
        
        @if(isset($filters['status']) && $filters['status'])
            <p><strong>Status:</strong> 
                @switch($filters['status'])
                    @case('menunggu_pembayaran')
                        Menunggu Pembayaran
                        @break
                    @case('dibayar_sebagian')
                        Dibayar Sebagian
                        @break
                    @case('lunas')
                        Lunas
                        @break
                    @case('dibatalkan')
                        Dibatalkan
                        @break
                    @default
                        {{ $filters['status'] }}
                @endswitch
            </p>
        @endif
        
        @if(isset($filters['tanggal_dari']) && $filters['tanggal_dari'])
            <p><strong>Tanggal Dari:</strong> {{ \Carbon\Carbon::parse($filters['tanggal_dari'])->format('d F Y') }}</p>
        @endif
        
        @if(isset($filters['tanggal_sampai']) && $filters['tanggal_sampai'])
            <p><strong>Tanggal Sampai:</strong> {{ \Carbon\Carbon::parse($filters['tanggal_sampai'])->format('d F Y') }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th width="12%">NO. TAGIHAN</th>
                <th width="12%">NO. PO</th>
                <th width="20%">SUPPLIER</th>
                <th width="10%">TGL TAGIHAN</th>
                <th width="10%">JATUH TEMPO</th>
                <th width="12%">GRAND TOTAL</th>
                <th width="12%">SISA TAGIHAN</th>
                <th width="7%">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalGrandTotal = 0;
                $totalSisaTagihan = 0;
            @endphp
            
            @forelse($tagihan as $index => $item)
                @php
                    $totalGrandTotal += $item->grand_total;
                    $totalSisaTagihan += $item->sisa_tagihan;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->no_tagihan }}</td>
                    <td>{{ $item->purchaseOrder->no_gr ?? '-' }}</td>
                    <td>{{ $item->purchaseOrder->supplier->nama_supplier ?? '-' }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_tagihan)->format('d/m/Y') }}</td>
                    <td class="text-center">
                        {{ $item->tanggal_jatuh_tempo ? \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d/m/Y') : '-' }}
                    </td>
                    <td class="text-right">Rp {{ number_format($item->grand_total, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->sisa_tagihan, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @switch($item->status)
                            @case('draft')
                                <span class="status status-draft">Draft</span>
                                @break
                            @case('menunggu_pembayaran')
                                <span class="status status-menunggu">Menunggu</span>
                                @break
                            @case('dibayar_sebagian')
                                <span class="status status-sebagian">Sebagian</span>
                                @break
                            @case('lunas')
                                <span class="status status-lunas">Lunas</span>
                                @break
                            @case('dibatalkan')
                                <span class="status status-dibatalkan">Batal</span>
                                @break
                        @endswitch
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
        @if($tagihan->count() > 0)
            <tfoot>
                <tr style="background-color: #e9ecef; font-weight: bold;">
                    <td colspan="6" class="text-right">TOTAL:</td>
                    <td class="text-right">Rp {{ number_format($totalGrandTotal, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($totalSisaTagihan, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        @endif
    </table>

    @if($tagihan->count() > 0)
        <div class="summary">
            <h4>RINGKASAN</h4>
            <p><strong>Total Tagihan:</strong> {{ $tagihan->count() }} tagihan</p>
            <p><strong>Total Nilai:</strong> Rp {{ number_format($totalGrandTotal, 0, ',', '.') }}</p>
            <p><strong>Total Outstanding:</strong> Rp {{ number_format($totalSisaTagihan, 0, ',', '.') }}</p>
        </div>
    @endif

    <div class="footer">
        <p>Dokumen ini dicetak otomatis oleh sistem</p>
    </div>
</body>
</html>