<!-- PIN Verification Modal (Selalu ada di DOM untuk inactivity popup) -->
<div class="modal fade" id="pinVerificationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 24px; overflow: hidden; border: none;">
            <div class="modal-header text-center d-block border-0 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 2.5rem 2rem;">
                <div class="user-avatar mx-auto mb-3" style="width: 90px; height: 90px; border-radius: 50%; background: white; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                    <i class="ri-shield-keyhole-line" style="font-size: 42px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                </div>
                <h4 class="mb-2 text-white fw-bold">Verifikasi PIN</h4>
                <p class="mb-0 text-white-50">{{ Auth::user()->name }}</p>
            </div>
            
            <div class="modal-body p-4">
                <div id="pinErrorAlert" class="alert alert-danger alert-dismissible fade" role="alert" style="display: none;">
                    <i class="ri-error-warning-line me-2"></i>
                    <span id="pinErrorMessage"></span>
                    <button type="button" class="btn-close" onclick="dismissError()"></button>
                </div>

                <!-- Info Message (untuk inactivity timeout) -->
                <div id="pinInfoAlert" class="alert alert-warning alert-dismissible fade" role="alert" style="display: none;">
                    <i class="ri-time-line me-2"></i>
                    <span id="pinInfoMessage"></span>
                    <button type="button" class="btn-close" onclick="dismissInfo()"></button>
                </div>

                <div class="text-center mb-4">
                    <p class="text-muted mb-0 small">Masukkan PIN 6 digit untuk melanjutkan</p>
                </div>

                <form id="pinForm" method="POST">
                    @csrf
                    <input type="hidden" name="pin" id="pinHiddenInput">
                    
                    <!-- OTP Style PIN Input -->
                    <div class="pin-input-container d-flex justify-content-center gap-2 mb-4">
                        <input type="password" class="pin-box" maxlength="1" data-index="0" autocomplete="off" inputmode="numeric">
                        <input type="password" class="pin-box" maxlength="1" data-index="1" autocomplete="off" inputmode="numeric">
                        <input type="password" class="pin-box" maxlength="1" data-index="2" autocomplete="off" inputmode="numeric">
                        <input type="password" class="pin-box" maxlength="1" data-index="3" autocomplete="off" inputmode="numeric">
                        <input type="password" class="pin-box" maxlength="1" data-index="4" autocomplete="off" inputmode="numeric">
                        <input type="password" class="pin-box" maxlength="1" data-index="5" autocomplete="off" inputmode="numeric">
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-lg" id="submitPinBtn" disabled style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: white; padding: 14px; font-weight: 600; border-radius: 12px;">
                            <i class="ri-check-line me-2"></i>Verifikasi PIN
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="clearAllPin()">
                            <i class="ri-refresh-line me-2"></i>Reset PIN
                        </button>
                    </div>
                </form>

                <hr class="my-4">

                <div class="text-center">
                    <p class="text-muted small mb-2">Bukan Anda?</p>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link text-danger text-decoration-none">
                            <i class="ri-logout-box-line me-1"></i> Logout dari Sistem
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .pin-box {
        width: 60px;
        height: 70px;
        font-size: 28px;
        font-weight: bold;
        border: 2px solid #dee2e6;
        border-radius: 12px;
        transition: all 0.3s ease;
        text-align: center;
        caret-color: #667eea;
        background: white;
    }

    .pin-box:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        transform: scale(1.05);
    }

    .pin-box.filled {
        background-color: #e7f3ff;
        border-color: #667eea;
    }

    .pin-box.shake {
        animation: shake 0.5s;
        border-color: #dc3545 !important;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-8px); }
        20%, 40%, 60%, 80% { transform: translateX(8px); }
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .pulse {
        animation: pulse 1s infinite;
    }

    #submitPinBtn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }

    #submitPinBtn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = new bootstrap.Modal(document.getElementById('pinVerificationModal'));
    const pinBoxes = document.querySelectorAll('.pin-box');
    const hiddenInput = document.getElementById('pinHiddenInput');
    const submitBtn = document.getElementById('submitPinBtn');
    const pinForm = document.getElementById('pinForm');
    const errorAlert = document.getElementById('pinErrorAlert');
    const errorMessage = document.getElementById('pinErrorMessage');
    const infoAlert = document.getElementById('pinInfoAlert');
    const infoMessage = document.getElementById('pinInfoMessage');

    // Auto show modal jika belum terverifikasi
    @if(!session('pin_verified'))
        modal.show();
    @endif

    // Auto focus first box ketika modal dibuka
    document.getElementById('pinVerificationModal').addEventListener('shown.bs.modal', function () {
        clearAllPin();
        pinBoxes[0].focus();
    });

    // PIN Box Logic
    pinBoxes.forEach((box, index) => {
        box.addEventListener('input', function(e) {
            let value = this.value;

            // Only allow numbers - PENTING untuk keamanan
            if (!/^\d*$/.test(value)) {
                this.value = '';
                return;
            }

            if (value.length > 1) {
                value = value.slice(-1);
                this.value = value;
            }

            if (value) {
                this.classList.add('filled');
                
                // Move to next box
                if (index < pinBoxes.length - 1) {
                    pinBoxes[index + 1].focus();
                } else {
                    this.blur();
                }
            } else {
                this.classList.remove('filled');
            }

            updateHiddenInput();
        });

        box.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace') {
                if (!this.value && index > 0) {
                    pinBoxes[index - 1].focus();
                    pinBoxes[index - 1].value = '';
                    pinBoxes[index - 1].classList.remove('filled');
                } else {
                    this.value = '';
                    this.classList.remove('filled');
                }
                updateHiddenInput();
            }

            if (e.key === 'Enter') {
                e.preventDefault();
                if (getPin().length === 6) {
                    submitPin();
                }
            }

            if (e.key === 'ArrowLeft' && index > 0) {
                e.preventDefault();
                pinBoxes[index - 1].focus();
            }
            if (e.key === 'ArrowRight' && index < pinBoxes.length - 1) {
                e.preventDefault();
                pinBoxes[index + 1].focus();
            }
        });

        box.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').trim();
            
            // KEAMANAN: Hanya izinkan paste 6 digit angka
            if (/^\d{6}$/.test(pastedData)) {
                pastedData.split('').forEach((char, i) => {
                    if (pinBoxes[i]) {
                        pinBoxes[i].value = char;
                        pinBoxes[i].classList.add('filled');
                    }
                });
                pinBoxes[5].focus();
                updateHiddenInput();
            }
        });

        box.addEventListener('keypress', function(e) {
            // KEAMANAN: Hanya izinkan digit
            if (!/\d/.test(e.key) && e.key !== 'Enter') {
                e.preventDefault();
            }
        });

        box.addEventListener('focus', function() {
            this.select();
        });
    });

    function getPin() {
        return Array.from(pinBoxes).map(box => box.value).join('');
    }

    function updateHiddenInput() {
        const pin = getPin();
        hiddenInput.value = pin;
        
        if (pin.length === 6) {
            submitBtn.disabled = false;
            submitBtn.classList.add('pulse');
        } else {
            submitBtn.disabled = true;
            submitBtn.classList.remove('pulse');
        }
    }

    window.clearAllPin = function() {
        pinBoxes.forEach(box => {
            box.value = '';
            box.classList.remove('filled', 'shake');
        });
        pinBoxes[0].focus();
        updateHiddenInput();
        dismissError();
        dismissInfo();
    };

    window.dismissError = function() {
        errorAlert.style.display = 'none';
        errorAlert.classList.remove('show');
    };

    window.dismissInfo = function() {
        infoAlert.style.display = 'none';
        infoAlert.classList.remove('show');
    };

    window.showPinInfo = function(message) {
        console.log('ℹ️ Showing info:', message);
        dismissError(); // Clear error dulu
        infoMessage.textContent = message;
        infoAlert.style.display = 'block';
        infoAlert.classList.add('show');
    };

    function showError(message) {
        errorMessage.textContent = message;
        errorAlert.style.display = 'block';
        errorAlert.classList.add('show');
        
        pinBoxes.forEach(box => {
            box.classList.add('shake');
        });

        setTimeout(() => {
            pinBoxes.forEach(box => {
                box.classList.remove('shake');
            });
            clearAllPin();
        }, 600);
    }

    // Submit PIN via AJAX
    function submitPin() {
        const pin = getPin();
        if (pin.length !== 6) return;

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memverifikasi...';

        fetch('{{ route("pin.verify") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ pin: pin })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Success - tutup modal TANPA reload
                modal.hide();
                clearAllPin();
                
                // Show success notification (toast, tidak ganggu)
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'PIN Terverifikasi',
                        text: data.message || 'Anda dapat melanjutkan aktivitas',
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end',
                        timerProgressBar: true
                    });
                }

                // Reset submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="ri-check-line me-2"></i>Verifikasi PIN';
                
                // PENTING: Tidak ada reload, user tetap di halaman yang sama
                console.log('PIN verified successfully - no page reload');
            } else {
                // Error
                showError(data.message || 'PIN tidak valid');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="ri-check-line me-2"></i>Verifikasi PIN';
            }
        })
        .catch(error => {
            console.error('PIN Verification Error:', error);
            showError('Terjadi kesalahan. Silakan coba lagi.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="ri-check-line me-2"></i>Verifikasi PIN';
        });
    }

    pinForm.addEventListener('submit', function(e) {
        e.preventDefault();
        submitPin();
    });

    // Expose function untuk dipanggil dari inactivity script
    window.showPinModalForInactivity = function() {
        console.log('📱 showPinModalForInactivity called');
        
        // Clear semua input dulu
        if (typeof window.clearAllPin === 'function') {
            window.clearAllPin();
        }
        
        // Show info message
        showPinInfo('Sesi Anda tidak aktif. Silakan masukkan PIN untuk melanjutkan.');
        
        // Show modal
        let modalInstance = bootstrap.Modal.getInstance(document.getElementById('pinVerificationModal'));
        if (!modalInstance) {
            modalInstance = new bootstrap.Modal(document.getElementById('pinVerificationModal'), {
                backdrop: 'static',
                keyboard: false
            });
        }
        modalInstance.show();
        
        console.log('✅ Modal shown for inactivity');
    };
    
    console.log('✅ PIN Modal functions ready');
});
</script>