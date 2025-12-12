@extends('layouts.app')

@section('title', 'Manajemen User')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Manajemen User</li>
@endsection

@section('content')
<div class="app-body">
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

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-primary bg-soft">
                                <span class="avatar-title rounded-circle bg-primary bg-gradient">
                                    <i class="ri-user-line fs-4 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Total Users</p>
                            <h4 class="mb-0">{{ $users->total() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-success bg-soft">
                                <span class="avatar-title rounded-circle bg-success bg-gradient">
                                    <i class="ri-user-smile-line fs-4 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">User Aktif</p>
                            <h4 class="mb-0">{{ $totalAktif }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-danger bg-soft">
                                <span class="avatar-title rounded-circle bg-danger bg-gradient">
                                    <i class="ri-user-unfollow-line fs-4 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">User Nonaktif</p>
                            <h4 class="mb-0">{{ $totalNonaktif }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-info bg-soft">
                                <span class="avatar-title rounded-circle bg-info bg-gradient">
                                    <i class="ri-shield-user-line fs-4 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Total Roles</p>
                            <h4 class="mb-0">{{ $roles->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="ri-team-line me-2"></i>Daftar User
                        </h5>
                        <a class="btn btn-primary btn-sm" href="{{ route('users.create') }}">
                            <i class="ri-add-circle-line me-1"></i>Tambah User
                        </a>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="card-body border-bottom bg-light">
                    <form action="{{ route('users.index') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Filter Status</label>
                                <select class="form-select form-select-sm" name="status">
                                    <option value="">Semua Status</option>
                                    <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Nonaktif" {{ request('status') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Filter Role</label>
                                <select class="form-select form-select-sm" name="role">
                                    <option value="">Semua Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Cari</label>
                                <input type="text" class="form-control form-control-sm" name="search" 
                                       value="{{ request('search') }}" placeholder="Cari nama, username, atau email...">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold d-block">&nbsp;</label>
                                <button type="submit" class="btn btn-sm btn-primary w-100">
                                    <i class="ri-search-line me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>User Info</th>
                                    <th width="150">NIP</th>
                                    <th width="150">Role</th>
                                    <th width="100">Status</th>
                                    <th width="150">Last Login</th>
                                    <th width="150" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $index => $user)
                                    <tr>
                                        <td class="text-center">{{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-3">
                                                    @if($user->karyawan && $user->karyawan->foto)
                                                        <img src="{{ asset('storage/' . $user->karyawan->foto) }}" 
                                                             class="rounded-circle" 
                                                             alt="{{ $user->name }}"
                                                             style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <span class="avatar-title rounded-circle bg-soft-primary text-primary fs-5">
                                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <strong class="text-primary">{{ $user->name }}</strong>
                                                    <br><small class="text-muted">
                                                        <i class="ri-user-line"></i> {{ $user->username }}
                                                    </small>
                                                    <br><small class="text-muted">
                                                        <i class="ri-mail-line"></i> {{ $user->email }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($user->karyawan)
                                                <span class="badge bg-secondary">{{ $user->karyawan->nip }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->roles->isNotEmpty())
                                                <span class="badge bg-info">
                                                    <i class="ri-shield-user-line"></i> {{ $user->roles->first()->name }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">No Role</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->status == 'Aktif')
                                                <span class="badge bg-success">
                                                    <i class="ri-checkbox-circle-line"></i> Aktif
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="ri-close-circle-line"></i> Nonaktif
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->last_login)
                                                <small class="text-muted">
                                                    <i class="ri-time-line"></i> 
                                                    {{ \Carbon\Carbon::parse($user->last_login)->diffForHumans() }}
                                                </small>
                                            @else
                                                <small class="text-muted">Belum pernah login</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-2-fill"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('users.show', $user->id) }}">
                                                            <i class="ri-eye-line me-2"></i>Detail
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('users.edit', $user->id) }}">
                                                            <i class="ri-pencil-fill me-2"></i>Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('users.toggle-status', $user->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="ri-toggle-line me-2"></i>
                                                                {{ $user->status == 'Aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a href="{{ route('users.destroy', $user->id) }}" 
                                                           class="dropdown-item text-danger" 
                                                           data-confirm-delete="true">
                                                            <i class="ri-delete-bin-6-line me-2"></i>Hapus
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="ri-user-line ri-3x text-muted d-block mb-3"></i>
                                            <p class="text-muted mb-0">Belum ada data user</p>
                                            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm mt-3">
                                                <i class="ri-add-circle-line me-1"></i>Tambah User
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($users->hasPages())
                        <div class="mt-3 d-flex justify-content-end">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <style>
        .badge {
            font-weight: 500;
            padding: 0.4em 0.8em;
            font-size: 0.85rem;
        }
        
        .avatar-sm {
            width: 40px;
            height: 40px;
            display: inline-flex;
        }
        
        .avatar-title {
            align-items: center;
            display: flex;
            font-weight: 600;
            height: 100%;
            justify-content: center;
            width: 100%;
        }
        
        .bg-soft-primary {
            background-color: rgba(13, 110, 253, 0.15) !important;
        }
        
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .card {
            border-radius: 0.5rem;
        }

        .dropdown-item i {
            width: 20px;
        }
        
        .table td {
            vertical-align: middle;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Auto dismiss alerts
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });
    </script>
@endpush