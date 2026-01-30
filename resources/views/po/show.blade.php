@extends('layouts.app')

@section('title', 'Detail Purchase Order')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('po.index') }}">Purchase Order</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">{{ $po->no_po }}</li>
@endsection

@section('content')
<div class="app-body">
    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-checkbox-circle-line me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Left Column - PO Details -->
        <div class="col-xl-8">
            <!-- Header Info Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1"><i class="ri-file-list-3-line me-2"></i>{{ $po->no_po }}</h5>
                            <small>Dibuat: {{ $po->tanggal_permintaan->format('d F Y, H:i') }}</small>
                        </div>
                        <div>
                            @if($po->tipe_po == 'internal')
                                <span class="badge bg-light text-dark px-3 py-2">
                                    <i class="ri-arrow-right-line"></i> Internal
                                </span>
                            @else
                                <span class="badge bg-warning text-dark px-3 py-2">
                                    <i class="ri-external-link-line"></i> Eksternal
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small mb-1">Pemohon</label>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-2">
                                    <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                        <i class="ri-user-line fs-5"></i>
                                    </span>
                                </div>
                                <div>
                                    <strong>{{ $po->karyawanPemohon->nama_lengkap }}</strong>
                                    <br><small class="text-muted">{{ ucfirst($po->unit_pemohon) }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small mb-1">Tujuan</label>
                            <div>
                                @if($po->supplier)
                                    <strong class="text-primary">{{ $po->supplier->nama_supplier }}</strong>
                                    <br><small class="text-muted">Supplier</small>
                                @else
                                    <strong class="text-primary">{{ ucfirst($po->unit_tujuan) }}</strong>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($po->catatan_pemohon)
                    <div class="alert alert-info mb-0">
                        <i class="ri-information-line me-2"></i>
                        <strong>Catatan:</strong> {{ $po->catatan_pemohon }}
                    </div>
                    @endif
                </div>
            </div>

            {{-- Confirmation Receipt Card for Internal PO --}}
            @if($po->tipe_po === 'internal' && $po->status === 'dikirim')
            <div class="card shadow-sm border-0 mb-4 border-warning" style="border-width: 2px !important;">
                <div class="card-header bg-warning py-3">
                    <h5 class="mb-0">
                        <i class="ri-inbox-line me-2"></i>Konfirmasi Penerimaan Barang
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-3">
                        <div class="d-flex align-items-start">
                            <i class="ri-alert-line me-2 fs-4"></i>
                            <div>
                                <strong>Perhatian:</strong> PO ini telah disetujui oleh 
                                {{ $po->tipe_po === 'internal' ? 'Gudang' : 'Kepala Gudang' }}. 
                                Silakan lakukan pengecekan dan konfirmasi penerimaan barang dari gudang.
                            </div>
                        </div>
                    </div>
                    
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="mb-2">
                                <i class="ri-box-3-line me-2 text-primary"></i>
                                <strong>Total Item:</strong> {{ $po->items->count() }} produk
                            </div>
                            <div class="mb-2">
                                <i class="ri-stack-line me-2 text-primary"></i>
                                <strong>Total Quantity:</strong> {{ $po->items->sum('qty_diminta') }} unit
                            </div>
                            <div class="mb-0">
                                <i class="ri-user-line me-2 text-primary"></i>
                                <strong>Disetujui oleh:</strong> {{ $po->kepalaGudang->nama_lengkap ?? '-' }}
                                @if($po->tanggal_approval_kepala_gudang)
                                    <br><small class="text-muted ms-4">pada {{ $po->tanggal_approval_kepala_gudang->format('d/m/Y H:i') }}</small>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('po.show-confirmation', $po->id_po) }}" class="btn btn-warning btn-lg w-100">
                                <i class="ri-checkbox-multiple-line me-2"></i>
                                Konfirmasi Penerimaan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Status jika sudah diterima (Internal) --}}
            @if($po->tipe_po === 'internal' && $po->status === 'selesai')
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <div class="d-flex align-items-start">
                    <i class="ri-check-double-line me-3 fs-3"></i>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading mb-2"><strong>Penerimaan Dikonfirmasi</strong></h6>
                        <p class="mb-1">
                            Barang telah diterima dan stok apotik telah diupdate pada 
                            <strong>{{ $po->tanggal_diterima ? \Carbon\Carbon::parse($po->tanggal_diterima)->format('d/m/Y H:i') : '-' }}</strong>
                        </p>
                        @if($po->penerima)
                            <small class="text-muted d-block mb-2">
                                <i class="ri-user-line me-1"></i>Dikonfirmasi oleh: <strong>{{ $po->penerima->nama_lengkap }}</strong>
                            </small>
                        @endif
                        @if($po->catatan_penerima)
                            <div class="mt-2 p-2 bg-white rounded border">
                                <small>
                                    <i class="ri-chat-3-line me-1 text-muted"></i>
                                    <strong>Catatan:</strong> {{ $po->catatan_penerima }}
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            {{-- Confirmation Receipt Card for External PO --}}
            @if($po->tipe_po === 'eksternal' && $po->status === 'diterima' )
            <div class="card shadow-sm border-0 mb-4 border-success" style="border-width: 2px !important;">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0">
                        <i class="ri-inbox-line me-2"></i>Konfirmasi Penerimaan Barang dari Supplier
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-success mb-3">
                        <div class="d-flex align-items-start">
                            <i class="ri-truck-line me-2 fs-4"></i>
                            <div>
                                <strong>Barang Sudah Tiba!</strong> Barang dari supplier <strong>{{ $po->supplier->nama_supplier ?? '-' }}</strong> telah sampai. 
                                Silakan lakukan pengecekan dan konfirmasi penerimaan untuk menambahkan stok ke gudang.
                            </div>
                        </div>
                    </div>
                    
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="mb-2">
                                <i class="ri-store-line me-2 text-success"></i>
                                <strong>Supplier:</strong> {{ $po->supplier->nama_supplier ?? '-' }}
                            </div>
                            <div class="mb-2">
                                <i class="ri-box-3-line me-2 text-success"></i>
                                <strong>Total Item:</strong> {{ $po->items->count() }} produk
                            </div>
                            <div class="mb-2">
                                <i class="ri-stack-line me-2 text-success"></i>
                                <strong>Total Quantity:</strong> {{ $po->items->sum('qty_diminta') }} unit
                            </div>
                            @php
                                $lastShipping = $po->shippingActivities()
                                    ->whereIn('status_shipping', ['diterima', 'selesai'])
                                    ->latest('tanggal_aktivitas')
                                    ->first();
                            @endphp
                            @if($lastShipping)
                            <div class="mb-0">
                                <i class="ri-calendar-check-line me-2 text-success"></i>
                                <strong>Tiba pada:</strong> {{ $lastShipping->tanggal_aktivitas->format('d/m/Y H:i') }}
                                @if($lastShipping->karyawan)
                                    <br><small class="text-muted ms-4">Dicatat oleh: {{ $lastShipping->karyawan->nama_lengkap }}</small>
                                @endif
                            </div>
                            @endif
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('po.showex-confirmation', $po->id_po) }}" class="btn btn-success btn-lg w-100">
                                <i class="ri-checkbox-multiple-line me-2"></i>
                                Konfirmasi Penerimaan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Status jika sudah diterima (External) --}}
            @if($po->tipe_po === 'eksternal' && $po->status === 'selesai')
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <div class="d-flex align-items-start">
                    <i class="ri-check-double-line me-3 fs-3"></i>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading mb-2"><strong>Penerimaan Dikonfirmasi</strong></h6>
                        <p class="mb-1">
                            Barang dari supplier <strong>{{ $po->supplier->nama_supplier ?? '-' }}</strong> telah diterima dan stok gudang telah diupdate pada 
                            <strong>{{ $po->tanggal_diterima ? \Carbon\Carbon::parse($po->tanggal_diterima)->format('d/m/Y H:i') : '-' }}</strong>
                        </p>
                        @if($po->penerima)
                            <small class="text-muted d-block mb-2">
                                <i class="ri-user-line me-1"></i>Dikonfirmasi oleh: <strong>{{ $po->penerima->nama_lengkap }}</strong>
                            </small>
                        @endif
                        @if($po->catatan_penerima)
                            <div class="mt-2 p-2 bg-white rounded border">
                                <small>
                                    <i class="ri-chat-3-line me-1 text-muted"></i>
                                    <strong>Catatan:</strong> {{ $po->catatan_penerima }}
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            {{-- Invoice Input Card --}}
            @if($po->needsInvoice())
            <div class="card shadow-sm border-0 mb-4 border-info" style="border-width: 2px !important;">
                <div class="card-header bg-info text-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="ri-file-text-line me-2"></i>Input Invoice/Faktur Supplier
                    </h5>

                    <button type="button" class="btn btn-light btn-sm" onclick="showInvoiceProofModal()">
                        <i class="ri-image-line me-1"></i> 
                        {{ $po->hasInvoiceProof() ? 'Lihat Bukti Invoice': 'Upload Bukti Invoice' }} 
                    </button>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <div class="d-flex align-items-start">
                            <i class="ri-information-line me-2 fs-4"></i>
                            <div>
                                <strong>Tukar Faktur:</strong> Barang sudah diterima (GR: <strong>{{ $po->no_gr }}</strong>).
                                Silakan input nomor invoice/faktur dari supplier untuk melanjutkan ke proses pembayaran.
                            </div>
                        </div>
                    </div>
                    
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="mb-2">
                                <i class="ri-check-double-line me-2 text-success"></i>
                                <strong>Status:</strong> Barang sudah diterima dan diperiksa
                            </div>
                            <div class="mb-2">
                                <i class="ri-checkbox-circle-line me-2 text-success"></i>
                                <strong>No. GR:</strong> <span class="badge bg-primary">{{ $po->no_gr }}</span>
                            </div>
                            <div class="mb-2">
                                <i class="ri-calendar-check-line me-2 text-info"></i>
                                <strong>Tanggal Diterima: {{ $po->tanggal_diterima ? \Carbon\Carbon::parse($po->tanggal_diterima)->format('d/m/Y H:i') : '-' }}</strong>
                            </div>
                            <div class="mb-0">
                                <i class="ri-money-dollar-circle-line me-2 text-info"></i>
                                <strong>Grand Total:</strong> Rp {{ number_format($po->grand_total, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('po.invoice-form', $po->id_po) }}" class="btn btn-info btn-lg w-100">
                                <i class="ri-file-add-line me-2"></i>
                                Input Invoice/Faktur
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Invoice Display Card --}}
            @if($po->hasInvoice())
                <div class="card shadow-sm border-0 mb-4 border-success" style="border-width: 2px !important;">
                    <div class="card-header bg-success text-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="ri-file-check-line me-2"></i>Invoice/Faktur Supplier
                        </h5>

                        {{-- BUTTON LOGIC --}}
                        @if(!$po->needsInvoice())
                            @if(!$po->hasInvoiceProof())
                                <button type="button" class="btn btn-warning btn-sm" onclick="showInvoiceProofModal()">
                                    <i class="ri-upload-2-line me-1"></i>
                                    Upload Bukti Invoice
                                </button>
                            @else
                                <button type="button" class="btn btn-light btn-sm" onclick="showInvoiceProofModal()">
                                    <i class="ri-image-line me-1"></i>
                                    Lihat Bukti Invoice
                                </button>
                            @endif
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="150"><strong>No. Invoice</strong></td>
                                        <td>: <span class="badge bg-success">{{ $po->no_invoice }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal Invoice</strong></td>
                                        <td>: {{ \Carbon\Carbon::parse($po->tanggal_invoice)->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Jatuh Tempo</strong></td>
                                        <td>: 
                                            @php
                                                $dueDate = \Carbon\Carbon::parse($po->tanggal_jatuh_tempo);
                                                $today = \Carbon\Carbon::today();
                                                $daysLeft = $today->diffInDays($dueDate, false);
                                            @endphp
                                            <strong class="{{ $daysLeft < 0 ? 'text-danger' : ($daysLeft <= 3 ? 'text-warning' : 'text-success') }}">
                                                {{ $dueDate->format('d/m/Y') }}
                                                @if($daysLeft < 0)
                                                    (Terlambat {{ abs($daysLeft) }} hari)
                                                @elseif($daysLeft == 0)
                                                    (Jatuh tempo hari ini!)
                                                @else
                                                    ({{ $daysLeft }} hari lagi)
                                                @endif
                                            </strong>
                                        </td>
                                    </tr>
                                    @if($po->nomor_faktur_pajak)
                                    <tr>
                                        <td><strong>Faktur Pajak</strong></td>
                                        <td>: {{ $po->nomor_faktur_pajak }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="150"><strong>No. GR</strong></td>
                                        <td>: <span class="badge bg-primary">{{ $po->no_gr }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Grand Total</strong></td>
                                        <td>: <strong class="text-success">Rp {{ number_format($po->grand_total, 0, ',', '.') }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Diinput oleh</strong></td>
                                        <td>: {{ $po->karyawanInputInvoice->nama_lengkap ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal Input</strong></td>
                                        <td>: {{ $po->tanggal_input_invoice ? \Carbon\Carbon::parse($po->tanggal_input_invoice)->format('d/m/Y H:i') : '-' }}</td>
                                    </tr>
                                    @if($po->bukti_invoice)
                                    <tr>
                                        <td><strong>Bukti Invoice</strong></td>
                                        <td>: <span class="badge bg-success"><i class="ri-checkbox-circle-line"></i> Sudah diupload</span></td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                        
                        @if($daysLeft <= 3 && $daysLeft >= 0)
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="ri-time-line me-2"></i>
                            <strong>Perhatian:</strong> Invoice akan jatuh tempo dalam {{ $daysLeft }} hari. Segera lakukan pembayaran!
                        </div>
                        @elseif($daysLeft < 0)
                        <div class="alert alert-danger mt-3 mb-0">
                            <i class="ri-error-warning-line me-2"></i>
                            <strong>Terlambat!</strong> Invoice sudah melewati jatuh tempo {{ abs($daysLeft) }} hari. Segera lakukan pembayaran!
                        </div>
                        @endif
                    </div>
                </div>
                @endif

            {{-- Items Table with Batch Details --}}
            @if($po->status === 'diterima' && $po->items->first() && $po->items->first()->batches->count() > 0)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="ri-shopping-cart-line me-2"></i>Item Purchase Order & Batch Details</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Nama Produk</th>
                                    <th width="100">Qty Diminta</th>
                                    <th width="100">Qty Diterima</th>
                                    <th width="150">Batch Details</th>
                                    <th width="120" class="text-end">Harga</th>
                                    <th width="150" class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($po->items as $index => $item)
                                <tr>
                                    <td class="text-center" rowspan="{{ $item->batches->count() > 0 ? $item->batches->count() + 1 : 1 }}">
                                        {{ $index + 1 }}
                                    </td>
                                    <td rowspan="{{ $item->batches->count() > 0 ? $item->batches->count() + 1 : 1 }}">
                                        <strong>{{ $item->nama_produk }}</strong>
                                    </td>
                                    <td class="text-center" rowspan="{{ $item->batches->count() > 0 ? $item->batches->count() + 1 : 1 }}">
                                        <span class="badge bg-secondary">{{ $item->qty_diminta }}</span>
                                    </td>
                                    <td class="text-center" rowspan="{{ $item->batches->count() > 0 ? $item->batches->count() + 1 : 1 }}">
                                        <span class="badge bg-success">{{ $item->qty_diterima }}</span>
                                    </td>
                                    <td colspan="3" class="bg-light">
                                        <small class="text-muted"><strong>Detail Batch:</strong></small>
                                    </td>
                                </tr>
                                @foreach($item->batches as $batch)
                                <tr class="batch-detail-row">
                                    <td>
                                        <small>
                                            <i class="ri-stack-line me-1"></i>
                                            <strong>{{ $batch->batch_number }}</strong>
                                            <br>
                                            <span class="text-muted">Exp: {{ \Carbon\Carbon::parse($batch->tanggal_kadaluarsa)->format('d/m/Y') }}</span>
                                            <br>
                                            @if($batch->kondisi === 'baik')
                                                <span class="badge badge-sm bg-success">{{ ucfirst($batch->kondisi) }}</span>
                                            @elseif($batch->kondisi === 'rusak')
                                                <span class="badge badge-sm bg-danger">{{ ucfirst($batch->kondisi) }}</span>
                                            @else
                                                <span class="badge badge-sm bg-warning text-dark">{{ ucfirst($batch->kondisi) }}</span>
                                            @endif
                                            @if($batch->catatan)
                                                <br><small class="text-muted">Note: {{ $batch->catatan }}</small>
                                            @endif
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <small>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</small>
                                        <br><small class="text-muted">× {{ $batch->qty_diterima }}</small>
                                    </td>
                                    <td class="text-end">
                                        <small>Rp {{ number_format($item->harga_satuan * $batch->qty_diterima, 0, ',', '.') }}</small>
                                    </td>
                                </tr>
                                @endforeach
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="6" class="text-end">Subtotal:</th>
                                    <th class="text-end">Rp {{ number_format($po->total_harga, 0, ',', '.') }}</th>
                                </tr>
                                @if($po->pajak > 0)
                                <tr>
                                    <th colspan="6" class="text-end">Pajak:</th>
                                    <th class="text-end">Rp {{ number_format($po->pajak, 0, ',', '.') }}</th>
                                </tr>
                                @endif
                                <tr class="table-primary">
                                    <th colspan="6" class="text-end">Grand Total:</th>
                                    <th class="text-end">Rp {{ number_format($po->grand_total, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Items Table -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="ri-shopping-cart-line me-2"></i>Item Purchase Order</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Nama Produk</th>
                                    <th width="100">Qty Diminta</th>
                                    <th width="100">Qty Disetujui</th>
                                    <th width="100">Qty Diterima</th>
                                    @if($po->tipe_po !== 'internal')
                                    <th width="120" class="text-end">Harga</th>
                                    <th width="150" class="text-end">Subtotal</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($po->items as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $item->nama_produk }}</strong>
                                        @if($item->batch_number)
                                            <br><small class="text-muted">Batch: {{ $item->batch_number }}</small>
                                        @endif
                                        @if($item->tanggal_kadaluarsa)
                                            <br><span class="badge badge-sm bg-warning text-dark">
                                                <i class="ri-calendar-line"></i> Exp: {{ \Carbon\Carbon::parse($item->tanggal_kadaluarsa)->format('d/m/Y') }}
                                            </span>
                                        @endif
                                        @if($item->kondisi_barang && $item->kondisi_barang != 'baik')
                                            <br><span class="badge badge-sm bg-danger">
                                                <i class="ri-alert-line"></i> {{ ucfirst($item->kondisi_barang) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $item->qty_diminta }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($item->qty_disetujui)
                                            <span class="badge bg-primary">{{ $item->qty_disetujui }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item->qty_diterima)
                                            @if($item->kondisi_barang == 'baik')
                                                <span class="badge bg-success">{{ $item->qty_diterima }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ $item->qty_diterima }}</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    @if($po->tipe_po !== 'internal')
                                        <td class="text-end">
                                            <small>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</small>
                                        </td>

                                        <td class="text-end">
                                            <strong>
                                                @if($po->total_diterima > 0)
                                                    Rp {{ number_format($po->total_diterima, 0, ',', '.') }}
                                                @elseif($item->subtotal > 0)
                                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                                @endif
                                            </strong>
                                        </td>

                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                @if($po->tipe_po !== 'internal')

                                    @php
                                        $pajak    = $po->pajak_diterima ?? $po->pajak;
                                        $grand    = $po->grand_total_diterima ?? $po->grand_total;
                                    @endphp

                                    @if($po->total_diterima > 0)
                                        <tr>
                                            <th colspan="6" class="text-end">Subtotal:</th>
                                            <th class="text-end">
                                                Rp {{ number_format($po->total_diterima, 0, ',', '.') }}
                                            </th>
                                        </tr>

                                    @elseif($po->total_harga > 0)
                                        <tr>
                                            <th colspan="6" class="text-end">Subtotal:</th>
                                            <th class="text-end">
                                                Rp {{ number_format($po->total_harga, 0, ',', '.') }}
                                            </th>
                                        </tr>
                                    @endif

                                    @if($pajak > 0)
                                    <tr>
                                        <th colspan="6" class="text-end">Pajak:</th>
                                        <th class="text-end">
                                            Rp {{ number_format($pajak, 0, ',', '.') }}
                                        </th>
                                    </tr>
                                    @endif

                                    <tr class="table-primary">
                                        <th colspan="6" class="text-end">Grand Total:</th>
                                        <th class="text-end">
                                            Rp {{ number_format($grand, 0, ',', '.') }}
                                        </th>
                                    </tr>

                                @endif
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Shipping Activities -->
            @if($po->shippingActivities && $po->shippingActivities->count() > 0)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="ri-truck-line me-2"></i>Shipping Activities</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($po->shippingActivities->sortBy('tanggal_aktivitas') as $activity)
                        <div class="timeline-item mb-3">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">
                                            <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $activity->status_shipping)) }}</span>
                                        </h6>
                                        <p class="mb-1">{{ $activity->deskripsi_aktivitas }}</p>
                                        @if($activity->catatan)
                                            <small class="text-muted"><i class="ri-chat-3-line"></i> {{ $activity->catatan }}</small>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">
                                            {{ $activity->tanggal_aktivitas->format('d/m/Y H:i') }}
                                        </small>
                                        @if($activity->karyawan)
                                            <br><small class="text-muted">oleh {{ $activity->karyawan->nama_lengkap }}</small>
                                        @endif
                                    </div>
                                </div>
                                @if($activity->foto_bukti)
                                    <img src="{{ asset('storage/' . $activity->foto_bukti) }}" 
                                         class="img-thumbnail mt-2" style="max-width: 200px;" 
                                         alt="Bukti Shipping">
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Audit Trail -->
            @if($po->auditTrails && $po->auditTrails->count() > 0)
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="ri-history-line me-2"></i>Audit Trail</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="180">Waktu</th>
                                    <th>User</th>
                                    <th>Aksi</th>
                                    <th>Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($po->auditTrails->sortByDesc('tanggal_aksi') as $audit)
                                <tr>
                                    <td><small>{{ $audit->tanggal_aksi->format('d/m/Y H:i:s') }}</small></td>
                                    <td><small>{{ $audit->karyawan->nama_lengkap ?? '-' }}</small></td>
                                    <td>
                                        <span class="badge badge-sm bg-secondary">
                                            @php
                                                $aksiText = $audit->aksi;
                                                // Jika PO internal dan aksi adalah approval_kepala_gudang, ganti jadi approval_gudang
                                                if ($po->tipe_po === 'internal' && str_contains($aksiText, 'approve_kepala_gudang')) {
                                                    $aksiText = str_replace('approve_kepala_gudang', 'approve_gudang', $aksiText);
                                                }
                                                echo str_replace('_', ' ', $aksiText);
                                            @endphp
                                        </span>
                                    </td>
                                    <td><small>{{ $audit->deskripsi_aksi }}</small></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Status & Actions -->
        <div class="col-xl-4">
            <!-- Status Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="ri-status-line me-2"></i>Status PO</h5>
                </div>
                <div class="card-body text-center">
                    @php
                        $statusConfig = [
                            'draft' => ['color' => 'secondary', 'icon' => 'ri-draft-line', 'label' => 'Draft'],
                            'menunggu_persetujuan_kepala_gudang' => [
                                'color' => 'warning', 
                                'icon' => 'ri-time-line', 
                                'label' => $po->tipe_po === 'internal' ? 'Menunggu Persetujuan Gudang' : 'Menunggu Kepala Gudang'
                            ],
                            'menunggu_persetujuan_kasir' => ['color' => 'warning', 'icon' => 'ri-time-line', 'label' => 'Menunggu Kasir'],
                            'dikirim' => ['color' => 'info', 'icon' => 'ri-truck-line', 'label' => 'Dikirim'],
                            'selesai' => ['color' => 'success', 'icon' => 'ri-checkbox-circle-line', 'label' => 'Selesai'],
                            'disetujui' => ['color' => 'success', 'icon' => 'ri-checkbox-circle-line', 'label' => 'Disetujui'],
                            'dikirim_ke_supplier' => ['color' => 'info', 'icon' => 'ri-truck-line', 'label' => 'Dikirim ke Supplier'],
                            'dalam_pengiriman' => ['color' => 'primary', 'icon' => 'ri-map-pin-line', 'label' => 'Dalam Pengiriman'],
                            'diterima' => ['color' => 'success', 'icon' => 'ri-checkbox-circle-fill', 'label' => 'Diterima'],
                            'ditolak' => ['color' => 'danger', 'icon' => 'ri-close-circle-line', 'label' => 'Ditolak'],
                        ];
                        $currentStatus = $statusConfig[$po->status] ?? ['color' => 'secondary', 'icon' => 'ri-question-line', 'label' => $po->status];
                    @endphp
                    <div class="mb-3">
                        <i class="{{ $currentStatus['icon'] }} text-{{ $currentStatus['color'] }}" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="text-{{ $currentStatus['color'] }}">{{ $currentStatus['label'] }}</h4>
                </div>
            </div>

            <!-- Approval Info (for external PO) -->
            @if($po->tipe_po === 'eksternal')
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="ri-shield-check-line me-2"></i>Persetujuan</h5>
                </div>
                <div class="card-body">
                    <!-- Kepala Gudang Approval -->
                    <div class="mb-3 pb-3 border-bottom">
                        <label class="text-muted small mb-2">
                            {{ $po->tipe_po === 'internal' ? 'Gudang' : 'Kepala Gudang' }}
                        </label>
                        @if($po->kepalaGudang)
                            <div class="d-flex align-items-center mb-2">
                                <i class="ri-user-line me-2 text-primary"></i>
                                <strong>{{ $po->kepalaGudang->nama_lengkap }}</strong>
                            </div>
                            @if($po->status_approval_kepala_gudang === 'disetujui')
                                <span class="badge bg-success">
                                    <i class="ri-checkbox-circle-line"></i> Disetujui
                                </span>
                                <br><small class="text-muted">{{ $po->tanggal_approval_kepala_gudang->format('d/m/Y H:i') }}</small>
                            @elseif($po->status_approval_kepala_gudang === 'ditolak')
                                <span class="badge bg-danger">
                                    <i class="ri-close-circle-line"></i> Ditolak
                                </span>
                                <br><small class="text-muted">{{ $po->tanggal_approval_kepala_gudang->format('d/m/Y H:i') }}</small>
                            @else
                                <span class="badge bg-warning text-dark">
                                    <i class="ri-time-line"></i> Menunggu
                                </span>
                            @endif
                            @if($po->catatan_kepala_gudang)
                                <div class="alert alert-light mt-2 mb-0">
                                    <small>{{ $po->catatan_kepala_gudang }}</small>
                                </div>
                            @endif
                        @else
                            <span class="text-muted">Belum ada approval</span>
                        @endif
                    </div>

                    <!-- Kasir Approval -->
                    <div>
                        <label class="text-muted small mb-2">Kasir</label>
                        @if($po->kasir)
                            <div class="d-flex align-items-center mb-2">
                                <i class="ri-user-line me-2 text-primary"></i>
                                <strong>{{ $po->kasir->nama_lengkap }}</strong>
                            </div>
                            @if($po->status_approval_kasir === 'disetujui')
                                <span class="badge bg-success">
                                    <i class="ri-checkbox-circle-line"></i> Disetujui
                                </span>
                                <br><small class="text-muted">{{ $po->tanggal_approval_kasir->format('d/m/Y H:i') }}</small>
                            @elseif($po->status_approval_kasir === 'ditolak')
                                <span class="badge bg-danger">
                                    <i class="ri-close-circle-line"></i> Ditolak
                                </span>
                                <br><small class="text-muted">{{ $po->tanggal_approval_kasir->format('d/m/Y H:i') }}</small>
                            @else
                                <span class="badge bg-warning text-dark">
                                    <i class="ri-time-line"></i> Menunggu
                                </span>
                            @endif
                            @if($po->catatan_kasir)
                                <div class="alert alert-light mt-2 mb-0">
                                    <small>{{ $po->catatan_kasir }}</small>
                                </div>
                            @endif
                        @else
                            <span class="text-muted">Belum ada approval</span>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Actions Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="ri-tools-line me-2"></i>Aksi</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('po.index') }}" class="btn btn-outline-secondary">
                            <i class="ri-arrow-left-line me-1"></i> Kembali
                        </a>

                        @if(in_array($po->status, ['draft', 'ditolak', 'selesai']))
                            <a href="{{ route('po.edit', $po->id_po) }}" class="btn btn-outline-info">
                                <i class="ri-pencil-line me-1"></i> Edit PO
                            </a>
                        @endif
                        @if(in_array($po->status, ['draft', 'ditolak']))
                            <button class="btn btn-primary" onclick="submitPO('{{ $po->id_po }}')">
                                <i class="ri-send-plane-fill me-1"></i> Submit PO
                            </button>
                        @endif

                        @if($po->status === 'menunggu_persetujuan_kepala_gudang' && auth()->user()->hasAnyRole(['kepala_gudang', 'Superadmin']))
                            <button class="btn btn-success" onclick="showApprovalModal('kepala_gudang', 'disetujui')">
                                <i class="ri-checkbox-circle-line me-1"></i> Setujui
                            </button>
                            <button class="btn btn-danger" onclick="showApprovalModal('kepala_gudang', 'ditolak')">
                                <i class="ri-close-circle-line me-1"></i> Tolak
                            </button>
                        @endif

                        @if($po->status === 'menunggu_persetujuan_kasir' && auth()->user()->hasAnyRole(['kepala_gudang', 'Superadmin']))
                            <button class="btn btn-success" onclick="showApprovalModal('kasir', 'disetujui')">
                                <i class="ri-checkbox-circle-line me-1"></i> Setujui
                            </button>
                            <button class="btn btn-danger" onclick="showApprovalModal('kasir', 'ditolak')">
                                <i class="ri-close-circle-line me-1"></i> Tolak
                            </button>
                        @endif

                        {{-- @if($po->status === 'disetujui')
                            <button class="btn btn-info" onclick="sendToSupplier('{{ $po->id_po }}')">
                                <i class="ri-truck-line me-1"></i> Kirim ke Supplier
                            </button>
                        @endif --}}

                        @if(in_array($po->status, ['disetujui']))
                            <button class="btn btn-info" onclick="markAsReceived('{{ $po->id_po }}')">
                                <i class="ri-checkbox-circle-line me-1"></i> Tandai Diterima
                            </button>
                        @endif

                        <a href="{{ route('po.print', $po->id_po) }}" class="btn btn-outline-primary" target="_blank">
                            <i class="ri-printer-line me-1"></i> Print PO
                        </a>

                        @if(in_array($po->status, ['draft', 'ditolak']))
                            <button class="btn btn-outline-danger" onclick="deletePO('{{ $po->id_po }}')">
                                <i class="ri-delete-bin-line me-1"></i> Hapus PO
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Universal PIN Modal with OTP-style inputs --}}
<div class="modal fade" id="pinModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="pinModalTitle">
                    <i class="ri-lock-password-line me-2"></i>Masukkan PIN
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-4 py-4">
                <p class="text-muted mb-4" id="pinModalDescription">Masukkan PIN 6 digit untuk melanjutkan</p>
                
                <!-- OTP-style PIN Input -->
                <div class="otp-container d-flex justify-content-center gap-2 mb-4">
                    <input type="password" class="otp-input form-control text-center" maxlength="1" pattern="\d" inputmode="numeric" data-index="0">
                    <input type="password" class="otp-input form-control text-center" maxlength="1" pattern="\d" inputmode="numeric" data-index="1">
                    <input type="password" class="otp-input form-control text-center" maxlength="1" pattern="\d" inputmode="numeric" data-index="2">
                    <input type="password" class="otp-input form-control text-center" maxlength="1" pattern="\d" inputmode="numeric" data-index="3">
                    <input type="password" class="otp-input form-control text-center" maxlength="1" pattern="\d" inputmode="numeric" data-index="4">
                    <input type="password" class="otp-input form-control text-center" maxlength="1" pattern="\d" inputmode="numeric" data-index="5">
                </div>

                <!-- Additional Notes Textarea (hidden by default) -->
                <div id="notesContainer" class="mb-3" style="display: none;">
                    <label class="form-label text-start w-100">Catatan (opsional)</label>
                    <textarea class="form-control" id="modalNotes" rows="3" placeholder="Tambahkan catatan..."></textarea>
                </div>

                <!-- Hidden fields to store action context -->
                <input type="hidden" id="modalAction">
                <input type="hidden" id="modalPoId">
                <input type="hidden" id="modalRole">
                <input type="hidden" id="modalStatus">
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-primary" id="confirmPinBtn">
                    <i class="ri-check-line me-1"></i> Konfirmasi
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="invoiceProofModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 bg-gradient-primary text-white">
                <h5 class="modal-title">
                    <i class="ri-image-line me-2"></i>
                    <span id="invoiceProofModalTitle">Upload Bukti Invoice & Barang</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                
                <!-- PREVIEW MODE -->
            <div id="invoiceProofPreview" style="{{ $po->hasInvoiceProof() || $po->hasBarangProof() ? '' : 'display: none;' }}">
                <div class="row">
                    <!-- Bukti Invoice Preview -->
                    <div class="col-md-6">
                        <div class="card border">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="ri-file-text-line me-1"></i>
                                    Bukti Invoice
                                </h6>
                                @php
                                    $invoiceProofs = $po->invoiceProofs;
                                @endphp
                                @if($invoiceProofs->count() > 0)
                                    <span class="badge bg-primary">{{ $invoiceProofs->count() }} File</span>
                                @endif
                            </div>
                            <div class="card-body">
                                @if($invoiceProofs->count() > 0)
                                    <!-- Carousel untuk multiple bukti -->
                                    @if($invoiceProofs->count() > 1)
                                    <div id="invoiceCarousel" class="carousel slide mb-3" data-bs-ride="false">
                                        <div class="carousel-indicators">
                                            @foreach($invoiceProofs as $index => $proof)
                                            <button type="button" data-bs-target="#invoiceCarousel" data-bs-slide-to="{{ $index }}" 
                                                class="{{ $index == 0 ? 'active' : '' }}" 
                                                aria-label="Slide {{ $index + 1 }}"></button>
                                            @endforeach
                                        </div>
                                        <div class="carousel-inner rounded">
                                            @foreach($invoiceProofs as $index => $proof)
                                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                <div class="text-center bg-light p-3 rounded" style="min-height: 350px; display: flex; align-items: center; justify-content: center;">
                                                    @if(in_array($proof->file_type, ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']))
                                                        <img src="{{ asset('storage/' . $proof->file_path) }}" 
                                                            class="img-fluid rounded shadow-sm" 
                                                            style="max-height: 350px; max-width: 100%; object-fit: contain;"
                                                            alt="Bukti Invoice {{ $index + 1 }}">
                                                    @elseif($proof->file_type == 'application/pdf')
                                                        <div class="text-center">
                                                            <i class="ri-file-pdf-line text-danger mb-3" style="font-size: 5rem;"></i>
                                                            <h6 class="mb-2">{{ $proof->file_name }}</h6>
                                                            <p class="text-muted mb-3">{{ $proof->file_size_formatted }}</p>
                                                            <a href="{{ asset('storage/' . $proof->file_path) }}" 
                                                            target="_blank" 
                                                            class="btn btn-sm btn-primary">
                                                                <i class="ri-eye-line me-1"></i> Lihat PDF
                                                            </a>
                                                        </div>
                                                    @else
                                                        <div class="text-center">
                                                            <i class="ri-file-line text-secondary mb-3" style="font-size: 5rem;"></i>
                                                            <h6 class="mb-2">{{ $proof->file_name }}</h6>
                                                            <p class="text-muted">{{ $proof->file_size_formatted }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-75 rounded mx-2 mb-2">
                                                    <small class="text-white">File {{ $index + 1 }} dari {{ $invoiceProofs->count() }}</small>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        <button class="carousel-control-prev" type="button" data-bs-target="#invoiceCarousel" data-bs-slide="prev">
                                            <span class="bg-dark rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="ri-arrow-left-s-line text-white fs-4"></i>
                                            </span>
                                            <span class="visually-hidden">Previous</span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#invoiceCarousel" data-bs-slide="next">
                                            <span class="bg-dark rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="ri-arrow-right-s-line text-white fs-4"></i>
                                            </span>
                                            <span class="visually-hidden">Next</span>
                                        </button>
                                    </div>
                                    @else
                                    <!-- Single file -->
                                    @php $proof = $invoiceProofs->first(); @endphp
                                    <div class="text-center mb-3 bg-light p-3 rounded" style="min-height: 350px; display: flex; align-items: center; justify-content: center;">
                                        @if(in_array($proof->file_type, ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']))
                                            <img src="{{ asset('storage/' . $proof->file_path) }}" 
                                                class="img-fluid rounded shadow-sm border" 
                                                style="max-height: 350px; max-width: 100%; object-fit: contain;"
                                                alt="Bukti Invoice">
                                        @elseif($proof->file_type == 'application/pdf')
                                            <div class="text-center">
                                                <i class="ri-file-pdf-line text-danger mb-3" style="font-size: 5rem;"></i>
                                                <h6 class="mb-2">{{ $proof->file_name }}</h6>
                                                <p class="text-muted mb-3">{{ $proof->file_size_formatted }}</p>
                                                <a href="{{ asset('storage/' . $proof->file_path) }}" 
                                                target="_blank" 
                                                class="btn btn-sm btn-primary">
                                                    <i class="ri-eye-line me-1"></i> Lihat PDF
                                                </a>
                                            </div>
                                        @else
                                            <div class="text-center">
                                                <i class="ri-file-line text-secondary mb-3" style="font-size: 5rem;"></i>
                                                <h6 class="mb-2">{{ $proof->file_name }}</h6>
                                                <p class="text-muted">{{ $proof->file_size_formatted }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    @endif
                                    
                                    <!-- File List & Actions -->
                                    <div class="mt-3">
                                        <div class="accordion" id="invoiceAccordion">
                                            <div class="accordion-item border-0">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#invoiceFileList">
                                                        <i class="ri-file-list-line me-2"></i>
                                                        <strong>Daftar File ({{ $invoiceProofs->count() }})</strong>
                                                    </button>
                                                </h2>
                                                <div id="invoiceFileList" class="accordion-collapse collapse" data-bs-parent="#invoiceAccordion">
                                                    <div class="accordion-body p-0">
                                                        <div class="list-group list-group-flush">
                                                            @foreach($invoiceProofs as $index => $proof)
                                                            <div class="list-group-item">
                                                                <div class="d-flex justify-content-between align-items-start">
                                                                    <div class="flex-grow-1">
                                                                        <div class="d-flex align-items-start mb-2">
                                                                            <div class="me-2">
                                                                                @if($proof->file_type == 'application/pdf')
                                                                                    <i class="ri-file-pdf-line text-danger fs-4"></i>
                                                                                @elseif(in_array($proof->file_type, ['image/jpeg', 'image/jpg', 'image/png']))
                                                                                    <i class="ri-image-line text-success fs-4"></i>
                                                                                @else
                                                                                    <i class="ri-file-line text-secondary fs-4"></i>
                                                                                @endif
                                                                            </div>
                                                                            <div class="flex-grow-1">
                                                                                <h6 class="mb-1">
                                                                                    <span class="badge bg-secondary me-2">#{{ $index + 1 }}</span>
                                                                                    {{ Str::limit($proof->file_name, 40) }}
                                                                                </h6>
                                                                                <div class="small text-muted">
                                                                                    <div class="mb-1">
                                                                                        <i class="ri-hard-drive-line me-1"></i>
                                                                                        <strong>Ukuran:</strong> {{ $proof->file_size_formatted }}
                                                                                    </div>
                                                                                    <div class="mb-1">
                                                                                        <i class="ri-calendar-line me-1"></i>
                                                                                        <strong>Diupload:</strong> {{ $proof->tanggal_upload->format('d/m/Y H:i') }}
                                                                                    </div>
                                                                                    @if($proof->karyawan)
                                                                                    <div>
                                                                                        <i class="ri-user-line me-1"></i>
                                                                                        <strong>Oleh:</strong> {{ $proof->karyawan->nama_lengkap }}
                                                                                    </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="btn-group-vertical btn-group-sm ms-2">
                                                                        @if($invoiceProofs->count() > 1)
                                                                        <button type="button" 
                                                                                class="btn btn-sm btn-outline-primary"
                                                                                onclick="showInCarousel('invoiceCarousel', {{ $index }})">
                                                                            <i class="ri-eye-line me-1"></i> Lihat
                                                                        </button>
                                                                        @endif
                                                                        <a href="{{ asset('storage/' . $proof->file_path) }}" 
                                                                        target="_blank" 
                                                                        class="btn btn-sm btn-outline-success">
                                                                            <i class="ri-download-line me-1"></i> Download
                                                                        </a>
                                                                        <button type="button" 
                                                                                class="btn btn-sm btn-outline-warning" 
                                                                                onclick="replaceProof('invoice', '{{ $proof->id_po_proof }}')">
                                                                            <i class="ri-refresh-line me-1"></i> Ganti
                                                                        </button>
                                                                        <button type="button" 
                                                                                class="btn btn-sm btn-outline-danger" 
                                                                                onclick="confirmDeleteProof('invoice', '{{ $proof->id_po_proof }}')">
                                                                            <i class="ri-delete-bin-line me-1"></i> Hapus
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="mt-3 d-flex gap-2 justify-content-center">
                                        <button type="button" 
                                                class="btn btn-sm btn-warning" 
                                                onclick="changeProof('invoice')">
                                            <i class="ri-add-line me-1"></i> Tambah Bukti Invoice
                                        </button>
                                        @if($invoiceProofs->count() > 1)
                                        <button type="button" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="confirmDeleteProof('invoice')">
                                            <i class="ri-delete-bin-line me-1"></i> Hapus Semua
                                        </button>
                                        @endif
                                    </div>
                                @else
                                    <!-- No files -->
                                    <div class="text-center py-5 text-muted">
                                        <i class="ri-file-forbid-line" style="font-size: 3rem;"></i>
                                        <p class="mt-3 mb-0 fw-semibold">Belum ada bukti invoice</p>
                                        <p class="text-muted small mb-3">Upload bukti invoice untuk melengkapi dokumentasi PO</p>
                                        <button type="button" 
                                                class="btn btn-sm btn-primary" 
                                                onclick="changeProof('invoice')">
                                            <i class="ri-upload-line me-1"></i> Upload Sekarang
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Bukti Barang Preview -->
                    <div class="col-md-6">
                        <div class="card border">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="ri-box-3-line me-1"></i>
                                    Bukti Barang
                                </h6>
                                @php
                                    $barangProofs = $po->barangProofs;
                                @endphp
                                @if($barangProofs->count() > 0)
                                    <span class="badge bg-success">{{ $barangProofs->count() }} File</span>
                                @endif
                            </div>
                            <div class="card-body">
                                @if($barangProofs->count() > 0)
                                    <!-- Carousel untuk multiple bukti -->
                                    @if($barangProofs->count() > 1)
                                    <div id="barangCarousel" class="carousel slide mb-3" data-bs-ride="false">
                                        <div class="carousel-indicators">
                                            @foreach($barangProofs as $index => $proof)
                                            <button type="button" data-bs-target="#barangCarousel" data-bs-slide-to="{{ $index }}" 
                                                class="{{ $index == 0 ? 'active' : '' }}" 
                                                aria-label="Slide {{ $index + 1 }}"></button>
                                            @endforeach
                                        </div>
                                        <div class="carousel-inner rounded">
                                            @foreach($barangProofs as $index => $proof)
                                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                <div class="text-center bg-light p-3 rounded" style="min-height: 350px; display: flex; align-items: center; justify-content: center;">
                                                    @if(in_array($proof->file_type, ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']))
                                                        <img src="{{ asset('storage/' . $proof->file_path) }}" 
                                                            class="img-fluid rounded shadow-sm" 
                                                            style="max-height: 350px; max-width: 100%; object-fit: contain;"
                                                            alt="Bukti Barang {{ $index + 1 }}">
                                                    @elseif($proof->file_type == 'application/pdf')
                                                        <div class="text-center">
                                                            <i class="ri-file-pdf-line text-danger mb-3" style="font-size: 5rem;"></i>
                                                            <h6 class="mb-2">{{ $proof->file_name }}</h6>
                                                            <p class="text-muted mb-3">{{ $proof->file_size_formatted }}</p>
                                                            <a href="{{ asset('storage/' . $proof->file_path) }}" 
                                                            target="_blank" 
                                                            class="btn btn-sm btn-success">
                                                                <i class="ri-eye-line me-1"></i> Lihat PDF
                                                            </a>
                                                        </div>
                                                    @else
                                                        <div class="text-center">
                                                            <i class="ri-file-line text-secondary mb-3" style="font-size: 5rem;"></i>
                                                            <h6 class="mb-2">{{ $proof->file_name }}</h6>
                                                            <p class="text-muted">{{ $proof->file_size_formatted }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-75 rounded mx-2 mb-2">
                                                    <small class="text-white">File {{ $index + 1 }} dari {{ $barangProofs->count() }}</small>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        <button class="carousel-control-prev" type="button" data-bs-target="#barangCarousel" data-bs-slide="prev">
                                            <span class="bg-dark rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="ri-arrow-left-s-line text-white fs-4"></i>
                                            </span>
                                            <span class="visually-hidden">Previous</span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#barangCarousel" data-bs-slide="next">
                                            <span class="bg-dark rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="ri-arrow-right-s-line text-white fs-4"></i>
                                            </span>
                                            <span class="visually-hidden">Next</span>
                                        </button>
                                    </div>
                                    @else
                                    <!-- Single file -->
                                    @php $proof = $barangProofs->first(); @endphp
                                    <div class="text-center mb-3 bg-light p-3 rounded" style="min-height: 350px; display: flex; align-items: center; justify-content: center;">
                                        @if(in_array($proof->file_type, ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']))
                                            <img src="{{ asset('storage/' . $proof->file_path) }}" 
                                                class="img-fluid rounded shadow-sm border" 
                                                style="max-height: 350px; max-width: 100%; object-fit: contain;"
                                                alt="Bukti Barang">
                                        @elseif($proof->file_type == 'application/pdf')
                                            <div class="text-center">
                                                <i class="ri-file-pdf-line text-danger mb-3" style="font-size: 5rem;"></i>
                                                <h6 class="mb-2">{{ $proof->file_name }}</h6>
                                                <p class="text-muted mb-3">{{ $proof->file_size_formatted }}</p>
                                                <a href="{{ asset('storage/' . $proof->file_path) }}" 
                                                target="_blank" 
                                                class="btn btn-sm btn-success">
                                                    <i class="ri-eye-line me-1"></i> Lihat PDF
                                                </a>
                                            </div>
                                        @else
                                            <div class="text-center">
                                                <i class="ri-file-line text-secondary mb-3" style="font-size: 5rem;"></i>
                                                <h6 class="mb-2">{{ $proof->file_name }}</h6>
                                                <p class="text-muted">{{ $proof->file_size_formatted }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    @endif
                                    
                                    <!-- File List & Actions -->
                                    <div class="mt-3">
                                        <div class="accordion" id="barangAccordion">
                                            <div class="accordion-item border-0">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#barangFileList">
                                                        <i class="ri-file-list-line me-2"></i>
                                                        <strong>Daftar File ({{ $barangProofs->count() }})</strong>
                                                    </button>
                                                </h2>
                                                <div id="barangFileList" class="accordion-collapse collapse" data-bs-parent="#barangAccordion">
                                                    <div class="accordion-body p-0">
                                                        <div class="list-group list-group-flush">
                                                            @foreach($barangProofs as $index => $proof)
                                                            <div class="list-group-item">
                                                                <div class="d-flex justify-content-between align-items-start">
                                                                    <div class="flex-grow-1">
                                                                        <div class="d-flex align-items-start mb-2">
                                                                            <div class="me-2">
                                                                                @if($proof->file_type == 'application/pdf')
                                                                                    <i class="ri-file-pdf-line text-danger fs-4"></i>
                                                                                @elseif(in_array($proof->file_type, ['image/jpeg', 'image/jpg', 'image/png']))
                                                                                    <i class="ri-image-line text-success fs-4"></i>
                                                                                @else
                                                                                    <i class="ri-file-line text-secondary fs-4"></i>
                                                                                @endif
                                                                            </div>
                                                                            <div class="flex-grow-1">
                                                                                <h6 class="mb-1">
                                                                                    <span class="badge bg-secondary me-2">#{{ $index + 1 }}</span>
                                                                                    {{ Str::limit($proof->file_name, 40) }}
                                                                                </h6>
                                                                                <div class="small text-muted">
                                                                                    <div class="mb-1">
                                                                                        <i class="ri-hard-drive-line me-1"></i>
                                                                                        <strong>Ukuran:</strong> {{ $proof->file_size_formatted }}
                                                                                    </div>
                                                                                    <div class="mb-1">
                                                                                        <i class="ri-calendar-line me-1"></i>
                                                                                        <strong>Diupload:</strong> {{ $proof->tanggal_upload->format('d/m/Y H:i') }}
                                                                                    </div>
                                                                                    @if($proof->karyawan)
                                                                                    <div>
                                                                                        <i class="ri-user-line me-1"></i>
                                                                                        <strong>Oleh:</strong> {{ $proof->karyawan->nama_lengkap }}
                                                                                    </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="btn-group-vertical btn-group-sm ms-2">
                                                                        @if($barangProofs->count() > 1)
                                                                        <button type="button" 
                                                                                class="btn btn-sm btn-outline-success"
                                                                                onclick="showInCarousel('barangCarousel', {{ $index }})">
                                                                            <i class="ri-eye-line me-1"></i> Lihat
                                                                        </button>
                                                                        @endif
                                                                        <a href="{{ asset('storage/' . $proof->file_path) }}" 
                                                                        target="_blank" 
                                                                        class="btn btn-sm btn-outline-primary">
                                                                            <i class="ri-download-line me-1"></i> Download
                                                                        </a>
                                                                        <button type="button" 
                                                                                class="btn btn-sm btn-outline-danger" 
                                                                                onclick="confirmDeleteProof('barang', '{{ $proof->id_po_proof }}')">
                                                                            <i class="ri-delete-bin-line me-1"></i> Hapus
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="mt-3 d-flex gap-2 justify-content-center">
                                        <button type="button" 
                                                class="btn btn-sm btn-warning" 
                                                onclick="changeProof('barang')">
                                            <i class="ri-add-line me-1"></i> Tambah Bukti Barang
                                        </button>
                                        @if($barangProofs->count() > 1)
                                        <button type="button" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="confirmDeleteProof('barang')">
                                            <i class="ri-delete-bin-line me-1"></i> Hapus Semua
                                        </button>
                                        @endif
                                    </div>
                                @else
                                    <!-- No files -->
                                    <div class="text-center py-5 text-muted">
                                        <i class="ri-box-3-line" style="font-size: 3rem;"></i>
                                        <p class="mt-3 mb-0 fw-semibold">Belum ada bukti barang</p>
                                        <p class="text-muted small mb-3">Upload bukti barang untuk melengkapi dokumentasi PO</p>
                                        <button type="button" 
                                                class="btn btn-sm btn-success" 
                                                onclick="changeProof('barang')">
                                            <i class="ri-upload-line me-1"></i> Upload Sekarang
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                <!-- UPLOAD FORM MODE -->
                <div id="invoiceProofUploadForm" style="{{ $po->hasInvoiceProof() && $po->hasBarangProof() ? 'display: none;' : '' }}">
                    <form id="uploadInvoiceForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="uploadType" name="upload_type" value="both">
                        
                        <div class="row">
                            <!-- Upload Bukti Invoice -->
                            <div class="col-md-6">
                                <div class="card border mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="ri-file-text-line me-1"></i>
                                            Upload Bukti Invoice
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="upload-area border-2 border-dashed rounded-3 p-4 text-center position-relative" 
                                             id="uploadAreaInvoice"
                                             onclick="document.getElementById('buktiInvoiceInput').click()"
                                             style="cursor: pointer; border-color: #dee2e6; transition: all 0.3s ease;">
                                            <div id="uploadPromptInvoice">
                                                <i class="ri-upload-cloud-2-line text-primary mb-2" style="font-size: 3rem;"></i>
                                                <h6 class="mb-2">Klik atau Drag & Drop</h6>
                                                <p class="text-muted mb-0 small">
                                                    JPG, PNG, PDF (Max 5MB)
                                                </p>
                                            </div>
                                            
                                            <div id="filePreviewThumbInvoice" style="display: none;">
                                                <div class="d-flex align-items-center gap-2 p-2 bg-light rounded">
                                                    <div class="file-icon">
                                                        <i class="ri-file-line text-primary" style="font-size: 2rem;"></i>
                                                    </div>
                                                    <div class="file-info text-start flex-grow-1">
                                                        <div class="file-name fw-semibold text-truncate small"></div>
                                                        <small class="file-size text-muted"></small>
                                                    </div>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger rounded-circle" 
                                                            onclick="clearFileInput(event, 'invoice')"
                                                            style="width: 28px; height: 28px; padding: 0;">
                                                        <i class="ri-close-line"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <input type="file" 
                                                   id="buktiInvoiceInput" 
                                                   name="bukti_invoice" 
                                                   accept="image/jpeg,image/jpg,image/png,application/pdf"
                                                   style="display: none;"
                                                   onchange="previewFile(this, 'invoice')">
                                        </div>
                                        <div class="invalid-feedback d-block" id="buktiInvoiceError" style="display: none !important;"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Upload Bukti Barang -->
                            <div class="col-md-6">
                                <div class="card border mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="ri-box-3-line me-1"></i>
                                            Upload Bukti Barang
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="upload-area border-2 border-dashed rounded-3 p-4 text-center position-relative" 
                                             id="uploadAreaBarang"
                                             onclick="document.getElementById('buktiBarangInput').click()"
                                             style="cursor: pointer; border-color: #dee2e6; transition: all 0.3s ease;">
                                            <div id="uploadPromptBarang">
                                                <i class="ri-upload-cloud-2-line text-primary mb-2" style="font-size: 3rem;"></i>
                                                <h6 class="mb-2">Klik atau Drag & Drop</h6>
                                                <p class="text-muted mb-0 small">
                                                    JPG, PNG, PDF (Max 5MB)
                                                </p>
                                            </div>
                                            
                                            <div id="filePreviewThumbBarang" style="display: none;">
                                                <div class="d-flex align-items-center gap-2 p-2 bg-light rounded">
                                                    <div class="file-icon">
                                                        <i class="ri-file-line text-primary" style="font-size: 2rem;"></i>
                                                    </div>
                                                    <div class="file-info text-start flex-grow-1">
                                                        <div class="file-name fw-semibold text-truncate small"></div>
                                                        <small class="file-size text-muted"></small>
                                                    </div>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger rounded-circle" 
                                                            onclick="clearFileInput(event, 'barang')"
                                                            style="width: 28px; height: 28px; padding: 0;">
                                                        <i class="ri-close-line"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <input type="file" 
                                                   id="buktiBarangInput" 
                                                   name="bukti_barang" 
                                                   accept="image/jpeg,image/jpg,image/png,application/pdf"
                                                   style="display: none;"
                                                   onchange="previewFile(this, 'barang')">
                                        </div>
                                        <div class="invalid-feedback d-block" id="buktiBarangError" style="display: none !important;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PIN Input -->
                        <div class="card border">
                            <div class="card-body">
                                <label class="form-label fw-semibold">
                                    <i class="ri-lock-password-line me-1"></i>
                                    PIN Karyawan <span class="text-danger">*</span>
                                </label>
                                
                                <div class="otp-input-container d-flex justify-content-center gap-2 mb-2">
                                    <input type="password" 
                                        class="otp-input-invoice form-control text-center" 
                                        maxlength="1" 
                                        pattern="[0-9]" 
                                        inputmode="numeric"
                                        data-index="0"
                                        autocomplete="off">
                                    <input type="password" 
                                        class="otp-input-invoice form-control text-center" 
                                        maxlength="1" 
                                        pattern="[0-9]" 
                                        inputmode="numeric"
                                        data-index="1"
                                        autocomplete="off">
                                    <input type="password" 
                                        class="otp-input-invoice form-control text-center" 
                                        maxlength="1" 
                                        pattern="[0-9]" 
                                        inputmode="numeric"
                                        data-index="2"
                                        autocomplete="off">
                                    <input type="password" 
                                        class="otp-input-invoice form-control text-center" 
                                        maxlength="1" 
                                        pattern="[0-9]" 
                                        inputmode="numeric"
                                        data-index="3"
                                        autocomplete="off">
                                    <input type="password" 
                                        class="otp-input-invoice form-control text-center" 
                                        maxlength="1" 
                                        pattern="[0-9]" 
                                        inputmode="numeric"
                                        data-index="4"
                                        autocomplete="off">
                                    <input type="password" 
                                        class="otp-input-invoice form-control text-center" 
                                        maxlength="1" 
                                        pattern="[0-9]" 
                                        inputmode="numeric"
                                        data-index="5"
                                        autocomplete="off">
                                </div>
                                
                                <input type="hidden" id="pinInput" name="pin">
                                
                                <div class="invalid-feedback d-block" id="pinError" style="display: none !important;"></div>
                                <small class="text-muted d-block text-center">
                                    <i class="ri-information-line me-1"></i>
                                    Masukkan 6 digit PIN untuk verifikasi
                                </small>
                            </div>
                        </div>

                        <div class="alert alert-info d-flex align-items-start mt-3">
                            <i class="ri-information-line me-2 mt-1"></i>
                            <small>
                                <strong>Catatan:</strong> Anda dapat mengupload bukti invoice dan bukti barang sekaligus atau secara terpisah.
                            </small>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i> Tutup
                </button>
                <button type="button" 
                        class="btn btn-primary" 
                        id="btnUploadProof"
                        onclick="directUploadProof(event)"
                        style="{{ $po->bukti_invoice && $po->bukti_barang ? 'display: none;' : '' }}">
                    <i class="ri-upload-line me-1"></i> Upload Bukti
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* Timeline Styles */
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    .timeline-item {
        position: relative;
    }
    .timeline-marker {
        position: absolute;
        left: -30px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #0d6efd;
    }
    .timeline-item:before {
        content: '';
        position: absolute;
        left: -24px;
        top: 17px;
        bottom: -17px;
        width: 2px;
        background: #dee2e6;
    }
    .timeline-item:last-child:before {
        display: none;
    }
    .timeline-content {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border-left: 3px solid #0d6efd;
    }
    
    .avatar-sm {
        width: 3rem;
        height: 3rem;
        display: flex;
        align-items-center;
        justify-content: center;
    }
    
    .bg-soft-primary {
        background-color: rgba(13, 110, 253, 0.1);
    }
    
    /* Animation for confirmation card */
    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .border-warning {
        animation: slideInDown 0.5s ease-out;
    }

    /* OTP-style PIN Input Styles */
    .otp-container {
        max-width: 400px;
        margin: 0 auto;
    }

    .otp-input {
        width: 50px;
        height: 60px;
        font-size: 24px;
        font-weight: bold;
        border: 2px solid #dee2e6;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .otp-input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        outline: none;
        transform: scale(1.05);
    }

    .otp-input.filled {
        background-color: #f8f9fa;
        border-color: #198754;
    }

    .otp-input.error {
        border-color: #dc3545;
        animation: shake 0.5s;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    /* Modal Enhancements */
    #pinModal .modal-content {
        border-radius: 15px;
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    }

    #pinModal .modal-header {
        padding: 1.5rem;
    }

    #pinModal .modal-title {
        color: #0d6efd;
        font-weight: 600;
    }

    /* Loading State */
    .btn-loading {
        position: relative;
        pointer-events: none;
        opacity: 0.7;
    }

    .btn-loading::after {
        content: "";
        position: absolute;
        width: 16px;
        height: 16px;
        top: 50%;
        left: 50%;
        margin-left: -8px;
        margin-top: -8px;
        border: 2px solid #ffffff;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spinner 0.6s linear infinite;
    }

    @keyframes spinner {
        to { transform: rotate(360deg); }
    }

    .upload-area {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        transition: all 0.3s ease;
    }
    
    .upload-area:hover {
        border-color: #0d6efd !important;
        background: linear-gradient(135deg, #e7f3ff 0%, #f8f9fa 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
    }
    
    .upload-area.drag-over {
        border-color: #0d6efd !important;
        background: linear-gradient(135deg, #cfe2ff 0%, #e7f3ff 100%);
        transform: scale(1.02);
    }

    /* OTP Input Styles untuk Invoice */
    .otp-container-invoice {
        max-width: 380px;
        margin: 0 auto;
    }

    .otp-input-invoice {
        width: 55px;
        height: 65px;
        font-size: 24px;
        font-weight: bold;
        border: 2px solid #dee2e6;
        border-radius: 12px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .otp-input-invoice:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        outline: none;
        transform: scale(1.08);
    }

    .otp-input-invoice.filled {
        background-color: #f8f9fa;
        border-color: #198754;
        color: #198754;
    }

    .otp-input-invoice.error {
        border-color: #dc3545;
        animation: shake 0.5s;
        background-color: #fff5f5;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-8px); }
        50% { transform: translateX(8px); }
        75% { transform: translateX(-8px); }
    }

    /* Modal Enhancements */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    }

    #invoiceProofModal .modal-content {
        border-radius: 16px;
        overflow: hidden;
    }

    #invoicePinModal .modal-content {
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }

    /* Loading State */
    .btn-loading {
        position: relative;
        pointer-events: none;
        opacity: 0.7;
    }

    .btn-loading::after {
        content: "";
        position: absolute;
        width: 16px;
        height: 16px;
        top: 50%;
        left: 50%;
        margin-left: -8px;
        margin-top: -8px;
        border: 2px solid #ffffff;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spinner 0.6s linear infinite;
    }

    @keyframes spinner {
        to { transform: rotate(360deg); }
    }

    /* File Preview Animation */
    #filePreviewThumb {
        animation: slideInDown 0.3s ease-out;
    }

    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    /* OTP Input untuk Invoice Modal - SEPARATED */
    .otp-input-invoice {
        width: 50px;
        height: 60px;
        font-size: 24px;
        font-weight: bold;
        border: 2px solid #dee2e6;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .otp-input-invoice:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        outline: none;
        transform: scale(1.05);
    }

    .otp-input-invoice.filled {
        background-color: #f8f9fa;
        border-color: #198754;
    }

    .otp-input-invoice.error {
        border-color: #dc3545;
        animation: shake 0.5s;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ============================================
    // OTP-STYLE PIN INPUT HANDLER
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        const otpInputs = document.querySelectorAll('.otp-input');
        
        otpInputs.forEach((input, index) => {
            // Handle input
            input.addEventListener('input', function(e) {
                const value = e.target.value;
                
                // Only allow numbers
                if (!/^\d$/.test(value)) {
                    e.target.value = '';
                    return;
                }
                
                // Add filled class
                e.target.classList.add('filled');
                
                // Move to next input
                if (value && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
                
                // Check if all inputs are filled
                checkPinComplete();
            });
            
            // Handle backspace
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace') {
                    if (!e.target.value && index > 0) {
                        // Move to previous input if current is empty
                        otpInputs[index - 1].focus();
                        otpInputs[index - 1].value = '';
                        otpInputs[index - 1].classList.remove('filled', 'error');
                    } else {
                        // Clear current input
                        e.target.value = '';
                        e.target.classList.remove('filled', 'error');
                    }
                } else if (e.key === 'ArrowLeft' && index > 0) {
                    e.preventDefault();
                    otpInputs[index - 1].focus();
                } else if (e.key === 'ArrowRight' && index < otpInputs.length - 1) {
                    e.preventDefault();
                    otpInputs[index + 1].focus();
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    const confirmBtn = document.getElementById('confirmPinBtn');
                    if (!confirmBtn.disabled) {
                        confirmBtn.click();
                    }
                }
            });
            
            // Handle paste
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').trim();
                
                if (/^\d{6}$/.test(pastedData)) {
                    pastedData.split('').forEach((char, i) => {
                        if (otpInputs[i]) {
                            otpInputs[i].value = char;
                            otpInputs[i].classList.add('filled');
                        }
                    });
                    otpInputs[5].focus();
                    checkPinComplete();
                }
            });
            
            // Select all on focus
            input.addEventListener('focus', function() {
                this.select();
            });
        });
        
        function checkPinComplete() {
            const allFilled = Array.from(otpInputs).every(input => input.value !== '');
            const confirmBtn = document.getElementById('confirmPinBtn');
            
            if (allFilled) {
                confirmBtn.disabled = false;
                confirmBtn.classList.remove('disabled');
            } else {
                confirmBtn.disabled = true;
                confirmBtn.classList.add('disabled');
            }
        }

        // Auto dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (!alert.classList.contains('alert-warning') && !alert.classList.contains('alert-permanent')) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            });
        }, 5000);
    });

    function markAsReceived(poId) {
        const modal = new bootstrap.Modal(document.getElementById('pinModal'));
        
        document.getElementById('modalAction').value = 'mark_received';
        document.getElementById('modalPoId').value = poId;
        document.getElementById('pinModalTitle').innerHTML = 
            '<i class="ri-lock-password-line me-2"></i>Tandai Barang Diterima';
        document.getElementById('pinModalDescription').textContent = 
            'Masukkan PIN untuk mengkonfirmasi bahwa barang dari supplier sudah diterima';
        
        // Show notes container for shipping confirmation
        const notesContainer = document.getElementById('notesContainer');
        notesContainer.style.display = 'block';
        document.getElementById('modalNotes').value = '';
        document.getElementById('modalNotes').placeholder = 'Catatan penerimaan (opsional)';
        
        document.getElementById('confirmPinBtn').className = 'btn btn-success';
        document.getElementById('confirmPinBtn').innerHTML = 
            '<i class="ri-checkbox-circle-line me-1"></i> Konfirmasi Penerimaan';
        
        resetPinInputs();
        modal.show();
    }

    function handleMarkReceived(poId, pin, notes, btn, originalHTML) {
        fetch(`/po/po/${poId}/mark-received`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ 
                pin: pin,
                catatan: notes 
            })
        })
        .then(response => response.json())
        .then(data => {
            btn.classList.remove('btn-loading');
            btn.disabled = false;
            btn.innerHTML = originalHTML;
            
            if (data.message) {
                bootstrap.Modal.getInstance(document.getElementById('pinModal')).hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                showPinError();
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.error || 'PIN tidak valid atau terjadi kesalahan',
                    confirmButtonText: 'Coba Lagi'
                });
                resetPinInputs();
            }
        })
        .catch(error => {
            btn.classList.remove('btn-loading');
            btn.disabled = false;
            btn.innerHTML = originalHTML;
            showPinError();
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan sistem',
                confirmButtonText: 'OK'
            });
            console.error('Mark received error:', error);
        });
    }

    // ============================================
    // PIN UTILITY FUNCTIONS
    // ============================================
    function getPinValue() {
        const otpInputs = document.querySelectorAll('.otp-input');
        return Array.from(otpInputs).map(input => input.value).join('');
    }

    function resetPinInputs() {
        const otpInputs = document.querySelectorAll('.otp-input');
        otpInputs.forEach(input => {
            input.value = '';
            input.classList.remove('filled', 'error');
        });
        document.getElementById('confirmPinBtn').disabled = true;
        document.getElementById('confirmPinBtn').classList.add('disabled');
        
        // Focus first input
        setTimeout(() => {
            if (otpInputs[0]) otpInputs[0].focus();
        }, 100);
    }

    function showPinError() {
        const otpInputs = document.querySelectorAll('.otp-input');
        otpInputs.forEach(input => {
            input.classList.add('error');
        });
        
        setTimeout(() => {
            otpInputs.forEach(input => {
                input.classList.remove('error');
            });
        }, 500);
    }

    // ============================================
    // MODAL SHOW FUNCTIONS
    // ============================================
    function showApprovalModal(role, status) {
        const modal = new bootstrap.Modal(document.getElementById('pinModal'));
        
        // Set modal context
        document.getElementById('modalAction').value = 'approval';
        document.getElementById('modalRole').value = role;
        document.getElementById('modalStatus').value = status;
        document.getElementById('modalPoId').value = '{{ $po->id_po }}';
        
        // Update modal title and description
        const actionText = status === 'disetujui' ? 'Setujui' : 'Tolak';
        
        // Dynamic role text - only for kepala_gudang role
        const poType = '{{ $po->tipe_po }}';
        const roleText = role === 'kepala_gudang' 
            ? (poType === 'internal' ? 'Gudang' : 'Kepala Gudang')
            : 'Kasir';
        
        const titleText = `${actionText} Purchase Order - ${roleText}`;
        const descText = status === 'disetujui' 
            ? `Masukkan PIN untuk menyetujui PO ini sebagai ${roleText}` 
            : `Masukkan PIN untuk menolak PO ini sebagai ${roleText}`;
        
        document.getElementById('pinModalTitle').innerHTML = 
            `<i class="ri-lock-password-line me-2"></i>${titleText}`;
        document.getElementById('pinModalDescription').textContent = descText;
        
        // Show/hide notes container
        const notesContainer = document.getElementById('notesContainer');
        notesContainer.style.display = 'block';
        document.getElementById('modalNotes').value = '';
        document.getElementById('modalNotes').placeholder = 
            status === 'disetujui' ? 'Catatan persetujuan (opsional)' : 'Alasan penolakan (opsional)';
        
        // Update confirm button
        const confirmBtn = document.getElementById('confirmPinBtn');
        confirmBtn.className = status === 'disetujui' ? 'btn btn-success' : 'btn btn-danger';
        confirmBtn.innerHTML = status === 'disetujui' 
            ? '<i class="ri-check-line me-1"></i> Setujui' 
            : '<i class="ri-close-line me-1"></i> Tolak';
        
        resetPinInputs();
        modal.show();
    }

    function submitPO(poId) {
        Swal.fire({
            title: 'Submit Purchase Order?',
            text: 'PO akan dikirim untuk persetujuan kepala gudang',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Submit',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/po/${poId}/submit`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(res => {
                    if (res.message) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', res.error || 'Terjadi kesalahan', 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Error', 'Gagal menghubungi server', 'error');
                });
            }
        });
    }


    function sendToSupplier(poId) {
        const modal = new bootstrap.Modal(document.getElementById('pinModal'));
        
        document.getElementById('modalAction').value = 'send_to_supplier';
        document.getElementById('modalPoId').value = poId;
        document.getElementById('pinModalTitle').innerHTML = 
            '<i class="ri-lock-password-line me-2"></i>Kirim ke Supplier';
        document.getElementById('pinModalDescription').textContent = 
            'Masukkan PIN untuk mengirim PO ke supplier';
        
        document.getElementById('notesContainer').style.display = 'none';
        document.getElementById('confirmPinBtn').className = 'btn btn-info';
        document.getElementById('confirmPinBtn').innerHTML = 
            '<i class="ri-truck-line me-1"></i> Kirim';
        
        resetPinInputs();
        modal.show();
    }

    function deletePO(poId) {
        Swal.fire({
            title: 'Hapus Purchase Order?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const modal = new bootstrap.Modal(document.getElementById('pinModal'));
                
                document.getElementById('modalAction').value = 'delete';
                document.getElementById('modalPoId').value = poId;
                document.getElementById('pinModalTitle').innerHTML = 
                    '<i class="ri-lock-password-line me-2"></i>Konfirmasi Penghapusan';
                document.getElementById('pinModalDescription').textContent = 
                    'Masukkan PIN untuk menghapus PO ini';
                
                document.getElementById('notesContainer').style.display = 'none';
                document.getElementById('confirmPinBtn').className = 'btn btn-danger';
                document.getElementById('confirmPinBtn').innerHTML = 
                    '<i class="ri-delete-bin-line me-1"></i> Hapus';
                
                resetPinInputs();
                modal.show();
            }
        });
    }

    // ============================================
    // PIN CONFIRMATION HANDLER
    // ============================================
    document.getElementById('confirmPinBtn').addEventListener('click', function() {
        const pin = getPinValue();
        const action = document.getElementById('modalAction').value;
        const poId = document.getElementById('modalPoId').value;
        const role = document.getElementById('modalRole').value;
        const status = document.getElementById('modalStatus').value;
        const notes = document.getElementById('modalNotes').value;
        const btn = this;
        
        if (pin.length !== 6) {
            showPinError();
            Swal.fire({
                icon: 'error',
                title: 'PIN Tidak Lengkap',
                text: 'Silakan masukkan 6 digit PIN',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }
        
        // Add loading state
        btn.classList.add('btn-loading');
        btn.disabled = true;
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
        
        // Route actions
        if (action === 'approval') {
            handleApproval(poId, role, status, notes, pin, btn, originalHTML);
        } else if (action === 'submit') {
            handleSubmit(poId, pin, btn, originalHTML);
        } else if (action === 'send_to_supplier') {
            handleSendToSupplier(poId, pin, btn, originalHTML);
        } else if (action === 'delete') {
            handleDelete(poId, pin, btn, originalHTML);
        } else if (action === 'mark_received') {
            handleMarkReceived(poId, pin, notes, btn, originalHTML);
        }
    });

    // ============================================
    // ACTION HANDLERS
    // ============================================
    function handleApproval(poId, role, status, notes, pin, btn, originalHTML) {
        const endpoint = role === 'kepala_gudang' 
            ? `/po/${poId}/approve-kepala-gudang`
            : `/po/${poId}/approve-kasir`;

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                pin: pin,
                status_approval: status,
                catatan: notes
            })
        })
        .then(response => response.json())
        .then(data => {
            btn.classList.remove('btn-loading');
            btn.disabled = false;
            btn.innerHTML = originalHTML;
            
            if (data.message) {
                bootstrap.Modal.getInstance(document.getElementById('pinModal')).hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                showPinError();
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.error || 'PIN tidak valid atau terjadi kesalahan',
                    confirmButtonText: 'Coba Lagi'
                });
                resetPinInputs();
            }
        })
        .catch(error => {
            btn.classList.remove('btn-loading');
            btn.disabled = false;
            btn.innerHTML = originalHTML;
            showPinError();
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan sistem',
                confirmButtonText: 'OK'
            });
            console.error('Approval error:', error);
        });
    }

    function handleSubmit(poId, pin, btn, originalHTML) {
        fetch(`/po/${poId}/submit`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ pin: pin })
        })
        .then(response => response.json())
        .then(data => {
            btn.classList.remove('btn-loading');
            btn.disabled = false;
            btn.innerHTML = originalHTML;
            
            if (data.message) {
                bootstrap.Modal.getInstance(document.getElementById('pinModal')).hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                showPinError();
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.error || 'PIN tidak valid atau terjadi kesalahan',
                    confirmButtonText: 'Coba Lagi'
                });
                resetPinInputs();
            }
        })
        .catch(error => {
            btn.classList.remove('btn-loading');
            btn.disabled = false;
            btn.innerHTML = originalHTML;
            showPinError();
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan sistem',
                confirmButtonText: 'OK'
            });
            console.error('Submit error:', error);
        });
    }

    function handleSendToSupplier(poId, pin, btn, originalHTML) {
        fetch(`/po/${poId}/send-to-supplier`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ pin: pin })
        })
        .then(response => response.json())
        .then(data => {
            btn.classList.remove('btn-loading');
            btn.disabled = false;
            btn.innerHTML = originalHTML;
            
            if (data.message) {
                bootstrap.Modal.getInstance(document.getElementById('pinModal')).hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                showPinError();
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.error || data.errors?.pin?.[0] || 'PIN tidak valid atau terjadi kesalahan',
                    confirmButtonText: 'Coba Lagi'
                });
                resetPinInputs();
            }
        })
        .catch(error => {
            btn.classList.remove('btn-loading');
            btn.disabled = false;
            btn.innerHTML = originalHTML;
            showPinError();
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan sistem',
                confirmButtonText: 'OK'
            });
            console.error('Send to supplier error:', error);
        });
    }

    function handleDelete(poId, pin, btn, originalHTML) {
        fetch(`/po/${poId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ pin: pin })
        })
        .then(response => response.json())
        .then(data => {
            btn.classList.remove('btn-loading');
            btn.disabled = false;
            btn.innerHTML = originalHTML;
            
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('pinModal')).hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = '{{ route("po.index") }}';
                });
            } else {
                showPinError();
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message || 'PIN tidak valid atau terjadi kesalahan',
                    confirmButtonText: 'Coba Lagi'
                });
                resetPinInputs();
            }
        })
        .catch(error => {
            btn.classList.remove('btn-loading');
            btn.disabled = false;
            btn.innerHTML = originalHTML;
            showPinError();
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan sistem',
                confirmButtonText: 'OK'
            });
            console.error('Delete error:', error);
        });
    }

    // ============================================
    // KEYBOARD SHORTCUTS
    // ============================================
    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('pinModal');
        
        // Escape to close modal
        if (e.key === 'Escape' && modal.classList.contains('show')) {
            bootstrap.Modal.getInstance(modal).hide();
        }
    });

    // Clear inputs when modal is hidden
    document.getElementById('pinModal').addEventListener('hidden.bs.modal', function() {
        resetPinInputs();
        document.getElementById('modalNotes').value = '';
    });

    // Focus first input when modal is shown
    document.getElementById('pinModal').addEventListener('shown.bs.modal', function() {
        const firstInput = document.querySelector('.otp-input');
        if (firstInput) {
            firstInput.focus();
        }
    });
</script>

<script>
// ============================================
// MODAL INITIALIZATION
// ============================================

function showInvoiceProofModal() {
    const modalElement = document.getElementById('invoiceProofModal');
    const modal = new bootstrap.Modal(modalElement);
    
    const hasInvoice = {{ $po->hasInvoiceProof() ? 'true' : 'false' }};
    const hasBarang = {{ $po->hasBarangProof() ? 'true' : 'false' }};
    
    if (hasInvoice || hasBarang) {
        document.getElementById('invoiceProofModalTitle').textContent = 'Bukti Invoice & Barang';
        document.getElementById('invoiceProofPreview').style.display = 'block';
        document.getElementById('invoiceProofUploadForm').style.display = 'none';
        document.getElementById('btnUploadProof').style.display = 'none';
    } else {
        document.getElementById('invoiceProofModalTitle').textContent = 'Upload Bukti Invoice & Barang';
        document.getElementById('invoiceProofPreview').style.display = 'none';
        document.getElementById('invoiceProofUploadForm').style.display = 'block';
        document.getElementById('btnUploadProof').style.display = 'block';
        clearAllFileInputs();
        clearInvoiceOtpInputs();
    }
    
    modal.show();
}

// Tambahkan di bagian scripts
function showInCarousel(carouselId, slideIndex) {
    const carousel = document.getElementById(carouselId);
    if (carousel) {
        const bsCarousel = bootstrap.Carousel.getOrCreateInstance(carousel);
        bsCarousel.to(slideIndex);
        
        // Scroll to carousel
        carousel.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Collapse accordion to show carousel
        const accordionId = carouselId === 'invoiceCarousel' ? 'invoiceFileList' : 'barangFileList';
        const accordionElement = document.getElementById(accordionId);
        if (accordionElement && accordionElement.classList.contains('show')) {
            const bsCollapse = bootstrap.Collapse.getInstance(accordionElement);
            if (bsCollapse) {
                bsCollapse.hide();
            }
        }
    }
}

function changeProof(type) {
    document.getElementById('invoiceProofPreview').style.display = 'none';
    document.getElementById('invoiceProofUploadForm').style.display = 'block';
    document.getElementById('btnUploadProof').style.display = 'block';
    
    // Set upload type
    document.getElementById('uploadType').value = type;
    
    // ✅ Set mode sebagai "add" (tambah), bukan replace
    let uploadModeInput = document.getElementById('uploadMode');
    if (!uploadModeInput) {
        uploadModeInput = document.createElement('input');
        uploadModeInput.type = 'hidden';
        uploadModeInput.id = 'uploadMode';
        uploadModeInput.name = 'replace_mode';
        document.getElementById('uploadInvoiceForm').appendChild(uploadModeInput);
    }
    uploadModeInput.value = 'false'; // ✅ Mode ADD
    
    const title = type === 'invoice' ? 'Tambah Bukti Invoice' : 
                  type === 'barang' ? 'Tambah Bukti Barang' : 
                  'Upload Bukti';
    document.getElementById('invoiceProofModalTitle').textContent = title;
    
    clearAllFileInputs();
    clearInvoiceOtpInputs();
}

// ============================================
// OTP INPUT INITIALIZATION
// ============================================
function initInvoiceOtpInputs() {
    const invoiceModal = document.getElementById('invoiceProofModal');
    if (!invoiceModal) return;
    
    const otpInputs = invoiceModal.querySelectorAll('.otp-input-invoice');
    if (otpInputs.length === 0) return;
    
    // Remove existing listeners
    otpInputs.forEach((input, index) => {
        const newInput = input.cloneNode(true);
        input.parentNode.replaceChild(newInput, input);
    });
    
    // Get fresh references
    const freshInputs = invoiceModal.querySelectorAll('.otp-input-invoice');
    
    freshInputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            const value = this.value;
            this.value = value.replace(/[^0-9]/g, '');
            
            if (this.value) {
                this.classList.add('filled');
                this.classList.remove('error');
                if (index < freshInputs.length - 1) {
                    freshInputs[index + 1].focus();
                }
            } else {
                this.classList.remove('filled');
            }
            
            updateInvoicePinValue();
            clearInvoicePinError();
        });
        
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value && index > 0) {
                freshInputs[index - 1].focus();
                freshInputs[index - 1].value = '';
                freshInputs[index - 1].classList.remove('filled');
                updateInvoicePinValue();
            }
            
            if (e.key === 'ArrowLeft' && index > 0) {
                e.preventDefault();
                freshInputs[index - 1].focus();
            }
            if (e.key === 'ArrowRight' && index < freshInputs.length - 1) {
                e.preventDefault();
                freshInputs[index + 1].focus();
            }
            
            if (e.key === 'Enter') {
                e.preventDefault();
                const uploadBtn = document.getElementById('btnUploadProof');
                if (uploadBtn && uploadBtn.style.display !== 'none') {
                    uploadBtn.click();
                }
            }
        });
        
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = (e.clipboardData || window.clipboardData).getData('text');
            const numbers = pastedData.replace(/[^0-9]/g, '').slice(0, 6);
            
            numbers.split('').forEach((num, i) => {
                if (freshInputs[i]) {
                    freshInputs[i].value = num;
                    freshInputs[i].classList.add('filled');
                }
            });
            
            const lastIndex = Math.min(numbers.length, freshInputs.length - 1);
            freshInputs[lastIndex].focus();
            updateInvoicePinValue();
        });
        
        input.addEventListener('focus', function() {
            this.select();
        });
    });
    
    if (freshInputs[0]) {
        setTimeout(() => freshInputs[0].focus(), 100);
    }
}

function updateInvoicePinValue() {
    const invoiceModal = document.getElementById('invoiceProofModal');
    const otpInputs = invoiceModal.querySelectorAll('.otp-input-invoice');
    const pin = Array.from(otpInputs).map(input => input.value).join('');
    
    const hiddenInput = document.getElementById('pinInput');
    if (hiddenInput) {
        hiddenInput.value = pin;
    }
}

function clearInvoiceOtpInputs() {
    const invoiceModal = document.getElementById('invoiceProofModal');
    const otpInputs = invoiceModal.querySelectorAll('.otp-input-invoice');
    
    otpInputs.forEach(input => {
        input.value = '';
        input.classList.remove('filled', 'error');
    });
    
    const pinInput = document.getElementById('pinInput');
    if (pinInput) {
        pinInput.value = '';
    }
    
    clearInvoicePinError();
}

function clearInvoicePinError() {
    const pinError = document.getElementById('pinError');
    if (pinError) {
        pinError.style.display = 'none';
        pinError.textContent = '';
    }
    
    const invoiceModal = document.getElementById('invoiceProofModal');
    if (invoiceModal) {
        const otpInputs = invoiceModal.querySelectorAll('.otp-input-invoice');
        otpInputs.forEach(input => {
            input.classList.remove('error');
        });
    }
}

function showInvoicePinError(message) {
    const pinError = document.getElementById('pinError');
    if (pinError) {
        pinError.textContent = message;
        pinError.style.display = 'block';
    }
    
    const invoiceModal = document.getElementById('invoiceProofModal');
    if (invoiceModal) {
        const otpInputs = invoiceModal.querySelectorAll('.otp-input-invoice');
        otpInputs.forEach(input => {
            input.classList.add('error');
        });
        
        if (otpInputs[0]) {
            otpInputs[0].focus();
        }
    }
}

// ============================================
// FILE HANDLING
// ============================================
function previewFile(input, type) {
    const file = input.files[0];
    if (!file) return;

    // Validasi ukuran
    if (file.size > 5 * 1024 * 1024) {
        Swal.fire({
            icon: 'error',
            title: 'File Terlalu Besar',
            text: 'Ukuran file maksimal 5MB',
        });
        clearFileInput(null, type);
        return;
    }

    // Validasi tipe
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
    if (!allowedTypes.includes(file.type)) {
        Swal.fire({
            icon: 'error',
            title: 'Format File Tidak Valid',
            text: 'Format file harus JPG, PNG, atau PDF',
        });
        clearFileInput(null, type);
        return;
    }

    // Update UI
    const uploadPrompt = document.getElementById('uploadPrompt' + (type === 'invoice' ? 'Invoice' : 'Barang'));
    const filePreview = document.getElementById('filePreviewThumb' + (type === 'invoice' ? 'Invoice' : 'Barang'));
    const fileName = filePreview.querySelector('.file-name');
    const fileSize = filePreview.querySelector('.file-size');
    const fileIcon = filePreview.querySelector('.file-icon i');

    uploadPrompt.style.display = 'none';
    filePreview.style.display = 'block';
    
    fileName.textContent = file.name;
    fileSize.textContent = formatFileSize(file.size);
    
    if (file.type === 'application/pdf') {
        fileIcon.className = 'ri-file-pdf-line text-danger';
    } else {
        fileIcon.className = 'ri-image-line text-success';
    }

    // Clear error
    const errorId = type === 'invoice' ? 'buktiInvoiceError' : 'buktiBarangError';
    document.getElementById(errorId).style.display = 'none';
}

function clearFileInput(event, type) {
    if (event) {
        event.stopPropagation();
        event.preventDefault();
    }
    
    const fileInput = document.getElementById(type === 'invoice' ? 'buktiInvoiceInput' : 'buktiBarangInput');
    const uploadPrompt = document.getElementById('uploadPrompt' + (type === 'invoice' ? 'Invoice' : 'Barang'));
    const filePreview = document.getElementById('filePreviewThumb' + (type === 'invoice' ? 'Invoice' : 'Barang'));
    
    if (fileInput) fileInput.value = '';
    if (uploadPrompt) uploadPrompt.style.display = 'block';
    if (filePreview) filePreview.style.display = 'none';
    
    const errorId = type === 'invoice' ? 'buktiInvoiceError' : 'buktiBarangError';
    const errorElement = document.getElementById(errorId);
    if (errorElement) {
        errorElement.style.display = 'none';
        errorElement.textContent = '';
    }
}

function clearAllFileInputs() {
    clearFileInput(null, 'invoice');
    clearFileInput(null, 'barang');
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

// ============================================
// UPLOAD HANDLER
// ============================================
function directUploadProof(e) {
    if (e) e.preventDefault();
    
    const invoiceFileInput = document.getElementById('buktiInvoiceInput');
    const barangFileInput = document.getElementById('buktiBarangInput');
    const pinInput = document.getElementById('pinInput');
    const btn = document.getElementById('btnUploadProof');
    const uploadType = document.getElementById('uploadType').value;
    
    // Reset errors
    clearValidationErrors();
    
    // Validate at least one file
    const hasInvoiceFile = invoiceFileInput.files && invoiceFileInput.files[0];
    const hasBarangFile = barangFileInput.files && barangFileInput.files[0];
    
    if (!hasInvoiceFile && !hasBarangFile) {
        Swal.fire({
            icon: 'warning',
            title: 'File Belum Dipilih',
            text: 'Silakan pilih minimal satu file (Invoice atau Barang)',
        });
        return;
    }
    
    // Validate PIN
    const pin = pinInput.value.trim();
    if (!pin) {
        showInvoicePinError('PIN harus diisi');
        return;
    }
    
    if (pin.length !== 6) {
        showInvoicePinError('PIN harus 6 digit');
        return;
    }
    
    if (!/^\d{6}$/.test(pin)) {
        showInvoicePinError('PIN harus berupa angka');
        return;
    }
    
    // Perform upload
    performUpload(invoiceFileInput.files[0], barangFileInput.files[0], pin, btn);
}

function showValidationError(elementId, message) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }
}

function clearValidationErrors() {
    ['buktiInvoiceError', 'buktiBarangError'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.style.display = 'none';
            element.textContent = '';
        }
    });
    clearInvoicePinError();
}

function replaceProof(type, proofId) {
    bootstrap.Modal.getInstance(document.getElementById('invoiceProofModal')).hide();
    
    // Show upload form with replace mode
    document.getElementById('invoiceProofPreview').style.display = 'none';
    document.getElementById('invoiceProofUploadForm').style.display = 'block';
    document.getElementById('btnUploadProof').style.display = 'block';
    
    // Set upload type
    document.getElementById('uploadType').value = type;
    
    // ✅ Set mode sebagai "replace"
    let uploadModeInput = document.getElementById('uploadMode');
    if (!uploadModeInput) {
        uploadModeInput = document.createElement('input');
        uploadModeInput.type = 'hidden';
        uploadModeInput.id = 'uploadMode';
        uploadModeInput.name = 'replace_mode';
        document.getElementById('uploadInvoiceForm').appendChild(uploadModeInput);
    }
    uploadModeInput.value = 'true'; // ✅ Mode REPLACE
    
    // Store proof ID to replace
    let proofIdInput = document.getElementById('replaceProofId');
    if (!proofIdInput) {
        proofIdInput = document.createElement('input');
        proofIdInput.type = 'hidden';
        proofIdInput.id = 'replaceProofId';
        proofIdInput.name = 'replace_proof_id';
        document.getElementById('uploadInvoiceForm').appendChild(proofIdInput);
    }
    proofIdInput.value = proofId;
    
    const title = type === 'invoice' ? 'Ganti Bukti Invoice' : 'Ganti Bukti Barang';
    document.getElementById('invoiceProofModalTitle').textContent = title;
    
    // Re-show modal
    const modal = new bootstrap.Modal(document.getElementById('invoiceProofModal'));
    modal.show();
    
    clearAllFileInputs();
    clearInvoiceOtpInputs();
}

function performUpload(invoiceFile, barangFile, pin, btn) {
    // Add loading state
    btn.classList.add('btn-loading');
    btn.disabled = true;
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';
    
    // Disable OTP inputs
    const invoiceModal = document.getElementById('invoiceProofModal');
    const otpInputs = invoiceModal.querySelectorAll('.otp-input-invoice');
    otpInputs.forEach(input => input.disabled = true);
    
    // Prepare FormData
    const formData = new FormData();
    if (invoiceFile) {
        formData.append('bukti_invoice', invoiceFile, invoiceFile.name);
    }
    if (barangFile) {
        formData.append('bukti_barang', barangFile, barangFile.name);
    }
    formData.append('pin', pin);
    
    // ✅ Tambahkan mode (replace atau add)
    const uploadMode = document.getElementById('uploadMode');
    if (uploadMode) {
        formData.append('replace_mode', uploadMode.value === 'true' ? '1' : '0');
    } else {
        formData.append('replace_mode', '0'); // Default: ADD mode
    }
    
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    
    // Upload
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 60000);
    
    fetch(`{{ route('po.upload-proof', $po->id_po) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: formData,
        signal: controller.signal
    })
    .then(response => {
        clearTimeout(timeoutId);
        return response.json().then(data => ({
            status: response.status,
            data: data
        }));
    })
    .then(({status, data}) => {
        btn.classList.remove('btn-loading');
        btn.disabled = false;
        btn.innerHTML = originalHTML;
        otpInputs.forEach(input => input.disabled = false);
        
        if (status === 200 && data.message) {
            bootstrap.Modal.getInstance(document.getElementById('invoiceProofModal')).hide();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.reload();
            });
        } else if (status === 403) {
            showInvoicePinError(data.error || 'PIN tidak valid');
            clearInvoiceOtpInputs();
            
            Swal.fire({
                icon: 'error',
                title: 'PIN Salah',
                text: data.error || 'PIN yang Anda masukkan tidak valid',
                confirmButtonText: 'Coba Lagi'
            });
        } else if (status === 422) {
            let errorMessage = data.error || 'Periksa kembali data yang Anda masukkan';
            
            if (data.errors) {
                if (data.errors.pin) {
                    showInvoicePinError(data.errors.pin[0]);
                }
                if (data.errors.bukti_invoice) {
                    showValidationError('buktiInvoiceError', data.errors.bukti_invoice[0]);
                }
                if (data.errors.bukti_barang) {
                    showValidationError('buktiBarangError', data.errors.bukti_barang[0]);
                }
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: errorMessage,
                confirmButtonText: 'OK'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Upload',
                text: data.error || 'Terjadi kesalahan saat upload.',
                confirmButtonText: 'Coba Lagi'
            });
        }
    })
    .catch(error => {
        clearTimeout(timeoutId);
        btn.classList.remove('btn-loading');
        btn.disabled = false;
        btn.innerHTML = originalHTML;
        otpInputs.forEach(input => input.disabled = false);
        
        let errorMessage = 'Terjadi kesalahan sistem';
        if (error.name === 'AbortError') {
            errorMessage = 'Upload timeout. File terlalu besar atau koneksi lambat.';
        } else if (error.message) {
            errorMessage = error.message;
        }
        
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: errorMessage,
            confirmButtonText: 'OK'
        });
        
        console.error('Upload error:', error);
    });
}

