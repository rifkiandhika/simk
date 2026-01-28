@extends('layouts.app')

@section('title', 'Input Invoice/Faktur')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('po.index') }}">Purchase Order</a></li>
    <li class="breadcrumb-item"><a href="{{ route('po.show', $po->id_po) }}">Detail PO</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">Input Invoice</li>
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

    <div class="row">
        <div class="col-xl-8">
            <form id="formInvoice">
                @csrf
                
                <!-- PO & GR Info -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-success text-white py-3">
                        <h5 class="mb-0">
                            <i class="ri-file-list-3-line me-2"></i>Informasi PO & Good Receive
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="150"><strong>No. PO</strong></td>
                                        <td>: {{ $po->no_po }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>No. GR</strong></td>
                                        <td>: <span class="badge bg-primary">{{ $po->no_gr }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Supplier</strong></td>
                                        <td>: <strong class="text-success">{{ $po->supplier->nama_supplier }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal Diterima</strong></td>
                                        <td>: {{ $po->tanggal_diterima ? \Carbon\Carbon::parse($po->tanggal_diterima)->format('d/m/Y H:i') : '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="150"><strong>Total Item</strong></td>
                                        <td>: {{ $po->items->count() }} produk</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Quantity</strong></td>
                                        <td>: {{ $po->items->sum('qty_diterima') }} unit</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Grand Total</strong></td>
                                        <td>: <strong class="text-primary">Rp {{ number_format($po->grand_total, 0, ',', '.') }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Form -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="ri-file-text-line me-2"></i>Data Invoice/Faktur
                        </h5>
                        <small class="text-muted">Masukkan data invoice yang diterima dari supplier</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    No. Surat Jalan <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control"
                                    name="surat_jalan"
                                    id="noSuratJalan"
                                    placeholder="Contoh: SJ-001/01/2025"
                                    value="{{ $po->surat_jalan }}"
                                    required>
                                <small class="text-muted">Nomor surat jalan dari supplier</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    Tanggal Surat Jalan <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                    class="form-control"
                                    name="tanggal_surat_jalan"
                                    id="tanggalSuratJalan"
                                    value="{{ $po->tanggal_surat_jalan }}"
                                    required>
                                <small class="text-muted">Tanggal surat jalan diterbitkan</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    No. Invoice/Faktur <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       name="no_invoice" 
                                       id="noInvoice"
                                       placeholder="Contoh: INV-SUP-001"
                                       value="{{ $po->no_invoice }}"
                                       required>
                                <small class="text-muted">Nomor invoice dari supplier</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    Tanggal Invoice <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       name="tanggal_invoice" 
                                       id="tanggalInvoice"
                                       value="{{ $po->tanggal_invoice ??  date('Y-m-d') }}"
                                       required>
                                <small class="text-muted">Tanggal invoice dari supplier</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    Tanggal Jatuh Tempo <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       name="tanggal_jatuh_tempo" 
                                       id="tanggalJatuhTempo"
                                       value="{{ $po->tanggal_jatuh_tempo ? \Carbon\Carbon::parse($po->tanggal_jatuh_tempo)->format('Y-m-d') : '' }}"
                                       required>
                                <small class="text-muted">Tanggal pembayaran harus dilakukan</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    Nomor Faktur Pajak
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       name="nomor_faktur_pajak" 
                                       id="nomorFakturPajak"
                                       value="{{ $po->nomor_faktur_pajak }}"
                                       placeholder="Contoh: 010.000-25.00000001">
                                <small class="text-muted">Opsional, jika ada faktur pajak</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    Nomor Kwitansi
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       name="no_kwitansi" 
                                       id="noKwitansi"
                                       value="{{ $po->no_kwitansi }}"
                                       placeholder="Contoh: KW-1024/12">
                                <small class="text-muted">Opsional, jika ada nomor kwitansi</small>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="ri-information-line me-2"></i>
                            <strong>Informasi:</strong> Data invoice ini akan digunakan untuk proses pembayaran dan pencatatan keuangan.
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-3">
                    <button type="button" class="btn btn-success btn-lg" id="btnSubmit" onclick="showPinModal()">
                        <i class="ri-save-line me-1"></i> Simpan Invoice
                    </button>
                    <a href="{{ route('po.show', $po->id_po) }}" class="btn btn-outline-secondary">
                        <i class="ri-arrow-left-line me-1"></i> Kembali
                    </a>
                </div>
            </form>
        </div>

        <!-- Right Column - Info -->
        <div class="col-xl-4">
            <!-- Items Summary -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="ri-shopping-cart-line me-2"></i>Ringkasan Item</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($po->items as $item)
                                <tr>
                                    <td>
                                        <small>{{ $item->nama_produk }}</small>
                                        @if($item->batches->count() > 0)
                                            <br><span class="badge badge-sm bg-secondary">{{ $item->batches->count() }} batch</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ $item->qty_diterima }}</span>
                                    </td>
                                    <td class="text-end">
                                        <small>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="2">Total</th>
                                    <th class="text-end">Rp {{ number_format($po->total_harga, 0, ',', '.') }}</th>
                                </tr>
                                @if($po->pajak > 0)
                                <tr>
                                    <th colspan="2">Pajak</th>
                                    <th class="text-end">Rp {{ number_format($po->pajak, 0, ',', '.') }}</th>
                                </tr>
                                @endif
                                <tr class="table-success">
                                    <th colspan="2">Grand Total</th>
                                    <th class="text-end">Rp {{ number_format($po->grand_total, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="ri-information-line text-info me-2"></i>Informasi Penting
                    </h6>
                    <ul class="small mb-0">
                        <li class="mb-2">Pastikan nomor invoice sesuai dengan dokumen dari supplier</li>
                        <li class="mb-2">Tanggal jatuh tempo digunakan untuk reminder pembayaran</li>
                        <li class="mb-2">Faktur pajak diisi jika supplier mengeluarkan faktur pajak</li>
                        <li class="mb-2">Data invoice dapat diedit sebelum pembayaran dilakukan</li>
                        <li class="text-success"><strong>Setelah disimpan, PO ini siap untuk proses pembayaran</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
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
                <p class="text-muted mb-4">Masukkan PIN 6 digit untuk konfirmasi data invoice</p>
                
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
                        PIN akan digunakan untuk mencatat karyawan yang menginput data invoice
                    </small>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-success" id="confirmPinBtn" disabled>
                    <i class="ri-check-line me-1"></i> Simpan Invoice
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
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
    // Auto calculate due date (default 14 days)
    $('#tanggalInvoice').on('change', function() {
        let invoiceDate = new Date($(this).val());
        if (invoiceDate && !$('#tanggalJatuhTempo').val()) {
            invoiceDate.setDate(invoiceDate.getDate() + 14);
            let dueDate = invoiceDate.toISOString().split('T')[0];
            $('#tanggalJatuhTempo').val(dueDate);
        }
    });
    
    // Validate due date must be after invoice date
    $('#tanggalJatuhTempo').on('change', function() {
        let invoiceDate = new Date($('#tanggalInvoice').val());
        let dueDate = new Date($(this).val());
        
        if (dueDate < invoiceDate) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Tanggal jatuh tempo tidak boleh lebih awal dari tanggal invoice',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            $(this).val('');
        }
    });
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
    const noInvoice = $('#noInvoice').val().trim();
    const tanggalInvoice = $('#tanggalInvoice').val();
    const tanggalJatuhTempo = $('#tanggalJatuhTempo').val();
    const noSuratJalan = $('#noSuratJalan').val().trim();
    const tanggalSuratJalan = $('#tanggalSuratJalan').val();
    
    // Validation
    if (!noInvoice) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Nomor invoice harus diisi'
        });
        $('#noInvoice').focus();
        return;
    }
    
    if (!tanggalInvoice) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Tanggal invoice harus diisi'
        });
        $('#tanggalInvoice').focus();
        return;
    }

    if (!noSuratJalan) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Nomor surat jalan harus diisi'
        });
        $('#noSuratJalan').focus();
        return;
    }

    if (!tanggalSuratJalan) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Tanggal surat jalan harus diisi'
        });
        $('#tanggalSuratJalan').focus();
        return;
    }
    
    if (!tanggalJatuhTempo) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Tanggal jatuh tempo harus diisi'
        });
        $('#tanggalJatuhTempo').focus();
        return;
    }

    resetPinInput();
    pinModal.show();
}

