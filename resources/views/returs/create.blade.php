@extends('layouts.app')

@section('title', 'Buat Retur Baru')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('returs.index') }}">Retur</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">Buat Baru</li>
@endsection

@section('content')
<div class="app-body">
    {{-- Alert Messages --}}
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="ri-error-warning-line me-2"></i>
        <h5>Terdapat kesalahan:</h5>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('returs.store') }}" method="POST" id="returForm">
        @csrf
        
        <!-- Hidden fields untuk karyawan pelapor (akan diisi via PIN) -->
        <input type="hidden" name="id_karyawan_pelapor" id="id_karyawan_pelapor">
        <input type="hidden" name="pin" id="pin_hidden">
        
        <div class="row">
            <!-- Left Column -->
            <div class="col-md-8">
                <!-- Informasi Retur -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="ri-information-line me-2"></i>Informasi Retur
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tipe Retur <span class="text-danger">*</span></label>
                                <select name="tipe_retur" id="tipeRetur" class="form-select" required>
                                    <option value="">Pilih Tipe Retur</option>
                                    <option value="po">Purchase Order (PO)</option>
                                    <option value="stock_apotik">Stock Apotik</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Sumber <span class="text-danger">*</span></label>
                                <select name="id_sumber" id="sumberRetur" class="form-select select2-sumber" required disabled>
                                    <option value="">Pilih tipe retur terlebih dahulu</option>
                                </select>
                                <input type="hidden" name="kode_referensi" id="kodeReferensi">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tanggal Retur <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_retur" class="form-control" 
                                       value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Alasan Retur <span class="text-danger">*</span></label>
                                <select name="alasan_retur" class="form-select" required>
                                    <option value="">Pilih Alasan</option>
                                    <option value="barang_rusak">Barang Rusak</option>
                                    <option value="barang_kadaluarsa">Barang Kadaluarsa</option>
                                    <option value="barang_tidak_sesuai">Barang Tidak Sesuai</option>
                                    <option value="kelebihan_pengiriman">Kelebihan Pengiriman</option>
                                    <option value="kesalahan_order">Kesalahan Order</option>
                                    <option value="kualitas_tidak_baik">Kualitas Tidak Baik</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Keterangan Alasan</label>
                                <textarea name="keterangan_alasan" class="form-control" rows="3" 
                                          placeholder="Jelaskan detail alasan retur..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Item Retur -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="ri-box-3-line me-2"></i>Item Retur
                        </h5>
                        <button type="button" class="btn btn-sm btn-success" id="btnLoadItems" disabled>
                            <i class="ri-download-line me-1"></i>Load Item dari Sumber
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle" id="itemTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="25%">Produk</th>
                                        <th width="12%">Batch</th>
                                        <th width="10%">Qty Tersedia</th>
                                        <th width="10%">Qty Retur</th>
                                        <th width="12%">Harga</th>
                                        <th width="12%">Kondisi</th>
                                        <th width="12%">Subtotal</th>
                                        <th width="5%">
                                            <i class="ri-settings-3-line"></i>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="itemTableBody">
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="ri-information-line me-2"></i> 
                                            Pilih sumber retur dan klik "Load Item dari Sumber"
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="table-secondary">
                                    <tr>
                                        <td colspan="7" class="text-end"><strong>Total:</strong></td>
                                        <td colspan="2">
                                            <strong id="grandTotal">Rp 0</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-4">
                <!-- Informasi Unit -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="ri-building-line me-2"></i>Informasi Pelapor
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="ri-information-line me-2"></i>
                            <strong>Info:</strong> Data pelapor akan terisi otomatis berdasarkan PIN yang Anda masukkan saat submit retur.
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Unit Pelapor <span class="text-danger">*</span></label>
                            <select name="unit_pelapor" class="form-select" required>
                                <option value="">Pilih Unit</option>
                                <option value="apotik">Apotik</option>
                                <option value="gudang">Gudang</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Unit Tujuan</label>
                            <select name="unit_tujuan" class="form-select">
                                <option value="">Pilih Tujuan</option>
                                <option value="gudang">Gudang</option>
                                <option value="supplier">Supplier</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Supplier (jika ke supplier)</label>
                            <select name="id_supplier" class="form-select">
                                <option value="">Pilih Supplier</option>
                                @foreach($suppliers ?? [] as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->nama_supplier ?? $supplier->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Catatan -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="ri-sticky-note-line me-2"></i>Catatan
                        </h5>
                    </div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control" rows="4" 
                                  placeholder="Catatan tambahan..."></textarea>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-primary" id="btnSubmitRetur">
                                <i class="ri-save-line me-1"></i>Submit Retur
                            </button>
                            <a href="{{ route('returs.index') }}" class="btn btn-outline-secondary">
                                <i class="ri-close-line me-1"></i>Batal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- PIN OTP Modal --}}