// ============================================
// DELETE PROOF
// ============================================
function confirmDeleteProof(type, proofId = null) {
    bootstrap.Modal.getInstance(document.getElementById('invoiceProofModal')).hide();
    
    const title = type === 'invoice' ? 'Bukti Invoice' : 'Bukti Barang';
    const message = proofId 
        ? `Hapus ${title} ini?` 
        : `Hapus semua ${title}?`;
    
    Swal.fire({
        title: message,
        html: `
            <div class="text-start">
                <div class="alert alert-warning mb-3">
                    <i class="ri-alert-line me-2"></i>
                    <strong>Peringatan:</strong> File yang dihapus tidak dapat dikembalikan!
                </div>
                <p class="mb-3">Untuk melanjutkan, masukkan PIN Anda:</p>
                <div class="d-flex justify-content-center gap-2 mb-2" id="deleteOtpContainer">
                    <input type="text" class="delete-otp-input form-control text-center" maxlength="1" style="width: 45px; height: 45px; font-size: 1.25rem; font-weight: 600;">
                    <input type="text" class="delete-otp-input form-control text-center" maxlength="1" style="width: 45px; height: 45px; font-size: 1.25rem; font-weight: 600;">
                    <input type="text" class="delete-otp-input form-control text-center" maxlength="1" style="width: 45px; height: 45px; font-size: 1.25rem; font-weight: 600;">
                    <input type="text" class="delete-otp-input form-control text-center" maxlength="1" style="width: 45px; height: 45px; font-size: 1.25rem; font-weight: 600;">
                    <input type="text" class="delete-otp-input form-control text-center" maxlength="1" style="width: 45px; height: 45px; font-size: 1.25rem; font-weight: 600;">
                    <input type="text" class="delete-otp-input form-control text-center" maxlength="1" style="width: 45px; height: 45px; font-size: 1.25rem; font-weight: 600;">
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="ri-delete-bin-line me-1"></i> Ya, Hapus!',
        cancelButtonText: '<i class="ri-close-line me-1"></i> Batal',
        showLoaderOnConfirm: true,
        didOpen: () => {
            // ... OTP input handlers sama seperti sebelumnya
        },
        preConfirm: () => {
            const deleteInputs = document.querySelectorAll('.delete-otp-input');
            const pin = Array.from(deleteInputs).map(input => input.value).join('');
            
            if (!pin || pin.length !== 6 || !/^\d{6}$/.test(pin)) {
                Swal.showValidationMessage('PIN harus 6 digit angka');
                return false;
            }
            
            const requestData = { 
                pin: pin, 
                type: type 
            };
            
            if (proofId) {
                requestData.id_proof = proofId;
            }
            
            return fetch(`/po/po/{{ $po->id_po }}/delete-proof`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(requestData)
            })
            .then(response => response.json().then(data => ({ status: response.status, data: data })))
            .then(({status, data}) => {
                if (status === 403) throw new Error('PIN tidak valid');
                if (status !== 200) throw new Error(data.error || 'Gagal menghapus file');
                return data;
            })
            .catch(error => {
                Swal.showValidationMessage(error.message);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: result.value.message,
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.reload();
            });
        } else {
            showInvoiceProofModal();
        }
    });
}

// ============================================
// DRAG & DROP
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    ['invoice', 'barang'].forEach(type => {
        const uploadArea = document.getElementById('uploadArea' + (type === 'invoice' ? 'Invoice' : 'Barang'));
        
        if (uploadArea) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });

            uploadArea.addEventListener('dragenter', () => uploadArea.classList.add('drag-over'));
            uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('drag-over'));
            
            uploadArea.addEventListener('drop', (e) => {
                uploadArea.classList.remove('drag-over');
                const files = e.dataTransfer.files;
                
                if (files.length > 0) {
                    const inputId = type === 'invoice' ? 'buktiInvoiceInput' : 'buktiBarangInput';
                    document.getElementById(inputId).files = files;
                    previewFile(document.getElementById(inputId), type);
                }
            });
        }
    });
});

// ============================================
// MODAL EVENTS
// ============================================
const invoiceModal = document.getElementById('invoiceProofModal');
if (invoiceModal) {
    invoiceModal.addEventListener('shown.bs.modal', initInvoiceOtpInputs);
    invoiceModal.addEventListener('hidden.bs.modal', function() {
        clearAllFileInputs();
        clearInvoiceOtpInputs();
        clearValidationErrors();
    });
}
</script>
@endpush