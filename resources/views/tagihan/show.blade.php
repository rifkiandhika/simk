@extends('layouts.app')

@section('title', 'Detail Tagihan - ' . $tagihan->no_tagihan)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tagihan.index') }}">Tagihan PO</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">Detail</li>
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

    <!-- Header Actions -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="ri-file-list-line me-2"></i>Detail Tagihan
                </h4>
                <div class="btn-group">
                    <a href="{{ route('tagihan.index') }}" class="btn btn-secondary btn-sm">
                        <i class="ri-arrow-left-line me-1"></i>Kembali
                    </a>
                    @if($tagihan->canBePaid())
                    <a href="{{ route('tagihan.payment.form', $tagihan->id_tagihan) }}" class="btn btn-success btn-sm">
                        <i class="ri-money-dollar-circle-line me-1"></i>Bayar Tagihan
                    </a>
                    @endif
                    <a href="{{ route('tagihan.print', $tagihan->id_tagihan) }}" target="_blank" class="btn btn-info btn-sm">
                        <i class="ri-printer-line me-1"></i>Print
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informasi Tagihan -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="ri-information-line me-2"></i>Informasi Tagihan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">No Tagihan</label>
                            <h6>{{ $tagihan->no_tagihan }}</h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Status</label>
                            <br>
                            @php
                                $statusColors = [
                                    'draft' => 'bg-secondary',
                                    'menunggu_pembayaran' => 'bg-warning text-dark',
                                    'dibayar_sebagian' => 'bg-info',
                                    'lunas' => 'bg-success',
                                    'dibatalkan' => 'bg-danger',
                                ];
                            @endphp
                            <span class="badge {{ $statusColors[$tagihan->status] ?? 'bg-secondary' }} fs-6">
                                {{ ucwords(str_replace('_', ' ', $tagihan->status)) }}
                            </span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">No Purchase Order</label>
                            <h6>
                                <a href="{{ route('po.show', $tagihan->id_po) }}" class="text-decoration-none">
                                    {{ $tagihan->purchaseOrder->no_po }}
                                </a>
                            </h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Supplier</label>
                            <h6>{{ $tagihan->supplier->nama_supplier ?? '-' }}</h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Tanggal Tagihan</label>
                            <h6>{{ $tagihan->tanggal_tagihan ? $tagihan->tanggal_tagihan->format('d/m/Y') : '-' }}</h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Jatuh Tempo</label>
                            @if($tagihan->tanggal_jatuh_tempo)
                                @php
                                    $isOverdue = now()->isAfter($tagihan->tanggal_jatuh_tempo) && !in_array($tagihan->status, ['lunas', 'dibatalkan']);
                                @endphp
                                <h6 class="{{ $isOverdue ? 'text-danger' : '' }}">
                                    {{ $tagihan->tanggal_jatuh_tempo->format('d/m/Y') }}
                                    @if($isOverdue)
                                        <span class="badge bg-danger ms-2">Lewat Tempo</span>
                                    @endif
                                </h6>
                            @else
                                <h6>-</h6>
                            @endif
                        </div>
                        <div class="col-12">
                            <label class="text-muted small">Catatan</label>
                            <p>{{ $tagihan->catatan ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Items -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="ri-list-check-2 me-2"></i>Detail Barang
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Nama Produk</th>
                                    <th width="100" class="text-center">Diminta</th>
                                    <th width="100" class="text-center">Diterima</th>
                                    <th width="100" class="text-center">Ditagihkan</th>
                                    <th width="130" class="text-end">Harga</th>
                                    <th width="150" class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tagihan->items as $x => $item)
                                <tr>
                                    <td class="text-center">{{ $x + 1 }}</td>
                                    <td>
                                        <strong>{{ $item->nama_produk }}</strong>
                                        @if($item->batch_number)
                                            <br><small class="text-muted">Batch: {{ $item->batch_number }}</small>
                                        @endif
                                        @if($item->tanggal_kadaluarsa)
                                            <br><small class="text-muted">Exp: {{ \Carbon\Carbon::parse($item->tanggal_kadaluarsa)->format('d/m/Y') }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->qty_diminta }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $item->qty_diterima }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ $item->qty_ditagihkan }}</span>
                                    </td>
                                    <td class="text-end">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="6" class="text-end fw-bold">Subtotal:</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</td>
                                </tr>
                                @if($tagihan->pajak > 0)
                                <tr>
                                    <td colspan="6" class="text-end">Pajak:</td>
                                    <td class="text-end">Rp {{ number_format($tagihan->pajak, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                <tr class="table-primary">
                                    <td colspan="6" class="text-end fw-bold fs-6">GRAND TOTAL:</td>
                                    <td class="text-end fw-bold fs-6">Rp {{ number_format($tagihan->grand_total, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- History Pembayaran -->
            @if($tagihan->pembayaran->count() > 0)
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="ri-history-line me-2"></i>History Pembayaran
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>No Pembayaran</th>
                                    <th>Tanggal</th>
                                    <th>Metode</th>
                                    <th class="text-end">Jumlah</th>
                                    <th>Status</th>
                                    <th class="text-center">Bukti</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tagihan->pembayaran as $payment)
                                <tr>
                                    <td>{{ $payment->no_pembayaran }}</td>
                                    <td>{{ $payment->tanggal_bayar->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ ucfirst($payment->metode_pembayaran) }}</span>
                                    </td>
                                    <td class="text-end fw-bold">Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}</td>
                                    <td>
                                        @if($payment->status_pembayaran == 'diverifikasi')
                                            <span class="badge bg-success">Verified</span>
                                        @elseif($payment->status_pembayaran == 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @else
                                            <span class="badge bg-danger">Ditolak</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($payment->bukti_pembayaran)
                                            <a href="{{ route('tagihan.payment.download', $payment->id_pembayaran) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="ri-download-line"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Summary & Info -->
        <div class="col-lg-4">
            <!-- Payment Summary -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="mb-0 text-white">
                        <i class="ri-money-dollar-circle-line me-2"></i>Ringkasan Pembayaran
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Grand Total:</span>
                            <strong class="fs-5">Rp {{ number_format($tagihan->grand_total, 0, ',', '.') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Dibayar:</span>
                            <strong class="text-success">Rp {{ number_format($tagihan->total_dibayar, 0, ',', '.') }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Sisa Tagihan:</span>
                            <strong class="fs-5 {{ $tagihan->sisa_tagihan > 0 ? 'text-danger' : 'text-success' }}">
                                Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}
                            </strong>
                        </div>

                        <!-- Progress Bar -->
                        @if($tagihan->grand_total > 0)
                        <div class="mb-2">
                            <small class="text-muted">Progress Pembayaran:</small>
                            <div class="progress mt-2" style="height: 20px;">
                                <div class="progress-bar {{ $tagihan->status == 'lunas' ? 'bg-success' : 'bg-info' }}" 
                                     role="progressbar" 
                                     style="width: {{ ($tagihan->total_dibayar / $tagihan->grand_total) * 100 }}%"
                                     aria-valuenow="{{ ($tagihan->total_dibayar / $tagihan->grand_total) * 100 }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ number_format(($tagihan->total_dibayar / $tagihan->grand_total) * 100, 1) }}%
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    @if($tagihan->canBePaid())
                    <div class="d-grid">
                        <a href="{{ route('tagihan.payment.form', $tagihan->id_tagihan) }}" 
                           class="btn btn-success">
                            <i class="ri-money-dollar-circle-line me-2"></i>Bayar Sekarang
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Info PO -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="ri-file-list-3-line me-2"></i>Informasi PO
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">No PO:</small>
                        <br><strong>{{ $tagihan->purchaseOrder->no_po }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Tipe PO:</small>
                        <br>
                        @if($tagihan->purchaseOrder->tipe_po == 'internal')
                            <span class="badge bg-info">Internal</span>
                        @else
                            <span class="badge bg-warning text-dark">Eksternal</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Status PO:</small>
                        <br><span class="badge bg-success">{{ ucwords(str_replace('_', ' ', $tagihan->purchaseOrder->status)) }}</span>
                    </div>
                    @if($tagihan->purchaseOrder->no_gr)
                    <div class="mb-3">
                        <small class="text-muted">No GR:</small>
                        <br><strong>{{ $tagihan->purchaseOrder->no_gr }}</strong>
                    </div>
                    @endif
                    <div class="d-grid mt-3">
                        <a href="{{ route('po.show', $tagihan->id_po) }}" class="btn btn-outline-primary btn-sm">
                            <i class="ri-eye-line me-1"></i>Lihat Detail PO
                        </a>
                    </div>
                </div>
            </div>

            <!-- Created By -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="ri-user-line me-2"></i>Informasi Lainnya
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Dibuat oleh:</small>
                        <br><strong>{{ $tagihan->karyawanBuat->nama_lengkap ?? 'Sistem' }}</strong>
                        <br><small class="text-muted">{{ $tagihan->tanggal_dibuat->format('d/m/Y H:i') }}</small>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Tenor Pembayaran:</small>
                        <br><strong>{{ $tagihan->tenor_hari }} Hari</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge {
        font-weight: 500;
        padding: 0.4em 0.8em;
    }
    
    .card {
        border-radius: 0.5rem;
    }
    
    .progress {
        background-color: #e9ecef;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
@endpush