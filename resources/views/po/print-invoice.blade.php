<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Invoice - {{ $po->no_invoice }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #27ae60;
        }

        .company-info {
            flex: 1;
        }

        .company-info h1 {
            color: #27ae60;
            font-size: 24pt;
            margin-bottom: 5px;
        }

        .company-info p {
            font-size: 9pt;
            color: #666;
            line-height: 1.6;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h2 {
            color: #27ae60;
            font-size: 28pt;
            margin-bottom: 5px;
        }

        .invoice-title .invoice-number {
            font-size: 14pt;
            color: #2c3e50;
            font-weight: bold;
        }

        /* Invoice Status Badge */
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 5px;
            font-size: 10pt;
            font-weight: bold;
            margin-top: 10px;
        }

        .badge-paid {
            background: #27ae60;
            color: white;
        }

        .badge-unpaid {
            background: #e74c3c;
            color: white;
        }

        .badge-overdue {
            background: #c0392b;
            color: white;
        }

        /* Info Section */
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
        }

        .info-box {
            width: 48%;
        }

        .info-box h3 {
            color: #27ae60;
            font-size: 11pt;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #27ae60;
        }

        .info-box table {
            width: 100%;
            font-size: 10pt;
        }

        .info-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .info-box table td:first-child {
            width: 140px;
            font-weight: bold;
            color: #555;
        }

        /* Reference Section */
        .reference-section {
            background: #fff9e6;
            padding: 15px;
            border-left: 4px solid #f39c12;
            margin-bottom: 25px;
        }

        .reference-section h4 {
            color: #f39c12;
            margin-bottom: 10px;
            font-size: 11pt;
        }

        .reference-section table {
            width: 100%;
            font-size: 10pt;
        }

        .reference-section td {
            padding: 5px;
        }

        .reference-section td:first-child {
            width: 150px;
            font-weight: bold;
            color: #555;
        }

        /* Items Table */
        .items-section {
            margin-bottom: 25px;
        }

        .items-section h3 {
            background: #27ae60;
            color: white;
            padding: 8px 12px;
            font-size: 11pt;
            margin-bottom: 10px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }

        .items-table thead {
            background: #ecf0f1;
        }

        .items-table th {
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            color: #2c3e50;
            border-bottom: 2px solid #bdc3c7;
        }

        .items-table td {
            padding: 8px;
            border-bottom: 1px solid #ecf0f1;
        }

        .items-table tbody tr:hover {
            background: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Summary */
        .summary {
            margin-top: 20px;
            float: right;
            width: 350px;
        }

        .summary table {
            width: 100%;
            font-size: 10pt;
        }

        .summary td {
            padding: 8px;
            border-bottom: 1px solid #ecf0f1;
        }

        .summary td:first-child {
            font-weight: bold;
            color: #555;
        }

        .summary td:last-child {
            text-align: right;
        }

        .summary .grand-total {
            background: #27ae60;
            color: white;
            font-size: 12pt;
            font-weight: bold;
        }

        /* Payment Terms */
        .payment-terms {
            clear: both;
            margin-top: 30px;
            padding: 15px;
            background: #e8f5e9;
            border-left: 4px solid #27ae60;
        }

        .payment-terms h4 {
            color: #27ae60;
            margin-bottom: 10px;
            font-size: 11pt;
        }

        .payment-terms table {
            width: 100%;
            font-size: 10pt;
        }

        .payment-terms td {
            padding: 5px;
        }

        .payment-terms td:first-child {
            width: 180px;
            font-weight: bold;
            color: #555;
        }

        .payment-terms .overdue {
            color: #e74c3c;
            font-weight: bold;
        }

        /* Bank Details */
        .bank-details {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border: 2px dashed #bdc3c7;
            border-radius: 5px;
        }

        .bank-details h4 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 11pt;
        }

        .bank-details table {
            width: 100%;
            font-size: 10pt;
        }

        .bank-details td {
            padding: 5px;
        }

        .bank-details td:first-child {
            width: 150px;
            font-weight: bold;
            color: #555;
        }

        /* Notes */
        .notes {
            margin-top: 20px;
            padding: 15px;
            background: #fff3cd;
            border-left: 4px solid #ffc107;
        }

        .notes h4 {
            color: #856404;
            margin-bottom: 8px;
            font-size: 11pt;
        }

        .notes p {
            font-size: 10pt;
            color: #666;
        }

        /* Signatures */
        .signatures {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 30%;
            text-align: center;
        }

        .signature-box h4 {
            font-size: 10pt;
            margin-bottom: 60px;
            color: #555;
        }

        .signature-box p {
            border-top: 1px solid #333;
            padding-top: 5px;
            font-size: 10pt;
            font-weight: bold;
        }

        .signature-box small {
            display: block;
            color: #999;
            font-size: 8pt;
            margin-top: 3px;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 2px solid #ecf0f1;
            text-align: center;
            font-size: 8pt;
            color: #999;
        }

        /* Watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100pt;
            color: rgba(231, 76, 60, 0.1);
            font-weight: bold;
            z-index: -1;
            pointer-events: none;
        }

        .watermark.paid {
            color: rgba(39, 174, 96, 0.1);
        }

        /* Print Styles */
        @media print {
            body {
                padding: 0;
            }
            
            .no-print {
                display: none !important;
            }

            .container {
                max-width: 100%;
            }

            @page {
                margin: 15mm;
            }
        }

        /* Print Button */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
        }

        .print-button:hover {
            background: #229954;
        }

        /* Urgent Notice */
        .urgent-notice {
            background: #ffebee;
            border: 2px solid #e74c3c;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .urgent-notice h4 {
            color: #c0392b;
            margin-bottom: 5px;
        }

        .urgent-notice p {
            color: #e74c3c;
            font-weight: bold;
            margin: 0;
        }
    </style>
