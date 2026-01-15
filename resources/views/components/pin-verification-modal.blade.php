<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIN Verification - Medical Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/image/icon.png') }}">
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/remix/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/main.min.css') }}">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .pin-wrapper {
            width: 100%;
            padding: 20px;
        }

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
            font-weight: bold;
        }

        .pin-box.shake {
            animation: shake 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-8px); }
            20%, 40%, 60%, 80% { transform: translateX(8px); }
        }

        .modal-content {
            border-radius: 24px;
            border: none;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 2rem;
        }

        .user-avatar {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .user-avatar i {
            font-size: 42px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .pulse {
            animation: pulse 1s infinite;
        }

        .btn-verify {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 14px 28px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.3s;
        }

        .btn-verify:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-verify:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    @include('sweetalert::alert')
    
    <div class="pin-wrapper">
        <!-- Modal yang langsung muncul -->
        <div class="modal fade show d-block" id="pinVerificationModal" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header text-center d-block">
                        <div class="user-avatar mx-auto mb-3">
                            <i class="ri-shield-keyhole-line"></i>
                        </div>
                        <h4 class="mb-2 text-white">Verifikasi PIN</h4>
                        <p class="mb-0 text-white-50">{{ Auth::user()->name }}</p>
                    </div>
                    
                    <div class="modal-body p-4">
                        @if(session('error_pin'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="ri-error-warning-line me-2"></i>
                                {{ session('error_pin') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="ri-checkbox-circle-line me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="text-center mb-4">
                            <p class="text-muted mb-0">Masukkan PIN 6 digit Anda</p>
                        </div>

                        <form id="pinForm" action="{{ route('pin.verify') }}" method="POST">
                            @csrf
                            <input type="hidden" name="pin" id="pinHiddenInput">
                            
                            <!-- OTP Style PIN Input -->
                            <div class="pin-input-container d-flex justify-content-center gap-2 mb-4">
                                <input type="password" class="pin-box form-control text-center" maxlength="1" data-index="0" autocomplete="off">
                                <input type="password" class="pin-box form-control text-center" maxlength="1" data-index="1" autocomplete="off">
                                <input type="password" class="pin-box form-control text-center" maxlength="1" data-index="2" autocomplete="off">
                                <input type="password" class="pin-box form-control text-center" maxlength="1" data-index="3" autocomplete="off">
                                <input type="password" class="pin-box form-control text-center" maxlength="1" data-index="4" autocomplete="off">
                                <input type="password" class="pin-box form-control text-center" maxlength="1" data-index="5" autocomplete="off">
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-verify btn-lg" id="submitPinBtn" disabled>
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
    </div>

    <!-- JavaScript Files -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pinBoxes = document.querySelectorAll('.pin-box');
            const hiddenInput = document.getElementById('pinHiddenInput');
            const submitBtn = document.getElementById('submitPinBtn');
            const pinForm = document.getElementById('pinForm');

            // Auto focus first box
            pinBoxes[0].focus();

            // PIN Box Logic
            pinBoxes.forEach((box, index) => {
                // Handle input
                box.addEventListener('input', function(e) {
                    let value = this.value;

                    // Only allow numbers
                    if (!/^\d*$/.test(value)) {
                        this.value = '';
                        return;
                    }

                    // Keep only last character if multiple entered
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
                            // Last box filled, blur to show completion
                            this.blur();
                        }
                    } else {
                        this.classList.remove('filled');
                    }

                    updateHiddenInput();
                });

                // Handle backspace
                box.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace') {
                        if (!this.value && index > 0) {
                            // Move to previous box
                            pinBoxes[index - 1].focus();
                            pinBoxes[index - 1].value = '';
                            pinBoxes[index - 1].classList.remove('filled');
                        } else {
                            // Clear current box
                            this.value = '';
                            this.classList.remove('filled');
                        }
                        updateHiddenInput();
                    }

                    // Handle Enter key
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const pin = getPin();
                        if (pin.length === 6) {
                            pinForm.submit();
                        }
                    }

                    // Handle arrow keys
                    if (e.key === 'ArrowLeft' && index > 0) {
                        e.preventDefault();
                        pinBoxes[index - 1].focus();
                    }
                    if (e.key === 'ArrowRight' && index < pinBoxes.length - 1) {
                        e.preventDefault();
                        pinBoxes[index + 1].focus();
                    }
                });

                // Handle paste
                box.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pastedData = e.clipboardData.getData('text').trim();
                    
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

                // Prevent non-numeric input
                box.addEventListener('keypress', function(e) {
                    if (!/\d/.test(e.key) && e.key !== 'Enter') {
                        e.preventDefault();
                    }
                });

                // Select all on focus
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
                
                // Enable submit button if PIN is complete
                if (pin.length === 6) {
                    submitBtn.disabled = false;
                    submitBtn.classList.add('pulse');
                } else {
                    submitBtn.disabled = true;
                    submitBtn.classList.remove('pulse');
                }
            }

            // Global clear function
            window.clearAllPin = function() {
                pinBoxes.forEach(box => {
                    box.value = '';
                    box.classList.remove('filled');
                });
                pinBoxes[0].focus();
                updateHiddenInput();
            };

            // Handle form submission errors
            @if(session('error_pin'))
                pinBoxes.forEach(box => {
                    box.classList.add('shake');
                    box.style.borderColor = '#dc3545';
                });

                setTimeout(() => {
                    pinBoxes.forEach(box => {
                        box.classList.remove('shake');
                        box.style.borderColor = '';
                    });
                    clearAllPin();
                }, 600);
            @endif
        });
    </script>
</body>
</html>