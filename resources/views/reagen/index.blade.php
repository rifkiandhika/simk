@extends('layouts.app')

@section('title', 'Reagensia')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Reagensia</li>
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
                                        <i class="ri-test-tube-line fs-4 text-white"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1">Total Reagensia</p>
                                <h4 class="mb-0">{{ $reagens->count() }}</h4>
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
                                <p class="text-muted mb-1">Reagensia Aktif</p>
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
                                <div class="avatar-sm rounded-circle bg-warning bg-soft">
                                    <span class="avatar-title rounded-circle bg-warning bg-gradient">
                                        <i class="ri-alert-line fs-4 text-white"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1">Dengan Bahaya</p>
                                <h4 class="mb-0">{{ $withHazard }}</h4>
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
                                        <i class="ri-price-tag-3-line fs-4 text-white"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1">Dengan Harga</p>
                                <h4 class="mb-0">{{ $withPrice }}</h4>
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
                                <i class="ri-list-check-2 me-2"></i>Daftar Reagensia
                            </h5>
                            <a class="btn btn-primary btn-sm" href="{{ route('reagens.create') }}">
                                <i class="ri-add-circle-line me-1"></i>Tambah Reagensia
                            </a>
                        </div>
                    </div>

                    <!-- Filter Section -->
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
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Filter Harga</label>
                                <select class="form-select form-select-sm" id="filterHarga">
                                    <option value="">Semua</option>
                                    <option value="Ada">Dengan Harga</option>
                                    <option value="Tidak">Tanpa Harga</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Cari</label>
                                <input type="text" class="form-control form-control-sm" id="searchBox" placeholder="Cari kode atau nama reagensia...">
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle" id="reagenTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Kode</th>
                                        <th>Nama Reagensia</th>
                                        <th width="120">Merk</th>
                                        <th width="100">Satuan</th>
                                        <th width="120">Harga Aktif</th>
                                        <th width="120">Stok Minimal</th>
                                        <th width="100">Status</th>
                                        <th width="100" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($reagens as $x => $data)
                                        <tr>
                                            <td class="text-center">{{ $x + 1 }}</td>
                                            <td>
                                                <strong class="text-primary">{{ $data->kode_reagensia }}</strong>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs me-2">
                                                        <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                            <i class="ri-test-tube-fill"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <strong>{{ $data->nama_reagensia }}</strong>
                                                        @if($data->no_katalog)
                                                            <br><small class="text-muted"><i class="ri-file-list-line"></i> {{ $data->no_katalog }}</small>
                                                        @endif
                                                        @if($data->bahaya_keselamatan)
                                                            <br><span class="badge badge-sm bg-danger"><i class="ri-alert-line"></i> Berbahaya</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($data->merk)
                                                    <span class="badge bg-info">{{ $data->merk }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary text-dark">{{ $data->volume_kemasan }} {{ $data->satuan }}</span>
                                            </td>
                                            <td>
                                                <small class="text-success fw-bold">
                                                    <i class="ri-money-dollar-circle-line"></i> 
                                                    Rp {{ ($data->harga_per_test) }}/test
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-warning text-dark">
                                                    {{ $data->stok_minimal }} {{ $data->satuan }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($data->status == 'Aktif')
                                                    <span class="badge bg-success">
                                                        <i class="ri-checkbox-circle-line"></i> Aktif
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="ri-close-circle-line"></i> Nonaktif
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
                                                            <a class="dropdown-item" href="{{ route('reagens.edit', $data->id) }}">
                                                                <i class="ri-pencil-fill me-2"></i>Edit
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('reagens.destroy', $data->id) }}" method="POST" class="delete-confirm">
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
                                                <i class="ri-test-tube-line ri-3x text-muted d-block mb-3"></i>
                                                <p class="text-muted mb-0">Belum ada data reagensia</p>
                                                <a href="{{ route('reagens.create') }}" class="btn btn-primary btn-sm mt-3">
                                                    <i class="ri-add-circle-line me-1"></i>Tambah Reagensia
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
                const $table = $('#reagenTable');
                
                // Check if table has data
                const isEmpty = $table.find('tbody tr td[colspan]').length > 0;
                
                // Destroy existing DataTable if exists
                if ($.fn.DataTable.isDataTable('#reagenTable')) {
                    $table.DataTable().destroy();
                }
                
                // Only initialize DataTable if table has data
                if (!isEmpty) {
                    try {
                        dataTable = $table.DataTable({
                            responsive: true,
                            pageLength: 10,
                            order: [[2, 'asc']],
                            dom: 'rtip',
                            language: {
                                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                                emptyTable: "Belum ada data reagensia"
                            },
                            columnDefs: [
                                { orderable: false, targets: [8] }
                            ]
                        });

                        // Custom search
                        $('#searchBox').on('keyup', function() {
                            dataTable.search(this.value).draw();
                        });

                        // Filter by status
                        $('#filterStatus').on('change', function() {
                            var selectedStatus = this.value;
                            dataTable.column(7).search(selectedStatus).draw();
                        });

                        // Filter by price
                        $('#filterHarga').on('change', function() {
                            var selectedFilter = this.value;
                            if (selectedFilter === 'Ada') {
                                dataTable.column(5).search('Rp', true, false).draw();
                            } else if (selectedFilter === 'Tidak') {
                                dataTable.column(5).search('Belum ada harga').draw();
                            } else {
                                dataTable.column(5).search('').draw();
                            }
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
                    text: "Data reagensia akan dihapus permanen!",
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