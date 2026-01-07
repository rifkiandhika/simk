@extends('layouts.app')

@section('title', 'Penjualan Bebas')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Penjualan Bebas</li>
@endsection

@section('content')
<div class="app-body">
    <div class="row">
        <div class="col-xl-12">
            {{-- Statistics Cards --}}
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm rounded-circle bg-primary bg-soft">
                                        <span class="avatar-title rounded-circle bg-primary bg-gradient">
                                            <i class="ri-shopping-cart-line fs-4 text-white"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Total Transaksi</p>
                                    <h4 class="mb-0" id="statTotalTransaksi">{{ number_format($stats['total_transaksi']) }}</h4>
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
                                            <i class="ri-calendar-check-line fs-4 text-white"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Transaksi Hari Ini</p>
                                    <h4 class="mb-0" id="statHariIni">{{ number_format($stats['transaksi_hari_ini']) }}</h4>
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
                                            <i class="ri-money-dollar-circle-line fs-4 text-white"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Pendapatan Hari Ini</p>
                                    <h4 class="mb-0" id="statPendapatanHariIni">Rp {{ number_format($stats['pendapatan_hari_ini'], 0, ',', '.') }}</h4>
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
                                            <i class="ri-line-chart-line fs-4 text-white"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Pendapatan Bulan Ini</p>
                                    <h4 class="mb-0" id="statPendapatanBulan">Rp {{ number_format($stats['pendapatan_bulan_ini'], 0, ',', '.') }}</h4>
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
                            <i class="ri-file-list-3-line me-2"></i>Daftar Penjualan Bebas
                        </h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm" id="btnHistory">
                                <i class="ri-history-line me-1"></i>History
                            </button>
                            <a class="btn btn-primary btn-sm" href="{{ route('penjualan_bebas.create') }}">
                                <i class="ri-add-circle-line me-1"></i>Transaksi Baru
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Filter Section --}}
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Tanggal Dari</label>
                            <input type="date" class="form-control form-control-sm" id="filterTanggalDari">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Tanggal Sampai</label>
                            <input type="date" class="form-control form-control-sm" id="filterTanggalSampai">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Pencarian</label>
                            <input type="text" class="form-control form-control-sm" id="filterSearch" placeholder="Kode transaksi, nama pasien...">
                        </div>
                        <div class="col-md-2 d-flex align-items-end gap-2">
                            <button type="button" class="btn btn-sm btn-primary" id="btnFilter">
                                <i class="ri-search-line me-1"></i>Filter
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnReset">
                                <i class="ri-refresh-line me-1"></i>Reset
                            </button>
                        </div>
                    </div>

                    {{-- DataTable --}}
                    <div class="table-responsive" id="tableContainer">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Kode Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Nama Pasien</th>
                                    <th>Total Item</th>
                                    <th>Total Bayar</th>
                                    <th>Metode</th>
                                    <th>Petugas</th>
                                    <th width="150" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                @forelse($penjualan as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $penjualan->firstItem() + $index }}</td>
                                    <td><strong class="text-primary">{{ $item->kode_transaksi }}</strong></td>
                                    <td><small>{{ $item->tanggal_transaksi->format('d/m/Y H:i') }}</small></td>
                                    <td>
                                        <strong>{{ $item->nama_pasien }}</strong>
                                        @if($item->no_rm_pasien)
                                        <br><small class="text-muted">RM: {{ $item->no_rm_pasien }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $item->details->count() }} item</span>
                                    </td>
                                    <td><strong>Rp {{ number_format($item->total, 0, ',', '.') }}</strong></td>
                                    <td>
                                        <span class="badge bg-secondary">{{ strtoupper($item->metode_pembayaran) }}</span>
                                    </td>
                                    <td><small>{{ $item->user->name ?? '-' }}</small></td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewDetail('{{ $item->id }}')">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="printStruk('{{ $item->id }}')">
                                                <i class="ri-printer-line"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteTransaksi('{{ $item->id }}')">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="ri-inbox-line ri-3x text-muted d-block mb-3"></i>
                                        <p class="text-muted mb-0">Belum ada transaksi penjualan bebas</p>
                                        <a href="{{ route('penjualan_bebas.create') }}" class="btn btn-primary btn-sm mt-3">
                                            <i class="ri-add-circle-line me-1"></i>Buat Transaksi Pertama
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div id="paginationContainer">
                        @if($penjualan->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <p class="text-muted mb-0 small">
                                    Menampilkan {{ $penjualan->firstItem() }} - {{ $penjualan->lastItem() }} dari {{ $penjualan->total() }} data
                                </p>
                            </div>
                            <div>
                                {{ $penjualan->links() }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Detail --}}
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalDetailBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal History --}}
<div class="modal fade" id="modalHistory" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">History Transaksi Penjualan Bebas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalHistoryBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
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
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Filter button
    $('#btnFilter').on('click', function() {
        loadData();
    });
    
    // Reset button
    $('#btnReset').on('click', function() {
        $('#filterTanggalDari').val('');
        $('#filterTanggalSampai').val('');
        $('#filterSearch').val('');
        loadData();
    });
    
    // History button
    $('#btnHistory').on('click', function() {
        loadHistory();
    });
    
    // Enter key on search
    $('#filterSearch').on('keypress', function(e) {
        if (e.which === 13) {
            loadData();
        }
    });
});

