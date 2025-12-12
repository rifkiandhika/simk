@extends('layouts.app')

@section('title', 'Tambah User')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Manajemen User</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">Tambah User</li>
@endsection

@section('content')
<div class="app-body">
    <form action="{{ route('users.store') }}" method="POST" id="userForm">
        @csrf
        
        <div class="row">
            <!-- Left Column - User Info -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="ri-user-add-line me-2"></i>Informasi User
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Karyawan Selection -->
                            <div class="col-md-12 mb-3">
                                <label for="id_karyawan" class="form-label fw-bold">
                                    Pilih Karyawan <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('id_karyawan') is-invalid @enderror" 
                                        id="id_karyawan" 
                                        name="id_karyawan" 
                                        required>
                                    <option value="">-- Pilih Karyawan --</option>
                                    @foreach($karyawans as $karyawan)
                                        <option value="{{ $karyawan->id_karyawan }}" 
                                                data-nip="{{ $karyawan->nip }}"
                                                data-email="{{ $karyawan->email }}"
                                                data-nama="{{ $karyawan->nama_lengkap }}"
                                                {{ old('id_karyawan') == $karyawan->id_karyawan ? 'selected' : '' }}>
                                            {{ $karyawan->nip }} - {{ $karyawan->nama_lengkap }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_karyawan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    <i class="ri-information-line"></i> Pilih karyawan yang akan dijadikan user
                                </small>
                            </div>

                            <!-- Display Selected Karyawan Info -->
                            <div class="col-md-12 mb-3" id="karyawanInfo" style="display: none;">
                                <div class="alert alert-info">
                                    <strong><i class="ri-user-line"></i> Data Karyawan:</strong>
                                    <table class="table table-sm table-borderless mb-0 mt-2">
                                        <tr>
                                            <td width="100">NIP:</td>
                                            <td><strong id="displayNip">-</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Nama:</td>
                                            <td><strong id="displayNama">-</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Email:</td>
                                            <td><strong id="displayEmail">-</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Username -->
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label fw-bold">
                                    Username <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-user-line"></i></span>
                                    <input type="text" 
                                           class="form-control @error('username') is-invalid @enderror" 
                                           id="username" 
                                           name="username" 
                                           value="{{ old('username') }}"
                                           placeholder="Username untuk login"
                                           required>
                                </div>
                                @error('username')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Username harus unik</small>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label fw-bold">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-mail-line"></i></span>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}"
                                           placeholder="email@example.com"
                                           required>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label fw-bold">
                                    Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-lock-line"></i></span>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Minimal 8 karakter"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="ri-eye-line" id="eyeIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password Confirmation -->
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label fw-bold">
                                    Konfirmasi Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-lock-line"></i></span>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           placeholder="Ulangi password"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                        <i class="ri-eye-line" id="eyeIconConfirm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Role & Status -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow-sm border-0 mb-4 sticky-top" style="top: 20px;">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="ri-settings-3-line me-2"></i>Pengaturan
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Role -->
                        <div class="mb-3">
                            <label for="role" class="form-label fw-bold">
                                Role <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('role') is-invalid @enderror" 
                                    id="role" 
                                    name="role" 
                                    required>
                                <option value="">-- Pilih Role --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="ri-shield-user-line"></i> Tentukan role untuk user ini
                            </small>
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label fw-bold">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" 
                                    name="status" 
                                    required>
                                <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Nonaktif" {{ old('status') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <div class="alert alert-warning">
                            <i class="ri-information-line me-2"></i>
                            <strong>Catatan:</strong>
                            <ul class="mb-0 mt-2 ps-3 small">
                                <li>Pastikan data karyawan sudah benar</li>
                                <li>Username harus unik dan mudah diingat</li>
                                <li>Password minimal 8 karakter</li>
                                <li>Role menentukan hak akses user</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line me-2"></i>Simpan User
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                <i class="ri-arrow-left-line me-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
    <style>
        .card {
            border-radius: 0.5rem;
        }
        
        .sticky-top {
            position: sticky;
            z-index: 1020;
        }
        
        .input-group-text {
            background-color: #f8f9fa;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Auto-fill email from karyawan
            $('#id_karyawan').on('change', function() {
                var selectedOption = $(this).find('option:selected');
                var email = selectedOption.data('email');
                var nip = selectedOption.data('nip');
                var nama = selectedOption.data('nama');
                
                if (email) {
                    $('#email').val(email);
                    $('#displayEmail').text(email);
                }
                
                if (nip) {
                    $('#displayNip').text(nip);
                    // Auto-generate username from NIP
                    $('#username').val(nip.toLowerCase());
                }
                
                if (nama) {
                    $('#displayNama').text(nama);
                }
                
                // Show karyawan info
                if (selectedOption.val()) {
                    $('#karyawanInfo').slideDown();
                } else {
                    $('#karyawanInfo').slideUp();
                }
            });

            // Toggle password visibility
            $('#togglePassword').on('click', function() {
                var passwordField = $('#password');
                var eyeIcon = $('#eyeIcon');
                
                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    eyeIcon.removeClass('ri-eye-line').addClass('ri-eye-off-line');
                } else {
                    passwordField.attr('type', 'password');
                    eyeIcon.removeClass('ri-eye-off-line').addClass('ri-eye-line');
                }
            });

            $('#togglePasswordConfirm').on('click', function() {
                var passwordField = $('#password_confirmation');
                var eyeIcon = $('#eyeIconConfirm');
                
                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    eyeIcon.removeClass('ri-eye-line').addClass('ri-eye-off-line');
                } else {
                    passwordField.attr('type', 'password');
                    eyeIcon.removeClass('ri-eye-off-line').addClass('ri-eye-line');
                }
            });

            // Form validation
            $('#userForm').on('submit', function(e) {
                var password = $('#password').val();
                var passwordConfirm = $('#password_confirmation').val();
                
                if (password !== passwordConfirm) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Password tidak cocok',
                        text: 'Password dan konfirmasi password harus sama!'
                    });
                    return false;
                }
                
                if (password.length < 8) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Password terlalu pendek',
                        text: 'Password minimal 8 karakter!'
                    });
                    return false;
                }
            });
        });
    </script>
@endpush