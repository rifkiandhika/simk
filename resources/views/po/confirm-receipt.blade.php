@extends('layouts.app')

@section('title', 'Konfirmasi Penerimaan')

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

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-check-line me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form id="formConfirm" enctype="multipart/form-data">
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
                                        <td>: {{ $po->karyawanPemohon->nama_lengkap ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="120"><strong>Disetujui Oleh</strong></td>
                                        <td>: {{ $po->kepalaGudang->nama_lengkap ?? '-' }}</td>
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
                                        <th>Produk</th>
                                        <th width="100">Qty Diminta</th>
                                        <th width="120">Qty Diterima <span class="text-danger">*</span></th>
                                        <th width="150">Kondisi <span class="text-danger">*</span></th>
                                        <th width="200">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($po->items as $index => $item)
                                    <tr id="row-{{ $index }}">
                                        <td class="text-center">
                                            {{ $index + 1 }}
                                            <input type="hidden" 
                                                   name="items[{{ $index }}][id_po_item]" 
                                                   value="{{ $item->id_po_item }}"
                                                   data-item-id="{{ $item->id_po_item }}"
                                                   data-produk-id="{{ $item->id_produk }}"
                                                   class="item-id-input">
                                        </td>
                                        <td>
                                            <strong>{{ $item->nama_produk }}</strong>
                                            @if($item->produk)
                                                <br><small class="text-muted">
                                                    {{ $item->produk->merk ?? '' }}
                                                    @if($item->produk->merk && $item->produk->satuan) - @endif
                                                    {{ $item->produk->satuan ?? '' }}
                                                </small>
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
                                                <option value="baik" selected>✓ Baik</option>
                                                <option value="rusak">✗ Rusak</option>
                                                <option value="kadaluarsa">⚠ Kadaluarsa</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" 
                                                   class="form-control form-control-sm" 
                                                   name="items[{{ $index }}][catatan]"
                                                   id="catatan-{{ $index }}"
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
                            <strong id="totalBaik" class="text-success">{{ $po->items->sum('qty_diminta') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-danger">Qty Rusak/Expired:</span>
                            <strong id="totalRusak" class="text-danger">0</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Total Diterima:</span>
                            <h5 class="text-primary mb-0" id="totalDiterima">{{ $po->items->sum('qty_diminta') }}</h5>
                        </div>
                        
                        <div class="alert alert-success mt-3 small">
                            <i class="ri-checkbox-circle-line me-1"></i>
                            <strong>Stok akan otomatis:</strong>
                            <ul class="mb-0 mt-2 ps-3">
                                <li>Dikurangi dari gudang</li>
                                <li>Ditambahkan ke apotik</li>
                                <li>Per batch untuk tracking</li>
                            </ul>
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
                            <li class="mb-2">✓ Periksa kondisi fisik setiap barang</li>
                            <li class="mb-2">✓ Pastikan jumlah sesuai permintaan</li>
                            <li class="mb-2">✓ Barang rusak/kadaluarsa ubah kondisi</li>
                            <li class="mb-2">✓ Masukkan PIN untuk verifikasi</li>
                            <li class="mb-2 text-primary"><strong>✓ Stok gudang akan otomatis berkurang</strong></li>
                            <li class="mb-2 text-success"><strong>✓ Stok apotik akan otomatis bertambah</strong></li>
                            <li>⚠ Barang rusak/kadaluarsa masuk retur</li>
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
    console.log('Confirm Receipt Form Loaded');
    console.log('Total Items:', {{ $po->items->count() }});
    
    calculateSummary();
    
    $('.qty-input').on('input change', function() {
        const maxQty = parseInt($(this).data('diminta'));
        const currentQty = parseInt($(this).val()) || 0;
        
        if (currentQty > maxQty) {
            $(this).val(maxQty);
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: `Jumlah tidak boleh melebihi ${maxQty}`,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        }
        
        calculateSummary();
    });
    
    $('.kondisi-select').on('change', function() {
        const kondisi = $(this).val();
        const row = $(this).closest('tr');
        
        if (kondisi === 'baik') {
            row.removeClass('table-danger table-warning');
        } else if (kondisi === 'rusak') {
            row.removeClass('table-warning').addClass('table-danger');
        } else {
            row.removeClass('table-danger').addClass('table-warning');
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
        
        if (totalDiterima === 0) {
            $('#btnSubmit').prop('disabled', true).addClass('disabled');
        } else {
            $('#btnSubmit').prop('disabled', false).removeClass('disabled');
        }
    }

    // Auto-hide alerts
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
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

    // Validate all id_po_item exists
    let hasError = false;
    $('.item-id-input').each(function(i) {
        const value = $(this).val();
        if (!value || value === '') {
            console.error(`Item ${i} missing id_po_item:`, value);
            hasError = true;
        }
    });
    
    if (hasError) {
        Swal.fire({
            icon: 'error',
            title: 'Error Validasi',
            text: 'Terdapat item dengan ID tidak valid. Silakan refresh halaman.'
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

    const totalBaik = parseInt($('#totalBaik').text());
    const totalRusak = parseInt($('#totalRusak').text());

    // Show confirmation
    Swal.fire({
        title: 'Konfirmasi Penerimaan',
        html: `
            <div class="text-start">
                <p><strong>Anda akan mengkonfirmasi penerimaan:</strong></p>
                <ul>
                    <li>Total Diterima (Baik): <strong class="text-success">${totalBaik} item</strong></li>
                    <li>Total Retur: <strong class="text-danger">${totalRusak} item</strong></li>
                </ul>
                <div class="alert alert-info small mb-0">
                    <strong>Proses otomatis:</strong><br>
                    ✓ Stock gudang akan berkurang<br>
                    ✓ Stock apotik akan bertambah<br>
                    ✓ Tracking per batch
                </div>
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
                title: 'Memproses Penerimaan...',
                html: `
                    <div class="text-center">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p>Mohon tunggu, sedang memproses:</p>
                        <ul class="text-start small">
                            <li>✓ Mengurangi stock gudang</li>
                            <li>✓ Menambah stock apotik</li>
                            <li>✓ Menyimpan tracking batch</li>
                            <li>✓ Update status PO</li>
                        </ul>
                    </div>
                `,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false
            });

            fetch("{{ route('po.confirm-receipt', $po->id_po) }}", {
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
                    throw new Error(res.error || 'PIN salah');
                }
            })
            .catch(err => {
                resetPinInput(true);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: err.message || 'PIN tidak valid'
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