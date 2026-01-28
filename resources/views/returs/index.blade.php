@extends('layouts.app')

@section('title', 'Daftar Retur')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Retur</li>
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
                                    <i class="ri-arrow-go-back-fill fs-4 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small">Total Retur</p>
                            <h4 class="mb-0">{{ $returs->total() }}</h4>
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
                                    <i class="ri-time-line fs-4 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small">Menunggu Persetujuan</p>
                            <h4 class="mb-0">{{ \App\Models\Retur::where('status', 'menunggu_persetujuan')->count() }}</h4>
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
                                    <i class="ri-loader-line fs-4 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small">Diproses</p>
                            <h4 class="mb-0">{{ \App\Models\Retur::where('status', 'diproses')->count() }}</h4>
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
                                    <i class="ri-check-double-line fs-4 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small">Selesai</p>
                            <h4 class="mb-0">{{ \App\Models\Retur::where('status', 'selesai')->count() }}</h4>
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
                            <i class="ri-list-check-2 me-2"></i>Daftar Retur
                        </h5>
                        <a class="btn btn-primary btn-sm" href="{{ route('returs.create') }}">
                            <i class="ri-add-circle-line me-1"></i>Buat Retur Baru
                        </a>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="card-body border-bottom bg-light">
                    <form method="GET" action="{{ route('returs.index') }}" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Tipe Retur</label>
                                <select name="tipe_retur" class="form-select form-select-sm">
                                    <option value="">Semua Tipe</option>
                                    <option value="po" {{ request('tipe_retur') == 'po' ? 'selected' : '' }}>Purchase Order</option>
                                    <option value="stock_apotik" {{ request('tipe_retur') == 'stock_apotik' ? 'selected' : '' }}>Stock Apotik</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">Semua Status</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="menunggu_persetujuan" {{ request('status') == 'menunggu_persetujuan' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                    <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Tanggal Dari</label>
                                <input type="date" name="tanggal_dari" class="form-control form-control-sm" value="{{ request('tanggal_dari') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Tanggal Sampai</label>
                                <input type="date" name="tanggal_sampai" class="form-control form-control-sm" value="{{ request('tanggal_sampai') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Cari</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="No Retur / Kode Referensi" 
                                           value="{{ request('search') }}">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="ri-search-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle" id="returTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th width="130">No Retur</th>
                                    <th width="100">Tipe</th>
                                    <th width="130">Kode Referensi</th>
                                    <th width="100">Tanggal</th>
                                    <th width="150">Pelapor</th>
                                    <th>Alasan</th>
                                    <th width="120">Total Nilai</th>
                                    <th width="120">Status</th>
                                    <th width="120" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($returs as $index => $retur)
                                    <tr>
                                        <td class="text-center">{{ $returs->firstItem() + $index }}</td>
                                        <td>
                                            <code class="text-primary fw-bold">{{ $retur->no_retur }}</code>
                                        </td>
                                        <td>
                                            @if($retur->tipe_retur == 'po')
                                                <span class="badge bg-info">
                                                    <i class="ri-file-list-3-line me-1"></i>PO
                                                </span>
                                            @else
                                                <span class="badge bg-primary">
                                                    <i class="ri-store-3-line me-1"></i>Stock
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted small">{{ $retur->kode_referensi }}</span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($retur->tanggal_retur)->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-2">
                                                    <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                        <i class="ri-user-line"></i>
                                                    </span>
                                                </div>
                                                <span class="small">{{ $retur->karyawanPelapor->nama_lengkap ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ str_replace('_', ' ', ucwords($retur->alasan_retur)) }}
                                            </small>
                                        </td>
                                        <td class="text-end">
                                            <strong class="text-success">Rp {{ number_format($retur->total_nilai_retur, 0, ',', '.') }}</strong>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = [
                                                    'draft' => 'secondary',
                                                    'menunggu_persetujuan' => 'warning',
                                                    'disetujui' => 'info',
                                                    'ditolak' => 'danger',
                                                    'diproses' => 'primary',
                                                    'selesai' => 'success',
                                                    'dibatalkan' => 'dark'
                                                ];
                                                $statusIcon = [
                                                    'draft' => 'ri-draft-line',
                                                    'menunggu_persetujuan' => 'ri-time-line',
                                                    'disetujui' => 'ri-checkbox-circle-line',
                                                    'ditolak' => 'ri-close-circle-line',
                                                    'diproses' => 'ri-loader-line',
                                                    'selesai' => 'ri-check-double-line',
                                                    'dibatalkan' => 'ri-forbid-line'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusClass[$retur->status] ?? 'secondary' }}">
                                                <i class="{{ $statusIcon[$retur->status] ?? 'ri-information-line' }} me-1"></i>
                                                {{ str_replace('_', ' ', ucwords($retur->status)) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-2-fill"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('returs.show', $retur->id_retur) }}">
                                                            <i class="ri-eye-line me-2"></i>Detail
                                                        </a>
                                                    </li>
                                                    
                                                    @if($retur->status == 'draft' || $retur->status == 'ditolak')
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('returs.edit', $retur->id_retur) }}">
                                                            <i class="ri-pencil-fill me-2"></i>Edit
                                                        </a>
                                                    </li>
                                                    @endif

                                                    @if($retur->status == 'draft')
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('returs.submit', $retur->id_retur) }}" method="POST" class="submit-confirm">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item text-primary">
                                                                <i class="ri-send-plane-line me-2"></i>Submit
                                                            </button>
                                                        </form>
                                                    </li>
                                                    @endif

                                                    @if(in_array($retur->status, ['draft', 'ditolak', 'dibatalkan']))
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('returs.destroy', $retur->id_retur) }}" method="POST" class="delete-confirm">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="ri-delete-bin-6-line me-2"></i>Hapus
                                                            </button>
                                                        </form>
                                                    </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-5">
                                            <i class="ri-inbox-line ri-3x text-muted d-block mb-3"></i>
                                            <p class="text-muted mb-0">Belum ada data retur</p>
                                            <a href="{{ route('returs.create') }}" class="btn btn-primary btn-sm mt-3">
                                                <i class="ri-add-circle-line me-1"></i>Buat Retur Baru
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($returs->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted small">
                            Menampilkan {{ $returs->firstItem() }} sampai {{ $returs->lastItem() }} 
                            dari {{ $returs->total() }} data
                        </div>
                        <div>
                            {{ $returs->links() }}
                        </div>
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
    
    .avatar-xs {
        height: 2rem;
        width: 2rem;
    }
    
    .avatar-sm {
        width: 2.5rem;
        height: 2.5rem;
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
    
    .bg-soft-success {
        background-color: rgba(25, 135, 84, 0.15) !important;
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
    .table {
        border: 1px solid #ced4da !important;
    }

    code {
        background-color: #f0f7ff;
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Delete confirmation
        $(document).on('submit', '.delete-confirm', function(e) {
            e.preventDefault();
            var form = this;
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data retur akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Submit confirmation
        $(document).on('submit', '.submit-confirm', function(e) {
            e.preventDefault();
            var form = this;
            
            Swal.fire({
                title: 'Submit Retur?',
                text: "Retur akan diajukan untuk persetujuan",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Submit!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Auto hide alerts
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
@endpush