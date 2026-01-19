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

    <form id="formConfirm">
        @csrf
        
        <div class="row">
            <!-- Left Column - Items -->
            <div class="col-xl-8">
                <!-- PO Info -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header {{ $po->tipe_po === 'internal' ? 'bg-primary' : 'bg-success' }} text-white py-3">
                        <h5 class="mb-0">
                            <i class="ri-file-list-3-line me-2"></i>Informasi Purchase Order
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="130"><strong>No. PO</strong></td>
                                        <td>: {{ $po->no_po }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tipe PO</strong></td>
                                        <td>: 
                                            @if($po->tipe_po === 'internal')
                                                <span class="badge bg-primary">Internal</span>
                                            @else
                                                <span class="badge bg-success">Eksternal</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal</strong></td>
                                        <td>: {{ $po->tanggal_permintaan->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Pemohon</strong></td>
                                        <td>: {{ $po->karyawanPemohon->nama_lengkap ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    @if($po->tipe_po === 'internal')
                                    <tr>
                                        <td width="130"><strong>Disetujui Oleh</strong></td>
                                        <td>: {{ $po->kepalaGudang->nama_lengkap ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tgl Disetujui</strong></td>
                                        <td>: {{ $po->tanggal_approval_kepala_gudang ? $po->tanggal_approval_kepala_gudang->format('d/m/Y H:i') : '-' }}</td>
                                    </tr>
                                    @else
                                    <tr>
                                        <td width="130"><strong>Supplier</strong></td>
                                        <td>: <strong class="text-success">{{ $po->supplier->nama_supplier ?? '-' }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tgl Dikirim</strong></td>
                                        <td>: {{ $po->tanggal_dikirim_ke_supplier ? $po->tanggal_dikirim_ke_supplier->format('d/m/Y H:i') : '-' }}</td>
                                    </tr>
                                    @php
                                        $lastShipping = $po->shippingActivities()
                                            ->whereIn('status_shipping', ['diterima', 'selesai'])
                                            ->latest('tanggal_aktivitas')
                                            ->first();
                                    @endphp
                                    @if($lastShipping)
                                    <tr>
                                        <td><strong>Tiba Pada</strong></td>
                                        <td>: {{ $lastShipping->tanggal_aktivitas->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    @endif
                                    @endif
                                    <tr>
                                        <td><strong>Status</strong></td>
                                        <td>: <span class="badge bg-warning">Menunggu Konfirmasi</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Checklist dengan Batch Support -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="ri-checkbox-multiple-line me-2"></i>Pengecekan Barang Diterima
                        </h5>
                        <small class="text-muted">
                            @if($po->tipe_po === 'internal')
                                Silakan periksa setiap item yang diterima dari gudang
                            @else
                                Silakan periksa setiap item yang diterima dari supplier
                            @endif
                        </small>
                    </div>
                    <div class="card-body p-0">
                        <div id="itemsContainer">
                            @foreach($po->items as $index => $item)
                            <div class="item-wrapper border-bottom" data-item-index="{{ $index }}">
                                <div class="p-3 bg-light">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h6 class="mb-1">
                                                <strong>{{ $item->nama_produk }}</strong>
                                                <input type="hidden" name="items[{{ $index }}][id_po_item]" value="{{ $item->id_po_item }}">
                                            </h6>
                                            @if($item->produk)
                                                <small class="text-muted">{{ $item->produk->merk ?? '' }} - {{ $item->produk->satuan ?? '' }}</small>
                                            @endif
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Qty Diminta:</small>
                                            <h5 class="mb-0 text-primary">{{ number_format($item->qty_diminta) }}</h5>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <button type="button" class="btn btn-sm btn-primary add-batch-btn" data-item-index="{{ $index }}">
                                                <i class="ri-add-line"></i> Tambah Batch
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Batch Container -->
                                <div class="batch-container p-3" id="batchContainer-{{ $index }}">
                                    <!-- Batch pertama (default) -->
                                    <div class="batch-row mb-3 p-3 border rounded bg-white" data-batch-index="0">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="mb-0 text-muted"><i class="ri-stack-line me-1"></i> Batch #1</h6>
                                            <button type="button" class="btn btn-sm btn-danger remove-batch-btn" style="display: none;">
                                                <i class="ri-delete-bin-line"></i> Hapus
                                            </button>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label small">No. Batch</label>
                                                <input type="text" 
                                                       class="form-control form-control-sm" 
                                                       name="items[{{ $index }}][batches][0][batch_number]" 
                                                       placeholder="BATCH001">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small">Exp Date <span class="text-danger">*</span></label>
                                                <input type="date" 
                                                       class="form-control form-control-sm" 
                                                       name="items[{{ $index }}][batches][0][tanggal_kadaluarsa]" 
                                                       required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small">Qty <span class="text-danger">*</span></label>
                                                <input type="number" 
                                                       class="form-control form-control-sm qty-batch-input" 
                                                       name="items[{{ $index }}][batches][0][qty_diterima]" 
                                                       min="1" 
                                                       data-item-index="{{ $index }}"
                                                       required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small">Kondisi <span class="text-danger">*</span></label>
                                                <select class="form-select form-select-sm kondisi-batch-select" 
                                                        name="items[{{ $index }}][batches][0][kondisi]" 
                                                        data-item-index="{{ $index }}"
                                                        required>
                                                    <option value="baik">Baik</option>
                                                    <option value="rusak">Rusak</option>
                                                    <option value="kadaluarsa">Kadaluarsa</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small">Catatan</label>
                                                <input type="text" 
                                                       class="form-control form-control-sm" 
                                                       name="items[{{ $index }}][batches][0][catatan]" 
                                                       placeholder="Opsional">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Summary per item -->
                                <div class="px-3 pb-3">
                                    <div class="alert alert-info mb-0 small">
                                        <strong>Total diterima:</strong> <span class="total-qty-item" data-item-index="{{ $index }}">0</span> dari {{ number_format($item->qty_diminta) }}
                                    </div>
                                </div>
                            </div>
                            @endforeach
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
                            <span class="text-muted">Total Batch:</span>
                            <strong id="totalBatch">1</strong>
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
                        
                        <div class="alert {{ $po->tipe_po === 'internal' ? 'alert-info' : 'alert-success' }} mt-3 small">
                            <i class="ri-information-line me-1"></i>
                            @if($po->tipe_po === 'internal')
                                Sistem akan generate nomor <strong>GR (Good Receive)</strong> dan otomatis menambahkan stok ke apotik
                            @else
                                Sistem akan generate nomor <strong>GR (Good Receive)</strong> dan otomatis menambahkan stok ke gudang
                            @endif
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success btn-lg" id="btnSubmit" onclick="showPinModal()">
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
                            <li class="mb-2">Klik tombol <strong>"Tambah Batch"</strong> jika ada beberapa batch dengan exp date berbeda</li>
                            <li class="mb-2">Isi nomor batch, exp date, dan quantity untuk setiap batch</li>
                            <li class="mb-2">Tentukan kondisi setiap batch (Baik/Rusak/Kadaluarsa)</li>
                            <li class="mb-2">Total qty semua batch tidak boleh melebihi qty yang diminta</li>
                            @if($po->tipe_po === 'internal')
                            <li class="mb-2 text-primary"><strong>Nomor GR akan digenerate otomatis (format: PO-GR-XXXXXX)</strong></li>
                            @else
                            <li class="mb-2 text-success"><strong>Nomor GR akan digenerate otomatis (format: PO-GR-XXXXXX)</strong></li>
                            <li class="text-success"><strong>Setelah konfirmasi, Anda bisa input nomor invoice/faktur</strong></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- PIN Modal --}}
<div class="modal fade" id="pinModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="ri-lock-password-line me-2"></i>Verifikasi PIN Karyawan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-4 py-4">
                <p class="text-muted mb-4">Masukkan PIN 6 digit untuk konfirmasi penerimaan barang</p>
                
                <!-- OTP-style PIN Input -->
                <div class="otp-container d-flex justify-content-center gap-2 mb-4">
                    <input type="password" class="otp-input form-control text-center" maxlength="1" pattern="\d" inputmode="numeric" data-index="0" autocomplete="off">
                    <input type="password" class="otp-input form-control text-center" maxlength="1" pattern="\d" inputmode="numeric" data-index="1" autocomplete="off">
                    <input type="password" class="otp-input form-control text-center" maxlength="1" pattern="\d" inputmode="numeric" data-index="2" autocomplete="off">
                    <input type="password" class="otp-input form-control text-center" maxlength="1" pattern="\d" inputmode="numeric" data-index="3" autocomplete="off">
                    <input type="password" class="otp-input form-control text-center" maxlength="1" pattern="\d" inputmode="numeric" data-index="4" autocomplete="off">
                    <input type="password" class="otp-input form-control text-center" maxlength="1" pattern="\d" inputmode="numeric" data-index="5" autocomplete="off">
                </div>

                <div class="alert alert-info">
                    <small>
                        <i class="ri-information-line me-1"></i>
                        PIN akan digunakan untuk mencatat karyawan yang memproses penerimaan barang
                    </small>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-success" id="confirmPinBtn" disabled>
                    <i class="ri-check-line me-1"></i> Konfirmasi Penerimaan
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .position-sticky {
        position: sticky;
        z-index: 10;
    }
    
    .batch-row {
        transition: all 0.3s ease;
    }
    
    .batch-row:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .item-wrapper {
        transition: all 0.3s ease;
    }
    
    .remove-batch-btn {
        opacity: 0.7;
    }
    
    .remove-batch-btn:hover {
        opacity: 1;
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
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    let batchCounters = {};
    
    // Initialize batch counters
    $('.item-wrapper').each(function() {
        let itemIndex = $(this).data('item-index');
        batchCounters[itemIndex] = 1;
    });
     
    // Add batch button
    $('.add-batch-btn').on('click', function() {
        let itemIndex = $(this).data('item-index');
        let batchIndex = batchCounters[itemIndex];
        let container = $('#batchContainer-' + itemIndex);
        
        let batchHTML = `
            <div class="batch-row mb-3 p-3 border rounded bg-white" data-batch-index="${batchIndex}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 text-muted"><i class="ri-stack-line me-1"></i> Batch #${batchIndex + 1}</h6>
                    <button type="button" class="btn btn-sm btn-danger remove-batch-btn">
                        <i class="ri-delete-bin-line"></i> Hapus
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label small">No. Batch</label>
                        <input type="text" 
                               class="form-control form-control-sm" 
                               name="items[${itemIndex}][batches][${batchIndex}][batch_number]" 
                               placeholder="BATCH001">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Exp Date <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control form-control-sm" 
                               name="items[${itemIndex}][batches][${batchIndex}][tanggal_kadaluarsa]" 
                               required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Qty <span class="text-danger">*</span></label>
                        <input type="number" 
                               class="form-control form-control-sm qty-batch-input" 
                               name="items[${itemIndex}][batches][${batchIndex}][qty_diterima]" 
                               min="1" 
                               data-item-index="${itemIndex}"
                               required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Kondisi <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm kondisi-batch-select" 
                                name="items[${itemIndex}][batches][${batchIndex}][kondisi]" 
                                data-item-index="${itemIndex}"
                                required>
                            <option value="baik">Baik</option>
                            <option value="rusak">Rusak</option>
                            <option value="kadaluarsa">Kadaluarsa</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Catatan</label>
                        <input type="text" 
                               class="form-control form-control-sm" 
                               name="items[${itemIndex}][batches][${batchIndex}][catatan]" 
                               placeholder="Opsional">
                    </div>
                </div>
            </div>
        `;
        
        container.append(batchHTML);
        batchCounters[itemIndex]++;
        
        // Show remove button on first batch if more than 1
        if (batchCounters[itemIndex] > 1) {
            container.find('.remove-batch-btn').show();
        }
        
        calculateSummary();
    });
    
    // Remove batch button
    $(document).on('click', '.remove-batch-btn', function() {
        let batchRow = $(this).closest('.batch-row');
        let itemIndex = $(this).closest('.item-wrapper').data('item-index');
        
        Swal.fire({
            title: 'Hapus Batch?',
            text: 'Batch ini akan dihapus dari daftar',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                batchRow.remove();
                
                // Hide remove button if only 1 batch left
                let container = $('#batchContainer-' + itemIndex);
                if (container.find('.batch-row').length === 1) {
                    container.find('.remove-batch-btn').hide();
                }
                
                calculateSummary();
            }
        });
    });
    
    // Monitor qty and kondisi changes
    $(document).on('input change', '.qty-batch-input, .kondisi-batch-select', function() {
        calculateSummary();
    });
    
    function calculateSummary() {
        let totalBaik = 0;
        let totalRusak = 0;
        let totalDiterima = 0;
        let totalBatch = 0;
        
        // Calculate per item
        $('.item-wrapper').each(function() {
            let itemIndex = $(this).data('item-index');
            let itemTotal = 0;
            
            $(this).find('.batch-row').each(function() {
                totalBatch++;
                let qty = parseInt($(this).find('.qty-batch-input').val()) || 0;
                let kondisi = $(this).find('.kondisi-batch-select').val();
                
                itemTotal += qty;
                totalDiterima += qty;
                
                if (kondisi === 'baik') {
                    totalBaik += qty;
                } else {
                    totalRusak += qty;
                }
            });
            
            // Update per item summary
            $(`.total-qty-item[data-item-index="${itemIndex}"]`).text(itemTotal);
        });
        
        $('#totalBatch').text(totalBatch);
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
    
    // Initial calculation
    calculateSummary();
});

// ============================================
// OTP-STYLE PIN INPUT HANDLER
// ============================================
const otpInputs = document.querySelectorAll('.otp-input');
const confirmPinBtn = document.getElementById('confirmPinBtn');
const pinModal = new bootstrap.Modal(document.getElementById('pinModal'));

otpInputs.forEach((input, index) => {
    input.addEventListener('input', function (e) {
        const value = e.target.value;

        if (!/^\d$/.test(value)) {
            e.target.value = '';
            return;
        }

        e.target.classList.add('filled');

        if (index < otpInputs.length - 1) {
            otpInputs[index + 1].focus();
        }

        checkPinComplete();
    });

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Backspace') {
            if (!e.target.value && index > 0) {
                otpInputs[index - 1].focus();
                otpInputs[index - 1].value = '';
                otpInputs[index - 1].classList.remove('filled', 'error');
            } else {
                e.target.value = '';
                e.target.classList.remove('filled', 'error');
            }
            checkPinComplete();
        } 
        else if (e.key === 'ArrowLeft' && index > 0) {
            e.preventDefault();
            otpInputs[index - 1].focus();
        } 
        else if (e.key === 'ArrowRight' && index < otpInputs.length - 1) {
            e.preventDefault();
            otpInputs[index + 1].focus();
        }
        else if (e.key === 'Enter') {
            e.preventDefault();
            if (isPinComplete()) {
                confirmPinBtn.click();
            }
        }
    });

    input.addEventListener('paste', function (e) {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData).getData('text');
        if (!/^\d{6}$/.test(pasted)) return;

        pasted.split('').forEach((char, i) => {
            if (otpInputs[i]) {
                otpInputs[i].value = char;
                otpInputs[i].classList.add('filled');
            }
        });

        otpInputs[5].focus();
        checkPinComplete();
    });
});

function checkPinComplete() {
    const pin = Array.from(otpInputs).map(i => i.value).join('');
    confirmPinBtn.disabled = pin.length !== 6;
}

function isPinComplete() {
    return Array.from(otpInputs).every(input => input.value !== '');
}

function resetPinInput(error = false) {
    otpInputs.forEach(input => {
        input.value = '';
        input.classList.remove('filled', 'error');
        if (error) input.classList.add('error');
    });
    otpInputs[0].focus();
    confirmPinBtn.disabled = true;
}

// ============================================
// SHOW PIN MODAL
// ============================================
function showPinModal() {
    const totalDiterima = parseInt($('#totalDiterima').text());
    
    if (totalDiterima === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Minimal harus ada 1 item yang diterima'
        });
        return;
    }

    resetPinInput();
    pinModal.show();
}

// ============================================
// SUBMIT PENERIMAAN
// ============================================
confirmPinBtn.addEventListener('click', function () {
    const pin = Array.from(otpInputs).map(i => i.value).join('');
    const form = document.getElementById('formConfirm');
    const formData = new FormData(form);

    formData.append('pin', pin);

    const totalBatch = parseInt($('#totalBatch').text());
    const totalBaik = parseInt($('#totalBaik').text());
    const totalRusak = parseInt($('#totalRusak').text());
    const tipePO = '{{ $po->tipe_po }}';
    const destination = tipePO === 'internal' ? 'Apotik' : 'Gudang';

    // Show confirmation
    Swal.fire({
        title: 'Konfirmasi Penerimaan',
        html: `
            <div class="text-start">
                <p><strong>Anda akan mengkonfirmasi penerimaan:</strong></p>
                <ul>
                    <li>Total Batch: <strong>${totalBatch}</strong></li>
                    <li>Total Diterima (Baik): <strong class="text-success">${totalBaik} unit</strong></li>
                    <li>Total Rusak/Kadaluarsa: <strong class="text-danger">${totalRusak} unit</strong></li>
                </ul>
                <p class="${tipePO === 'internal' ? 'text-primary' : 'text-success'}">
                    <strong><i class="ri-check-line"></i> Nomor GR akan digenerate otomatis</strong>
                </p>
                <p class="${tipePO === 'internal' ? 'text-primary' : 'text-success'}">
                    <strong><i class="ri-arrow-right-line"></i> Stok akan otomatis ditambahkan ke ${destination}</strong>
                </p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Konfirmasi!',
        cancelButtonText: 'Batal',
        width: '600px'
    }).then((result) => {
        if (result.isConfirmed) {
            confirmPinBtn.classList.add('btn-loading');
            confirmPinBtn.disabled = true;

            // Show loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu, sedang memproses penerimaan barang dan generate nomor GR',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch("{{ route('po.confirmex-receipt', $po->id_po) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if (res.success || res.message) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        html: res.message || 'Konfirmasi penerimaan berhasil',
                        confirmButtonColor: '#198754'
                    }).then(() => {
                        window.location.href = "{{ route('po.show', $po->id_po) }}";
                    });
                } else {
                    throw new Error(res.error || 'Terjadi kesalahan');
                }
            })
            .catch(err => {
                resetPinInput(true);
                pinModal.hide();
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: err.message || 'PIN tidak valid atau terjadi kesalahan'
                }).then(() => {
                    pinModal.show();
                    otpInputs[0].focus();
                });
            })
            .finally(() => {
                confirmPinBtn.classList.remove('btn-loading');
                confirmPinBtn.disabled = false;
            });
        }
    });
});
</script>
@endpush