@extends('layouts.app')

@section('title', 'Detail Role')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('role-permissions.index') }}">Role & Permissions</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">Detail Role</li>
@endsection

@section('content')
<div class="app-body">
    <div class="row">
        <!-- Left Column - Role Info -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="ri-information-line me-2"></i>Informasi Role
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-lg mx-auto mb-3">
                            <span class="avatar-title rounded-circle bg-info bg-gradient fs-1">
                                <i class="ri-shield-user-line text-white"></i>
                            </span>
                        </div>
                        <h4 class="mb-1">{{ $role->name }}</h4>
                        @if($role->name == 'Superadmin')
                            <span class="badge bg-danger">
                                <i class="ri-vip-crown-line"></i> System Role
                            </span>
                        @endif
                    </div>

                    <div class="border-top pt-3">
                        <div class="row text-center">
                            <div class="col-6 border-end">
                                <div class="p-2">
                                    <h3 class="text-primary mb-1">{{ $role->permissions->count() }}</h3>
                                    <p class="text-muted mb-0 small">Permissions</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2">
                                    <h3 class="text-success mb-1">{{ $users->count() }}</h3>
                                    <p class="text-muted mb-0 small">Users</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-top pt-3 mt-3">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td width="120"><small class="text-muted">Created:</small></td>
                                <td><small>{{ $role->created_at->format('d M Y H:i') }}</small></td>
                            </tr>
                            <tr>
                                <td><small class="text-muted">Updated:</small></td>
                                <td><small>{{ $role->updated_at->format('d M Y H:i') }}</small></td>
                            </tr>
                            <tr>
                                <td><small class="text-muted">Guard:</small></td>
                                <td><span class="badge bg-secondary">{{ $role->guard_name }}</span></td>
                            </tr>
                        </table>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('role-permissions.edit', $role->id) }}" class="btn btn-warning text-dark">
                            <i class="ri-pencil-line me-2"></i>Edit Role
                        </a>
                        <a href="{{ route('role-permissions.index') }}" class="btn btn-outline-secondary">
                            <i class="ri-arrow-left-line me-2"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Permissions & Users -->
        <div class="col-xl-8 col-lg-7">
            <!-- Permissions Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="ri-lock-password-line me-2"></i>Permissions ({{ $role->permissions->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    @if($permissions->isEmpty())
                        <div class="alert alert-warning">
                            <i class="ri-alert-line me-2"></i>
                            Belum ada permissions yang tersedia.
                        </div>
                    @else
                        <div class="row">
                            @foreach($permissions as $group => $perms)
                                <div class="col-md-6 mb-4">
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0 text-uppercase fw-bold">
                                                <i class="ri-folder-line me-1"></i>
                                                {{ str_replace('_', ' ', $group) }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            @php
                                                $hasPermission = false;
                                            @endphp
                                            @foreach($perms as $permission)
                                                @if($role->hasPermissionTo($permission->name))
                                                    @php $hasPermission = true; @endphp
                                                    <div class="mb-2">
                                                        <i class="ri-checkbox-circle-fill text-success me-2"></i>
                                                        <span>{{ ucwords(str_replace('_', ' ', $permission->name)) }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                            
                                            @if(!$hasPermission)
                                                <p class="text-muted mb-0 small">
                                                    <i class="ri-close-circle-line me-1"></i>Tidak ada permission
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Users Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="ri-group-line me-2"></i>Users dengan Role ini ({{ $users->count() }})
                        </h5>
                    </div>
                </div>
                <div class="card-body">
                    @if($users->isEmpty())
                        <div class="text-center py-5">
                            <i class="ri-user-line ri-3x text-muted d-block mb-3"></i>
                            <p class="text-muted mb-0">Belum ada user yang menggunakan role ini</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>NIP</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Last Login</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $index => $user)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs me-2">
                                                        @if($user->karyawan && $user->karyawan->foto)
                                                            <img src="{{ asset('storage/' . $user->karyawan->foto) }}" 
                                                                 class="rounded-circle" 
                                                                 alt="{{ $user->name }}"
                                                                 style="width: 32px; height: 32px; object-fit: cover;">
                                                        @else
                                                            <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <strong>{{ $user->name }}</strong>
                                                        <br><small class="text-muted">{{ $user->username }}</small>
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
                                                <small>{{ $user->email }}</small>
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
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
        .card {
            border-radius: 0.5rem;
        }
        
        .avatar-lg {
            width: 5rem;
            height: 5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .avatar-xs {
            height: 2rem;
            width: 2rem;
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
        
        .sticky-top {
            position: sticky;
            z-index: 1020;
        }
        
        .table td {
            vertical-align: middle;
        }
        
        .badge {
            font-weight: 500;
            padding: 0.4em 0.8em;
        }
    </style>
@endpush