// Load data with AJAX
function loadData(page = 1) {
    const tanggalDari = $('#filterTanggalDari').val();
    const tanggalSampai = $('#filterTanggalSampai').val();
    const search = $('#filterSearch').val();
    
    $.ajax({
        url: '{{ route("penjualan_bebas.index") }}',
        method: 'GET',
        data: {
            tanggal_dari: tanggalDari,
            tanggal_sampai: tanggalSampai,
            search: search,
            page: page
        },
        beforeSend: function() {
            $('#tableBody').html('<tr><td colspan="9" class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr>');
        },
        success: function(response) {
            if (response.success) {
                updateTable(response.data);
                updateStats(response.stats);
            }
        },
        error: function() {
            Swal.fire('Error!', 'Gagal memuat data', 'error');
        }
    });
}

// Update table content
function updateTable(data) {
    let html = '';
    
    if (data.data.length === 0) {
        html = `<tr>
            <td colspan="9" class="text-center py-5">
                <i class="ri-inbox-line ri-3x text-muted d-block mb-3"></i>
                <p class="text-muted mb-0">Tidak ada data ditemukan</p>
            </td>
        </tr>`;
    } else {
        data.data.forEach((item, index) => {
            const detailCount = item.details ? item.details.length : 0;
            const tanggal = new Date(item.tanggal_transaksi).toLocaleString('id-ID');
            const rmText = item.no_rm_pasien ? `<br><small class="text-muted">RM: ${item.no_rm_pasien}</small>` : '';
            
            html += `<tr>
                <td class="text-center">${data.from + index}</td>
                <td><strong class="text-primary">${item.kode_transaksi}</strong></td>
                <td><small>${tanggal}</small></td>
                <td><strong>${item.nama_pasien}</strong>${rmText}</td>
                <td class="text-center"><span class="badge bg-info">${detailCount} item</span></td>
                <td><strong>Rp ${Number(item.total).toLocaleString('id-ID')}</strong></td>
                <td><span class="badge bg-secondary">${item.metode_pembayaran.toUpperCase()}</span></td>
                <td><small>${item.user ? item.user.name : '-'}</small></td>
                <td class="text-center">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewDetail('${item.id}')">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="printStruk('${item.id}')">
                            <i class="ri-printer-line"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteTransaksi('${item.id}')">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                </td>
            </tr>`;
        });
    }
    
    $('#tableBody').html(html);
    
    // Update pagination
    updatePagination(data);
}

// Update pagination
function updatePagination(data) {
    // Implement pagination update logic here
}

