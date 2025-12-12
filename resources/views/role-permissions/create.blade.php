@extends('layouts.app')

@section('title', 'Tambah Role')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('role-permissions.index') }}">Role & Permissions</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">Tambah Role</li>
@endsection

@section('content')
<div class="app-body">
    <form action="{{ route('role-permissions.store') }}" method="POST" id="roleForm">
        @csrf
        
        <div class="row">
            <!-- Left Column - Role Info -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="ri-shield-user-line me-2"></i>Informasi Role
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold">
                                Nama Role <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   placeholder="Contoh: Manager, Staff, Admin"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="ri-information-line"></i> Nama role harus unik dan deskriptif
                            </small>
                        </div>

                        <div class="alert alert-info">
                            <i class="ri-lightbulb-line me-2"></i>
                            <strong>Tips:</strong>
                            <ul class="mb-0 mt-2 ps-3">
                                <li>Gunakan nama yang jelas dan mudah dimengerti</li>
                                <li>Pilih permissions sesuai kebutuhan role</li>
                                <li>Pastikan tidak ada duplikasi nama</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line me-2"></i>Simpan Role
                            </button>
                            <a href="{{ route('role-permissions.index') }}" class="btn btn-outline-secondary">
                                <i class="ri-arrow-left-line me-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Permissions -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="ri-lock-password-line me-2"></i>Pilih Permissions
                            </h5>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="selectAll">
                                    <i class="ri-checkbox-multiple-line"></i> Pilih Semua
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAll">
                                    <i class="ri-checkbox-blank-line"></i> Batal Pilih Semua
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($permissions->isEmpty())
                            <div class="alert alert-warning">
                                <i class="ri-alert-line me-2"></i>
                                Belum ada permissions yang tersedia. Silakan buat permissions terlebih dahulu.
                            </div>
                        @else
                            <div class="row">
                                @foreach($permissions as $group => $perms)
                                    <div class="col-md-6 mb-4">
                                        <div class="card border">
                                            <div class="card-header bg-light">
                                                <div class="form-check">
                                                    <input class="form-check-input group-checkbox" 
                                                           type="checkbox" 
                                                           id="group_{{ $loop->index }}"
                                                           data-group="{{ $loop->index }}">
                                                    <label class="form-check-label fw-bold text-uppercase" for="group_{{ $loop->index }}">
                                                        <i class="ri-folder-line me-1"></i>
                                                        {{ str_replace('_', ' ', $group) }}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                @foreach($perms as $permission)
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input permission-checkbox" 
                                                               type="checkbox" 
                                                               name="{{ $permission->name }}" 
                                                               value="{{ $permission->name }}"
                                                               id="permission_{{ $permission->id }}"
                                                               data-group="{{ $loop->parent->index }}">
                                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                            {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
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
        
        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        
        .card-header {
            border-bottom: 2px solid #e9ecef;
        }
        
        .sticky-top {
            position: sticky;
            z-index: 1020;
        }
        
        .form-check-label {
            cursor: pointer;
        }
        
        .group-checkbox:checked ~ label {
            color: #0d6efd;
            font-weight: 600;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Select All
            $('#selectAll').on('click', function() {
                $('.permission-checkbox').prop('checked', true);
                $('.group-checkbox').prop('checked', true);
            });
            
            // Deselect All
            $('#deselectAll').on('click', function() {
                $('.permission-checkbox').prop('checked', false);
                $('.group-checkbox').prop('checked', false);
            });
            
            // Group checkbox functionality
            $('.group-checkbox').on('change', function() {
                const groupIndex = $(this).data('group');
                const isChecked = $(this).prop('checked');
                $(`.permission-checkbox[data-group="${groupIndex}"]`).prop('checked', isChecked);
            });
            
            // Update group checkbox when individual permission changes
            $('.permission-checkbox').on('change', function() {
                const groupIndex = $(this).data('group');
                const totalInGroup = $(`.permission-checkbox[data-group="${groupIndex}"]`).length;
                const checkedInGroup = $(`.permission-checkbox[data-group="${groupIndex}"]:checked`).length;
                
                $(`.group-checkbox[data-group="${groupIndex}"]`).prop('checked', totalInGroup === checkedInGroup);
            });
            
            // Form validation
            $('#roleForm').on('submit', function(e) {
                const roleName = $('#name').val().trim();
                
                if (roleName === '') {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Nama role harus diisi!'
                    });
                    return false;
                }
            });
        });
    </script>
@endpush