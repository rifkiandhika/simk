@extends('layouts.app')

@section('title', 'Konfirmasi Penerimaan Barang')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('po.index') }}">Purchase Order</a></li>
    <li class="breadcrumb-item"><a href="{{ route('po.show', $po->id_po) }}">Detail PO</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">Konfirmasi Penerimaan</li>
@endsection

@section('content')
<div class="app-body">
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i>
            <strong>Terdapat kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('po.confirm-receipt', $po->id_po) }}" method="POST" id="formConfirm">
        @csrf
        
        <div class="row">
            <!-- Left Column - Items -->
            <div class="col-xl-8">
                <!-- PO Info -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0">
                            <i class="ri-file-list-3-line me-2"></i>Informasi Purchase Order
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="120"><strong>No. PO</strong></td>
                                        <td>: {{ $po->no_po }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal</strong></td>
                                        <td>: {{ \Carbon\Carbon::parse($po->tanggal_permintaan)->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Pemohon</strong></td>
                                        <td>: {{ $po->karyawanPemohon->nama ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="120"><strong>Disetujui Oleh</strong></td>
                                        <td>: {{ $po->kepalaGudang->nama ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tgl Disetujui</strong></td>
                                        <td>: {{ $po->tanggal_approval_kepala_gudang ? \Carbon\Carbon::parse($po->tanggal_approval_kepala_gudang)->format('d/m/Y H:i') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status</strong></td>
                                        <td>: <span class="badge bg-success">Disetujui</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Checklist -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="ri-checkbox-multiple-line me-2"></i>Pengecekan Barang Diterima
                        </h5>
                        <small class="text-muted">Silakan periksa setiap item yang diterima dari gudang</small>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">No</th>
                                        <th width="5"></th>
                                        <th>Produk</th>
                                        <th width="100">Qty Diminta</th>
                                        <th width="120">Qty Diterima <span class="text-danger">*</span></th>
                                        <th width="150">Kondisi <span class="text-danger">*</span></th>
                                        <th width="200">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($po->items as $index => $item)
                                    <tr>
                                        
                                        <td class="text-center">
                                            <p class="hidden">{{ $index + 1 }}</p>
                                            {{-- PERBAIKAN: Ganti id_item menjadi id_po_item --}}
                                            <input type="hidden" 
                                                    name="items[{{ $index }}][id_po_item]" 
                                                    value="{{ $item->id_po_item }}"
                                                    class="item-id-input">
                                        </td>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $item->nama_produk }}</strong>
                                            @if($item->produk)
                                                <br><small class="text-muted">{{ $item->produk->merk ?? '' }} - {{ $item->produk->satuan }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ number_format($item->qty_diminta) }}</span>
                                        </td>
                                        <td>
                                            <input type="number" 
                                                   class="form-control form-control-sm qty-input" 
                                                   name="items[{{ $index }}][qty_diterima]" 
                                                   id="qty-{{ $index }}"
                                                   min="0" 
                                                   max="{{ $item->qty_diminta }}"
                                                   value="{{ $item->qty_diminta }}"
                                                   data-diminta="{{ $item->qty_diminta }}"
                                                   data-index="{{ $index }}"
                                                   required>
                                            <small class="text-muted">Max: {{ $item->qty_diminta }}</small>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm kondisi-select" 
                                                    name="items[{{ $index }}][kondisi]" 
                                                    id="kondisi-{{ $index }}"
                                                    data-index="{{ $index }}"
                                                    required>
                                                <option value="baik">✓ Baik</option>
                                                <option value="rusak">✗ Rusak</option>
                                                <option value="kadaluarsa">⚠ Kadaluarsa</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" 
                                                   class="form-control form-control-sm" 
                                                   name="items[{{ $index }}][catatan]"
                                                   placeholder="Catatan (opsional)">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Additional Notes -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="ri-chat-3-line me-2"></i>Catatan Tambahan
                        </h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" 
                                  name="catatan_penerima" 
                                  rows="3" 
                                  placeholder="Tambahkan catatan untuk penerimaan ini (opsional)..."></textarea>
                    </div>
                </div>

                <!-- PIN Confirmation -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-warning py-3">
                        <h5 class="mb-0">
                            <i class="ri-lock-line me-2"></i>Konfirmasi PIN
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning mb-3">
                            <i class="ri-shield-check-line me-2"></i>
                            <strong>Keamanan:</strong> Masukkan PIN 6 digit Anda untuk mengonfirmasi penerimaan barang ini.
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">PIN (6 digit) <span class="text-danger">*</span></label>
                            <input type="password" 
                                   class="form-control @error('pin') is-invalid @enderror" 
                                   name="pin" 
                                   id="pin"
                                   maxlength="6" 
                                   placeholder="Masukkan PIN 6 digit"
                                   required>
                            @error('pin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Summary -->
            <div class="col-xl-4">
                <!-- Summary Card -->
                <div class="card shadow-sm border-0 mb-4 position-sticky" style="top: 20px;">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="ri-calculator-line me-2"></i>Ringkasan Penerimaan</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Item:</span>
                            <strong>{{ $po->items->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Qty Diminta:</span>
                            <strong id="totalDiminta">{{ $po->items->sum('qty_diminta') }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-success">Qty Baik:</span>
                            <strong id="totalBaik" class="text-success">0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-danger">Qty Rusak/Expired:</span>
                            <strong id="totalRusak" class="text-danger">0</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Total Diterima:</span>
                            <h5 class="text-primary mb-0" id="totalDiterima">0</h5>
                        </div>
                        
                        <div class="alert alert-info mt-3 small">
                            <i class="ri-information-line me-1"></i>
                            Sistem akan otomatis menambahkan stok untuk produk yang sudah ada di apotik
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg" id="btnSubmit">
                                <i class="ri-check-double-line me-1"></i> Konfirmasi Penerimaan
                            </button>
                            <a href="{{ route('po.show', $po->id_po) }}" class="btn btn-outline-secondary">
                                <i class="ri-arrow-left-line me-1"></i> Batal
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">
                            <i class="ri-information-line text-info me-2"></i>Panduan Pengecekan
                        </h6>
                        <ul class="small mb-0">
                            <li class="mb-2">Periksa kondisi fisik setiap barang yang diterima</li>
                            <li class="mb-2">Pastikan jumlah yang diterima sesuai dengan permintaan</li>
                            <li class="mb-2">Jika ada barang rusak atau kadaluarsa, ubah kondisi dan sesuaikan jumlah</li>
                            <li class="mb-2 text-primary"><strong>Stok akan otomatis ditambahkan untuk produk yang sama</strong></li>
                            <li class="mb-2 text-primary"><strong>Produk baru akan dibuatkan record baru di apotik</strong></li>
                            <li>Barang dengan kondisi rusak/kadaluarsa akan masuk sebagai retur</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .position-sticky {
        position: sticky;
        z-index: 10;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .qty-input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .kondisi-select {
        font-weight: 500;
    }
    
    .kondisi-select option[value="baik"] {
        color: #198754;
    }
    
    .kondisi-select option[value="rusak"] {
        color: #dc3545;
    }
    
    .kondisi-select option[value="kadaluarsa"] {
        color: #ffc107;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Calculate summary on load
    calculateSummary();
    
    // Monitor qty input changes
    $('.qty-input').on('input', function() {
        const maxQty = parseInt($(this).data('diminta'));
        const currentQty = parseInt($(this).val()) || 0;
        
        if (currentQty > maxQty) {
            $(this).val(maxQty);
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Jumlah tidak boleh melebihi qty yang diminta',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        }
        
        calculateSummary();
    });
    
    // Monitor kondisi changes
    $('.kondisi-select').on('change', function() {
        const kondisi = $(this).val();
        const row = $(this).closest('tr');
        
        if (kondisi === 'baik') {
            row.removeClass('table-danger table-warning').addClass('table-light');
        } else if (kondisi === 'rusak') {
            row.removeClass('table-light table-warning').addClass('table-danger');
        } else {
            row.removeClass('table-light table-danger').addClass('table-warning');
        }
        
        calculateSummary();
    });
    
    function calculateSummary() {
        let totalBaik = 0;
        let totalRusak = 0;
        let totalDiterima = 0;
        
        $('.qty-input').each(function() {
            const qty = parseInt($(this).val()) || 0;
            const index = $(this).data('index');
            const kondisi = $('#kondisi-' + index).val();
            
            totalDiterima += qty;
            
            if (kondisi === 'baik') {
                totalBaik += qty;
            } else {
                totalRusak += qty;
            }
        });
        
        $('#totalBaik').text(totalBaik);
        $('#totalRusak').text(totalRusak);
        $('#totalDiterima').text(totalDiterima);
        
        // Update button state
        if (totalDiterima === 0) {
            $('#btnSubmit').prop('disabled', true).addClass('disabled');
        } else {
            $('#btnSubmit').prop('disabled', false).removeClass('disabled');
        }
    }
    
    // Form validation
    $('#formConfirm').on('submit', function(e) {
        const pin = $('#pin').val();
        
        if (!pin || pin.length !== 6) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'PIN harus 6 digit'
            });
            return false;
        }
        
        const totalDiterima = parseInt($('#totalDiterima').text());
        
        if (totalDiterima === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Minimal harus ada 1 item yang diterima'
            });
            return false;
        }
        
        // Show confirmation
        e.preventDefault();
        const form = this;
        
        Swal.fire({
            title: 'Konfirmasi Penerimaan',
            html: `
                <div class="text-start">
                    <p>Anda akan mengkonfirmasi penerimaan:</p>
                    <ul>
                        <li>Total Diterima (Baik): <strong class="text-success">${$('#totalBaik').text()} item</strong></li>
                        <li>Total Retur: <strong class="text-danger">${$('#totalRusak').text()} item</strong></li>
                    </ul>
                    <p class="text-primary"><strong>Stok akan otomatis ditambahkan ke apotik!</strong></p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Konfirmasi!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu, sedang memproses penerimaan barang',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                form.submit();
            }
        });
    });
    
    // Quick fill all with same qty
    $('#btnFillAll').on('click', function() {
        $('.qty-input').each(function() {
            const maxQty = $(this).data('diminta');
            $(this).val(maxQty);
        });
        calculateSummary();
    });
});
</script>
@endpush