// ============================================
// SUBMIT INVOICE
// ============================================
confirmPinBtn.addEventListener('click', function () {
    const pin = Array.from(otpInputs).map(i => i.value).join('');
    const form = document.getElementById('formInvoice');
    const formData = new FormData(form);

    formData.append('pin', pin);

    const noInvoice = $('#noInvoice').val();
    const tanggalInvoice = $('#tanggalInvoice').val();
    const tanggalJatuhTempo = $('#tanggalJatuhTempo').val();

    // Show confirmation
    Swal.fire({
        title: 'Konfirmasi Data Invoice',
        html: `
            <div class="text-start">
                <p><strong>Pastikan data invoice sudah benar:</strong></p>
                <ul>
                    <li><strong>No. Invoice:</strong> ${noInvoice}</li>
                    <li><strong>Tanggal Invoice:</strong> ${tanggalInvoice}</li>
                    <li><strong>Jatuh Tempo:</strong> ${tanggalJatuhTempo}</li>
                    <li><strong>Grand Total:</strong> Rp {{ number_format($po->grand_total, 0, ',', '.') }}</li>
                </ul>
                <p class="text-success"><strong>Data akan disimpan dan PO siap untuk pembayaran</strong></p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Simpan!',
        cancelButtonText: 'Periksa Lagi',
        width: '600px'
    }).then((result) => {
        if (result.isConfirmed) {
            confirmPinBtn.classList.add('btn-loading');
            confirmPinBtn.disabled = true;

            // Show loading
            Swal.fire({
                title: 'Menyimpan...',
                text: 'Mohon tunggu, sedang menyimpan data invoice',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch("{{ route('po.store-invoice', $po->id_po) }}", {
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
                        html: res.message || 'Data invoice berhasil disimpan',
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