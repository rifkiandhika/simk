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
                                            <i class="ri-file-list-3-line fs-4 text-white"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Total Resep Hari Ini</p>
                                    <h4 class="mb-0">{{ $totalResepHariIni ?? 0 }}</h4>
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
                                    <p class="text-muted mb-1">Menunggu Verifikasi</p>
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
                                            <i class="ri-loader-4-line fs-4 text-white"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Sedang Diproses</p>
                                    <h4 class="mb-0">{{ $resepProses ?? 0 }}</h4>
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
                                    <p class="text-muted mb-1">Selesai</p>
                                    <h4 class="mb-0">{{ $resepSelesai ?? 0 }}</h4>
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
                            <i class="ri-file-list-3-line me-2"></i>Daftar Resep
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
                                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                                    <option value="proses" {{ request('status') == 'proses' ? 'selected' : '' }}>Sedang Diproses</option>
                                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Dibatalkan</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Pencarian</label>
                                <input type="text" class="form-control form-control-sm" name="search" placeholder="No Resep / No RM / Nama" value="{{ request('search') }}">
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
                                    <th>No Resep</th>
                                    <th>Tanggal</th>
                                    <th>No RM</th>
                                    <th>Nama Pasien</th>
                                    <th>Jenis Pembayaran</th>
                                    <th>Status Obat</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th width="150" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $rowNumber = $reseps->firstItem(); @endphp
                                @forelse($reseps as $resep)
                                <tr>
                                    <td class="text-center">{{ $rowNumber++ }}</td>
                                    <td>
                                        <strong class="text-primary">{{ $resep->no_resep }}</strong>
                                    </td>
                                    <td>
                                        <small>{{ \Carbon\Carbon::parse($resep->tanggal_resep)->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $resep->pasien->no_rm ?? '-' }}</strong>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($resep->pasien && $resep->pasien->foto)
                                                <img src="{{ asset('storage/' . $resep->pasien->foto) }}" alt="{{ $resep->pasien->nama_lengkap }}" class="rounded-circle me-2" width="35" height="35">
                                            @else
                                                <div class="avatar-sm rounded-circle bg-secondary me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                    <span class="text-white fw-bold">{{ $resep->pasien ? strtoupper(substr($resep->pasien->nama_lengkap, 0, 1)) : '-' }}</span>
                                                </div>
                                            @endif
                                            <div>
                                                <strong>{{ $resep->pasien->nama_lengkap ?? '-' }}</strong>
                                                @if($resep->pasien && $resep->pasien->tanggal_lahir)
                                                    <br><small class="text-muted">{{ \Carbon\Carbon::parse($resep->pasien->tanggal_lahir)->age }} tahun</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($resep->pasien)
                                            @php
                                                $badgeClass = match($resep->pasien->jenis_pembayaran) {
                                                    'BPJS' => 'bg-success',
                                                    'Asuransi' => 'bg-info',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $resep->pasien->jenis_pembayaran }}</span>
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $resep->status_obat == 'Racik' ? 'bg-warning' : 'bg-info' }}">
                                            {{ $resep->status_obat }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="text-success">Rp {{ number_format($resep->total_harga, 0, ',', '.') }}</strong>
                                    </td>
                                    <td>
                                        @php
                                            $statusConfig = [
                                                'menunggu' => ['class' => 'bg-warning', 'icon' => 'ri-time-line', 'text' => 'Menunggu'],
                                                'proses' => ['class' => 'bg-info', 'icon' => 'ri-loader-4-line', 'text' => 'Diproses'],
                                                'selesai' => ['class' => 'bg-success', 'icon' => 'ri-checkbox-circle-line', 'text' => 'Selesai'],
                                                'batal' => ['class' => 'bg-danger', 'icon' => 'ri-close-circle-line', 'text' => 'Dibatalkan'],
                                            ];
                                            $config = $statusConfig[$resep->status] ?? $statusConfig['menunggu'];
                                        @endphp
                                        <span class="badge {{ $config['class'] }}">
                                            <i class="{{ $config['icon'] }} me-1"></i>{{ $config['text'] }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                <i class="ri-more-2-fill"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                                <li>
                                                    <button type="button" class="dropdown-item btn-detail-resep" 
                                                        data-resep-id="{{ $resep->id }}">
                                                        <i class="ri-eye-line me-2"></i>Detail Resep
                                                    </button>
                                                </li>
                                                
                                                @if($resep->status == 'menunggu')
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <button type="button" class="dropdown-item text-success btn-verifikasi" 
                                                        data-resep-id="{{ $resep->id }}">
                                                        <i class="ri-checkbox-line me-2"></i>Verifikasi
                                                    </button>
                                                </li>
                                                <li>
                                                    <button type="button" class="dropdown-item text-danger btn-tolak" 
                                                        data-resep-id="{{ $resep->id }}">
                                                        <i class="ri-close-line me-2"></i>Tolak
                                                    </button>
                                                </li>
                                                @endif

                                                @if($resep->status == 'proses')
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <button type="button" class="dropdown-item text-primary btn-serahkan" 
                                                        data-resep-id="{{ $resep->id }}">
                                                        <i class="ri-hand-heart-line me-2"></i>Serahkan Obat
                                                    </button>
                                                </li>
                                                @endif

                                                @if($resep->status == 'selesai')
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a href="{{ route('apotik.print', $resep->id) }}" 
                                                       class="dropdown-item" target="_blank">
                                                        <i class="ri-printer-line me-2"></i>Print Bukti
                                                    </a>
                                                </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-5">
                                            <i class="ri-file-list-3-line ri-3x text-muted d-block mb-3"></i>
                                            <p class="text-muted mb-0">Belum ada data resep</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($reseps->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <p class="text-muted mb-0 small">
                                Menampilkan {{ $reseps->firstItem() }} - {{ $reseps->lastItem() }} dari {{ $reseps->total() }} data
                            </p>
                        </div>
                        <div>
                            {{ $reseps->links() }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Include Modal Detail & Verifikasi --}}
@include('apotik.partials.modal_detail')
@include('apotik.partials.modal_tolak')

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
    
    // Button Detail Resep
    $(document).on('click', '.btn-detail-resep', function() {
        const resepId = $(this).data('resep-id');
        loadDetailResep(resepId);
    });

    // Button Verifikasi
    $(document).on('click', '.btn-verifikasi', function() {
        const resepId = $(this).data('resep-id');
        verifikasiResep(resepId);
    });

    // Button Serahkan
    $(document).on('click', '.btn-serahkan', function() {
        const resepId = $(this).data('resep-id');
        serahkanObat(resepId);
    });

    // Button Tolak
    $(document).on('click', '.btn-tolak', function() {
        const resepId = $(this).data('resep-id');
        $('#resep_id_tolak').val(resepId);
        $('#modalTolak').modal('show');
    });

    // Submit Tolak
    $('#btnSubmitTolak').on('click', function() {
        const resepId = $('#resep_id_tolak').val();
        const reason = $('#rejection_reason').val();
        
        if (!reason) {
            Swal.fire('Error', 'Alasan penolakan harus diisi', 'error');
            return;
        }
        
        tolakResep(resepId, reason);
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

    // Button Resep Luar
    $('#btnResepLuar').on('click', function() {
        Swal.fire({
            title: 'Resep Luar',
            text: 'Fitur resep luar akan segera tersedia',
            icon: 'info',
            confirmButtonText: 'OK'
        });
    });
    
    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});

// Load detail resep
function loadDetailResep(resepId) {
    Swal.fire({
        title: 'Memuat Data...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: `/apotik/detail/${resepId}`,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                populateDetailModal(response.data);
                Swal.close();
                $('#modalDetail').modal('show');
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function(xhr) {
            Swal.fire('Error', 'Gagal memuat detail resep', 'error');
        }
    });
}

// Populate modal detail
function populateDetailModal(data) {
    const resep = data.resep;
    const obatDetails = data.obat_details;
    const allStockAvailable = data.all_stock_available;

    // Set info resep
    $('#detail_no_resep').text(resep.no_resep);
    $('#detail_tanggal').text(formatDateTime(resep.tanggal_resep));
    $('#detail_status').html(getStatusBadge(resep.status));
    
    // Set info pasien
    $('#detail_no_rm').text(resep.pasien?.no_rm || '-');
    $('#detail_nama_pasien').text(resep.pasien?.nama_lengkap || '-');
    $('#detail_jenis_pembayaran').text(resep.pasien?.jenis_pembayaran || '-');

    // Set info obat
    $('#detail_status_obat').text(resep.status_obat);
    
    if (resep.status_obat === 'Racik') {
        $('#detail_racik_info').show();
        $('#detail_jenis_racikan').text(resep.jenis_racikan || '-');
        $('#detail_hasil_racikan').text(resep.hasil_racikan || '-');
        $('#detail_dosis_signa').text(resep.dosis_signa || '-');
        $('#detail_aturan_pakai').text(resep.aturan_pakai || '-');
    } else {
        $('#detail_racik_info').hide();
    }

    // ===== INFORMASI NON RACIK =====
    if (resep.status_obat === 'Non Racik') {
        $('#detail_racik_info').hide();
        $('#detail_non_racik_info').show();

        let html = '';

        obatDetails.forEach((obat, index) => {
            html += `
                <div class="border rounded p-3 mb-2">
                    <strong class="text-success">
                        ${index + 1}. ${obat.nama_obat}
                    </strong>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Dosis</small>
                            <strong>${resep.dosis_signa ?? '-'}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Aturan Pakai</small>
                            <strong>${resep.aturan_pakai ?? '-'}</strong>
                        </div>
                    </div>
                </div>
            `;
        });

        $('#detail_non_racik_body').html(html);
    } else {
        $('#detail_non_racik_info').hide();
    }

    // Set daftar obat
    let obatHtml = '';
    obatDetails.forEach((obat, index) => {
        const stockClass = obat.stock_cukup ? 'text-success' : 'text-danger';
        const stockIcon = obat.stock_cukup ? 'ri-checkbox-circle-line' : 'ri-close-circle-line';
        
        obatHtml += `
            <tr>
                <td>${index + 1}</td>
                <td>
                    <strong>${obat.nama_obat}</strong><br>
                    <small class="text-muted">${obat.judul || ''}</small>
                </td>
                <td>${obat.satuan}</td>
                <td class="text-center">${obat.jumlah_diminta}</td>
                <td class="text-center ${stockClass}">
                    <i class="${stockIcon}"></i> ${obat.stock_tersedia}
                </td>
                <td class="text-end">Rp ${formatRupiah(obat.harga_satuan)}</td>
                <td class="text-end"><strong>Rp ${formatRupiah(obat.subtotal)}</strong></td>
            </tr>
        `;
    });
    $('#tbody_detail_obat').html(obatHtml);

    // ✅ HITUNG DAN TAMPILKAN DISKON & PAJAK
    let totalObat = 0;
    obatDetails.forEach(obat => {
        totalObat += parseFloat(obat.subtotal);
    });

    // Hitung diskon
    let nilaiDiskon = 0;
    if (resep.diskon > 0) {
        if (resep.diskon_type === 'percent') {
            nilaiDiskon = (totalObat * resep.diskon) / 100;
        } else {
            nilaiDiskon = parseFloat(resep.diskon);
        }
    }

    // Subtotal setelah diskon
    const subtotalSetelahDiskon = totalObat - nilaiDiskon;
    
    // Tambah jasa racik
    const jasaRacik = parseFloat(resep.jasa_racik) || 0;
    const subtotalDenganJasaRacik = subtotalSetelahDiskon + jasaRacik;

    // Hitung pajak
    let nilaiPajak = 0;
    if (resep.pajak > 0) {
        if (resep.pajak_type === 'percent') {
            nilaiPajak = (subtotalDenganJasaRacik * resep.pajak) / 100;
        } else {
            nilaiPajak = parseFloat(resep.pajak);
        }
    }

    // Total akhir
    const totalAkhir = subtotalDenganJasaRacik + nilaiPajak;

    // ✅ SET NILAI KE MODAL
    $('#detail_total_obat').text('Rp ' + formatRupiah(totalObat));
    
    // Diskon
    if (resep.diskon > 0) {
        const diskonLabel = resep.diskon_type === 'percent' ? `${resep.diskon}%` : 'IDR';
        $('#detail_diskon_label').text(`Diskon (${diskonLabel}):`);
        $('#detail_diskon').text('- Rp ' + formatRupiah(nilaiDiskon));
        $('#detail_diskon_row').show();
    } else {
        $('#detail_diskon_row').hide();
    }

    // Jasa Racik
    if (jasaRacik > 0) {
        $('#detail_jasa_racik').text('Rp ' + formatRupiah(jasaRacik));
        $('#detail_jasa_racik_row').show();
    } else {
        $('#detail_jasa_racik_row').hide();
    }

    // Pajak
    if (resep.pajak > 0) {
        const pajakLabel = resep.pajak_type === 'percent' ? `${resep.pajak}%` : 'IDR';
        $('#detail_pajak_label').text(`Pajak (${pajakLabel}):`);
        $('#detail_pajak').text('Rp ' + formatRupiah(nilaiPajak));
        $('#detail_pajak_row').show();
    } else {
        $('#detail_pajak_row').hide();
    }

    $('#detail_total').text('Rp ' + formatRupiah(totalAkhir));

    // Set keterangan
    $('#detail_keterangan').text(resep.keterangan || '-');

    // Show/hide action buttons
    $('#btn_verifikasi_modal').hide();
    $('#btn_serahkan_modal').hide();
    $('#stock_warning').hide();

    if (resep.status === 'menunggu') {
        if (allStockAvailable) {
            $('#btn_verifikasi_modal').show().attr('data-resep-id', resep.id);
        } else {
            $('#stock_warning').show();
        }
    } else if (resep.status === 'proses') {
        $('#btn_serahkan_modal').show().attr('data-resep-id', resep.id);
    }
}

// Verifikasi resep
function verifikasiResep(resepId) {
    Swal.fire({
        title: 'Verifikasi Resep',
        text: 'Apakah Anda yakin ingin memverifikasi resep ini? Stock akan dicek terlebih dahulu.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Verifikasi',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/apotik/verifikasi/${resepId}`,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    Swal.fire({
                        title: 'Memverifikasi...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON?.message || 'Gagal memverifikasi resep';
                    Swal.fire('Error', errorMsg, 'error');
                }
            });
        }
    });
}

// Serahkan obat
function serahkanObat(resepId) {
    Swal.fire({
        title: 'Serahkan Obat',
        html: `
            <div class="text-center">
                <p>Apakah Anda yakin ingin menyerahkan obat?</p>
                <div class="alert alert-warning mb-2">
                    <small><i class="ri-information-line me-1"></i>Stock akan otomatis berkurang</small>
                </div>
                <div class="alert alert-info mb-0">
                    <small><i class="ri-shield-check-line me-1"></i>Sistem akan memvalidasi status pembayaran</small>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Serahkan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#28a745'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/apotik/serahkan/${resepId}`,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    Swal.fire({
                        title: 'Memproses...',
                        html: 'Memeriksa pembayaran dan stock obat...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    if (response.success) {
                        let messageHtml = response.message;
                        
                        // Tambahkan info jika cicilan
                        if (response.data && response.data.status_pembayaran === 'CICILAN') {
                            messageHtml += `<br><br>
                                <div class="alert alert-info mt-2 mb-0">
                                    <small>
                                        <i class="ri-information-line me-1"></i>
                                        Status: <strong>CICILAN</strong><br>
                                        Sisa Tagihan: <strong>Rp ${formatRupiah(response.data.sisa_tagihan)}</strong>
                                    </small>
                                </div>
                            `;
                        }
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            html: messageHtml,
                            showCancelButton: true,
                            confirmButtonText: '<i class="ri-printer-line me-1"></i>Print Bukti',
                            cancelButtonText: 'Tutup'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.open(`/apotik/print/${resepId}`, '_blank');
                            }
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON?.message || 'Gagal menyerahkan obat';
                    
                    // Tampilkan error yang lebih informatif
                    Swal.fire({
                        icon: 'error',
                        title: 'Tidak Dapat Menyerahkan Obat',
                        html: `
                            <div class="text-start">
                                <p>${errorMsg}</p>
                                <div class="alert alert-warning mt-2 mb-0">
                                    <small>
                                        <i class="ri-error-warning-line me-1"></i>
                                        Silakan hubungi bagian kasir untuk menyelesaikan pembayaran
                                    </small>
                                </div>
                            </div>
                        `,
                        confirmButtonText: 'Mengerti'
                    });
                }
            });
        }
    });
}