<div class="modal fade" id="pinModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="ri-lock-password-line me-2"></i>Verifikasi PIN Pelapor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-4 py-4">
                <p class="text-muted mb-4">Masukkan PIN 6 digit Anda untuk melanjutkan pembuatan retur</p>
                
                <!-- OTP-style PIN Input -->
                <div class="otp-container d-flex justify-content-center gap-2 mb-4">
                    <input type="password" class="otp-input form-control text-center" maxlength="1" pattern="\d" inputmode="numeric" data-index="0" autocomplete="off">
                    <input type="password" class="otp-input form-control text-center" maxlength="1" pattern="\d" inputmode="numeric" data-index="1" autocomplete="off">
                    <input type="password" class="otp-input form-control text-center" maxlength="1" pattern="\d" inputmode="numeric" data-index="2" autocomplete="off">
                    <input type="password" class="otp-input form-control text-center" maxlength="1" pattern="\d" inputmode="numeric" data-index="3" autocomplete="off">
                    <input type="password" class="otp-input form-control text-center" maxlength="1" pattern="\d" inputmode="numeric" data-index="4" autocomplete="off">
                    <input type="password" class="otp-input form-control text-center" maxlength="1" pattern="\d" inputmode="numeric" data-index="5" autocomplete="off">
                </div>

                <div id="karyawanInfo" style="display: none;">
                    <div class="alert alert-success">
                        <i class="ri-user-line me-2"></i>
                        <strong>Pelapor:</strong> <span id="karyawanNama"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-primary" id="confirmPinBtn" disabled>
                    <i class="ri-check-line me-1"></i> Konfirmasi & Submit
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<style>
    .card {
        border-radius: 0.5rem;
    }
    
    .table td {
        vertical-align: middle;
    }

    code {
        background-color: #f0f7ff;
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
    }

    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
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
    .table {
        border: 1px solid #ced4da !important;
    }
</style>
@endpush

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let itemCounter = 0;
let availableItems = [];
let verifiedKaryawan = null;

// Initialize on document ready
$(document).ready(function() {
    initializeSelect2();
    initializePinOTP();
});

function initializeSelect2() {
    if ($.fn.select2 && $('#sumberRetur').hasClass('select2-hidden-accessible')) {
        $('#sumberRetur').select2('destroy');
    }

    $('#sumberRetur').select2({
        theme: 'bootstrap-5',
        placeholder: 'Ketik untuk mencari...',
        allowClear: true,
        minimumInputLength: 0,
        width: '100%'
    });
}

// ============================================
// PIN OTP INITIALIZATION
// ============================================
function initializePinOTP() {
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
            e.target.classList.remove('error');
            
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
                    otpInputs[index - 1].focus();
                    otpInputs[index - 1].value = '';
                    otpInputs[index - 1].classList.remove('filled', 'error');
                } else {
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
}

