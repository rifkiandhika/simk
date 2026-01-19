@extends('layouts.app')

@section('title', 'Stock Apotik')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Stock Apotik</li>
@endsection

@section('page-actions')
    <div class="d-flex flex-row gap-1 day-sorting">
        <button class="btn btn-sm btn-primary" data-filter="today">Today</button>
        <button class="btn btn-sm" data-filter="7d">7d</button>
        <button class="btn btn-sm" data-filter="2w">2w</button>
        <button class="btn btn-sm" data-filter="1m">1m</button>
        <button class="btn btn-sm" data-filter="3m">3m</button>
        <button class="btn btn-sm" data-filter="6m">6m</button>
        <button class="btn btn-sm" data-filter="1y">1y</button>
    </div>
@endsection

@section('content')
<div class="app-body">
    <div class="row">
        <div class="col-xl-12">
            {{-- Alert Messages --}}
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ri-error-warning-line me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ri-check-line me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Statistics Cards --}}
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm rounded-circle bg-primary bg-soft">
                                        <span class="avatar-title rounded-circle bg-primary bg-gradient">
                                            <i class="ri-box-3-line fs-4 text-white"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Total Transaksi</p>
                                    <h4 class="mb-0">{{ $stocks->total() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm rounded-circle bg-success bg-soft">
                                        <span class="avatar-title rounded-circle bg-success bg-gradient">
                                            <i class="ri-stack-line fs-4 text-white"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Total Item</p>
                                    <h4 class="mb-0">{{ number_format($totalItems) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm rounded-circle bg-warning bg-soft">
                                        <span class="avatar-title rounded-circle bg-warning bg-gradient">
                                            <i class="ri-building-line fs-4 text-white"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Supplier Aktif</p>
                                    <h4 class="mb-0">{{ $totalSuppliers }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm rounded-circle bg-danger bg-soft">
                                        <span class="avatar-title rounded-circle bg-danger bg-gradient">
                                            <i class="ri-arrow-left-right-line fs-4 text-white"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Total Retur</p>
                                    <h4 class="mb-0">{{ number_format($totalRetur) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Table Card --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="ri-file-list-3-line me-2"></i>Daftar Stock Apotik
                        </h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-success btn-sm" id="btnExport">
                                <i class="ri-file-excel-line me-1"></i>Export Excel
                            </button>
                            <a class="btn btn-primary btn-sm" href="{{ route('stock_apotiks.create') }}">
                                <i class="ri-add-circle-line me-1"></i>Tambah Stock
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Filter Section --}}
                    <form method="GET" action="{{ route('stock_apotiks.index') }}" id="filterForm">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Filter Gudang</label>
                                <select class="form-select form-select-sm" name="gudang_id" id="filterGudang">
                                    <option value="">Semua Gudang</option>
                                    @foreach($gudangs ?? [] as $gudang)
                                        <option value="{{ $gudang->id }}" {{ request('gudang_id') == $gudang->id ? 'selected' : '' }}>
                                            {{ $gudang->nama_gudang }} - {{ $gudang->supplier->nama_supplier ?? 'Tanpa Supplier' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Filter Tanggal</label>
                                <input type="date" class="form-control form-control-sm" name="date" id="filterDate" value="{{ request('date') }}">
                            </div>
                            <div class="col-md-5 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="ri-search-line me-1"></i>Filter
                                </button>
                                <a href="{{ route('stock_apotiks.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="ri-refresh-line me-1"></i>Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    {{-- DataTable --}}
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle" id="myTables">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Kode Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Gudang</th>
                                    <th>Supplier</th>
                                    <th width="100">No Batch</th>
                                    <th>Nama Barang</th>
                                    <th width="80">Satuan</th>
                                    <th width="100">Exp Date</th>
                                    <th width="100">Stock Apotik</th>
                                    <th width="80">Retur</th>
                                    <th width="100" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $rowNumber = $stocks->firstItem(); @endphp
                                @forelse($stocks as $stock)
                                    @php
                                        $detailCount = $stock->details->count();
                                        $isFirstDetail = true;
                                    @endphp
                                    @forelse($stock->details as $detail)
                                    <tr>
                                        @if($isFirstDetail)
                                        <td class="text-center" rowspan="{{ $detailCount }}">{{ $rowNumber++ }}</td>
                                        <td rowspan="{{ $detailCount }}">
                                            <strong class="text-primary">{{ $stock->kode_transaksi }}</strong>
                                        </td>
                                        <td rowspan="{{ $detailCount }}">
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($stock->tanggal_penerimaan)->format('d/m/Y H:i') }}
                                            </small>
                                        </td>
                                        <td rowspan="{{ $detailCount }}">
                                            <strong>{{ $stock->gudang->nama_gudang ?? '-' }}</strong>
                                        </td>
                                        <td rowspan="{{ $detailCount }}">
                                            <span class="badge bg-info">{{ $stock->gudang->supplier->nama_supplier ?? '-' }}</span>
                                        </td>
                                        @php $isFirstDetail = false; @endphp
                                        @endif
                                        
                                        <td>
                                            <span class="badge bg-secondary">{{ $detail->no_batch ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $detail->detailSupplier->nama ?? '-' }}</strong>
                                                @if(isset($detail->detailSupplier->merk) && $detail->detailSupplier->merk != '-')
                                                    <br><small class="text-muted">{{ $detail->detailSupplier->merk }}</small>
                                                @endif
                                                @if(isset($detail->detailSupplier->judul) && $detail->detailSupplier->judul != '-')
                                                    <br><small class="badge bg-light text-dark">{{ $detail->detailSupplier->judul }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center">{{ $detail->detailSupplier->satuan ?? '-' }}</td>
                                        <td>
                                            @php
                                                $expDate = $detail->tanggal_kadaluarsa ? \Carbon\Carbon::parse($detail->tanggal_kadaluarsa) : null;
                                                if ($expDate) {
                                                    $daysUntilExpiry = now()->diffInDays($expDate, false);
                                                    $badgeClass = 'bg-success';
                                                    if ($daysUntilExpiry < 0) {
                                                        $badgeClass = 'bg-danger';
                                                    } elseif ($daysUntilExpiry <= 30) {
                                                        $badgeClass = 'bg-warning';
                                                    }
                                                } else {
                                                    $badgeClass = 'bg-secondary';
                                                }
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">
                                                {{ $expDate ? $expDate->format('d/m/Y') : '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                @php
                                                    $stockApotik = $detail->stock_apotik ?? 0;
                                                    $minPersediaan = $detail->min_persediaan ?? 0;
                                                    $stockClass = 'text-success';
                                                    if ($stockApotik <= 0) {
                                                        $stockClass = 'text-danger';
                                                    } elseif ($stockApotik <= $minPersediaan) {
                                                        $stockClass = 'text-warning';
                                                    }
                                                @endphp
                                                <strong class="{{ $stockClass }}">{{ number_format($stockApotik) }}</strong>
                                                @if($minPersediaan > 0)
                                                    <br><small class="text-muted">Min: {{ $minPersediaan }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if($detail->retur > 0)
                                                <span class="badge bg-danger">{{ number_format($detail->retur) }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        
                                        @if($loop->first)
                                        <td class="text-center" rowspan="{{ $detailCount }}">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                    <i class="ri-more-2-fill"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                                    <li>
                                                        <a href="{{ route('stock_apotiks.show', $stock->id) }}" class="dropdown-item">
                                                            <i class="ri-eye-line me-2"></i>Detail
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('stock_apotiks.edit', $stock->id) }}" class="dropdown-item">
                                                            <i class="ri-pencil-fill me-2"></i>Edit
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('stock_apotiks.destroy', $stock->id) }}" method="POST" class="delete-confirm">
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
                                        @endif
                                    </tr>
                                    @empty
                                    {{-- <tr>
                                        <td colspan="12" class="text-center py-3 text-muted">
                                            <em>Data detail tidak tersedia untuk transaksi ini</em>
                                        </td>
                                    </tr> --}}
                                    @endforelse
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center py-5">
                                            <i class="ri-inbox-line ri-3x text-muted d-block mb-3"></i>
                                            <p class="text-muted mb-0">Belum ada data stock apotik</p>
                                            <a href="{{ route('stock_apotiks.create') }}" class="btn btn-primary btn-sm mt-3">
                                                <i class="ri-add-circle-line me-1"></i>Tambah Stock Pertama
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($stocks->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <p class="text-muted mb-0 small">
                                Menampilkan {{ $stocks->firstItem() }} - {{ $stocks->lastItem() }} dari {{ $stocks->total() }} data
                            </p>
                        </div>
                        <div>
                            {{ $stocks->links() }}
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
    .avatar-sm {
        width: 3rem;
        height: 3rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .bg-soft {
        opacity: 0.1;
    }
    .day-sorting button {
        transition: all 0.3s ease;
    }
    .day-sorting button:hover {
        transform: translateY(-2px);
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
     .table {
        border: 1px solid #ced4da !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable if has data
    const isEmpty = $('#myTables tbody tr td[colspan]').length > 0;
    if (!isEmpty) {
        $('#myTables').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[2, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            columnDefs: [
                { orderable: false, targets: [11] }
            ]
        });
    }
    
    // Day sorting buttons
    $('.day-sorting button').on('click', function() {
        $('.day-sorting button').removeClass('btn-primary').addClass('btn-outline-primary');
        $(this).removeClass('btn-outline-primary').addClass('btn-primary');
        
        const filter = $(this).data('filter');
        window.location.href = '{{ route("stock_apotiks.index") }}?filter=' + filter;
    });
    
    // Delete confirmation
    $(document).on('submit', '.delete-confirm', function(e) {
        e.preventDefault();
        const form = this;
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            html: 'Data stock apotik ini akan dihapus dan stock akan dikembalikan ke gudang!',
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
    
    // Export button
    $('#btnExport').on('click', function() {
        Swal.fire({
            title: 'Export Data',
            text: 'Fitur export akan segera tersedia',
            icon: 'info',
            confirmButtonText: 'OK'
        });
    });
    
    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
@endpush