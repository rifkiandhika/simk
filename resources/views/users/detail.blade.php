@extends('layouts.app')

@section('title', 'Detail User')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Manajemen User</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">Detail User</li>
@endsection

@section('content')
<div class="app-body">
    <div class="row">
        <!-- Left Column - User Profile -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="ri-user-line me-2"></i>Profil User
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        @if($user->karyawan && $user->karyawan->foto)
                            <img src="{{ asset('storage/' . $user->karyawan->foto) }}" 
                                 class="rounded-circle mb-3" 
                                 alt="{{ $user->name }}"
                                 style="width: 120px; height: 120px; object-fit: cover; border: 4px solid #e9ecef;">
                        @else
                            <div class="avatar-xl mx-auto mb-3">
                                <span class="avatar-title rounded-circle bg-info bg-gradient fs-1">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </span>
                            </div>
                        @endif
                        
                        <h4 class="mb-1">{{ $user->name }}</h4>
                        <p class="text-muted mb-2">{{ '@' . $user->username }}</p>
                        
                        @if($user->status == 'Aktif')
                            <span class="badge bg-success badge-lg">
                                <i class="ri-checkbox-circle-line"></i> Aktif
                            </span>
                        @else
                            <span class="badge bg-danger badge-lg">
                                <i class="ri-close-circle-line"></i> Nonaktif
                            </span>
                        @endif
                    </div>

                    <div class="border-top pt-3">
                        <div class="row text-center">
                            <div class="col-6 border-end">
                                <div class="p-2">
                                    <h3 class="text-primary mb-1">
                                        {{ $user->roles->first()->permissions->count() ?? 0 }}
                                    </h3>
                                    <p class="text-muted mb-0 small">Permissions</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2">
                                    <h3 class="text-success mb-1">
                                        @if($user->last_login)
                                            {{ \Carbon\Carbon::parse($user->last_login)->diffInDays() }}
                                        @else
                                            0
                                        @endif
                                    </h3>
                                    <p class="text-muted mb-0 small">Hari Login Terakhir</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Info Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="ri-contacts-line me-2"></i>Informasi Kontak
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td width="100"><i class="ri-mail-line text-muted"></i> Email</td>
                            <td><strong>{{ $user->email }}</strong></td>
                        </tr>
                        @if($user->karyawan)
                        <tr>
                            <td><i class="ri-phone-line text-muted"></i> No. Telp</td>
                            <td><strong>{{ $user->karyawan->no_telp ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td><i class="ri-map-pin-line text-muted"></i> Alamat</td>
                            <td><strong>{{ $user->karyawan->alamat ?? '-' }}</strong></td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning text-dark">
                            <i class="ri-pencil-line me-2"></i>Edit User
                        </a>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                            <i class="ri-lock-password-line me-2"></i>Reset Password
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                            <i class="ri-arrow-left-line me-2"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Details -->
        <div class="col-xl-8 col-lg-7">
            <!-- Karyawan Info Card -->
            @if($user->karyawan)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="ri-id-card-line me-2"></i>Data Karyawan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">NIP</label>
                            <h6>{{ $user->karyawan->nip }}</h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Nama Lengkap</label>
                            <h6>{{ $user->karyawan->nama_lengkap }}</h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Tempat, Tanggal Lahir</label>
                            <h6>
                                {{ $user->karyawan->tempat_lahir ?? '-' }}, 
                                {{ $user->karyawan->tanggal_lahir ? \Carbon\Carbon::parse($user->karyawan->tanggal_lahir)->format('d M Y') : '-' }}
                            </h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Jenis Kelamin</label>
                            <h6>{{ $user->karyawan->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Status</label>
                            <h6>
                                @if($user->karyawan->status_aktif == 'Aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @elseif($user->karyawan->status_aktif == 'Nonaktif')
                                    <span class="badge bg-danger">Nonaktif</span>
                                @else
                                    <span class="badge bg-warning text-dark">Cuti</span>
                                @endif
                            </h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Tanggal Bergabung</label>
                            <h6>{{ $user->karyawan->tanggal_bergabung ? \Carbon\Carbon::parse($user->karyawan->tanggal_bergabung)->format('d M Y') : '-' }}</h6>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Role & Permissions Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="ri-shield-user-line me-2"></i>Role & Permissions
                    </h5>
                </div>
                <div class="card-body">
                    @if($user->roles->isNotEmpty())
                        @foreach($user->roles as $role)
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">
                                        <span class="badge bg-info badge-lg">{{ $role->name }}</span>
                                    </h6>
                                    <small class="text-muted">{{ $role->permissions->count() }} Permissions</small>
                                </div>

                                @if($role->permissions->isNotEmpty())
                                    <div class="row">
                                        @php
                                            $grouped = $role->permissions->groupBy(function($item) {
                                                $parts = explode('_', $item->name);
                                                return count($parts) > 2 ? $parts[1] . '_' . $parts[2] : $parts[1];
                                            });
                                        @endphp

                                        @foreach($grouped as $group => $permissions)
                                            <div class="col-md-6 mb-3">
                                                <div class="card border">
                                                    <div class="card-header bg-light py-2">
                                                        <h6 class="mb-0 text-uppercase small fw-bold">
                                                            <i class="ri-folder-line me-1"></i>
                                                            {{ str_replace('_', ' ', $group) }}
                                                        </h6>
                                                    </div>
                                                    <div class="card-body p-2">
                                                        @foreach($permissions as $permission)
                                                            <div class="mb-1">
                                                                <i class="ri-checkbox-circle-fill text-success me-2"></i>
                                                                <small>{{ ucwords(str_replace('_', ' ', $permission->name)) }}</small>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted mb-0">Tidak ada permissions untuk role ini</p>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-warning">
                            <i class="ri-alert-line me-2"></i>
                            User ini belum memiliki role
                        </div>
                    @endif
                </div>
            </div>

            <!-- Account Info Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="ri-information-line me-2"></i>Informasi Akun
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td width="200"><strong>Username</strong></td>
                            <td>{{ $user->username }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email</strong></td>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email Verified</strong></td>
                            <td>
                                @if($user->email_verified_at)
                                    <span class="badge bg-success">
                                        <i class="ri-checkbox-circle-line"></i> Verified
                                    </span>
                                    <small class="text-muted ms-2">
                                        ({{ \Carbon\Carbon::parse($user->email_verified_at)->format('d M Y H:i') }})
                                    </small>
                                @else
                                    <span class="badge bg-warning text-dark">
                                        <i class="ri-close-circle-line"></i> Not Verified
                                    </span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Status</strong></td>
                            <td>
                                @if($user->status == 'Aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Last Login</strong></td>
                            <td>
                                @if($user->last_login)
                                    {{ \Carbon\Carbon::parse($user->last_login)->format('d M Y H:i') }}
                                    <small class="text-muted">({{ \Carbon\Carbon::parse($user->last_login)->diffForHumans() }})</small>
                                @else
                                    <span class="text-muted">Belum pernah login</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Created At</strong></td>
                            <td>{{ $user->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Updated At</strong></td>
                            <td>{{ $user->updated_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('users.reset-password', $user->id) }}" method="POST" id="resetPasswordForm">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title" id="resetPasswordModalLabel">
                        <i class="ri-lock-password-line me-2"></i>Reset Password
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="ri-alert-line me-2"></i>
                        Anda akan mereset password untuk user <strong>{{ $user->name }}</strong>
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label fw-bold">
                            Password Baru <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new_password" name="new_password" 
                                   placeholder="Minimal 8 karakter" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                <i class="ri-eye-line" id="eyeIconNew"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label fw-bold">
                            Konfirmasi Password <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new_password_confirmation" 
                                   name="new_password_confirmation" placeholder="Ulangi password" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleNewPasswordConfirm">
                                <i class="ri-eye-line" id="eyeIconNewConfirm"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line me-2"></i>Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <style>
        .card {
            border-radius: 0.5rem;
        }
        
        .avatar-xl {
            width: 120px;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .avatar-title {
            align-items: center;
            display: flex;
            font-weight: 600;
            height: 100%;
            justify-content: center;
            width: 100%;
        }
        
        .badge-lg {
            padding: 0.5em 1em;
            font-size: 0.9rem;
        }
        
        .table td {
            vertical-align: middle;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Toggle password visibility in modal
            $('#toggleNewPassword').on('click', function() {
                var passwordField = $('#new_password');
                var eyeIcon = $('#eyeIconNew');
                
                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    eyeIcon.removeClass('ri-eye-line').addClass('ri-eye-off-line');
                } else {
                    passwordField.attr('type', 'password');
                    eyeIcon.removeClass('ri-eye-off-line').addClass('ri-eye-line');
                }
            });

            $('#toggleNewPasswordConfirm').on('click', function() {
                var passwordField = $('#new_password_confirmation');
                var eyeIcon = $('#eyeIconNewConfirm');
                
                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    eyeIcon.removeClass('ri-eye-line').addClass('ri-eye-off-line');
                } else {
                    passwordField.attr('type', 'password');
                    eyeIcon.removeClass('ri-eye-off-line').addClass('ri-eye-line');
                }
            });

            // Form validation
            $('#resetPasswordForm').on('submit', function(e) {
                var password = $('#new_password').val();
                var passwordConfirm = $('#new_password_confirmation').val();
                
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