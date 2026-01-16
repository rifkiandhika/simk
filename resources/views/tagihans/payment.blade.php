@extends('layouts.app')

@section('title', 'Input Pembayaran - ' . $tagihan->no_tagihan)

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('tagihans.index') }}">Tagihan</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('tagihans.show', $tagihan->id_tagihan) }}">{{ $tagihan->no_tagihan }}</a>
    </li>
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
                <a href="{{ route('tagihans.show', $tagihan->id_tagihan) }}" class="btn btn-secondary btn-sm">
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
                <input type="hidden" name="id_tagihan" value="{{ $tagihan->id_tagihan }}">
                
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
                                    <i class="ri-money-dollar-circle-line me-1"></i>
                                    Jumlah Bayar <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" 
                                           class="form-control @error('jumlah_bayar') is-invalid @enderror" 
                                           name="jumlah_bayar" 
                                           id="jumlahBayar"
                                           value="{{ old('jumlah_bayar') }}"
                                           placeholder="0"
                                           required>
                                    @error('jumlah_bayar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">
                                    <i class="ri-information-line"></i>
                                    Sisa tagihan: <strong class="text-danger">Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</strong>
                                </small>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="setBayarPenuh()">
                                        <i class="ri-checkbox-circle-line me-1"></i>
                                        Bayar Penuh (Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }})
                                    </button>
                                </div>
                            </div>

                            <!-- Tanggal Bayar -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="ri-calendar-line me-1"></i>
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
                                    <i class="ri-bank-card-line me-1"></i>
                                    Metode Pembayaran <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('metode') is-invalid @enderror" 
                                        name="metode" 
                                        id="metodePembayaran"
                                        required>
                                    <option value="">-- Pilih Metode Pembayaran --</option>
                                    <option value="TUNAI" {{ old('metode') == 'TUNAI' ? 'selected' : '' }}>Tunai</option>
                                    <option value="TRANSFER" {{ old('metode') == 'TRANSFER' ? 'selected' : '' }}>Transfer Bank</option>
                                    <option value="DEBIT" {{ old('metode') == 'DEBIT' ? 'selected' : '' }}>Kartu Debit</option>
                                    <option value="CREDIT" {{ old('metode') == 'CREDIT' ? 'selected' : '' }}>Kartu Kredit</option>
                                    <option value="GIRO" {{ old('metode') == 'GIRO' ? 'selected' : '' }}>Giro</option>
                                    <option value="BPJS" {{ old('metode') == 'BPJS' ? 'selected' : '' }}>BPJS</option>
                                    <option value="ASURANSI" {{ old('metode') == 'ASURANSI' ? 'selected' : '' }}>Asuransi</option>
                                </select>
                                @error('metode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Nomor Referensi -->
                            <div class="col-md-6 mb-3" id="noReferensiGroup" style="display: none;">
                                <label class="form-label fw-bold">
                                    <i class="ri-hashtag me-1"></i>
                                    Nomor Referensi / Approval Code
                                </label>
                                <input type="text" 
                                       class="form-control @error('no_referensi') is-invalid @enderror" 
                                       name="no_referensi" 
                                       id="noReferensi"
                                       placeholder="No Transfer/Giro/Approval Code"
                                       value="{{ old('no_referensi') }}">
                                @error('no_referensi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Opsional: No transfer, no giro, atau referensi lainnya</small>
                            </div>

                            <!-- Upload Bukti Pembayaran -->
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="ri-image-line me-1"></i>
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
                                <small class="text-muted">
                                    <i class="ri-information-line"></i>
                                    Format: JPG, PNG, PDF. Maksimal: 5MB
                                </small>
                                
                                <!-- Preview -->
                                <div id="previewContainer" class="mt-3" style="display: none;">
                                    <img id="previewImage" src="" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                                </div>
                            </div>

                            <!-- Catatan -->
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="ri-file-text-line me-1"></i>
                                    Keterangan
                                </label>
                                <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                          name="keterangan" 
                                          rows="3" 
                                          placeholder="Tambahkan catatan pembayaran (opsional)">{{ old('keterangan') }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between gap-2">
                            <a href="{{ route('tagihans.show', $tagihan->id_tagihan) }}" class="btn btn-secondary btn-sm">
                                <i class="ri-close-line me-1"></i>Batal
                            </a>
                            <button type="button" class="btn btn-success btn-sm px-5" id="submitBtn" onclick="showPinModal()">
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
                    <div class="d-flex align-items-start mb-3">
                        @if($tagihan->pasien && $tagihan->pasien->foto)
                            <img src="{{ asset('storage/' . $tagihan->pasien->foto) }}" 
                                 alt="{{ $tagihan->pasien->nama_lengkap }}" 
                                 class="rounded-circle me-3" 
                                 width="50" 
                                 height="50">
                        @else
                            <div class="avatar-md rounded-circle bg-secondary me-3 d-flex align-items-center justify-content-center">
                                <span class="text-white fw-bold fs-5">
                                    {{ strtoupper(substr($tagihan->pasien->nama ?? 'P', 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <div>
                            <small class="text-muted d-block">No. Tagihan</small>
                            <strong class="text-primary">{{ $tagihan->no_tagihan }}</strong>
                            <br>
                            <small class="text-muted d-block mt-2">Nama Pasien</small>
                            <strong>{{ $tagihan->pasien->nama_lengkap ?? '-' }}</strong>
                            <br>
                            <small class="text-muted d-block mt-2">No. RM</small>
                            <strong>{{ $tagihan->pasien->no_rm ?? '-' }}</strong>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Jenis Tagihan</small>
                        @php
                            $badgeClass = match($tagihan->jenis_tagihan ?? '') {
                                'IGD' => 'bg-danger',
                                'RAWAT_JALAN' => 'bg-info',
                                'RAWAT_INAP' => 'bg-warning',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }} fs-6">
                            {{ str_replace('_', ' ', $tagihan->jenis_tagihan ?? '-') }}
                        </span>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Total Tagihan:</span>
                            <strong>Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Sudah Dibayar:</span>
                            <strong class="text-success">Rp {{ number_format($tagihan->total_dibayar ?? 0, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                    
                    <hr class="my-2">
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted fw-bold">Sisa Tagihan:</span>
                            <strong class="text-danger fs-4">Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    @if($tagihan->tanggal_tagihan)
                    <div class="alert alert-warning mb-0">
                        <small>
                            <i class="ri-calendar-line me-1"></i>
                            Tanggal Tagihan: <strong>{{ \Carbon\Carbon::parse($tagihan->tanggal_tagihan)->format('d/m/Y') }}</strong>
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
            @if(isset($tagihan->pembayarans) && $tagihan->pembayarans->count() > 0)
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="ri-history-line me-2"></i>Riwayat Pembayaran
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($tagihan->pembayarans->take(5) as $payment)
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <div>
                            <small class="text-muted d-block">{{ \Carbon\Carbon::parse($payment->tanggal_bayar)->format('d/m/Y') }}</small>
                            <strong>Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}</strong>
                            <br>
                            <small class="text-muted">{{ $payment->metode }}</small>
                        </div>
                        <div>
                            @if(isset($payment->status) && $payment->status == 'verified')
                                <span class="badge bg-success">
                                    <i class="ri-checkbox-circle-line me-1"></i>Verified
                                </span>
                            @else
                                <span class="badge bg-info">
                                    <i class="ri-time-line me-1"></i>Paid
                                </span>
                            @endif
                        </div>
                    </div>
                    @endforeach
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
    .avatar-md {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .card {
        border-radius: 0.5rem;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .input-group-text {
        background-color: #f8f9fa;
        font-weight: 600;
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
    // Format currency input
    function formatRupiah(angka) {
        var number_string = angka.toString().replace(/[^,\d]/g, ''),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return rupiah;
    }

    function unformatRupiah(rupiah) {
        return parseInt(rupiah.replace(/\./g, '')) || 0;
    }

    function setBayarPenuh() {
        const sisaTagihan = {{ $tagihan->sisa_tagihan }};
        document.getElementById('jumlahBayar').value = formatRupiah(sisaTagihan);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Format input on keyup
        const jumlahBayarInput = document.getElementById('jumlahBayar');
        jumlahBayarInput.addEventListener('keyup', function() {
            const value = unformatRupiah(this.value);
            this.value = formatRupiah(value);
        });

        // Show/hide no_referensi based on metode
        const metodePembayaran = document.getElementById('metodePembayaran');
        metodePembayaran.addEventListener('change', function() {
            const metode = this.value;
            const requireRef = ['DEBIT', 'CREDIT', 'TRANSFER', 'GIRO', 'BPJS', 'ASURANSI'];
            
            if (requireRef.includes(metode)) {
                document.getElementById('noReferensiGroup').style.display = 'block';
                document.getElementById('noReferensi').setAttribute('required', true);
            } else {
                document.getElementById('noReferensiGroup').style.display = 'none';
                document.getElementById('noReferensi').removeAttribute('required');
            }
        });

        // Preview image upload
        const buktiBayar = document.getElementById('buktiBayar');
        const previewContainer = document.getElementById('previewContainer');
        const previewImage = document.getElementById('previewImage');

        buktiBayar.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Check file type
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

                // Check file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'File Terlalu Besar',
                        text: 'Ukuran file maksimal 5MB',
                        confirmButtonColor: '#f59e0b'
                    });
                    buktiBayar.value = '';
                    previewContainer.style.display = 'none';
                }
            }
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

    function resetPinInput(error = false) {
        otpInputs.forEach(input => {
            input.value = '';
            input.classList.remove('filled', 'error');
            if (error) input.classList.add('error');
        });
        otpInputs[0].focus();
        confirmPinBtn.disabled = true;
    }
    function isPinComplete() {
        return Array.from(otpInputs).every(input => input.value !== '');
    }
    otpInputs.forEach(input => {
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();

                if (isPinComplete()) {
                    confirmPinBtn.click();
                }
            }
        });
    });



    // ============================================
    // SHOW PIN MODAL
    // ============================================
    function showPinModal() {
        const jumlah = unformatRupiah(document.getElementById('jumlahBayar').value);
        const sisa = {{ $tagihan->sisa_tagihan }};

        if (!jumlah || jumlah <= 0) {
            Swal.fire('Perhatian', 'Jumlah bayar tidak boleh kosong', 'warning');
            return;
        }

        if (jumlah > sisa) {
            Swal.fire('Perhatian', 'Jumlah bayar melebihi sisa tagihan', 'warning');
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
        formData.set('jumlah_bayar', unformatRupiah(document.getElementById('jumlahBayar').value));

        confirmPinBtn.classList.add('btn-loading');
        confirmPinBtn.disabled = true;

        fetch("{{ route('tagihans.payment.store') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message,
                    confirmButtonColor: '#198754'
                }).then(() => {
                    window.location.href = "{{ route('tagihans.show', $tagihan->id_tagihan) }}";
                });
            } else {
                throw new Error(res.message || 'PIN salah');
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