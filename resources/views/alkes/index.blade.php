@extends('layouts.app')

@section('title', 'Alat Kesehatan (Alkes)')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Alat Kesehatan</li>
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
            <div class="col-xl-2 col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-primary bg-soft">
                                    <span class="avatar-title rounded-circle bg-primary bg-gradient">
                                        <i class="ri-heartbeat-line fs-4 text-white"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1 small">Total</p>
                                <h4 class="mb-0">{{ $alkes->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 mb-3">
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
                                <p class="text-muted mb-1 small">Aktif</p>
                                <h4 class="mb-0">{{ $totalAktif }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-danger bg-soft">
                                    <span class="avatar-title rounded-circle bg-danger bg-gradient">
                                        <i class="ri-tools-line fs-4 text-white"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1 small">Rusak</p>
                                <h4 class="mb-0">{{ $totalRusak }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-warning bg-soft">
                                    <span class="avatar-title rounded-circle bg-warning bg-gradient">
                                        <i class="ri-alert-line fs-4 text-white"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1 small">Stok Rendah</p>
                                <h4 class="mb-0">{{ $stokRendah }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-info bg-soft">
                                    <span class="avatar-title rounded-circle bg-info bg-gradient">
                                        <i class="ri-settings-3-line fs-4 text-white"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1 small">Perlu Kalibrasi</p>
                                <h4 class="mb-0">{{ $needKalibrasi }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-purple bg-soft">
                                    <span class="avatar-title rounded-circle bg-purple bg-gradient">
                                        <i class="ri-hospital-line fs-4 text-white"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1 small">Alat Medis</p>
                                <h4 class="mb-0">{{ $alatMedis }}</h4>
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
                                <i class="ri-list-check-2 me-2"></i>Daftar Alat Kesehatan
                            </h5>
                            <a class="btn btn-primary btn-sm" href="{{ route('alkes.create') }}">
                                <i class="ri-add-circle-line me-1"></i>Tambah Alkes
                            </a>
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div class="card-body border-bottom bg-light">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Kategori</label>
                                <select class="form-select form-select-sm" id="filterKategori">
                                    <option value="">Semua</option>
                                    <option value="Alat Medis">Alat Medis</option>
                                    <option value="Alat Lab">Alat Lab</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Status</label>
                                <select class="form-select form-select-sm" id="filterStatus">
                                    <option value="">Semua</option>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Nonaktif">Nonaktif</option>
                                    <option value="Rusak">Rusak</option>
                                    <option value="Dalam Perbaikan">Dalam Perbaikan</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Kondisi</label>
                                <select class="form-select form-select-sm" id="filterKondisi">
                                    <option value="">Semua</option>
                                    <option value="Baik">Baik</option>
                                    <option value="Rusak">Rusak</option>
                                    <option value="Perlu Maintenance">Perlu Maintenance</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Cari</label>
                                <input type="text" class="form-control form-control-sm" id="searchBox" placeholder="Cari kode atau nama alkes...">
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle" id="alkesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">No</th>
                                        <th width="100">Kode</th>
                                        <th>Nama Alkes</th>
                                        <th width="100">Kategori</th>
                                        <th width="80">Stok</th>
                                        <th width="120">Kondisi</th>
                                        <th width="100">Status</th>
                                        <th width="120">Kalibrasi</th>
                                        <th width="100" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($alkes as $x => $data)
                                        <tr>
                                            <td class="text-center">{{ $x + 1 }}</td>
                                            <td>
                                                <code class="text-primary fw-bold">{{ $data->kode_alkes }}</code>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs me-2">
                                                        <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                            {{ substr($data->nama_alkes, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <strong>{{ $data->nama_alkes }}</strong>
                                                        @if($data->merk)
                                                            <br><small class="text-muted">{{ $data->merk }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $badgeKategori = $data->kategori == 'Alat Medis' ? 'bg-purple' : 'bg-info';
                                                    $iconKategori = $data->kategori == 'Alat Medis' ? 'ri-hospital-line' : 'ri-flask-line';
                                                @endphp
                                                <span class="badge {{ $badgeKategori }}">
                                                    <i class="{{ $iconKategori }}"></i> {{ $data->kategori }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <strong>{{ $data->jumlah_stok }}</strong>
                                                    <small class="text-muted d-block">/ {{ $data->stok_minimal }} min</small>
                                                    @if($data->jumlah_stok <= $data->stok_minimal)
                                                        <span class="badge bg-warning badge-sm mt-1">Rendah</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $badgeKondisi = match($data->kondisi) {
                                                        'Baik' => 'bg-success',
                                                        'Rusak' => 'bg-danger',
                                                        'Perlu Maintenance' => 'bg-warning text-dark',
                                                        default => 'bg-secondary'
                                                    };
                                                    $iconKondisi = match($data->kondisi) {
                                                        'Baik' => 'ri-checkbox-circle-line',
                                                        'Rusak' => 'ri-close-circle-line',
                                                        'Perlu Maintenance' => 'ri-tools-line',
                                                        default => 'ri-question-line'
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeKondisi }}">
                                                    <i class="{{ $iconKondisi }}"></i> {{ $data->kondisi }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $badgeStatus = match($data->status) {
                                                        'Aktif' => 'bg-success',
                                                        'Nonaktif' => 'bg-secondary',
                                                        'Rusak' => 'bg-danger',
                                                        'Dalam Perbaikan' => 'bg-warning text-dark',
                                                        default => 'bg-secondary'
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeStatus }}">
                                                    {{ $data->status }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($data->tanggal_kalibrasi_berikutnya)
                                                    <small class="text-muted">
                                                        {{ $data->tanggal_kalibrasi_berikutnya->format('d/m/Y') }}
                                                    </small>
                                                    @if($data->tanggal_kalibrasi_berikutnya->isPast())
                                                        <br><span class="badge bg-danger badge-sm">Lewat Jadwal</span>
                                                    @elseif($data->tanggal_kalibrasi_berikutnya->diffInDays(now()) <= 30)
                                                        <br><span class="badge bg-warning text-dark badge-sm">Segera</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="ri-more-2-fill"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow">
                                                        {{-- <li>
                                                            <a class="dropdown-item" href="{{ route('alkes.show', $data->id) }}">
                                                                <i class="ri-eye-line me-2"></i> Detail
                                                            </a>
                                                        </li> --}}
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('alkes.edit', $data->id) }}">
                                                                <i class="ri-pencil-fill me-2"></i>Edit
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('alkes.destroy', $data->id) }}" method="POST" class="delete-confirm">
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
                                                <p class="text-muted mb-0">Belum ada data alat kesehatan</p>
                                                <a href="{{ route('alkes.create') }}" class="btn btn-primary btn-sm mt-3">
                                                    <i class="ri-add-circle-line me-1"></i>Tambah Alkes
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
        
        .badge-sm {
            font-size: 0.75rem;
            padding: 0.25em 0.6em;
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
            
            // Function to initialize DataTable
            function initializeDataTable() {
                const $table = $('#alkesTable');
                
                // Check if table has data (no colspan in tbody)
                const isEmpty = $table.find('tbody tr td[colspan]').length > 0;
                
                // Destroy existing DataTable if exists
                if ($.fn.DataTable.isDataTable('#alkesTable')) {
                    $table.DataTable().destroy();
                }
                
                // Only initialize DataTable if table has data
                if (!isEmpty) {
                    try {
                        dataTable = $table.DataTable({
                            responsive: true,
                            pageLength: 10,
                            order: [[1, 'asc']],
                            dom: 'rtip',
                            language: {
                                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                                emptyTable: "Belum ada data alat kesehatan"
                            },
                            columnDefs: [
                                { orderable: false, targets: [8] }
                            ]
                        });

                        // Custom search
                        $('#searchBox').on('keyup', function() {
                            dataTable.search(this.value).draw();
                        });

                        // Filter by kategori
                        $('#filterKategori').on('change', function() {
                            dataTable.column(3).search(this.value).draw();
                        });

                        // Filter by status
                        $('#filterStatus').on('change', function() {
                            dataTable.column(6).search(this.value).draw();
                        });

                        // Filter by kondisi
                        $('#filterKondisi').on('change', function() {
                            dataTable.column(5).search(this.value).draw();
                        });
                    } catch (error) {
                        console.log('DataTable initialization skipped - no data available');
                    }
                }
            }
            
            // Initialize DataTable on page load
            initializeDataTable();

            // Confirm delete with SweetAlert
            $(document).on('submit', '.delete-confirm', function(e) {
                e.preventDefault();
                var form = this;
                
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data alat kesehatan akan dihapus permanen!",
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

            // Auto dismiss alerts
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });
    </script>
@endpush