// Update statistics
function updateStats(stats) {
    $('#statTotalTransaksi').text(Number(stats.total_transaksi).toLocaleString('id-ID'));
    $('#statHariIni').text(Number(stats.transaksi_hari_ini).toLocaleString('id-ID'));
    $('#statPendapatanHariIni').text('Rp ' + Number(stats.pendapatan_hari_ini).toLocaleString('id-ID'));
    $('#statPendapatanBulan').text('Rp ' + Number(stats.pendapatan_bulan_ini).toLocaleString('id-ID'));
}

// View detail
function viewDetail(id) {
    const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
    modal.show();
    
    $.ajax({
        url: `/penjualan-bebas/${id}`,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                let html = `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr><th>Kode Transaksi:</th><td>${response.data.kode_transaksi}</td></tr>
                                <tr><th>Nama Pasien:</th><td>${response.data.nama_pasien}</td></tr>
                                <tr><th>Tanggal:</th><td>${new Date(response.data.tanggal_transaksi).toLocaleString('id-ID')}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr><th>Metode Bayar:</th><td>${response.data.metode_pembayaran.toUpperCase()}</td></tr>
                                <tr><th>Petugas:</th><td>${response.data.user ? response.data.user.name : '-'}</td></tr>
                            </table>
                        </div>
                    </div>
                    <h6>Detail Obat:</h6>
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Obat</th>
                                <th>Batch</th>
                                <th>Jumlah</th>
                                <th>Harga</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>`;
                
                response.data.details.forEach((detail, index) => {
                    html += `<tr>
                        <td>${index + 1}</td>
                        <td>${detail.nama_obat}</td>
                        <td>${detail.no_batch}</td>
                        <td>${detail.jumlah} ${detail.satuan}</td>
                        <td>Rp ${Number(detail.harga_satuan).toLocaleString('id-ID')}</td>
                        <td>Rp ${Number(detail.subtotal).toLocaleString('id-ID')}</td>
                    </tr>`;
                });
                
                html += `</tbody>
                    </table>
                    <div class="text-end">
                        <h5>Total: Rp ${Number(response.data.total).toLocaleString('id-ID')}</h5>
                        <p class="mb-0">Bayar: Rp ${Number(response.data.bayar).toLocaleString('id-ID')}</p>
                        <p class="mb-0">Kembalian: Rp ${Number(response.data.kembalian).toLocaleString('id-ID')}</p>
                    </div>`;
                
                $('#modalDetailBody').html(html);
            }
        }
    });
}

// Print struk
function printStruk(id) {
    window.open(`/penjualan-bebas/${id}/print`, '_blank');
}

// Delete transaksi
function deleteTransaksi(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: 'Transaksi akan dibatalkan dan stock akan dikembalikan',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Batalkan!',
        cancelButtonText: 'Tidak'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/penjualan-bebas/${id}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Berhasil!', response.message, 'success');
                        loadData();
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    Swal.fire('Error!', response.message, 'error');
                }
            });
        }
    });
}

// Load history
function loadHistory() {
    const modal = new bootstrap.Modal(document.getElementById('modalHistory'));
    modal.show();
    
    $.ajax({
        url: '{{ route("penjualan_bebas.history") }}',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                let html = '<table class="table table-bordered table-sm"><thead class="table-light"><tr><th>Tanggal</th><th>Kode Referensi</th><th>Obat</th><th>Jumlah</th><th>Stock Awal</th><th>Stock Akhir</th><th>Petugas</th></tr></thead><tbody>';
                
                response.data.data.forEach(item => {
                    html += `<tr>
                        <td>${new Date(item.tanggal_transaksi).toLocaleString('id-ID')}</td>
                        <td>${item.kode_referensi}</td>
                        <td>${item.detail_stock_apotik.detail_supplier ? item.detail_stock_apotik.detail_supplier.nama : '-'}</td>
                        <td class="text-danger">${item.jumlah_keluar}</td>
                        <td>${item.stock_awal}</td>
                        <td>${item.stock_akhir}</td>
                        <td>${item.user ? item.user.name : '-'}</td>
                    </tr>`;
                });
                
                html += '</tbody></table>';
                $('#modalHistoryBody').html(html);
            }
        }
    });
}
</script>
@endpush