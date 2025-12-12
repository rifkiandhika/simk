@extends('layouts.app')

@section('title', 'Edit User')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Manajemen User</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">Edit User</li>
@endsection

@section('content')
<div class="app-body">
    <form action="{{ route('users.update', $user->id) }}" method="POST" id="userForm">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Left Column - User Info -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="ri-pencil-line me-2"></i>Edit User
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
                                                {{ old('id_karyawan', $user->id_karyawan) == $karyawan->id_karyawan ? 'selected' : '' }}>
                                            {{ $karyawan->nip }} - {{ $karyawan->nama_lengkap }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_karyawan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Display Current Karyawan Info -->
                            <div class="col-md-12 mb-3">
                                <div class="alert alert-info">
                                    <strong><i class="ri-user-line"></i> Data Karyawan Saat Ini:</strong>
                                    <table class="table table-sm table-borderless mb-0 mt-2">
                                        <tr>
                                            <td width="100">NIP:</td>
                                            <td><strong>{{ $user->karyawan->nip ?? '-' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Nama:</td>
                                            <td><strong>{{ $user->karyawan->nama_lengkap ?? '-' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Email:</td>
                                            <td><strong>{{ $user->karyawan->email ?? '-' }}</strong></td>
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
                                           value="{{ old('username', $user->username) }}"
                                           placeholder="Username untuk login"
                                           required>
                                </div>
                                @error('username')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
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
                                           value="{{ old('email', $user->email) }}"
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
                                    Password Baru
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-lock-line"></i></span>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Kosongkan jika tidak ingin mengubah">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="ri-eye-line" id="eyeIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Minimal 8 karakter jika ingin mengubah password</small>
                            </div>

                            <!-- Password Confirmation -->
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label fw-bold">
                                    Konfirmasi Password Baru
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-lock-line"></i></span>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           placeholder="Ulangi password baru">
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
                                    <option value="{{ $role->name }}" 
                                            {{ old('role', $user->roles->first()->name ?? '') == $role->name ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="ri-shield-user-line"></i> Role saat ini: 
                                <strong>{{ $user->roles->first()->name ?? 'Tidak ada' }}</strong>
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
                                <option value="Aktif" {{ old('status', $user->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Nonaktif" {{ old('status', $user->status) == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <!-- User Info -->
                        <div class="alert alert-light border">
                            <div class="mb-2">
                                <strong><i class="ri-information-line"></i> Informasi User:</strong>
                            </div>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td width="120"><small>Created:</small></td>
                                    <td><small>{{ $user->created_at->format('d M Y') }}</small></td>
                                </tr>
                                <tr>
                                    <td><small>Last Login:</small></td>
                                    <td>
                                        <small>
                                            @if($user->last_login)
                                                {{ \Carbon\Carbon::parse($user->last_login)->diffForHumans() }}
                                            @else
                                                Belum pernah login
                                            @endif
                                        </small>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-warning text-dark">
                                <i class="ri-save-line me-2"></i>Update User
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
                
                // Only validate if password is being changed
                if (password || passwordConfirm) {
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
                }
            });
        });
    </script>
@endpush