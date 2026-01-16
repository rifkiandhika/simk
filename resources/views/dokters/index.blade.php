@extends('layouts.app')

@section('title', 'Dokter')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Dokter</li>
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
                                        <i class="ri-user-heart-line fs-4 text-white"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1 small">Total Dokter</p>
                                <h4 class="mb-0">{{ $dokters->count() }}</h4>
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
                                        <i class="ri-checkbox-circle-line fs-4 text-white"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1 small">Dokter Aktif</p>
                                <h4 class="mb-0">{{ $dokters->where('is_active', true)->count() }}</h4>
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
                                        <i class="ri-close-circle-line fs-4 text-white"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1 small">Dokter Nonaktif</p>
                                <h4 class="mb-0">{{ $dokters->where('is_active', false)->count() }}</h4>
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
                                        <i class="ri-stethoscope-line fs-4 text-white"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1 small">Spesialisasi</p>
                                <h4 class="mb-0">{{ $dokters->whereNotNull('spesialisasi')->unique('spesialisasi')->count() }}</h4>
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
                                <i class="ri-user-heart-line me-2"></i>Daftar Dokter
                            </h5>
                            <a class="btn btn-primary btn-sm" href="{{ route('dokters.create') }}">
                                <i class="ri-add-circle-line me-1"></i>Tambah Dokter
                            </a>
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div class="card-body border-bottom bg-light">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Cari</label>
                                <input type="text" class="form-control form-control-sm" id="searchBox" placeholder="Cari kode, nama dokter, spesialisasi...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Filter Status</label>
                                <select class="form-select form-select-sm" id="filterStatus">
                                    <option value="">Semua Status</option>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Nonaktif">Nonaktif</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Filter Spesialisasi</label>
                                <select class="form-select form-select-sm" id="filterSpesialisasi">
                                    <option value="">Semua Spesialisasi</option>
                                    @foreach($dokters->whereNotNull('spesialisasi')->unique('spesialisasi')->sortBy('spesialisasi') as $dok)
                                        <option value="{{ $dok->spesialisasi }}">{{ $dok->spesialisasi }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle" id="dokterTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">No</th>
                                        <th width="120">Kode</th>
                                        <th>Nama Dokter</th>
                                        <th width="180">Spesialisasi</th>
                                        <th width="120">No. STR</th>
                                        <th width="120">No. SIP</th>
                                        <th>Ruangan Praktek</th>
                                        <th width="100" class="text-center">Status</th>
                                        <th width="150" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($dokters as $x => $data)
                                        <tr>
                                            <td class="text-center">{{ $x + 1 }}</td>
                                            <td>
                                                <code class="text-primary fw-bold">{{ $data->kode_dokter }}</code>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="ri-user-heart-line text-muted me-2"></i>
                                                    <strong>{{ $data->nama_dokter }}</strong>
                                                </div>
                                            </td>
                                            <td>
                                                @if($data->spesialisasi)
                                                    <span class="badge bg-info">
                                                        <i class="ri-stethoscope-line me-1"></i>{{ $data->spesialisasi }}
                                                    </span>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($data->no_str)
                                                    <small class="text-muted">{{ $data->no_str }}</small>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($data->no_sip)
                                                    <small class="text-muted">{{ $data->no_sip }}</small>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($data->ruangans->count() > 0)
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach($data->ruangans as $ruangan)
                                                            <span class="badge bg-secondary" style="font-size: 0.7rem;">
                                                                <i class="ri-hospital-line me-1"></i>{{ $ruangan->nama_ruangan }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-muted small">Belum ada ruangan</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($data->status === 'Aktif')
                                                    <span class="badge bg-success">
                                                        <i class="ri-checkbox-circle-line me-1"></i>Aktif
                                                    </span>
                                                @elseif($data->status === 'Cuti')
                                                    <span class="badge bg-danger">
                                                        <i class="ri-close-circle-line me-1"></i>Cuti
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="ri-close-circle-line me-1"></i>Nonaktif
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="ri-more-2-fill"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('dokters.edit', $data->id_dokter) }}">
                                                                <i class="ri-pencil-fill me-2"></i>Edit
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('dokters.destroy', $data->id_dokter) }}" method="POST" class="delete-confirm">
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
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-5">
                                                <i class="ri-inbox-line ri-3x text-muted d-block mb-3"></i>
                                                <p class="text-muted mb-0">Belum ada data dokter</p>
                                                <a href="{{ route('dokters.create') }}" class="btn btn-primary btn-sm mt-3">
                                                    <i class="ri-add-circle-line me-1"></i>Tambah Dokter
                                                </a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
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

        .bg-soft-warning {
            background-color: rgba(255, 193, 7, 0.15) !important;
        }

        .bg-soft-info {
            background-color: rgba(13, 202, 240, 0.15) !important;
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
            let dataTable = null;
            
            function initializeDataTable() {
                const $table = $('#dokterTable');
                const isEmpty = $table.find('tbody tr td[colspan]').length > 0;
                
                if ($.fn.DataTable.isDataTable('#dokterTable')) {
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
                                emptyTable: "Belum ada data dokter"
                            },
                            columnDefs: [
                                { orderable: false, targets: [6, 8] }
                            ]
                        });

                        $('#searchBox').on('keyup', function() {
                            dataTable.search(this.value).draw();
                        });

                        $('#filterStatus').on('change', function() {
                            dataTable.column(7).search(this.value).draw();
                        });

                        $('#filterSpesialisasi').on('change', function() {
                            dataTable.column(3).search(this.value).draw();
                        });
                    } catch (error) {
                        console.log('DataTable initialization skipped - no data available');
                    }
                }
            }
            
            initializeDataTable();

            $(document).on('submit', '.delete-confirm', function(e) {
                e.preventDefault();
                var form = this;
                
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data dokter akan dihapus permanen!",
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

            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });
    </script>
@endpush