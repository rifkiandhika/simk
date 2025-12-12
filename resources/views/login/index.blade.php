<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login | SIMK</title>

    <link rel="shortcut icon" href="{{ asset('assets/image/icon.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/remix/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/main.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/login-custom.css') }}">

</head>

<body class="login-bg">

    <div class="login-container">
        <div class="auth-box">

            <a href="#" class="auth-logo">
                <img src="{{ asset('assets/image/icon.png') }}" alt="Logo">
                <span>SIMK</span>
            </a>

            <h3 class="mb-4 text-center fw-bold">Selamat Datang</h3>
            <p class="text-center text-muted mb-4">Silakan login untuk melanjutkan</p>

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

            <form action="{{ route('login.submit') }}" method="POST" id="loginForm">
                @csrf

                <div class="mb-3">
                    <label class="form-label" for="login">
                        <i class="ri-user-line me-1"></i>Username atau Email <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           id="login" 
                           name="login" 
                           class="form-control @error('login') is-invalid @enderror" 
                           placeholder="Masukkan username atau email"
                           value="{{ old('login') }}"
                           required
                           autofocus>
                    @error('login')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password">
                        <i class="ri-lock-line me-1"></i>Password <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               placeholder="Masukkan password"
                               required>
                        <button class="btn btn-outline-secondary toggle-pass" type="button">
                            <i class="ri-eye-line text-primary" id="toggleIcon"></i>
                        </button>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Ingat Saya
                        </label>
                    </div>
                    <a href="{{ route('forgot-password') }}" class="text-decoration-underline">
                        Lupa Password?
                    </a>
                </div>

                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-primary" id="loginBtn">
                        <i class="ri-login-box-line me-2"></i>Login
                    </button>
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
            // Toggle password visibility
            const togglePass = document.querySelector('.toggle-pass');
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            togglePass.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    toggleIcon.classList.remove('ri-eye-line');
                    toggleIcon.classList.add('ri-eye-off-line');
                } else {
                    passwordInput.type = 'password';
                    toggleIcon.classList.remove('ri-eye-off-line');
                    toggleIcon.classList.add('ri-eye-line');
                }
            });

            // Form validation
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');

            loginForm.addEventListener('submit', function(e) {
                loginBtn.disabled = true;
                loginBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
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