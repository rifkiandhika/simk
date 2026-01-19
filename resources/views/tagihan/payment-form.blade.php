@extends('layouts.app')

@section('title', 'Input Pembayaran - ' . $tagihan->no_tagihan)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tagihan.index') }}">Tagihan PO</a></li>
    <li class="breadcrumb-item"><a href="{{ route('tagihan.show', $tagihan->id_tagihan) }}">{{ $tagihan->no_tagihan }}</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">Input Pembayaran</li>
@endsection

@section('content')
<div class="app-body">
    {{-- Alert Messages --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i>
            <strong>Terjadi Kesalahan!</strong>
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

    <!-- Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="ri-money-dollar-circle-line me-2"></i>Input Pembayaran Tagihan
                </h4>
                <a href="{{ route('tagihan.show', $tagihan->id_tagihan) }}" class="btn btn-secondary btn-sm">
                    <i class="ri-arrow-left-line me-1"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Form Pembayaran -->
        <div class="col-lg-8">
            <form id="paymentForm" enctype="multipart/form-data">
                @csrf
                
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 text-white">
                            <i class="ri-file-edit-line me-2"></i>Form Pembayaran
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Jumlah Bayar -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    Jumlah Bayar <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" 
                                           class="form-control @error('jumlah_bayar') is-invalid @enderror" 
                                           name="jumlah_bayar" 
                                           id="jumlahBayar"
                                           value="{{ old('jumlah_bayar', $tagihan->sisa_tagihan) }}"
                                           max="{{ $tagihan->sisa_tagihan }}"
                                           min="1"
                                           step="1"
                                           required>
                                    @error('jumlah_bayar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">
                                    Maksimal: Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}
                                </small>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="setBayarPenuh()">
                                        Bayar Penuh (Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }})
                                    </button>
                                </div>
                            </div>

                            <!-- Tanggal Bayar -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    Tanggal Bayar <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('tanggal_bayar') is-invalid @enderror" 
                                       name="tanggal_bayar" 
                                       value="{{ old('tanggal_bayar', date('Y-m-d')) }}"
                                       max="{{ date('Y-m-d') }}"
                                       required>
                                @error('tanggal_bayar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Metode Pembayaran -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    Metode Pembayaran <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('metode_pembayaran') is-invalid @enderror" 
                                        name="metode_pembayaran" 
                                        id="metodePembayaran"
                                        required>
                                    <option value="">-- Pilih Metode --</option>
                                    <option value="transfer" {{ old('metode_pembayaran') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                    <option value="cash" {{ old('metode_pembayaran') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="giro" {{ old('metode_pembayaran') == 'giro' ? 'selected' : '' }}>Giro</option>
                                    <option value="kartu_kredit" {{ old('metode_pembayaran') == 'kartu_kredit' ? 'selected' : '' }}>Kartu Kredit</option>
                                    <option value="lainnya" {{ old('metode_pembayaran') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('metode_pembayaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Nomor Referensi -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    Nomor Referensi
                                </label>
                                <input type="text" 
                                       class="form-control @error('nomor_referensi') is-invalid @enderror" 
                                       name="nomor_referensi" 
                                       placeholder="No Transfer/Giro/Referensi"
                                       value="{{ old('nomor_referensi') }}">
                                @error('nomor_referensi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Opsional: No transfer, no giro, atau referensi lainnya</small>
                            </div>

                            <!-- Upload Bukti Pembayaran -->
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">
                                    Bukti Pembayaran
                                </label>
                                <input type="file" 
                                       class="form-control @error('bukti_pembayaran') is-invalid @enderror" 
                                       name="bukti_pembayaran" 
                                       id="buktiBayar"
                                       accept=".jpg,.jpeg,.png,.pdf">
                                @error('bukti_pembayaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Format: JPG, PNG, PDF. Max: 5MB</small>
                                
                                <!-- Preview -->
                                <div id="previewContainer" class="mt-3" style="display: none;">
                                    <img id="previewImage" src="" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                                </div>
                            </div>

                            <!-- Catatan -->
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Catatan</label>
                                <textarea class="form-control @error('catatan') is-invalid @enderror" 
                                          name="catatan" 
                                          rows="3" 
                                          placeholder="Catatan tambahan (opsional)">{{ old('catatan') }}</textarea>
                                @error('catatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('tagihan.show', $tagihan->id_tagihan) }}" class="btn btn-secondary">
                                <i class="ri-close-line me-1"></i>Batal
                            </a>
                            <button type="button" class="btn btn-success" id="submitBtn" onclick="showPinModal()">
                                <i class="ri-save-line me-1"></i>Simpan Pembayaran
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Summary -->
        <div class="col-lg-4">
            <!-- Info Tagihan -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h6 class="mb-0 text-white">
                        <i class="ri-file-list-line me-2"></i>Informasi Tagihan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">No Tagihan:</small>
                        <br><strong>{{ $tagihan->no_tagihan }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">No PO:</small>
                        <br><strong>{{ $tagihan->purchaseOrder->no_po }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Supplier:</small>
                        <br><strong>{{ $tagihan->supplier->nama_supplier ?? '-' }}</strong>
                    </div>
                    <hr>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Grand Total:</span>
                            <strong>Rp {{ number_format($tagihan->grand_total, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Sudah Dibayar:</span>
                            <strong class="text-success">Rp {{ number_format($tagihan->total_dibayar, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted fw-bold">Sisa Tagihan:</span>
                            <strong class="text-danger fs-5">Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    @if($tagihan->tanggal_jatuh_tempo)
                    <div class="alert alert-warning mb-0">
                        <small>
                            <i class="ri-calendar-line me-1"></i>
                            Jatuh Tempo: <strong>{{ $tagihan->tanggal_jatuh_tempo->format('d/m/Y') }}</strong>
                        </small>
                    </div>
                    @endif
                </div>
            </div>

           <!-- Info Pembayaran -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="ri-information-line me-2"></i>Informasi Penting
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="ri-checkbox-circle-line text-success me-2"></i>
                            Pembayaran dapat dilakukan bertahap
                        </li>
                        <li class="mb-2">
                            <i class="ri-checkbox-circle-line text-success me-2"></i>
                            Upload bukti pembayaran untuk dokumentasi
                        </li>
                        <li class="mb-2">
                            <i class="ri-checkbox-circle-line text-success me-2"></i>
                            Status tagihan akan langsung terupdate
                        </li>
                        <li class="mb-2">
                            <i class="ri-checkbox-circle-line text-success me-2"></i>
                            Masukkan PIN untuk verifikasi
                        </li>
                        <li class="mb-0">
                            <i class="ri-checkbox-circle-line text-success me-2"></i>
                            Pastikan data pembayaran sudah benar
                        </li>
                    </ul>
                </div>
            </div>

            <!-- History Pembayaran -->
            @if($tagihan->pembayaran->count() > 0)
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="ri-history-line me-2"></i>History Pembayaran
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($tagihan->pembayaran->take(5) as $payment)
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <div>
                            <small class="text-muted d-block">{{ $payment->tanggal_bayar->format('d/m/Y') }}</small>
                            <strong>Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}</strong>
                        </div>
                        <div>
                            @if($payment->status_pembayaran == 'diverifikasi')
                                <span class="badge bg-success">Verified</span>
                            @elseif($payment->status_pembayaran == 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @else
                                <span class="badge bg-danger">Ditolak</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    
                    @if($tagihan->pembayaran->count() > 5)
                    <a href="{{ route('tagihan.payment.history', $tagihan->id_tagihan) }}" class="btn btn-sm btn-outline-primary w-100 mt-2">
                        Lihat Semua
                    </a>
                    @endif
                </div>
            </div>
            @endif
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
                <p class="text-muted mb-4">Masukkan PIN 6 digit untuk memproses pembayaran</p>
                
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
                        PIN akan digunakan untuk mencatat karyawan yang memproses pembayaran
                    </small>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-success" id="confirmPinBtn" disabled>
                    <i class="ri-check-line me-1"></i> Konfirmasi Pembayaran
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .card {
        border-radius: 0.5rem;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .input-group-text {
        background-color: #f8f9fa;
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
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function setBayarPenuh() {
        document.getElementById('jumlahBayar').value = {{ $tagihan->sisa_tagihan }};
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Preview image upload
        const buktiBayar = document.getElementById('buktiBayar');
        const previewContainer = document.getElementById('previewContainer');
        const previewImage = document.getElementById('previewImage');

        buktiBayar.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        previewContainer.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                } else {
                    previewContainer.style.display = 'none';
                }

                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'File Terlalu Besar',
                        text: 'Ukuran file maksimal 5MB'
                    });
                    buktiBayar.value = '';
                    previewContainer.style.display = 'none';
                }
            }
        });

        // Format number input
        const jumlahBayarInput = document.getElementById('jumlahBayar');
        jumlahBayarInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            e.target.value = value;
        });

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

        // Handle paste 6 digit PIN
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
        const jumlah = parseInt(document.getElementById('jumlahBayar').value);
        const sisa = {{ $tagihan->sisa_tagihan }};

        if (!jumlah || jumlah <= 0) {
            Swal.fire('Perhatian', 'Jumlah bayar tidak boleh kosong', 'warning');
            return;
        }

        if (jumlah > sisa) {
            Swal.fire('Perhatian', 'Jumlah bayar melebihi sisa tagihan', 'warning');
            return;
        }

        const metode = document.getElementById('metodePembayaran').value;
        if (!metode) {
            Swal.fire('Perhatian', 'Pilih metode pembayaran terlebih dahulu', 'warning');
            return;
        }

        resetPinInput();
        pinModal.show();
    }

    // ============================================
    // SUBMIT PEMBAYARAN
    // ============================================
    confirmPinBtn.addEventListener('click', function () {
        const pin = Array.from(otpInputs).map(i => i.value).join('');
        const form = document.getElementById('paymentForm');
        const formData = new FormData(form);

        formData.append('pin', pin);

        confirmPinBtn.classList.add('btn-loading');
        confirmPinBtn.disabled = true;

        fetch("{{ route('tagihan.payment.process', $tagihan->id_tagihan) }}", {
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
                    text: res.message || 'Pembayaran berhasil disimpan',
                    confirmButtonColor: '#198754'
                }).then(() => {
                    window.location.href = "{{ route('tagihan.show', $tagihan->id_tagihan) }}";
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
    });
</script>
@endpush