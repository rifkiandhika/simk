@extends('layouts.app')

@section('title', 'Data Pasien')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Data Pasien</li>
@endsection

@section('content')
<div class="app-body">
    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
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
                            <p class="text-muted mb-1">Total Pasien</p>
                            <h4 class="mb-0">{{ $pasiens->total() }}</h4>
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
                                    <i class="ri-shield-cross-line fs-4 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">BPJS</p>
                            <h4 class="mb-0">{{ $pasiens->where('jenis_pembayaran', 'BPJS')->count() }}</h4>
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
                                    <i class="ri-bank-card-line fs-4 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Asuransi</p>
                            <h4 class="mb-0">{{ $pasiens->where('jenis_pembayaran', 'Asuransi')->count() }}</h4>
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
                            <div class="avatar-sm rounded-circle bg-warning bg-soft">
                                <span class="avatar-title rounded-circle bg-warning bg-gradient">
                                    <i class="ri-wallet-line fs-4 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Umum</p>
                            <h4 class="mb-0">{{ $pasiens->where('jenis_pembayaran', 'Umum')->count() }}</h4>
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
                            <i class="ri-list-check-2 me-2"></i>Daftar Pasien
                        </h5>
                        <a href="{{ route('pasiens.create') }}" class="btn btn-primary btn-sm">
                            <i class="ri-add-circle-line me-1"></i>Tambah Pasien
                        </a>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="card-body border-bottom bg-light">
                    <form action="{{ route('pasiens.index') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Jenis Pembayaran</label>
                                <select name="jenis_pembayaran" class="form-select form-select-sm">
                                    <option value="">Semua</option>
                                    <option value="BPJS" {{ request('jenis_pembayaran') == 'BPJS' ? 'selected' : '' }}>BPJS</option>
                                    <option value="Umum" {{ request('jenis_pembayaran') == 'Umum' ? 'selected' : '' }}>Umum</option>
                                    <option value="Asuransi" {{ request('jenis_pembayaran') == 'Asuransi' ? 'selected' : '' }}>Asuransi</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Status</label>
                                <select name="status_aktif" class="form-select form-select-sm">
                                    <option value="">Semua</option>
                                    <option value="Aktif" {{ request('status_aktif') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Nonaktif" {{ request('status_aktif') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Cari</label>
                                <input type="text" name="search" class="form-control form-control-sm" 
                                       value="{{ request('search') }}" 
                                       placeholder="No. RM, NIK, Nama, No. Telp">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold d-block">&nbsp;</label>
                                <button type="submit" class="btn btn-sm btn-info w-100">
                                    <i class="ri-filter-line me-1"></i>Terapkan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle" id="pasienTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>No. RM</th>
                                    <th>NIK</th>
                                    <th>Nama Lengkap</th>
                                    <th>Jenis Kelamin</th>
                                    <th>No. Telp</th>
                                    <th width="150">Jenis Pembayaran</th>
                                    <th width="100">Status</th>
                                    <th width="120" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pasiens as $index => $pasien)
                                <tr>
                                    <td class="text-center">{{ $pasiens->firstItem() + $index }}</td>
                                    <td>
                                        <strong class="text-primary">{{ $pasien->no_rm }}</strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $pasien->nik ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-2">
                                                <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                    <i class="ri-user-line"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <strong>{{ $pasien->nama_lengkap }}</strong>
                                                @if($pasien->tanggal_lahir)
                                                <br><small class="text-muted">{{ \Carbon\Carbon::parse($pasien->tanggal_lahir)->age }} tahun</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($pasien->jenis_kelamin == 'L')
                                            <span class="badge badge-sm bg-primary">Laki-laki</span>
                                        @else
                                            <span class="badge badge-sm bg-danger">Perempuan</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="ri-phone-line"></i> {{ $pasien->no_telp ?? '-' }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($pasien->jenis_pembayaran == 'BPJS')
                                            <span class="badge bg-success">
                                                <i class="ri-shield-cross-line"></i> {{ $pasien->jenis_pembayaran }}
                                            </span>
                                        @elseif($pasien->jenis_pembayaran == 'Asuransi')
                                            <span class="badge bg-info">
                                                <i class="ri-bank-card-line"></i> {{ $pasien->jenis_pembayaran }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="ri-wallet-line"></i> {{ $pasien->jenis_pembayaran }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($pasien->status_aktif == 'Aktif')
                                            <span class="badge bg-success">
                                                <i class="ri-record-circle-line"></i> Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="ri-record-circle-line"></i> Nonaktif
                                            </span>
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
                                                    <button type="button" class="dropdown-item btn-buat-resep" 
                                                        data-pasien-id="{{ $pasien->id_pasien }}">
                                                        <i class="ri-file-add-line me-2"></i>Buat Resep
                                                    </button>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('pasiens.show', $pasien->id_pasien) }}">
                                                        <i class="ri-eye-fill me-2"></i>Detail
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('pasiens.edit', $pasien->id_pasien) }}">
                                                        <i class="ri-pencil-fill me-2"></i>Edit
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <button class="dropdown-item text-danger" onclick="deletePasien('{{ $pasien->id_pasien }}')">
                                                        <i class="ri-delete-bin-6-line me-2"></i>Hapus
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- PIN Modal for Delete --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Konfirmasi Hapus Pasien</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Apakah Anda yakin ingin menghapus data pasien ini?</p>
                <div class="alert alert-warning">
                    <i class="ri-alert-line"></i> Tindakan ini tidak dapat dibatalkan!
                </div>
                <div class="mb-3">
                    <label class="form-label">PIN (6 digit)</label>
                    <input type="password" class="form-control" id="pinDelete" maxlength="6" placeholder="Masukkan PIN">
                </div>
                <input type="hidden" id="pasienIdDelete">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Hapus Pasien</button>
            </div>
        </div>
    </div>
</div>

@include('pasien.partials.modal_resep')
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
        $("#pasienTable").DataTable({
            ordering: false,
            searching: false,
            lengthChange: false,
            language: {
                emptyTable: `
                    <div class="text-center py-5">
                        <i class="ri-user-line ri-3x text-muted d-block mb-3"></i>
                        <p class="text-muted mb-0">Belum ada data pasien</p>
                        <a href="{{ route('pasiens.create') }}" class="btn btn-primary btn-sm mt-3">
                            <i class="ri-add-circle-line me-1"></i>Tambah Pasien Baru
                        </a>
                    </div>
                `
            }
        });

        // Auto dismiss alerts
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });

    let deleteModalInstance;

    document.addEventListener('DOMContentLoaded', function() {
        deleteModalInstance = new bootstrap.Modal(document.getElementById('deleteModal'));
    });

    function deletePasien(pasienId) {
        document.getElementById('pasienIdDelete').value = pasienId;
        document.getElementById('pinDelete').value = '';
        deleteModalInstance.show();
    }

    function confirmDelete() {
        const pin = document.getElementById('pinDelete').value;
        const pasienId = document.getElementById('pasienIdDelete').value;

        if (!pin || pin.length !== 6) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'PIN harus 6 digit'
            });
            return;
        }

        fetch(`/pasien/${pasienId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ pin: pin })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.message || 'Terjadi kesalahan'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan sistem'
            });
        });

        deleteModalInstance.hide();
    }

        $(document).on('click', '.btn-buat-resep', function() {
            const pasienId = $(this).data('pasien-id');
            
            // Langsung panggil fungsi dari modal_resep
            setDataPasienResep(pasienId);
            
            // Buka modal
            $('#modalResep').modal('show');
        });
</script>
@endpush