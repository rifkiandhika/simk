@extends('layouts.app')

@section('title', 'Gudang')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Gudang</li>
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
                                        <i class="ri-store-2-line fs-4 text-white"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1">Total Gudang</p>
                                <h4 class="mb-0">{{ $gudangs->count() }}</h4>
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
                                        <i class="ri-truck-line fs-4 text-white"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1">Total Supplier</p>
                                <h4 class="mb-0">{{ $gudangs->pluck('supplier')->unique()->count() }}</h4>
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
                                        <i class="ri-box-3-line fs-4 text-white"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1">Total Barang</p>
                                <h4 class="mb-0">{{ $gudangs->sum(function($g) { return $g->details->count(); }) }}</h4>
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
                                <p class="text-muted mb-1">Stok Rendah</p>
                                <h4 class="mb-0">0</h4>
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
                                <i class="ri-list-check-2 me-2"></i>Daftar Gudang
                            </h5>
                            <a class="btn btn-primary btn-sm" href="{{ route('gudangs.create') }}">
                                <i class="ri-add-circle-line me-1"></i>Tambah Barang
                            </a>
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div class="card-body border-bottom bg-light">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Filter Supplier</label>
                                <select class="form-select form-select-sm" id="filterSupplier">
                                    <option value="">Semua Supplier</option>
                                    @foreach($gudangs->pluck('supplier.nama_supplier')->unique()->filter() as $supplier)
                                        <option value="{{ $supplier }}">{{ $supplier }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-bold">Cari</label>
                                <input type="text" class="form-control form-control-sm" id="searchBox" placeholder="Cari supplier atau barang...">
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle" id="gudangTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Supplier</th>
                                        <th width="150">Jumlah Barang</th>
                                        <th width="150">Tanggal Dibuat</th>
                                        <th width="150" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($gudangs as $x => $data)
                                        <tr>
                                            <td class="text-center">{{ $x + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs me-2">
                                                        <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                            {{ substr($data->supplier->nama_supplier ?? 'N', 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <strong>{{ $data->supplier->nama_supplier ?? '-' }}</strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal{{ $data->id }}">
                                                    <i class="ri-eye-line me-1"></i>
                                                    Lihat ({{ $data->details->count() }})
                                                </button>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="ri-calendar-line"></i> {{ $data->created_at->format('d/m/Y H:i') }}
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <div>
                                                    <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="ri-more-2-fill"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('gudangs.edit', $data->id) }}">
                                                                <i class="ri-pencil-fill me-2"></i>Edit
                                                            </a>
                                                        </li>
                                                        {{-- <li>
                                                            <button type="button" class="dropdown-item" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#penerimaanModal"
                                                                    data-supplier="{{ $data->supplier_id }}">
                                                                <i class="ri-download-2-line me-2"></i>Penerimaan
                                                            </button>
                                                        </li> --}}
                                                        <li>
                                                            <button type="button" 
                                                                    class="dropdown-item"
                                                                    onclick="window.location.href='{{ route('gudang.history', $data->id) }}'">
                                                                <i class="ri-time-line me-2"></i>History
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('gudangs.destroy', $data->id) }}" method="POST" class="delete-confirm">
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
                                            <td colspan="5" class="text-center py-5">
                                                <i class="ri-inbox-line ri-3x text-muted d-block mb-3"></i>
                                                <p class="text-muted mb-0">Belum ada data gudang</p>
                                                <a href="{{ route('gudangs.create') }}" class="btn btn-primary btn-sm mt-3">
                                                    <i class="ri-add-circle-line me-1"></i>Tambah Barang
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

    <!-- Modal Penerimaan -->
    <div class="modal fade" id="penerimaanModal" tabindex="-1" aria-labelledby="penerimaanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="penerimaanModalLabel">
                        <i class="ri-download-2-line me-2"></i>Penerimaan Barang
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="penerimaanForm" method="POST">
                        @csrf
                        <input type="hidden" name="supplier_id" id="penerimaan_supplier_id">
                        
                        <!-- Pilih Produk -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="ri-checkbox-multiple-line me-2"></i>Pilih Barang
                                    <span class="badge bg-primary ms-2" id="penerimaanSelectedCount">0</span>
                                </h6>
                            </div>
                            <div class="card-body" style="max-height: 400px; overflow-y: auto;" id="penerimaan-products-modal">
                                <div class="text-center text-muted py-5">
                                    <i class="ri-box-3-line ri-2x"></i>
                                    <div class="mt-2">Pilih supplier untuk memuat produk</div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabel Input Jumlah -->
                        <div id="jumlah-section" style="display: none;">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="ri-edit-box-line me-2"></i>Input Jumlah & Batch
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered" id="tablePenerimaan">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Nama Barang</th>
                                                    <th width="120">Jenis</th>
                                                    <th width="120">Exp Date</th>
                                                    <th width="200">No. Batch</th>
                                                    <th width="120">Jumlah</th>
                                                    <th width="80" class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Tutup
                    </button>
                    <button type="button" class="btn btn-primary" id="btnProsesPenerimaan" disabled>
                        <i class="ri-check-line me-1"></i>Proses Penerimaan
                    </button>
                </div>
            </div>
        </div>
    </div>
    @foreach($gudangs as $data)
        <!-- Modal Detail -->
        <div class="modal fade" id="detailModal{{ $data->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $data->id }}" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">
                            <i class="ri-file-list-line me-2"></i>Detail Barang - {{ $data->supplier->nama_supplier ?? 'N/A' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped w-100" id="detailTable-{{ $data->id }}">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Nama Barang</th>
                                        <th width="100">Jenis</th>
                                        <th width="80">Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- DataTables akan mengisi bagian ini -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="ri-close-line me-1"></i>Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
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
        
        .cursor {
            cursor: pointer;
        }

        .list-group-item {
            transition: background-color 0.2s;
        }

        .list-group-item:hover {
            background-color: #f8f9fa;
        }

        .accordion-button:not(.collapsed) {
            background-color: #e7f1ff;
            color: #0d6efd;
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
        const $table = $('#gudangTable');
        
        // Check if table has data
        const isEmpty = $table.find('tbody tr td[colspan]').length > 0;
        
        // Destroy existing DataTable if exists
        if ($.fn.DataTable.isDataTable('#gudangTable')) {
            $table.DataTable().destroy();
        }
        
        // Only initialize DataTable if table has data
        if (!isEmpty) {
            try {
                dataTable = $table.DataTable({
                    responsive: true,
                    pageLength: 10,
                    order: [[0, 'asc']],
                    dom: 'rtip',
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                        emptyTable: "Belum ada data gudang"
                    },
                    columnDefs: [
                        { orderable: false, targets: [4] }
                    ]
                });

                // Custom search
                $('#searchBox').on('keyup', function() {
                    dataTable.search(this.value).draw();
                });

                // Filter by supplier
                $('#filterSupplier').on('change', function() {
                    dataTable.column(1).search(this.value).draw();
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
            text: "Data gudang akan dihapus permanen!",
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

    // ========== Detail Modal Script ===============
    // Store initialized tables
    let detailTables = {};

    $(document).on('show.bs.modal', '[id^="detailModal"]', function () {
        const modal = $(this);
        const gudangId = modal.attr('id').replace('detailModal', '');
        const tableId = `detailTable-${gudangId}`;
        const $table = $(`#${tableId}`);

        console.log('Modal opened for gudang:', gudangId);

        // Destroy existing DataTable if already initialized
        if (detailTables[tableId]) {
            try {
                detailTables[tableId].destroy();
                delete detailTables[tableId];
            } catch(e) {
                console.log('Error destroying table:', e);
            }
        }

        // Initialize new DataTable
        try {
            detailTables[tableId] = $table.DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: `/gudang/${gudangId}/details/data`,
                    type: 'GET',
                    error: function(xhr, error, code) {
                        console.error('Ajax error:', error, code);
                        console.error('Response:', xhr.responseText);
                    }
                },
                columns: [
                    { 
                        data: 'DT_RowIndex', 
                        name: 'DT_RowIndex', 
                        orderable: false, 
                        searchable: false,
                        width: '50px',
                        className: 'text-center'
                    },
                    { 
                        data: 'barang_nama', 
                        name: 'barang_nama',
                        render: function(data, type, row) {
                            return data || '-';
                        }
                    },
                    { 
                        data: 'barang_type', 
                        name: 'barang_type',
                        width: '100px',
                        className: 'text-center'
                    },
                    { 
                        data: 'stock_gudang', 
                        name: 'stock_gudang',
                        width: '80px',
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `<span>${data}</span>`;
                        }
                    },
                ],
                pageLength: 10,
                lengthChange: false,
                responsive: true,
                language: { 
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                    processing: '<i class="ri-loader-4-line ri-spin ri-2x"></i><br>Memuat data...',
                    emptyTable: 'Tidak ada data barang',
                    zeroRecords: 'Tidak ditemukan data yang sesuai'
                },
                order: [[0, 'asc']],
                drawCallback: function(settings) {
                    console.log('Table drawn with', settings.json ? settings.json.recordsTotal : 0, 'records');
                }
            });

            console.log('DataTable initialized for', tableId);
        } catch(e) {
            console.error('Error initializing DataTable:', e);
        }
    });

    // Clean up when modal is hidden
    $(document).on('hidden.bs.modal', '[id^="detailModal"]', function () {
        const modal = $(this);
        const gudangId = modal.attr('id').replace('detailModal', '');
        const tableId = `detailTable-${gudangId}`;

        if (detailTables[tableId]) {
            try {
                detailTables[tableId].destroy();
                delete detailTables[tableId];
                console.log('Table destroyed:', tableId);
            } catch(e) {
                console.log('Error destroying table on close:', e);
            }
        }
    });
});
</script>
@endpush