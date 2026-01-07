@extends('layouts.app')

@section('title', 'Penjualan Resep')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Penjualan Resep</li>
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
                                            <i class="ri-file-list-line fs-4 text-white"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Total Resep</p>
                                    <h4 class="mb-0" id="statTotalTransaksi">{{ number_format($stats['total_transaksi']) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
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
                                    <p class="text-muted mb-1">Menunggu</p>
                                    <h4 class="mb-0" id="statMenunggu">{{ number_format($stats['menunggu']) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm rounded-circle bg-info bg-soft">
                                        <span class="avatar-title rounded-circle bg-info bg-gradient">
                                            <i class="ri-loader-line fs-4 text-white"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Diproses</p>
                                    <h4 class="mb-0" id="statDiproses">{{ number_format($stats['diproses']) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm rounded-circle bg-success bg-soft">
                                        <span class="avatar-title rounded-circle bg-success bg-gradient">
                                            <i class="ri-check-line fs-4 text-white"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Selesai</p>
                                    <h4 class="mb-0" id="statSelesai">{{ number_format($stats['selesai']) }}</h4>
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
                                            <i class="ri-money-dollar-circle-line fs-4 text-white"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Pendapatan Hari Ini</p>
                                    <h5 class="mb-0" id="statPendapatan">Rp {{ number_format($stats['pendapatan_hari_ini'], 0, ',', '.') }}</h5>
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
                            <i class="ri-file-list-3-line me-2"></i>Daftar Penjualan Resep
                        </h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm" id="btnHistory">
                                <i class="ri-history-line me-1"></i>History
                            </button>
                            <a class="btn btn-primary btn-sm" href="{{ route('penjualan_resep.create') }}">
                                <i class="ri-add-circle-line me-1"></i>Resep Baru
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Filter Section --}}
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Tanggal Dari</label>
                            <input type="date" class="form-control form-control-sm" id="filterTanggalDari">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Tanggal Sampai</label>
                            <input type="date" class="form-control form-control-sm" id="filterTanggalSampai">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Status</label>
                            <select class="form-select form-select-sm" id="filterStatus">
                                <option value="">Semua Status</option>
                                <option value="menunggu">Menunggu</option>
                                <option value="diproses">Diproses</option>
                                <option value="selesai">Selesai</option>
                                <option value="diambil">Diambil</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Pencarian</label>
                            <input type="text" class="form-control form-control-sm" id="filterSearch" placeholder="No. resep, pasien, dokter...">
                        </div>
                        <div class="col-md-2 d-flex align-items-end gap-2">
                            <button type="button" class="btn btn-sm btn-primary" id="btnFilter">
                                <i class="ri-search-line me-1"></i>Filter
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnReset">
                                <i class="ri-refresh-line"></i>
                            </button>
                        </div>
                    </div>

                    {{-- DataTable --}}
                    <div class="table-responsive" id="tableContainer">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Kode</th>
                                    <th>No. Resep</th>
                                    <th>Pasien</th>
                                    <th>Dokter</th>
                                    <th>Tanggal</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th width="180" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                @forelse($penjualan as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $penjualan->firstItem() + $index }}</td>
                                    <td><strong class="text-primary">{{ $item->kode_transaksi }}</strong></td>
                                    <td><span class="badge bg-secondary">{{ $item->no_resep }}</span></td>
                                    <td>
                                        <strong>{{ $item->nama_pasien }}</strong>
                                        <br><small class="text-muted">RM: {{ $item->no_rm_pasien }}</small>
                                    </td>
                                    <td><small>{{ $item->nama_dokter }}</small></td>
                                    <td><small>{{ $item->tanggal_transaksi->format('d/m/Y H:i') }}</small></td>
                                    <td><strong>Rp {{ number_format($item->total, 0, ',', '.') }}</strong></td>
                                    <td>
                                        @php
                                            $badgeClass = match($item->status_resep) {
                                                'menunggu' => 'bg-warning',
                                                'diproses' => 'bg-info',
                                                'selesai' => 'bg-success',
                                                'diambil' => 'bg-primary',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ strtoupper($item->status_resep) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                <i class="ri-more-2-fill"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="viewDetail('{{ $item->id }}'); return false;">
                                                        <i class="ri-eye-line me-2"></i>Detail
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="updateStatus('{{ $item->id }}'); return false;">
                                                        <i class="ri-refresh-line me-2"></i>Update Status
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="printStruk('{{ $item->id }}'); return false;">
                                                        <i class="ri-printer-line me-2"></i>Print
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#" onclick="deleteTransaksi('{{ $item->id }}'); return false;">
                                                        <i class="ri-delete-bin-line me-2"></i>Hapus
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="ri-inbox-line ri-3x text-muted d-block mb-3"></i>
                                        <p class="text-muted mb-0">Belum ada transaksi penjualan resep</p>
                                        <a href="{{ route('penjualan_resep.create') }}" class="btn btn-primary btn-sm mt-3">
                                            <i class="ri-add-circle-line me-1"></i>Buat Resep Pertama
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
                <h5 class="modal-title">Detail Resep</h5>
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

{{-- Modal Update Status --}}
<div class="modal fade" id="modalStatus" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status Resep</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="statusResepId">
                <div class="mb-3">
                    <label class="form-label">Status Resep</label>
                    <select class="form-select" id="statusResep">
                        <option value="menunggu">Menunggu</option>
                        <option value="diproses">Diproses</option>
                        <option value="selesai">Selesai</option>
                        <option value="diambil">Diambil</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="simpanStatus()">Simpan</button>
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
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $('#btnFilter').on('click', () => loadData());
    $('#btnReset').on('click', function() {
        $('#filterTanggalDari, #filterTanggalSampai, #filterStatus, #filterSearch').val('');
        loadData();
    });
    $('#filterSearch').on('keypress', (e) => { if (e.which === 13) loadData(); });
});

function loadData() {
    $.ajax({
        url: '{{ route("penjualan_resep.index") }}',
        data: {
            tanggal_dari: $('#filterTanggalDari').val(),
            tanggal_sampai: $('#filterTanggalSampai').val(),
            status: $('#filterStatus').val(),
            search: $('#filterSearch').val()
        },
        beforeSend: () => {
            $('#tableBody').html('<tr><td colspan="9" class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr>');
        },
        success: (response) => {
            if (response.success) {
                updateTable(response.data);
                updateStats(response.stats);
            }
        }
    });
}

function updateTable(data) {
    let html = '';
    data.data.forEach((item, i) => {
        const badges = {menunggu: 'warning', diproses: 'info', selesai: 'success', diambil: 'primary'};
        html += `<tr>
            <td class="text-center">${data.from + i}</td>
            <td><strong class="text-primary">${item.kode_transaksi}</strong></td>
            <td><span class="badge bg-secondary">${item.no_resep}</span></td>
            <td><strong>${item.nama_pasien}</strong><br><small class="text-muted">RM: ${item.no_rm_pasien}</small></td>
            <td><small>${item.nama_dokter}</small></td>
            <td><small>${new Date(item.tanggal_transaksi).toLocaleString('id-ID')}</small></td>
            <td><strong>Rp ${Number(item.total).toLocaleString('id-ID')}</strong></td>
            <td><span class="badge bg-${badges[item.status_resep] || 'secondary'}">${item.status_resep.toUpperCase()}</span></td>
            <td class="text-center">
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown"><i class="ri-more-2-fill"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" onclick="viewDetail('${item.id}'); return false;"><i class="ri-eye-line me-2"></i>Detail</a></li>
                        <li><a class="dropdown-item" href="#" onclick="updateStatus('${item.id}'); return false;"><i class="ri-refresh-line me-2"></i>Update Status</a></li>
                        <li><a class="dropdown-item" href="#" onclick="printStruk('${item.id}'); return false;"><i class="ri-printer-line me-2"></i>Print</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteTransaksi('${item.id}'); return false;"><i class="ri-delete-bin-line me-2"></i>Hapus</a></li>
                    </ul>
                </div>
            </td>
        </tr>`;
    });
    $('#tableBody').html(html || '<tr><td colspan="9" class="text-center py-5"><i class="ri-inbox-line ri-3x text-muted d-block mb-3"></i><p class="text-muted mb-0">Tidak ada data</p></td></tr>');
}

function updateStats(stats) {
    $('#statTotalTransaksi').text(Number(stats.total_transaksi).toLocaleString('id-ID'));
    $('#statMenunggu').text(Number(stats.menunggu).toLocaleString('id-ID'));
    $('#statDiproses').text(Number(stats.diproses).toLocaleString('id-ID'));
    $('#statSelesai').text(Number(stats.selesai).toLocaleString('id-ID'));
    $('#statPendapatan').text('Rp ' + Number(stats.pendapatan_hari_ini).toLocaleString('id-ID'));
}

function viewDetail(id) {
    const modal = new bootstrap.Modal($('#modalDetail')[0]);
    modal.show();
    $.ajax({
        url: `/penjualan-resep/${id}`,
        success: (r) => {
            let html = `<div class="row mb-3">
                <div class="col-md-6"><table class="table table-sm">
                    <tr><th>No. Resep:</th><td>${r.data.no_resep}</td></tr>
                    <tr><th>Pasien:</th><td>${r.data.nama_pasien}</td></tr>
                    <tr><th>Dokter:</th><td>${r.data.nama_dokter}</td></tr>
                </table></div>
                <div class="col-md-6"><table class="table table-sm">
                    <tr><th>Status:</th><td><span class="badge bg-info">${r.data.status_resep.toUpperCase()}</span></td></tr>
                    <tr><th>Diagnosa:</th><td>${r.data.diagnosa || '-'}</td></tr>
                </table></div>
            </div><h6>Detail Obat:</h6><table class="table table-bordered table-sm">
                <thead><tr><th>No</th><th>Obat</th><th>Aturan Pakai</th><th>Jumlah</th><th>Harga</th><th>Subtotal</th></tr></thead><tbody>`;
            r.data.details.forEach((d, i) => {
                html += `<tr><td>${i+1}</td><td>${d.nama_obat}</td><td>${d.aturan_pakai}</td><td>${d.jumlah} ${d.satuan}</td><td>Rp ${Number(d.harga_satuan).toLocaleString('id-ID')}</td><td>Rp ${Number(d.subtotal).toLocaleString('id-ID')}</td></tr>`;
            });
            html += `</tbody></table><div class="text-end"><h5>Total: Rp ${Number(r.data.total).toLocaleString('id-ID')}</h5></div>`;
            $('#modalDetailBody').html(html);
        }
    });
}

function updateStatus(id) {
    $('#statusResepId').val(id);
    const modal = new bootstrap.Modal($('#modalStatus')[0]);
    modal.show();
}

function simpanStatus() {
    $.ajax({
        url: `/penjualan-resep/${$('#statusResepId').val()}/status`,
        method: 'PUT',
        data: { status_resep: $('#statusResep').val(), _token: '{{ csrf_token() }}' },
        success: (r) => {
            bootstrap.Modal.getInstance($('#modalStatus')[0]).hide();
            Swal.fire('Berhasil!', r.message, 'success');
            loadData();
        }
    });
}

function printStruk(id) {
    window.open(`/penjualan-resep/${id}/print`, '_blank');
}

function deleteTransaksi(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: 'Transaksi akan dibatalkan dan stock dikembalikan',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Batalkan!',
        cancelButtonText: 'Tidak'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/penjualan-resep/${id}`,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: (r) => {
                    Swal.fire('Berhasil!', r.message, 'success');
                    loadData();
                },
                error: (xhr) => {
                    Swal.fire('Error!', xhr.responseJSON.message, 'error');
                }
            });
        }
    });
}
</script>
@endpush