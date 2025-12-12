@extends('layouts.app')

@section('title', 'Satuan')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Satuan</li>
@endsection

@section('content')
<div class="app-body">
    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistik -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar-sm rounded-circle bg-primary bg-gradient d-flex align-items-center justify-content-center me-3">
                        <i class="ri-list-check-2 fs-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Total Satuan</p>
                        <h4 class="mb-0">{{ $satuans->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar-sm rounded-circle bg-success bg-gradient d-flex align-items-center justify-content-center me-3">
                        <i class="ri-check-line fs-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Satuan Aktif</p>
                        <h4 class="mb-0">{{ $satuans->where('status', 'Aktif')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar-sm rounded-circle bg-danger bg-gradient d-flex align-items-center justify-content-center me-3">
                        <i class="ri-close-circle-line fs-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Satuan Nonaktif</p>
                        <h4 class="mb-0">{{ $satuans->where('status', 'Nonaktif')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0"><i class="ri-database-2-line me-2"></i>Daftar Satuan</h5>
            <a href="{{ route('satuans.create') }}" class="btn btn-primary btn-sm">
                <i class="ri-add-circle-line me-1"></i>Tambah Satuan
            </a>
        </div>

        <div class="card-body border-bottom bg-light">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Filter Status</label>
                    <select class="form-select form-select-sm" id="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Nonaktif">Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label small fw-bold">Cari</label>
                    <input type="text" id="searchBox" class="form-control form-control-sm" placeholder="Cari nama atau deskripsi...">
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                @if($satuans->count() > 0)
                    <table class="table table-hover table-striped align-middle" id="satuanTable">
                        <thead class="table-light">
                            <tr>
                                <th width="50">No</th>
                                <th>Satuan</th>
                                <th>Deskripsi</th>
                                <th width="100">Status</th>
                                <th width="150" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($satuans as $x => $data)
                                <tr>
                                    <td class="text-center">{{ $x + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-2">
                                                <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                    {{ substr($data->nama_satuan, 0, 1) }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $data->deskripsi ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $data->status === 'Aktif' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $data->status }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                <i class="ri-more-2-fill"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                                <li>
                                                    <a href="{{ route('satuans.edit', $data->id) }}" class="dropdown-item">
                                                        <i class="ri-pencil-line me-2"></i>Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('satuans.destroy', $data->id) }}" method="POST" class="delete-confirm">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="ri-delete-bin-6-line me-2"></i>Hapus
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-5">
                        <i class="ri-inbox-line ri-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted mb-2">Belum ada data satuan</p>
                        <a href="{{ route('satuans.create') }}" class="btn btn-primary btn-sm">
                            <i class="ri-add-circle-line me-1"></i>Tambah Satuan
                        </a>
                    </div>
                @endif
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
        
        .bg-purple {
            background-color: #6f42c1;
            color: white;
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
    // Inisialisasi DataTable
    let table = $('#satuanTable').DataTable({
        dom: 'rtip',
        pageLength: 10,
        responsive: true,
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' }
    });

    // Custom search
    $('#searchBox').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Filter status
    $('#filterStatus').on('change', function() {
        table.column(3).search(this.value).draw();
    });

    // SweetAlert delete confirm
    $(document).on('submit', '.delete-confirm', function(e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data satuan akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    });

    // Auto-dismiss alert
    setTimeout(() => $('.alert').fadeOut('slow'), 4000);
});
</script>
@endpush
