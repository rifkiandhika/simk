@extends('layouts.app')

@section('title', 'Role & Permissions')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Role & Permissions</li>
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
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-primary bg-soft">
                                <span class="avatar-title rounded-circle bg-primary bg-gradient">
                                    <i class="ri-shield-user-line fs-4 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Total Roles</p>
                            <h4 class="mb-0">{{ $data->total() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-success bg-soft">
                                <span class="avatar-title rounded-circle bg-success bg-gradient">
                                    <i class="ri-user-settings-line fs-4 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Total Permissions</p>
                            <h4 class="mb-0">{{ \Spatie\Permission\Models\Permission::count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-info bg-soft">
                                <span class="avatar-title rounded-circle bg-info bg-gradient">
                                    <i class="ri-group-line fs-4 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Total Users</p>
                            <h4 class="mb-0">{{ \App\Models\User::count() }}</h4>
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
                            <i class="ri-shield-user-line me-2"></i>Daftar Role & Permissions
                        </h5>
                        <a class="btn btn-primary btn-sm" href="{{ route('role-permissions.create') }}">
                            <i class="ri-add-circle-line me-1"></i>Tambah Role
                        </a>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="card-body border-bottom bg-light">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Cari Role</label>
                            <input type="text" class="form-control form-control-sm" id="searchBox" placeholder="Cari nama role...">
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle" id="roleTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Nama Role</th>
                                    <th width="120">Total Users</th>
                                    <th width="150">Total Permissions</th>
                                    <th width="150">Created Date</th>
                                    <th width="120" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $x => $item)
                                    <tr>
                                        <td class="text-center">{{ ($data->currentPage() - 1) * $data->perPage() + $x + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-2">
                                                    <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                        <i class="ri-shield-user-fill"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <strong class="text-primary">{{ $item->name }}</strong>
                                                    @if($item->name == 'Superadmin')
                                                        <span class="badge badge-sm bg-danger ms-2">
                                                            <i class="ri-vip-crown-line"></i> System
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <i class="ri-group-line"></i> {{ $item->users_count ?? 0 }} Users
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">
                                                <i class="ri-lock-password-line"></i> {{ $item->permissions->count() }} Permissions
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <i class="ri-calendar-line"></i> 
                                                {{ $item->created_at->format('d M Y') }}
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-2-fill"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('role-permissions.show', $item->id) }}">
                                                            <i class="ri-eye-line me-2"></i>Detail
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('role-permissions.edit', $item->id) }}">
                                                            <i class="ri-pencil-fill me-2"></i>Edit
                                                        </a>
                                                    </li>
                                                    @if($item->name != 'Superadmin')
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a href="{{ route('role-permissions.destroy', $item->id) }}" 
                                                               class="dropdown-item text-danger delete-confirm" 
                                                               data-confirm-delete="true">
                                                                <i class="ri-delete-bin-6-line me-2"></i>Hapus
                                                            </a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <i class="ri-shield-user-line ri-3x text-muted d-block mb-3"></i>
                                            <p class="text-muted mb-0">Belum ada data role</p>
                                            <a href="{{ route('role-permissions.create') }}" class="btn btn-primary btn-sm mt-3">
                                                <i class="ri-add-circle-line me-1"></i>Tambah Role
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($data->hasPages())
                        <div class="mt-3 d-flex justify-content-end">
                            {{ $data->links() }}
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
        
        .badge-sm {
            font-size: 0.75rem;
            padding: 0.25em 0.6em;
        }
        
        .avatar-xs {
            height: 2rem;
            width: 2rem;
        }
        
        .avatar-sm {
            width: 3rem;
            height: 3rem;
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
        
        .bg-soft {
            opacity: 0.1;
        }
        
        .bg-soft-primary {
            background-color: rgba(13, 110, 253, 0.15) !important;
        }
        
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
            cursor: pointer;
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
        
        .table {
            border: 1px solid #ced4da !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            let dataTable = null;
            
            // Function to initialize DataTable
            function initializeDataTable() {
                const $table = $('#roleTable');
                const isEmpty = $table.find('tbody tr td[colspan]').length > 0;
                
                if ($.fn.DataTable.isDataTable('#roleTable')) {
                    $table.DataTable().destroy();
                }
                
                if (!isEmpty) {
                    try {
                        dataTable = $table.DataTable({
                            responsive: true,
                            pageLength: 10,
                            order: [[1, 'asc']],
                            dom: 'rtip',
                            language: {
                                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                                emptyTable: "Belum ada data role"
                            },
                            columnDefs: [
                                { orderable: false, targets: [5] }
                            ]
                        });

                        // Custom search
                        $('#searchBox').on('keyup', function() {
                            dataTable.search(this.value).draw();
                        });
                    } catch (error) {
                        console.log('DataTable initialization skipped');
                    }
                }
            }
            
            initializeDataTable();

            // Auto dismiss alerts
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });
    </script>
@endpush