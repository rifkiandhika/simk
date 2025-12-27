<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $tagihan->no_tagihan }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }

        .header {
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: start;
        }

        .company-info h1 {
            color: #667eea;
            font-size: 24px;
            margin-bottom: 5px;
        }

        .company-info p {
            font-size: 11px;
            color: #666;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h2 {
            font-size: 32px;
            color: #667eea;
            margin-bottom: 5px;
        }

        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .invoice-info .box {
            width: 48%;
        }

        .box h3 {
            font-size: 14px;
            color: #667eea;
            margin-bottom: 10px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 5px;
        }

        .box p {
            margin: 5px 0;
            font-size: 12px;
        }

        .box strong {
            display: inline-block;
            width: 120px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table thead {
            background: #667eea;
            color: white;
        }

        table thead th {
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
        }

        table tbody td {
            padding: 10px 8px;
            border-bottom: 1px solid #ddd;
        }

        table tbody tr:hover {
            background-color: #f9f9f9;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .summary {
            margin-top: 20px;
            float: right;
            width: 350px;
        }

        .summary table {
            margin: 0;
        }

        .summary td {
            padding: 8px;
            border: none;
        }

        .summary .total-row {
            background: #667eea;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }

        .payment-info {
            clear: both;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
        }

        .payment-status {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: bold;
            margin-top: 10px;
        }

        .status-lunas {
            background: #28a745;
            color: white;
        }

        .status-pending {
            background: #ffc107;
            color: #000;
        }

        .status-sebagian {
            background: #17a2b8;
            color: white;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 11px;
            color: #666;
        }

        .stamp-box {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .stamp {
            width: 45%;
            text-align: center;
        }

        .stamp p {
            margin: 5px 0;
        }

        .stamp .sign-space {
            height: 60px;
            border-bottom: 1px solid #333;
            margin: 30px 20px 10px 20px;
        }

        @media print {
            body {
                padding: 0;
            }
            
            .no-print {
                display: none;
            }
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-info {
            background: #17a2b8;
            color: white;
        }

        .badge-success {
            background: #28a745;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Print Button -->
        <div class="no-print" style="text-align: right; margin-bottom: 20px;">
            <button onclick="window.print()" style="padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Print Invoice
            </button>
            <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
                Close
            </button>
        </div>

        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="company-info">
                    <h1>{{ config('app.name', 'PT. COMPANY NAME') }}</h1>
                    <p>Jl. Alamat Perusahaan No. 123</p>
                    <p>Kota, Provinsi 12345</p>
                    <p>Telp: (021) 1234-5678 | Email: info@company.com</p>
                </div>
                <div class="invoice-title">
                    <h2>INVOICE</h2>
                    <p><strong>{{ $tagihan->no_tagihan }}</strong></p>
                </div>
            </div>
        </div>

        <!-- Invoice Info -->
        <div class="invoice-info">
            <div class="box">
                <h3>Kepada Yth:</h3>
                <p><strong>Supplier:</strong> {{ $tagihan->supplier->nama_supplier ?? '-' }}</p>
                @if($tagihan->supplier)
                <p><strong>Alamat:</strong> {{ $tagihan->supplier->alamat ?? '-' }}</p>
                <p><strong>Telp:</strong> {{ $tagihan->supplier->no_telp ?? '-' }}</p>
                @endif
            </div>
            <div class="box">
                <h3>Informasi Tagihan:</h3>
                <p><strong>No PO:</strong> {{ $tagihan->purchaseOrder->no_po }}</p>
                <p><strong>Tanggal:</strong> {{ $tagihan->tanggal_tagihan ? $tagihan->tanggal_tagihan->format('d/m/Y') : '-' }}</p>
                <p><strong>Jatuh Tempo:</strong> {{ $tagihan->tanggal_jatuh_tempo ? $tagihan->tanggal_jatuh_tempo->format('d/m/Y') : '-' }}</p>
                <p><strong>Status:</strong> 
                    @if($tagihan->status == 'lunas')
                        <span class="badge badge-success">LUNAS</span>
                    @elseif($tagihan->status == 'dibayar_sebagian')
                        <span class="badge badge-info">DIBAYAR SEBAGIAN</span>
                    @else
                        <span class="badge badge-info">{{ strtoupper(str_replace('_', ' ', $tagihan->status)) }}</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="40%">Nama Produk</th>
                    <th width="10%" class="text-center">Qty</th>
                    <th width="15%" class="text-right">Harga Satuan</th>
                    <th width="15%" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tagihan->items as $x => $item)
                <tr>
                    <td class="text-center">{{ $x + 1 }}</td>
                    <td>
                        {{ $item->nama_produk }}
                        @if($item->batch_number)
                            <br><small style="color: #666;">Batch: {{ $item->batch_number }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->qty_ditagihkan }}</td>
                    <td class="text-right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                    <td class="text-right"><strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary">
            <table>
                <tr>
                    <td><strong>Subtotal:</strong></td>
                    <td class="text-right">Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</td>
                </tr>
                @if($tagihan->pajak > 0)
                <tr>
                    <td><strong>Pajak:</strong></td>
                    <td class="text-right">Rp {{ number_format($tagihan->pajak, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td><strong>GRAND TOTAL:</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($tagihan->grand_total, 0, ',', '.') }}</strong></td>
                </tr>
            </table>
        </div>

        <!-- Payment Info -->
        <div class="payment-info">
            <h3 style="color: #667eea; margin-bottom: 15px;">Informasi Pembayaran:</h3>
            <table style="width: 100%; font-size: 11px;">
                <tr>
                    <td width="30%"><strong>Total Tagihan:</strong></td>
                    <td width="70%">Rp {{ number_format($tagihan->grand_total, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>Total Dibayar:</strong></td>
                    <td style="color: #28a745;">Rp {{ number_format($tagihan->total_dibayar, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>Sisa Tagihan:</strong></td>
                    <td style="color: {{ $tagihan->sisa_tagihan > 0 ? '#dc3545' : '#28a745' }};">
                        <strong>Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            </table>

            @if($tagihan->status == 'lunas')
                <span class="payment-status status-lunas">✓ LUNAS</span>
            @elseif($tagihan->status == 'dibayar_sebagian')
                <span class="payment-status status-sebagian">DIBAYAR SEBAGIAN</span>
            @else
                <span class="payment-status status-pending">MENUNGGU PEMBAYARAN</span>
            @endif
        </div>

        <!-- Payment History -->
        @if($tagihan->pembayaran->where('status_pembayaran', 'diverifikasi')->count() > 0)
        <div style="margin-top: 30px;">
            <h3 style="color: #667eea; margin-bottom: 15px;">History Pembayaran:</h3>
            <table style="font-size: 11px;">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="25%">No Pembayaran</th>
                        <th width="20%">Tanggal</th>
                        <th width="20%">Metode</th>
                        <th width="30%" class="text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tagihan->pembayaran->where('status_pembayaran', 'diverifikasi') as $x => $payment)
                    <tr>
                        <td class="text-center">{{ $x + 1 }}</td>
                        <td>{{ $payment->no_pembayaran }}</td>
                        <td>{{ $payment->tanggal_bayar->format('d/m/Y') }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $payment->metode_pembayaran)) }}</td>
                        <td class="text-right">Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Notes -->
        @if($tagihan->catatan)
        <div style="margin-top: 30px;">
            <p><strong>Catatan:</strong></p>
            <p style="font-size: 11px; color: #666;">{{ $tagihan->catatan }}</p>
        </div>
        @endif

        <!-- Stamp & Signature -->
        <div class="stamp-box">
            <div class="stamp">
                <p><strong>Hormat Kami,</strong></p>
                <div class="sign-space"></div>
                <p><strong>{{ $tagihan->karyawanBuat->nama_lengkap ?? 'Finance' }}</strong></p>
                <p style="font-size: 10px;">{{ $tagihan->karyawanBuat->jabatan ?? 'Staff Finance' }}</p>
            </div>
            <div class="stamp">
                <p><strong>Diterima Oleh,</strong></p>
                <div class="sign-space"></div>
                <p><strong>_____________________</strong></p>
                <p style="font-size: 10px;">Nama & Tanda Tangan</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Terima kasih atas kepercayaan Anda</p>
            <p>Invoice ini dicetak secara otomatis pada {{ now()->format('d/m/Y H:i:s') }}</p>
            <p style="margin-top: 10px; font-size: 10px;">
                Untuk pertanyaan mengenai invoice ini, hubungi finance@company.com atau (021) 1234-5678
            </p>
        </div>
    </div>

    <script>
        // Auto print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>