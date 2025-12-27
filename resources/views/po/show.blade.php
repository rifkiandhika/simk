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

            {{-- ===== TEMPAT 1: BUTTON KONFIRMASI PENERIMAAN ===== --}}
            @if($po->tipe_po === 'internal' && $po->status === 'diterima' && !$po->tanggal_diterima)
            <!-- Confirmation Receipt Card -->
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
                                <strong>Perhatian:</strong> PO ini telah disetujui oleh Kepala Gudang. 
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

            {{-- Status jika sudah diterima --}}
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
            @if($po->tipe_po === 'eksternal' && $po->status === 'diterima' )
                <!-- Confirmation Receipt Card for External PO -->
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

                {{-- Status jika sudah diterima (EKSTERNAL) --}}
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

                @if($po->needsInvoice())
                    <!-- Invoice Input Card -->
                    <div class="card shadow-sm border-0 mb-4 border-info" style="border-width: 2px !important;">
                        <div class="card-header bg-info text-white py-3">
                            <h5 class="mb-0">
                                <i class="ri-file-text-line me-2"></i>Input Invoice/Faktur Supplier
                            </h5>
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

                    {{-- ===== STATUS JIKA SUDAH ADA INVOICE ===== --}}
                    @if($po->hasInvoice())
                    <div class="card shadow-sm border-0 mb-4 border-success" style="border-width: 2px !important;">
                        <div class="card-header bg-success text-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="ri-file-check-line me-2"></i>Invoice/Faktur Supplier
                            </h5>

                            <a href="{{ route('po.print-invoice', $po->id_po) }}" 
                            class="btn btn-light btn-sm"
                            target="_blank">
                                <i class="ri-printer-line me-1"></i> Print Invoice
                            </a>
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

                    @if($po->status === 'diterima' && $po->items->first() && $po->items->first()->batches->count() > 0)
                    <!-- Items Table with Batch Details -->
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
                                    <th width="120" class="text-end">Harga</th>
                                    <th width="150" class="text-end">Subtotal</th>
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
                                    <td class="text-end">
                                        <small>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</small>
                                    </td>
                                    <td class="text-end">
                                        <strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
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

            <!-- Shipping Activities (if exists) -->
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
                                    <td><span class="badge badge-sm bg-secondary">{{ str_replace('_', ' ', $audit->aksi) }}</span></td>
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
                            'menunggu_persetujuan_kepala_gudang' => ['color' => 'warning', 'icon' => 'ri-time-line', 'label' => 'Menunggu Kepala Gudang'],
                            'menunggu_persetujuan_kasir' => ['color' => 'warning', 'icon' => 'ri-time-line', 'label' => 'Menunggu Kasir'],
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
                        <label class="text-muted small mb-2">Kepala Gudang</label>
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

                        @if($po->status === 'draft')
                            <a href="{{ route('po.edit', $po->id_po) }}" class="btn btn-warning">
                                <i class="ri-pencil-line me-1"></i> Edit PO
                            </a>
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

                        @if($po->status === 'disetujui')
                            <button class="btn btn-info" onclick="sendToSupplier('{{ $po->id_po }}')">
                                <i class="ri-truck-line me-1"></i> Kirim ke Supplier
                            </button>
                        @endif

                        @if(in_array($po->status, ['dikirim_ke_supplier', 'dalam_pengiriman']))
                            <a href="{{ route('shipping.by-po', $po->id_po) }}" class="btn btn-info">
                                <i class="ri-truck-line me-1"></i> Update Shipping
                            </a>
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

{{-- Approval Modal --}}
<div class="modal fade" id="approvalModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Approval</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">PIN (6 digit)</label>
                    <input type="password" class="form-control" id="pinApproval" maxlength="6" placeholder="Masukkan PIN">
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan (opsional)</label>
                    <textarea class="form-control" id="catatanApproval" rows="3" placeholder="Tambahkan catatan..."></textarea>
                </div>
                <input type="hidden" id="approvalRole">
                <input type="hidden" id="approvalStatus">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="confirmApproval()">Konfirmasi</button>
            </div>
        </div>
    </div>
</div>