function checkPinComplete() {
    const otpInputs = document.querySelectorAll('.otp-input');
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
    document.getElementById('karyawanInfo').style.display = 'none';
    
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
// SUBMIT RETUR BUTTON
// ============================================
document.getElementById('btnSubmitRetur').addEventListener('click', function(e) {
    e.preventDefault();
    
    // Validate form first
    const form = document.getElementById('returForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Validate items
    const items = document.querySelectorAll('#itemTableBody tr');
    let hasValidItem = false;
    
    items.forEach(row => {
        const qtyInput = row.querySelector('.qty-retur');
        if (qtyInput && parseFloat(qtyInput.value) > 0) {
            hasValidItem = true;
        }
    });
    
    if (!hasValidItem) {
        Swal.fire('Perhatian!', 'Tambahkan minimal 1 item dengan qty retur > 0', 'warning');
        return;
    }
    
    // Show PIN modal
    const modal = new bootstrap.Modal(document.getElementById('pinModal'));
    resetPinInputs();
    modal.show();
});

// ============================================
// PIN CONFIRMATION & VERIFICATION
// ============================================
document.getElementById('confirmPinBtn').addEventListener('click', function() {
    const pin = getPinValue();
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
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memverifikasi...';
    
    // Verify PIN
    fetch('/api/verify-pin', {
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
        
        if (data.success && data.karyawan) {
            // Show karyawan info
            verifiedKaryawan = data.karyawan;
            document.getElementById('karyawanNama').textContent = data.karyawan.nama_lengkap;
            document.getElementById('karyawanInfo').style.display = 'block';
            
            // Set hidden fields
            document.getElementById('id_karyawan_pelapor').value = data.karyawan.id_karyawan;
            document.getElementById('pin_hidden').value = pin;
            
            // Close modal and submit form
            setTimeout(() => {
                bootstrap.Modal.getInstance(document.getElementById('pinModal')).hide();
                
                // Actually submit the form
                const submitBtn = document.getElementById('btnSubmitRetur');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="ri-loader-4-line ri-spin me-1"></i>Menyimpan...';
                
                document.getElementById('returForm').submit();
            }, 1000);
        } else {
            showPinError();
            Swal.fire({
                icon: 'error',
                title: 'PIN Tidak Valid',
                text: data.message || 'PIN yang Anda masukkan tidak ditemukan',
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
        console.error('PIN verification error:', error);
    });
});

// ============================================
// TIPE RETUR CHANGE HANDLER
// ============================================
document.getElementById('tipeRetur').addEventListener('change', function() {
    const tipe = this.value;
    const sumberSelect = document.getElementById('sumberRetur');
    const btnLoad = document.getElementById('btnLoadItems');
    
    // Reset select2
    if ($('#sumberRetur').hasClass('select2-hidden-accessible')) {
        $('#sumberRetur').val(null).trigger('change');
    }
    
    sumberSelect.disabled = true;
    btnLoad.disabled = true;
    
    if (!tipe) {
        sumberSelect.innerHTML = '<option value="">Pilih tipe retur terlebih dahulu</option>';
        return;
    }
    
    // Show loading
    sumberSelect.innerHTML = '<option value="">Loading...</option>';
    
    let apiUrl = '';
    if (tipe === 'po') {
        apiUrl = '/api/purchase-orders/completed';
    } else if (tipe === 'stock_apotik') {
        apiUrl = '/api/stock-apotiks';
    }
    
    fetch(apiUrl, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        console.log('API Response:', data);
        
        sumberSelect.innerHTML = '<option value="">Pilih Sumber</option>';
        
        let items = [];
        if (data.success && data.data) {
            items = data.data;
        } else if (Array.isArray(data)) {
            items = data;
        } else if (data.data && Array.isArray(data.data)) {
            items = data.data;
        }
        
        if (items.length > 0) {
            items.forEach(item => {
                let optionText, optionValue, dataKode;
                
                if (tipe === 'po') {
                    optionValue = item.id_po || item.id;
                    dataKode = item.no_po || item.kode;
                    optionText = `${item.no_po || item.kode} - ${formatTanggal(item.tanggal_permintaan) || item.tanggal || ''}`;
                } else if (tipe === 'stock_apotik') {
                    optionValue = item.id;
                    dataKode = item.kode_transaksi || item.kode;
                    optionText = `${item.kode_transaksi || item.kode} - ${item.tanggal_penerimaan || item.tanggal || ''}`;
                }
                
                const option = new Option(optionText, optionValue, false, false);
                option.setAttribute('data-kode', dataKode);
                sumberSelect.appendChild(option);
            });
            
            sumberSelect.disabled = false;
            initializeSelect2();
        } else {
            sumberSelect.innerHTML = '<option value="">Tidak ada data tersedia</option>';
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                text: 'Tidak ada data ' + (tipe === 'po' ? 'Purchase Order' : 'Stock Apotik') + ' yang tersedia'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        sumberSelect.innerHTML = '<option value="">Gagal memuat data</option>';
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Gagal memuat data: ' + error.message
        });
    });
});

function formatTanggal(dateString) {
    if (!dateString) return '';
    return dateString.split('T')[0];
}

// Handle sumber change
$('#sumberRetur').on('select2:select', function (e) {
    const btnLoad = document.getElementById('btnLoadItems');
    const data = e.params.data;
    
    if (data.id) {
        btnLoad.disabled = false;
        const selectedOption = e.target.options[e.target.selectedIndex];
        document.getElementById('kodeReferensi').value = selectedOption.dataset.kode || '';
    } else {
        btnLoad.disabled = true;
        document.getElementById('kodeReferensi').value = '';
    }
});

$('#sumberRetur').on('select2:clear', function (e) {
    document.getElementById('btnLoadItems').disabled = true;
    document.getElementById('kodeReferensi').value = '';
});

// Load items from source
document.getElementById('btnLoadItems').addEventListener('click', function() {
    const tipe = document.getElementById('tipeRetur').value;
    const idSumber = document.getElementById('sumberRetur').value;
    
    if (!tipe || !idSumber) {
        Swal.fire('Perhatian!', 'Pilih tipe retur dan sumber terlebih dahulu', 'warning');
        return;
    }
    
    const tableBody = document.getElementById('itemTableBody');
    tableBody.innerHTML = '<tr><td colspan="9" class="text-center"><i class="ri-loader-4-line ri-spin"></i> Loading...</td></tr>';
    
    fetch('/api/returs/available-items', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            tipe_retur: tipe,
            id_sumber: idSumber
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Items Response:', data);
        
        if (data.success) {
            availableItems = data.data;
            renderItems();
        } else {
            Swal.fire('Error!', 'Gagal memuat item: ' + (data.message || 'Unknown error'), 'error');
            tableBody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Gagal memuat item</td></tr>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error!', 'Terjadi kesalahan saat memuat item: ' + error.message, 'error');
        tableBody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Terjadi kesalahan</td></tr>';
    });
});

function renderItems() {
    const tableBody = document.getElementById('itemTableBody');
    tableBody.innerHTML = '';
    
    if (!availableItems || availableItems.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">Tidak ada item tersedia</td></tr>';
        return;
    }
    
    availableItems.forEach((item, index) => {
        const row = createItemRow(item, index);
        tableBody.innerHTML += row;
    });
    
    attachItemEventListeners();
}

function createItemRow(item, index) {
    const qtyTersedia = item.qty_diterima || item.stock_apotik || 0;
    const hargaSatuan = item.harga_satuan || 0;
    const batchNumber = item.batch_number || item.no_batch || '';
    
    return `
        <tr data-index="${index}">
            <td class="text-center">${index + 1}</td>
            <td>
                <strong>${item.nama_produk}</strong>
                <input type="hidden" name="items[${index}][id_item_sumber]" value="${item.id_po_item || item.id_detail_stock_apotik}">
                <input type="hidden" name="items[${index}][id_produk]" value="${item.id_produk}">
                <input type="hidden" name="items[${index}][nama_produk]" value="${item.nama_produk}">
            </td>
            <td>
                <input type="text" name="items[${index}][batch_number]" class="form-control form-control-sm" 
                       value="${batchNumber}" readonly>
            </td>
            <td class="text-center">
                <span class="badge bg-info">${qtyTersedia}</span>
            </td>
            <td>
                <input type="number" name="items[${index}][qty_diretur]" 
                       class="form-control form-control-sm qty-retur text-center" 
                       min="0" max="${qtyTersedia}" value="0" required>
            </td>
            <td>
                <input type="number" name="items[${index}][harga_satuan]" 
                       class="form-control form-control-sm harga-satuan text-end" 
                       value="${hargaSatuan}" step="0.01" readonly>
            </td>
            <td>
                <select name="items[${index}][kondisi_barang]" class="form-select form-select-sm" required>
                    <option value="">Pilih</option>
                    <option value="rusak">Rusak</option>
                    <option value="kadaluarsa">Kadaluarsa</option>
                    <option value="baik">Baik</option>
                </select>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm subtotal text-end" readonly value="Rp 0">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger btn-remove-item" title="Hapus">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </td>
        </tr>
    `;
}

function attachItemEventListeners() {
    document.querySelectorAll('.qty-retur, .harga-satuan').forEach(input => {
        input.addEventListener('input', function() {
            const row = this.closest('tr');
            calculateSubtotal(row);
            calculateGrandTotal();
        });
    });
    
    document.querySelectorAll('.btn-remove-item').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Hapus item ini dari daftar retur?')) {
                this.closest('tr').remove();
                calculateGrandTotal();
            }
        });
    });
}

function calculateSubtotal(row) {
    const qty = parseFloat(row.querySelector('.qty-retur').value) || 0;
    const harga = parseFloat(row.querySelector('.harga-satuan').value) || 0;
    const subtotal = qty * harga;
    
    row.querySelector('.subtotal').value = formatRupiah(subtotal);
}

function calculateGrandTotal() {
    let total = 0;
    document.querySelectorAll('.qty-retur').forEach(input => {
        const row = input.closest('tr');
        const qty = parseFloat(input.value) || 0;
        const harga = parseFloat(row.querySelector('.harga-satuan').value) || 0;
        total += qty * harga;
    });
    
    document.getElementById('grandTotal').textContent = formatRupiah(total);
}

function formatRupiah(angka) {
    return 'Rp ' + Math.floor(angka).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Clear PIN modal when hidden
document.getElementById('pinModal').addEventListener('hidden.bs.modal', function() {
    if (!verifiedKaryawan) {
        resetPinInputs();
    }
});

// Focus first input when modal is shown
document.getElementById('pinModal').addEventListener('shown.bs.modal', function() {
    const firstInput = document.querySelector('.otp-input');
    if (firstInput) {
        firstInput.focus();
    }
});
</script>
@endpush