@extends('layouts.app')

@section('title', 'Asuransi')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Asuransi</li>
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
                                        <i class="ri-hospital-line fs-4 text-white"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1">Total Asuransi</p>
                                <h4 class="mb-0">{{ $asuransis->count() }}</h4>
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
                                <p class="text-muted mb-1">Asuransi Aktif</p>
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
                                        <i class="ri-file-list-line fs-4 text-white"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1">Kontrak Aktif</p>
                                <h4 class="mb-0">{{ $kontrakAktif }}</h4>
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
                                        <i class="ri-close-circle-line fs-4 text-white"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1">Asuransi Nonaktif</p>
                                <h4 class="mb-0">{{ $totalNonaktif }}</h4>
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
                                <i class="ri-list-check-2 me-2"></i>Daftar Asuransi
                            </h5>
                            <a class="btn btn-primary btn-sm" href="{{ route('asuransis.create') }}">
                                <i class="ri-add-circle-line me-1"></i>Tambah Asuransi
                            </a>
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div class="card-body border-bottom bg-light">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Filter Tipe</label>
                                <select class="form-select form-select-sm" id="filterTipe">
                                    <option value="">Semua Tipe</option>
                                    <option value="BPJS">BPJS</option>
                                    <option value="Pemerintah">Pemerintah</option>
                                    <option value="Swasta">Swasta</option>
                                    <option value="Corporate">Corporate</option>
                                    <option value="Asuransi Umum">Asuransi Umum</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Filter Status</label>
                                <select class="form-select form-select-sm" id="filterStatus">
                                    <option value="">Semua Status</option>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Nonaktif">Nonaktif</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Cari</label>
                                <input type="text" class="form-control form-control-sm" id="searchBox" placeholder="Cari nama asuransi...">
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle" id="asuransiTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Nama Asuransi</th>
                                        <th width="120">Tipe</th>
                                        <th width="150">No. Kontrak</th>
                                        <th width="150">Periode Kontrak</th>
                                        <th width="150">Kontak</th>
                                        <th width="100">Status</th>
                                        <th width="100" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($asuransis as $x => $data)
                                        <tr>
                                            <td class="text-center">{{ $x + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs me-2">
                                                        <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                            {{ substr($data->nama_asuransi, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <strong>{{ $data->nama_asuransi }}</strong>
                                                        @if($data->alamat)
                                                            <br><small class="text-muted"><i class="ri-map-pin-line"></i> {{ Str::limit($data->alamat, 40) }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($data->tipe)
                                                    @php
                                                        $badgeClass = match($data->tipe) {
                                                            'BPJS' => 'bg-primary',
                                                            'Pemerintah' => 'bg-success',
                                                            'Swasta' => 'bg-info',
                                                            'Corporate' => 'bg-purple',
                                                            'Asuransi Umum' => 'bg-warning text-dark',
                                                            default => 'bg-secondary'
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}">{{ $data->tipe }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($data->no_kontrak)
                                                    <small class="text-muted">
                                                        <i class="ri-file-text-line"></i> {{ $data->no_kontrak }}
                                                    </small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($data->tanggal_kontrak_mulai && $data->tanggal_kontrak_selesai)
                                                    <small class="text-muted">
                                                        <i class="ri-calendar-line"></i> 
                                                        {{ \Carbon\Carbon::parse($data->tanggal_kontrak_mulai)->format('d/m/Y') }}
                                                        <br>
                                                        <i class="ri-arrow-right-line"></i> 
                                                        {{ \Carbon\Carbon::parse($data->tanggal_kontrak_selesai)->format('d/m/Y') }}
                                                    </small>
                                                    @if(\Carbon\Carbon::parse($data->tanggal_kontrak_selesai)->isPast())
                                                        <br><span class="badge bg-danger badge-sm">Expired</span>
                                                    @elseif(\Carbon\Carbon::parse($data->tanggal_kontrak_selesai)->diffInDays(now()) <= 30)
                                                        <br><span class="badge bg-warning text-dark badge-sm">Segera Berakhir</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($data->no_telp || $data->email)
                                                    <small class="text-muted">
                                                        @if($data->no_telp)
                                                            <i class="ri-phone-line"></i> {{ $data->no_telp }}<br>
                                                        @endif
                                                        @if($data->email)
                                                            <i class="ri-mail-line"></i> {{ $data->email }}
                                                        @endif
                                                    </small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
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
                                                            <a class="dropdown-item" href="{{ route('asuransis.show', $data->id) }}">
                                                                <i class="ri-eye-line me-2"></i> Detail
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('asuransis.edit', $data->id) }}">
                                                                <i class="ri-pencil-fill me-2"></i>Edit
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('asuransis.destroy', $data->id) }}" method="POST" class="delete-confirm">
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
                                            <td colspan="8" class="text-center py-5">
                                                <i class="ri-inbox-line ri-3x text-muted d-block mb-3"></i>
                                                <p class="text-muted mb-0">Belum ada data asuransi</p>
                                                <a href="{{ route('asuransis.create') }}" class="btn btn-primary btn-sm mt-3">
                                                    <i class="ri-add-circle-line me-1"></i>Tambah Asuransi
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
            let dataTable = null;
            
            // Function to initialize DataTable
            function initializeDataTable() {
                const $table = $('#asuransiTable');
                
                // Check if table has data (no colspan in tbody)
                const isEmpty = $table.find('tbody tr td[colspan]').length > 0;
                
                // Destroy existing DataTable if exists
                if ($.fn.DataTable.isDataTable('#asuransiTable')) {
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
                                emptyTable: "Belum ada data asuransi"
                            },
                            columnDefs: [
                                { orderable: false, targets: [7] }
                            ]
                        });

                        // Custom search
                        $('#searchBox').on('keyup', function() {
                            dataTable.search(this.value).draw();
                        });

                        // Filter by type
                        $('#filterTipe').on('change', function() {
                            var selectedType = this.value;
                            dataTable.column(2).search(selectedType).draw();
                        });

                        // Filter by status
                        $('#filterStatus').on('change', function() {
                            var selectedStatus = this.value;
                            dataTable.column(6).search(selectedStatus).draw();
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
                    text: "Data asuransi akan dihapus permanen!",
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