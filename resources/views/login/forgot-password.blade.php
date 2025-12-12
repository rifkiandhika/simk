<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password | SIMK</title>

    <link rel="shortcut icon" href="{{ asset('assets/image/icon.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/remix/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/main.min.css') }}">

    <style>
        .login-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
        }

        .auth-box {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            font-size: 28px;
            font-weight: bold;
            color: #667eea;
            text-decoration: none;
            margin-bottom: 30px;
        }

        .auth-logo img {
            width: 50px;
            height: 50px;
            border-radius: 10px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-primary {
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            transition: transform 0.2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-outline-secondary {
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 12px 16px;
        }
    </style>
</head>

<body class="login-bg">

    <div class="login-container">
        <div class="auth-box">

            <a href="{{ route('login') }}" class="auth-logo">
                <img src="{{ asset('assets/image/icon.png') }}" alt="Logo">
                <span>SIMK</span>
            </a>

            <h3 class="mb-3 text-center fw-bold">Lupa Password?</h3>
            <p class="text-center text-muted mb-4">
                Masukkan email Anda dan kami akan mengirimkan link untuk reset password
            </p>

            {{-- Alert Messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ri-checkbox-circle-line me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ri-error-warning-line me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ri-error-warning-line me-2"></i>
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('send-reset-link') }}" method="POST" id="forgotForm">
                @csrf

                <div class="mb-4">
                    <label class="form-label" for="email">
                        <i class="ri-mail-line me-1"></i>Email <span class="text-danger">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           placeholder="Masukkan email Anda"
                           value="{{ old('email') }}"
                           required
                           autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="ri-mail-send-line me-2"></i>Kirim Link Reset
                    </button>
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                        <i class="ri-arrow-left-line me-2"></i>Kembali ke Login
                    </a>
                </div>

                <div class="text-center mt-4">
                    <small class="text-muted">
                        © 2024 SIMK - Sistem Informasi Manajemen Klinik
                    </small>
                </div>

            </form>

        </div>
    </div>

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form validation
            const forgotForm = document.getElementById('forgotForm');
            const submitBtn = document.getElementById('submitBtn');

            forgotForm.addEventListener('submit', function(e) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';
            });

            // Auto-dismiss alerts
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    if (alert) {
                        alert.style.transition = 'opacity 0.5s ease';
                        alert.style.opacity = '0';
                        setTimeout(function() {
                            alert.remove();
                        }, 500);
                    }
                });
            }, 5000);
        });
    </script>

</body>

</html>