{{-- Submit Modal --}}
<div class="modal fade" id="submitModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Submit Purchase Order</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="poIdSubmit">
                <label class="form-label">PIN (6 digit)</label>
                <input type="password" id="pinSubmit" class="form-control" maxlength="6">
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary" onclick="confirmSubmit()">Submit</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Purchase Order</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="poIdDelete">
                <label class="form-label">PIN (6 digit)</label>
                <input type="password" id="pinDelete" class="form-control" maxlength="6">
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-danger" onclick="confirmDelete()">Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
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
        align-items: center;
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
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let approvalModalInstance, submitModalInstance, deleteModalInstance;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all modals
        approvalModalInstance = new bootstrap.Modal(document.getElementById('approvalModal'));
        submitModalInstance = new bootstrap.Modal(document.getElementById('submitModal'));
        deleteModalInstance = new bootstrap.Modal(document.getElementById('deleteModal'));

        // Auto dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (!alert.classList.contains('alert-warning')) { // Don't auto-hide warning alerts
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            });
        }, 5000);
    });

    // ========== SUBMIT PO ==========
    function submitPO(poId) {
        document.getElementById('poIdSubmit').value = poId;
        document.getElementById('pinSubmit').value = '';
        submitModalInstance.show();
    }

    function confirmSubmit() {
        const pin = document.getElementById('pinSubmit').value;
        const poId = document.getElementById('poIdSubmit').value;

        if (!pin || pin.length !== 6) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'PIN harus 6 digit'
            });
            return;
        }

        // Show loading
        Swal.fire({
            title: 'Memproses...',
            text: 'Mohon tunggu, sedang mengirim PO',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

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
            if (data.message) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message,
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.error || 'Terjadi kesalahan'
                });
            }
        })
        .catch(error => {
            console.error('Submit error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan sistem'
            });
        });

        submitModalInstance.hide();
    }

    // ========== APPROVAL FUNCTIONS ==========
    function showApprovalModal(role, status) {
        document.getElementById('approvalRole').value = role;
        document.getElementById('approvalStatus').value = status;
        document.getElementById('pinApproval').value = '';
        document.getElementById('catatanApproval').value = '';
        
        // Update modal title based on action
        const modalTitle = document.querySelector('#approvalModal .modal-title');
        const actionText = status === 'disetujui' ? 'Menyetujui' : 'Menolak';
        const roleText = role === 'kepala_gudang' ? 'Kepala Gudang' : 'Kasir';
        modalTitle.textContent = `${actionText} PO - ${roleText}`;
        
        approvalModalInstance.show();
    }

    function confirmApproval() {
        const pin = document.getElementById('pinApproval').value;
        const catatan = document.getElementById('catatanApproval').value;
        const role = document.getElementById('approvalRole').value;
        const status = document.getElementById('approvalStatus').value;

        if (!pin || pin.length !== 6) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'PIN harus 6 digit'
            });
            return;
        }

        const endpoint = role === 'kepala_gudang' 
            ? `/po/{{ $po->id_po }}/approve-kepala-gudang`
            : `/po/{{ $po->id_po }}/approve-kasir`;

        // Show loading
        Swal.fire({
            title: 'Memproses...',
            text: 'Mohon tunggu, sedang memproses approval',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ 
                pin: pin,
                status_approval: status,
                catatan: catatan
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message,
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.error || 'Terjadi kesalahan'
                });
            }
        })
        .catch(error => {
            console.error('Approval error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan sistem'
            });
        });

        approvalModalInstance.hide();
    }

    // ========== SEND TO SUPPLIER ==========
    function sendToSupplier(id_po) {
        Swal.fire({
            title: "Konfirmasi Pengiriman",
            text: "Masukkan PIN untuk mengirim PO ke supplier",
            input: "password",
            inputAttributes: {
                maxlength: 6,
                minlength: 6,
                placeholder: "Masukkan PIN 6 digit"
            },
            showCancelButton: true,
            confirmButtonText: "Kirim ke Supplier",
            cancelButtonText: "Batal",
            confirmButtonColor: '#0dcaf0',
            cancelButtonColor: '#6c757d',
            preConfirm: (pin) => {
                if (!pin || pin.length !== 6) {
                    Swal.showValidationMessage("PIN harus 6 digit");
                    return false;
                }
                return pin;
            }
        }).then((result) => {
            if (!result.isConfirmed) return;

            let pin = result.value;

            // Show loading
            Swal.fire({
                title: 'Mengirim ke Supplier...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/po/${id_po}/send-to-supplier`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ pin })
            })
            .then(res => res.json())
            .then(data => {
                if (data.error || data.errors) {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal",
                        text: data.error ?? data.errors.pin[0]
                    });
                } else {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil",
                        text: data.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                }
            })
            .catch(err => {
                console.error('Send to supplier error:', err);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Terjadi kesalahan jaringan"
                });
            });
        });
    }

    // ========== DELETE PO ==========
    function deletePO(poId) {
        document.getElementById('poIdDelete').value = poId;
        document.getElementById('pinDelete').value = '';
        deleteModalInstance.show();
    }

    function confirmDelete() {
        const pin = document.getElementById('pinDelete').value;
        const poId = document.getElementById('poIdDelete').value;

        if (!pin || pin.length !== 6) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'PIN harus 6 digit'
            });
            return;
        }

        // Confirm deletion
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "PO ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Menghapus...',
                    text: 'Mohon tunggu',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

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
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: data.message,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = '{{ route("po.index") }}';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: data.message || 'Terjadi kesalahan'
                        });
                    }
                })
                .catch(error => {
                    console.error('Delete error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan sistem'
                    });
                });
            }
        });

        deleteModalInstance.hide();
    }

    // ========== HELPER FUNCTIONS ==========
    
    // Handle Enter key on PIN inputs
    document.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const activeModal = document.querySelector('.modal.show');
            if (activeModal) {
                if (activeModal.id === 'submitModal') {
                    confirmSubmit();
                } else if (activeModal.id === 'approvalModal') {
                    confirmApproval();
                } else if (activeModal.id === 'deleteModal') {
                    confirmDelete();
                }
            }
        }
    });

    // Clear PIN inputs when modals are closed
    document.getElementById('submitModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('pinSubmit').value = '';
    });

    document.getElementById('approvalModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('pinApproval').value = '';
        document.getElementById('catatanApproval').value = '';
    });

    document.getElementById('deleteModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('pinDelete').value = '';
    });
</script>
@endpush