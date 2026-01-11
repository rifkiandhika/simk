@extends('layouts.app')

@section('title', 'Daftar Tagihan')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Daftar Tagihan</li>
@endsection

@section('page-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('tagihans.dashboard') }}" class="btn btn-info btn-sm">
            <i class="ri-dashboard-line me-1"></i>Dashboard
        </a>
        <a href="{{ route('tagihans.create') }}" class="btn btn-success btn-sm">
            <i class="ri-add-line me-1"></i>Buat Tagihan Baru
        </a>
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
                                    <div class="avatar-sm rounded-circle bg-warning bg-soft">
                                        <span class="avatar-title rounded-circle bg-warning bg-gradient">
                                            <i class="ri-error-warning-line fs-4 text-white"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Belum Lunas</p>
                                    <h4 class="mb-0">{{ $summary['belum_lunas'] ?? 0 }}</h4>
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
                                    <div class="avatar-sm rounded-circle bg-info bg-soft">
                                        <span class="avatar-title rounded-circle bg-info bg-gradient">
                                            <i class="ri-time-line fs-4 text-white"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Cicilan</p>
                                    <h4 class="mb-0">{{ $summary['cicilan'] ?? 0 }}</h4>
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
                                            <i class="ri-checkbox-circle-line fs-4 text-white"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Lunas</p>
                                    <h4 class="mb-0">{{ $summary['lunas'] ?? 0 }}</h4>
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
                                    <div class="avatar-sm rounded-circle bg-primary bg-soft">
                                        <span class="avatar-title rounded-circle bg-primary bg-gradient">
                                            <i class="ri-money-dollar-circle-line fs-4 text-white"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Total Piutang</p>
                                    <h4 class="mb-0 small">Rp {{ number_format($summary['total_piutang'] ?? 0, 0, ',', '.') }}</h4>
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
                            <i class="ri-file-list-3-line me-2"></i>Data Tagihan
                        </h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-success btn-sm" id="btnExport">
                                <i class="ri-file-excel-line me-1"></i>Export Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Filter Section --}}
                    <form method="GET" action="{{ route('tagihans.index') }}" id="filterForm">
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Filter Tanggal Mulai</label>
                                <input type="date" class="form-control form-control-sm" name="start_date" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Tanggal Akhir</label>
                                <input type="date" class="form-control form-control-sm" name="end_date" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Status</label>
                                <select class="form-select form-select-sm" name="status">
                                    <option value="">Semua Status</option>
                                    <option value="BELUM_LUNAS" {{ request('status') == 'BELUM_LUNAS' ? 'selected' : '' }}>Belum Lunas</option>
                                    <option value="CICILAN" {{ request('status') == 'CICILAN' ? 'selected' : '' }}>Cicilan</option>
                                    <option value="LUNAS" {{ request('status') == 'LUNAS' ? 'selected' : '' }}>Lunas</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Jenis Tagihan</label>
                                <select class="form-select form-select-sm" name="jenis">
                                    <option value="">Semua Jenis</option>
                                    <option value="IGD" {{ request('jenis') == 'IGD' ? 'selected' : '' }}>IGD</option>
                                    <option value="RAWAT_JALAN" {{ request('jenis') == 'RAWAT_JALAN' ? 'selected' : '' }}>Rawat Jalan</option>
                                    <option value="RAWAT_INAP" {{ request('jenis') == 'RAWAT_INAP' ? 'selected' : '' }}>Rawat Inap</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Pencarian</label>
                                <input type="text" class="form-control form-control-sm" name="search" placeholder="No Tagihan / Nama" value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="ri-search-line me-1"></i>Filter
                                </button>
                                <a href="{{ route('tagihans.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="ri-refresh-line me-1"></i>Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    {{-- DataTable --}}
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle" id="myTables">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>No Tagihan</th>
                                    <th>Tanggal</th>
                                    <th>No RM</th>
                                    <th>Nama Pasien</th>
                                    <th>Jenis Tagihan</th>
                                    <th>Total Tagihan</th>
                                    <th>Sisa Tagihan</th>
                                    <th>Status</th>
                                    <th width="150" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $rowNumber = $tagihans->firstItem(); @endphp
                                @forelse($tagihans as $tagihan)
                                <tr>
                                    <td class="text-center">{{ $rowNumber++ }}</td>
                                    <td>
                                        <strong class="text-primary">{{ $tagihan->no_tagihan }}</strong>
                                        @if($tagihan->locked)
                                        <br><span class="badge bg-secondary"><i class="ri-lock-line"></i> Locked</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $tagihan->tanggal_tagihan->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $tagihan->pasien->no_rm }}</strong>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($tagihan->pasien && $tagihan->pasien->foto)
                                                <img src="{{ asset('storage/' . $tagihan->pasien->foto) }}" alt="{{ $tagihan->pasien->nama_lengkap }}" class="rounded-circle me-2" width="35" height="35">
                                            @else
                                                <div class="avatar-sm rounded-circle bg-secondary me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                    <span class="text-white fw-bold">{{ strtoupper(substr($tagihan->pasien->nama_lengkap, 0, 1)) }}</span>
                                                </div>
                                            @endif
                                            <div>
                                                <strong>{{ $tagihan->pasien->nama_lengkap }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = match($tagihan->jenis_tagihan) {
                                                'IGD' => 'bg-danger',
                                                'RAWAT_JALAN' => 'bg-info',
                                                'RAWAT_INAP' => 'bg-warning',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">
                                            {{ str_replace('_', ' ', $tagihan->jenis_tagihan) }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="text-primary">Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</strong>
                                    </td>
                                    <td>
                                        <strong class="{{ $tagihan->sisa_tagihan > 0 ? 'text-danger' : 'text-success' }}">
                                            Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}
                                        </strong>
                                        @if($tagihan->total_tagihan > 0)
                                        <br>
                                        <div class="progress mt-1" style="height: 5px;">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                 style="width: {{ ($tagihan->total_dibayar / $tagihan->total_tagihan) * 100 }}%">
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ number_format(($tagihan->total_dibayar / $tagihan->total_tagihan) * 100, 1) }}%</small>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusConfig = [
                                                'LUNAS' => ['class' => 'bg-success', 'icon' => 'ri-checkbox-circle-line', 'text' => 'Lunas'],
                                                'CICILAN' => ['class' => 'bg-info', 'icon' => 'ri-time-line', 'text' => 'Cicilan'],
                                                'BELUM_LUNAS' => ['class' => 'bg-warning', 'icon' => 'ri-error-warning-line', 'text' => 'Belum Lunas'],
                                            ];
                                            $config = $statusConfig[$tagihan->status] ?? $statusConfig['BELUM_LUNAS'];
                                        @endphp
                                        <span class="badge {{ $config['class'] }}">
                                            <i class="{{ $config['icon'] }} me-1"></i>{{ $config['text'] }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                <i class="ri-more-2-fill"></i>
                                            </button>
                                            <ul class="dropdown-menu shadow">
                                                <li>
                                                    <a href="{{ route('tagihans.show', $tagihan->id_tagihan) }}" class="dropdown-item">
                                                        <i class="ri-eye-line me-2"></i>Detail
                                                    </a>
                                                </li>
                                                
                                                @if(!$tagihan->locked && $tagihan->status != 'LUNAS')
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a href="{{ route('tagihans.payment', $tagihan->id_tagihan) }}" class="dropdown-item text-success">
                                                        <i class="ri-money-dollar-circle-line me-2"></i>Pembayaran
                                                    </a>
                                                </li>
                                                @endif

                                                @if(!$tagihan->locked)
                                                <li>
                                                    <a href="{{ route('tagihans.edit', $tagihan->id_tagihan) }}" class="dropdown-item text-warning">
                                                        <i class="ri-edit-line me-2"></i>Edit
                                                    </a>
                                                </li>
                                                @endif

                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a href="{{ route('tagihans.print', $tagihan->id_tagihan) }}" class="dropdown-item" target="_blank">
                                                        <i class="ri-printer-line me-2"></i>Print
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-5">
                                            <i class="ri-file-list-3-line ri-3x text-muted d-block mb-3"></i>
                                            <p class="text-muted mb-0">Belum ada data tagihan</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($tagihans->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <p class="text-muted mb-0 small">
                                Menampilkan {{ $tagihans->firstItem() }} - {{ $tagihans->lastItem() }} dari {{ $tagihans->total() }} data
                            </p>
                        </div>
                        <div>
                            {{ $tagihans->links() }}
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
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
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
            order: [[2, 'desc']], // Sort by tanggal
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            columnDefs: [
                { orderable: false, targets: [9] }
            ]
        });
    }

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