</head>
<body>
    <!-- Watermark -->
    @php
        $dueDate = \Carbon\Carbon::parse($po->tanggal_jatuh_tempo);
        $today = \Carbon\Carbon::today();
        $daysLeft = $today->diffInDays($dueDate, false);
        $isPaid = false; // Sesuaikan dengan status pembayaran Anda
    @endphp
    
    @if($daysLeft < 0 && !$isPaid)
        <div class="watermark">OVERDUE</div>
    @elseif($isPaid)
        <div class="watermark paid">PAID</div>
    @endif

    <!-- Print Button -->
    <button class="print-button no-print" onclick="window.print()">
        <i class="ri-printer-line"></i> Cetak / Print
    </button>

    <div class="container">
        <!-- Urgent Notice for Overdue -->
        @if($daysLeft < 0 && !$isPaid)
        <div class="urgent-notice no-print">
            <h4>⚠️ PERHATIAN - INVOICE TERLAMBAT</h4>
            <p>Invoice ini sudah melewati jatuh tempo {{ abs($daysLeft) }} hari. Segera lakukan pembayaran!</p>
        </div>
        @endif

        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <h1>RUMAH SAKIT MUSLIMAT</h1>
                <p>
                    Jl. Alamat Perusahaan No. 123<br>
                    Kota, Provinsi 12345<br>
                    Telp: (021) 1234-5678<br>
                    Email: info@perusahaan.com<br>
                    NPWP: 00.000.000.0-000.000
                </p>
            </div>
            <div class="invoice-title">
                <h2>INVOICE</h2>
                <div class="invoice-number">{{ $po->no_invoice }}</div>
                @if($daysLeft < 0)
                    <div class="status-badge badge-overdue">OVERDUE</div>
                @elseif($isPaid)
                    <div class="status-badge badge-paid">PAID</div>
                @else
                    <div class="status-badge badge-unpaid">UNPAID</div>
                @endif
            </div>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <!-- Bill To -->
            <div class="info-box">
                <h3>TAGIHAN KEPADA / BILL TO</h3>
                <table>
                    <tr>
                        <td>Nama Supplier</td>
                        <td>: <strong>{{ $po->supplier->nama_supplier }}</strong></td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>: {{ $po->supplier->alamat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Telepon</td>
                        <td>: {{ $po->supplier->no_telepon ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>: {{ $po->supplier->email ?? '-' }}</td>
                    </tr>
                    @if($po->supplier->npwp)
                    <tr>
                        <td>NPWP</td>
                        <td>: {{ $po->supplier->npwp }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            <!-- Invoice Details -->
            <div class="info-box">
                <h3>DETAIL INVOICE / INVOICE DETAILS</h3>
                <table>
                    <tr>
                        <td>Tanggal Invoice</td>
                        <td>: <strong>{{ \Carbon\Carbon::parse($po->tanggal_invoice)->format('d F Y') }}</strong></td>
                    </tr>
                    <tr>
                        <td>Tanggal Jatuh Tempo</td>
                        <td>: <strong class="{{ $daysLeft < 0 ? 'overdue' : '' }}">
                            {{ $dueDate->format('d F Y') }}
                        </strong></td>
                    </tr>
                    @if($daysLeft < 0)
                    <tr>
                        <td colspan="2" style="color: #e74c3c; font-weight: bold;">
                            ⚠️ Terlambat {{ abs($daysLeft) }} hari
                        </td>
                    </tr>
                    @elseif($daysLeft == 0)
                    <tr>
                        <td colspan="2" style="color: #f39c12; font-weight: bold;">
                            ⏰ Jatuh tempo hari ini!
                        </td>
                    </tr>
                    @else
                    <tr>
                        <td colspan="2" style="color: #27ae60;">
                            ✓ {{ $daysLeft }} hari lagi
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td>Diinput oleh</td>
                        <td>: {{ $po->karyawanInputInvoice->nama_lengkap ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Input</td>
                        <td>: {{ $po->tanggal_input_invoice ? \Carbon\Carbon::parse($po->tanggal_input_invoice)->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Reference Section -->
        <div class="reference-section">
            <h4>REFERENSI DOKUMEN / DOCUMENT REFERENCE</h4>
            <table>
                <tr>
                    <td>No. Purchase Order (PO)</td>
                    <td>: <strong>{{ $po->no_po }}</strong></td>
                </tr>
                <tr>
                    <td>No. Good Receipt (GR)</td>
                    <td>: <strong>{{ $po->no_gr }}</strong></td>
                </tr>
                <tr>
                    <td>Tanggal Diterima</td>
                    <td>: {{ $po->tanggal_diterima ? \Carbon\Carbon::parse($po->tanggal_diterima)->format('d F Y, H:i') : '-' }}</td>
                </tr>
                @if($po->nomor_faktur_pajak)
                <tr>
                    <td>No. Faktur Pajak</td>
                    <td>: <strong>{{ $po->nomor_faktur_pajak }}</strong></td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Items Table -->
        <div class="items-section">
            <h3>RINCIAN BARANG / ITEMS DETAIL</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th width="40">No</th>
                        <th>Nama Produk</th>
                        <th width="100" class="text-center">Qty</th>
                        <th width="120" class="text-right">Harga Satuan</th>
                        <th width="150" class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($po->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $item->nama_produk }}</strong>
                            @if($item->batch_number)
                                <br><small style="color: #666;">Batch: {{ $item->batch_number }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->qty_diterima ?? $item->qty_disetujui ?? $item->qty_diminta }}</td>
                        <td class="text-right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td class="text-right"><strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="summary">
            <table>
                <tr>
                    <td>Subtotal</td>
                    <td>Rp {{ number_format($po->total_harga, 0, ',', '.') }}</td>
                </tr>
                @if($po->pajak > 0)
                <tr>
                    <td>Pajak (PPN)</td>
                    <td>Rp {{ number_format($po->pajak, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="grand-total">
                    <td>TOTAL YANG HARUS DIBAYAR</td>
                    <td>Rp {{ number_format($po->grand_total, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <!-- Payment Terms -->
        <div class="payment-terms">
            <h4>KETENTUAN PEMBAYARAN / PAYMENT TERMS</h4>
            <table>
                <tr>
                    <td>Metode Pembayaran</td>
                    <td>: Transfer Bank</td>
                </tr>
                <tr>
                    <td>Jatuh Tempo</td>
                    <td>: <strong class="{{ $daysLeft < 0 ? 'overdue' : '' }}">
                        {{ $dueDate->format('d F Y') }}
                        @if($daysLeft < 0)
                            (Terlambat {{ abs($daysLeft) }} hari)
                        @elseif($daysLeft == 0)
                            (Hari ini)
                        @else
                            ({{ $daysLeft }} hari lagi)
                        @endif
                    </strong></td>
                </tr>
                <tr>
                    <td>Denda Keterlambatan</td>
                    <td>: 0.5% per hari dari total invoice</td>
                </tr>
            </table>
        </div>

        <!-- Bank Details -->
        <div class="bank-details">
            <h4>INFORMASI BANK / BANK DETAILS</h4>
            <table>
                <tr>
                    <td>Nama Bank</td>
                    <td>: <strong>Bank Mandiri</strong></td>
                </tr>
                <tr>
                    <td>Nomor Rekening</td>
                    <td>: <strong>1234567890</strong></td>
                </tr>
                <tr>
                    <td>Atas Nama</td>
                    <td>: <strong>PT. NAMA PERUSAHAAN ANDA</strong></td>
                </tr>
                <tr>
                    <td>Cabang</td>
                    <td>: Jakarta Pusat</td>
                </tr>
            </table>
        </div>

        <!-- Notes -->
        <div class="notes">
            <h4>CATATAN / NOTES</h4>
            <p>
                1. Mohon melakukan pembayaran sesuai dengan tanggal jatuh tempo yang tertera.<br>
                2. Pembayaran dapat dilakukan melalui transfer bank ke rekening yang tercantum di atas.<br>
                3. Harap menyertakan nomor invoice pada keterangan transfer.<br>
                4. Bukti transfer mohon dikirimkan ke email: finance@perusahaan.com<br>
                5. Invoice ini sah tanpa tanda tangan basah.
            </p>
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-box">
                <h4>Disetujui Oleh / Approved By</h4>
                <p>{{ $po->kepalaGudang->nama_lengkap ?? '_______________________' }}</p>
                <small>Kepala Gudang</small>
            </div>

            <div class="signature-box">
                <h4>Diterima Oleh / Received By</h4>
                <p>{{ $po->penerima->nama_lengkap ?? '_______________________' }}</p>
                <small>{{ $po->tanggal_diterima ? \Carbon\Carbon::parse($po->tanggal_diterima)->format('d/m/Y') : '' }}</small>
            </div>

            <div class="signature-box">
                <h4>Finance</h4>
                <p>_______________________</p>
                <small>Bagian Keuangan</small>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                Dokumen ini dibuat secara elektronis dan sah tanpa tanda tangan basah.<br>
                Untuk pertanyaan terkait invoice ini, hubungi: finance@perusahaan.com atau (021) 1234-5678<br>
                Dicetak pada: {{ now()->format('d F Y, H:i:s') }}
            </p>
        </div>
    </div>

    <script>
        // Optional: Auto print when page loads
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>