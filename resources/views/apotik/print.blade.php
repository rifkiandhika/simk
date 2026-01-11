<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Penyerahan Obat - {{ $resep->no_resep }}</title>
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
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 18px;
            color: #666;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 11px;
            color: #666;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            background-color: #f0f0f0;
            padding: 8px 10px;
            font-weight: bold;
            margin-bottom: 10px;
            border-left: 4px solid #007bff;
        }

        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 5px;
            vertical-align: top;
        }

        .info-table td:first-child {
            width: 150px;
            font-weight: bold;
        }

        .obat-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .obat-table th,
        .obat-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .obat-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .obat-table td.text-center {
            text-align: center;
        }

        .obat-table td.text-right {
            text-align: right;
        }

        .total-section {
            float: right;
            width: 300px;
            margin-top: 10px;
        }

        .total-table {
            width: 100%;
        }

        .total-table td {
            padding: 5px;
        }

        .total-table td:first-child {
            text-align: left;
        }

        .total-table td:last-child {
            text-align: right;
            font-weight: bold;
        }

        .total-table .grand-total {
            border-top: 2px solid #000;
            font-size: 14px;
            padding-top: 8px;
        }

        .signature-section {
            clear: both;
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 45%;
            text-align: center;
        }

        .signature-box p {
            margin-bottom: 80px;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 10px;
            color: #666;
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
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>APOTEK RUMAH SAKIT</h1>
            <h2>BUKTI PENYERAHAN OBAT</h2>
            <p>Jl. Contoh No. 123, Telp: (021) 1234567</p>
        </div>

        <!-- Info Resep -->
        <div class="section">
            <div class="section-title">INFORMASI RESEP</div>
            <table class="info-table">
                <tr>
                    <td>No. Resep</td>
                    <td>: <strong>{{ $resep->no_resep }}</strong></td>
                </tr>
                <tr>
                    <td>Tanggal Resep</td>
                    <td>: {{ \Carbon\Carbon::parse($resep->tanggal_resep)->format('d F Y, H:i') }}</td>
                </tr>
                <tr>
                    <td>Tanggal Penyerahan</td>
                    <td>: {{ \Carbon\Carbon::parse($resep->dispensed_at)->format('d F Y, H:i') }}</td>
                </tr>
            </table>
        </div>

        <!-- Info Pasien -->
        <div class="section">
            <div class="section-title">INFORMASI PASIEN</div>
            <table class="info-table">
                <tr>
                    <td>No. RM</td>
                    <td>: {{ $resep->pasien->no_rm }}</td>
                </tr>
                <tr>
                    <td>Nama Pasien</td>
                    <td>: {{ $resep->pasien->nama_lengkap }}</td>
                </tr>
                <tr>
                    <td>Jenis Pembayaran</td>
                    <td>: {{ $resep->pasien->jenis_pembayaran }}</td>
                </tr>
            </table>
        </div>

        <!-- Info Obat Racikan (jika ada) -->
        @if($resep->status_obat === 'Racik')
        <div class="section">
            <div class="section-title">INFORMASI RACIKAN</div>
            <table class="info-table">
                <tr>
                    <td>Jenis Racikan</td>
                    <td>: {{ $resep->jenis_racikan }}</td>
                </tr>
                <tr>
                    <td>Hasil Racikan</td>
                    <td>: {{ $resep->hasil_racikan }}</td>
                </tr>
                <tr>
                    <td>Dosis/Signa</td>
                    <td>: {{ $resep->dosis_signa }}</td>
                </tr>
                <tr>
                    <td>Aturan Pakai</td>
                    <td>: {{ $resep->aturan_pakai }}</td>
                </tr>
            </table>
        </div>
        @endif

        <!-- Daftar Obat -->
        <div class="section">
            <div class="section-title">DAFTAR OBAT</div>
            <table class="obat-table">
                <thead>
                    <tr>
                        <th width="40">No</th>
                        <th>Nama Obat</th>
                        <th width="80">Satuan</th>
                        <th width="80" class="text-center">Jumlah</th>
                        <th width="120" class="text-right">Harga</th>
                        <th width="120" class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($resep->details as $index => $detail)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $detail->detailSupplier->nama }}</strong><br>
                            <small>{{ $detail->detailSupplier->judul }}</small>
                        </td>
                        <td class="text-center">{{ $detail->detailSupplier->satuan }}</td>
                        <td class="text-center">{{ $detail->jumlah }}</td>
                        <td class="text-right">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Total -->
        <div class="total-section">
            <table class="total-table">
                <tr>
                    <td>Embalase:</td>
                    <td>Rp {{ number_format($resep->embalase, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Jasa Racik:</td>
                    <td>Rp {{ number_format($resep->jasa_racik, 0, ',', '.') }}</td>
                </tr>
                <tr class="grand-total">
                    <td><strong>TOTAL BAYAR:</strong></td>
                    <td><strong>Rp {{ number_format($resep->total_harga, 0, ',', '.') }}</strong></td>
                </tr>
            </table>
        </div>

        <!-- Keterangan -->
        @if($resep->keterangan)
        <div class="section" style="clear: both; margin-top: 20px;">
            <div class="section-title">KETERANGAN</div>
            <p>{{ $resep->keterangan }}</p>
        </div>
        @endif

        <!-- Tanda Tangan -->
        <div class="signature-section">
            <div class="signature-box">
                <p>Pasien / Keluarga</p>
                <div class="signature-line"></div>
                <p><strong>{{ $resep->pasien->nama_lengkap }}</strong></p>
            </div>
            <div class="signature-box">
                <p>Petugas Apotik</p>
                <div class="signature-line"></div>
                <p><strong>{{ $resep->dispensedBy->name ?? '-' }}</strong></p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Dokumen ini dicetak pada {{ \Carbon\Carbon::now()->format('d F Y, H:i:s') }}</p>
            <p>*** SIMPAN BUKTI INI DENGAN BAIK ***</p>
        </div>
    </div>

    <!-- Print Button (No Print) -->
    <div style="text-align: center; margin-top: 20px;" class="no-print">
        <button onclick="window.print()" style="padding: 10px 30px; font-size: 14px; cursor: pointer;">
            <i class="ri-printer-line"></i> Print
        </button>
        <button onclick="window.close()" style="padding: 10px 30px; font-size: 14px; cursor: pointer; margin-left: 10px;">
            Tutup
        </button>
    </div>

    <script>
        // Auto print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>