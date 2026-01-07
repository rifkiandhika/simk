@extends('layouts.app')

@section('title', 'Apotik')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Apotik</li>
@endsection

@section('page-actions')
    <div class="d-flex gap-2">
        <button class="btn btn-success btn-sm" id="btnResepLuar">
            <i class="ri-file-add-line me-1"></i>Resep Luar
        </button>
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
                                            <i class="ri-user-heart-line fs-4 text-white"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Total Pasien Hari Ini</p>
                                    <h4 class="mb-0">{{ $totalPasienHariIni ?? 0 }}</h4>
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
                                            <i class="ri-file-text-line fs-4 text-white"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Resep Selesai</p>
                                    <h4 class="mb-0">{{ $resepSelesai ?? 0 }}</h4>
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
                                            <i class="ri-time-line fs-4 text-white"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Menunggu Proses</p>
                                    <h4 class="mb-0">{{ $resepMenunggu ?? 0 }}</h4>
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
                                            <i class="ri-shopping-bag-line fs-4 text-white"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Resep Luar</p>
                                    <h4 class="mb-0">{{ $resepLuar ?? 0 }}</h4>
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
                            <i class="ri-user-line me-2"></i>Daftar Pasien Apotik
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
                    <form method="GET" action="{{ route('apotik.index') }}" id="filterForm">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Filter Tanggal</label>
                                <input type="date" class="form-control form-control-sm" name="date" id="filterDate" value="{{ request('date', date('Y-m-d')) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Status Resep</label>
                                <select class="form-select form-select-sm" name="status">
                                    <option value="">Semua Status</option>
                                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                    <option value="proses" {{ request('status') == 'proses' ? 'selected' : '' }}>Diproses</option>
                                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Pencarian</label>
                                <input type="text" class="form-control form-control-sm" name="search" placeholder="No RM / Nama Pasien" value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="ri-search-line me-1"></i>Filter
                                </button>
                                <a href="{{ route('apotik.index') }}" class="btn btn-sm btn-outline-secondary">
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
                                    <th>No RM</th>
                                    <th>Nama Pasien</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Umur</th>
                                    <th>Alamat</th>
                                    <th>Jenis Pembayaran</th>
                                    <th>No Telp</th>
                                    <th width="100" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $rowNumber = $pasiens->firstItem(); @endphp
                                @forelse($pasiens as $pasien)
                                <tr>
                                    <td class="text-center">{{ $rowNumber++ }}</td>
                                    <td>
                                        <strong class="text-primary">{{ $pasien->no_rm }}</strong>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($pasien->foto)
                                                <img src="{{ asset('storage/' . $pasien->foto) }}" alt="{{ $pasien->nama_lengkap }}" class="rounded-circle me-2" width="35" height="35">
                                            @else
                                                <div class="avatar-sm rounded-circle bg-secondary me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                    <span class="text-white fw-bold">{{ strtoupper(substr($pasien->nama_lengkap, 0, 1)) }}</span>
                                                </div>
                                            @endif
                                            <div>
                                                <strong>{{ $pasien->nama_lengkap }}</strong>
                                                @if($pasien->nik)
                                                    <br><small class="text-muted">NIK: {{ $pasien->nik }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($pasien->jenis_kelamin == 'L')
                                            <span class="badge bg-info">Laki-laki</span>
                                        @else
                                            <span class="badge bg-pink">Perempuan</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($pasien->tanggal_lahir)
                                            {{ \Carbon\Carbon::parse($pasien->tanggal_lahir)->age }} tahun
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ Str::limit($pasien->alamat, 40) ?? '-' }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = match($pasien->jenis_pembayaran) {
                                                'BPJS' => 'bg-success',
                                                'Asuransi' => 'bg-warning',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $pasien->jenis_pembayaran }}</span>
                                        @if($pasien->jenis_pembayaran == 'BPJS' && $pasien->no_bpjs)
                                            <br><small class="text-muted">{{ $pasien->no_bpjs }}</small>
                                        @elseif($pasien->jenis_pembayaran == 'Asuransi' && $pasien->asuransi)
                                            <br><small class="text-muted">{{ $pasien->asuransi->nama_asuransi }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $pasien->no_telp ?? '-' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                <i class="ri-more-2-fill"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                               <li>
                                                    <button type="button" class="dropdown-item btn-resep" 
                                                        data-pasien-id="{{ $pasien->id_pasien }}">
                                                        <i class="ri-file-text-line me-2"></i>Buat Resep
                                                    </button>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a href="#" class="dropdown-item">
                                                        <i class="ri-history-line me-2"></i>Riwayat Resep
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#" class="dropdown-item">
                                                        <i class="ri-eye-line me-2"></i>Detail Pasien
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <i class="ri-user-line ri-3x text-muted d-block mb-3"></i>
                                            <p class="text-muted mb-0">Belum ada data pasien untuk hari ini</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($pasiens->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <p class="text-muted mb-0 small">
                                Menampilkan {{ $pasiens->firstItem() }} - {{ $pasiens->lastItem() }} dari {{ $pasiens->total() }} data
                            </p>
                        </div>
                        <div>
                            {{ $pasiens->links() }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Include Modal Resep --}}
@include('apotik.partials.modal_resep')
{{-- @include('apotik.partials.modal_resep_luar') --}}

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
    .bg-pink {
        background-color: #e83e8c !important;
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
            order: [[0, 'asc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            columnDefs: [
                { orderable: false, targets: [8] }
            ]
        });
    }
    
    // Button Resep - Fetch data via API
    $(document).on('click', '.btn-resep', function() {
        const pasienId = $(this).data('pasien-id');
        const btnResep = $(this);
        
        // Disable button dan show loading
        btnResep.prop('disabled', true);
        btnResep.html('<span class="spinner-border spinner-border-sm me-1"></span>Loading...');
        
        // Show loading di modal jika sudah terbuka
        Swal.fire({
            title: 'Memuat Data Pasien...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Fetch data pasien via AJAX
        $.ajax({
            url: `/apotik/get-pasien/${pasienId}`,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const pasien = response.data;
                    
                    // Set data to modal
                    setDataPasien(
                        pasien.id,
                        pasien.nama_lengkap,
                        pasien.no_rm,
                        pasien.jenis_pembayaran
                    );
                    
                    // Close loading
                    Swal.close();
                    
                    // Switch to Resep Tab
                    $('#resep-tab').tab('show');
                    
                    // Show modal
                    $('#modalResep').modal('show');
                } else {
                    Swal.fire('Error', response.message || 'Gagal memuat data pasien', 'error');
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Terjadi kesalahan saat memuat data pasien';
                Swal.fire('Error', errorMsg, 'error');
            },
            complete: function() {
                // Re-enable button
                btnResep.prop('disabled', false);
                btnResep.html('<i class="ri-file-text-line me-2"></i>Buat Resep');
            }
        });
    });
    
    // Button Resep Luar - Open Modal Tab Resep Luar
    $('#btnResepLuar').on('click', function() {
        // Switch to Resep Luar Tab
        $('#resep-luar-tab').tab('show');
        
        // Show modal
        $('#modalResep').modal('show');
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

// Fungsi untuk set data pasien ke modal
function setDataPasien(pasienId, namaPasien, noRm, jenisPembayaran) {
    $('#pasien_id').val(pasienId);
    $('#pasien_nama_display').text(namaPasien);
    $('#pasien_no_rm_display').text(noRm);
    $('#jenis_pembayaran_pasien').val(jenisPembayaran);
    
    // Update badge jenis pembayaran
    // let badgeClass = 'bg-secondary';
    let badgeText = jenisPembayaran;
    
    if (jenisPembayaran) {
        const pembayaran = jenisPembayaran.toLowerCase();
        if (pembayaran === 'bpjs') {
            // badgeClass = 'bg-success';
        } else if (pembayaran === 'asuransi') {
            // badgeClass = 'bg-warning';
        } else if (pembayaran === 'umum') {
            // badgeClass = 'bg-info';
        }
    }
    
    $('#jenis_pembayaran_display')
        .removeClass('bg-info bg-success bg-warning bg-secondary')
        // .addClass(badgeClass)
        .text(badgeText || 'Umum');
}
</script>
@endpush