// Tolak resep
function tolakResep(resepId, reason) {
    $.ajax({
        url: `/apotik/tolak/${resepId}`,
        method: 'POST',
        data: {
            rejection_reason: reason
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            Swal.fire({
                title: 'Menolak Resep...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function(response) {
            if (response.success) {
                $('#modalTolak').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function(xhr) {
            const errorMsg = xhr.responseJSON?.message || 'Gagal menolak resep';
            Swal.fire('Error', errorMsg, 'error');
        }
    });
}

// Helper functions
function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID').format(angka);
}

function formatDateTime(datetime) {
    const date = new Date(datetime);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function getStatusBadge(status) {
    const config = {
        'menunggu': { class: 'bg-warning', icon: 'ri-time-line', text: 'Menunggu' },
        'proses': { class: 'bg-info', icon: 'ri-loader-4-line', text: 'Diproses' },
        'selesai': { class: 'bg-success', icon: 'ri-checkbox-circle-line', text: 'Selesai' },
        'batal': { class: 'bg-danger', icon: 'ri-close-circle-line', text: 'Dibatalkan' }
    };
    
    const c = config[status] || config['menunggu'];
    return `<span class="badge ${c.class}"><i class="${c.icon} me-1"></i>${c.text}</span>`;
}
</script>
@endpush