@extends('layouts.app')

@section('title', 'History Gudang - ' . ($gudang->nama ?? 'Detail'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('gudangs.index') }}">Gudang</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">History - {{ $gudang->nama_gudang }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('gudangs.index') }}" class="btn btn-secondary">
                        <i class="ri-arrow-left-line me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Gudang Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="ri-information-line me-2"></i>Informasi Gudang</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Nama Gudang:</strong>
                            <p class="mb-2">{{ $gudang->nama_gudang ?? '-' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Kode Gudang:</strong>
                            <p class="mb-2">{{ $gudang->kode_gudang ?? '-' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Lokasi:</strong>
                            <p class="mb-2">{{ $gudang->lokasi ?? '-' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Kapasitas:</strong>
                            <p class="mb-2">{{ $gudang->kapasitas ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">
                                <i class="ri-arrow-down-line"></i> Total Penerimaan
                            </h6>
                            <h3 class="mb-0 text-success" id="totalPenerimaan">
                                {{ number_format($totalPenerimaan ?? 0, 0, ',', '.') }}
                            </h3>
                        </div>
                        <div class="text-success">
                            <i class="ri-inbox-line" style="font-size: 3rem; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">
                                <i class="ri-arrow-up-line"></i> Total Pengiriman
                            </h6>
                            <h3 class="mb-0 text-warning" id="totalPengiriman">
                                {{ number_format($totalPengiriman ?? 0, 0, ',', '.') }}
                            </h3>
                        </div>
                        <div class="text-warning">
                            <i class="ri-inbox-unarchive-line" style="font-size: 3rem; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">
                                <i class="ri-file-list-line"></i> Total Transaksi
                            </h6>
                            <h3 class="mb-0 text-info" id="totalTransaksi">
                                {{ number_format($totalTransaksi ?? 0, 0, ',', '.') }}
                            </h3>
                        </div>
                        <div class="text-info">
                            <i class="ri-file-list-3-line" style="font-size: 3rem; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">
                                <i class="ri-stack-line"></i> Saldo Stok
                            </h6>
                            <h3 class="mb-0 text-primary" id="saldoStok">
                                {{ number_format(($totalPenerimaan ?? 0) - ($totalPengiriman ?? 0), 0, ',', '.') }}
                            </h3>
                        </div>
                        <div class="text-primary">
                            <i class="ri-database-2-line" style="font-size: 3rem; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Action Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="ri-filter-3-line me-2"></i>Filter & Export</h5>
                </div>
                <div class="card-body">
                    <form id="filterForm" onsubmit="return false;">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Tanggal Mulai</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="tanggalMulai" 
                                       name="tanggal_mulai"
                                       value="{{ date('Y-m-01') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Tanggal Akhir</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="tanggalAkhir" 
                                       name="tanggal_akhir"
                                       value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Status</label>
                                <select class="form-select" id="filterStatus" name="status">
                                    <option value="">Semua Status</option>
                                    <option value="penerimaan">Penerimaan</option>
                                    <option value="pengiriman">Pengiriman</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-primary w-100" onclick="filterHistory()">
                                    <i class="ri-search-line me-1"></i>Filter
                                </button>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-secondary w-100" onclick="resetFilter()">
                                    <i class="ri-refresh-line me-1"></i>Reset
                                </button>
                            </div>
                        </div>
                    </form>

                    <hr class="my-3">

                    <div class="row g-2">
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-success w-100" onclick="exportExcel()">
                                <i class="ri-file-excel-2-line me-1"></i>Export Excel
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-danger w-100" onclick="exportPDF()">
                                <i class="ri-file-pdf-line me-1"></i>Export PDF
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-info w-100" onclick="printHistory()">
                                <i class="ri-printer-line me-1"></i>Print
                            </button>
                        </div>
                        <div class="col-md-3">
                            <input type="text" 
                                   class="form-control" 
                                   id="searchHistory" 
                                   placeholder="🔍 Cari data...">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="ri-table-line me-2"></i>Data History Transaksi</h5>
                        <span class="badge bg-primary" id="recordCount">
                            Total: {{ number_format($histories->count(), 0, ',', '.') }} Records
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0" id="historyTable">
                            <thead class="table-dark">
                                <tr>
                                    <th width="3%" class="text-center">No</th>
                                    <th width="12%">Waktu Proses</th>
                                    <th width="10%" class="text-center">Status</th>
                                    <th width="12%">Referensi</th>
                                    <th width="12%">No. Referensi</th>
                                    <th width="15%">Barang</th>
                                    <th width="8%" class="text-end">Jumlah</th>
                                    <th width="13%">Supplier</th>
                                    <th width="10%">No. Batch</th>
                                    <th width="15%">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="historyTableBody">
                                @forelse($histories as $index => $history)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <i class="ri-calendar-line text-muted"></i>
                                        {{ $history->waktu_proses->format('d/m/Y') }}<br>
                                        <small class="text-muted">
                                            <i class="ri-time-line"></i>
                                            {{ $history->waktu_proses->format('H:i:s') }}
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        @if($history->status == 'penerimaan')
                                            <span class="badge bg-success">
                                                <i class="ri-arrow-down-line"></i> Penerimaan
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class="ri-arrow-up-line"></i> Pengiriman
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $history->referensi_type ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ $history->no_referensi ?? '-' }}</strong>
                                    </td>
                                    <td>
                                        <i class="ri-product-hunt-line text-primary"></i>
                                        {{ $history->barang->nama ?? '-' }}
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-primary">
                                            {{ number_format($history->jumlah, 0, ',', '.') }}
                                        </strong>
                                    </td>
                                    <td>
                                        <i class="ri-user-line text-muted"></i>
                                        {{ $history->supplier->nama_supplier ?? '-' }}
                                    </td>
                                    <td>
                                        <code>{{ $history->no_batch ?? '-' }}</code>
                                    </td>
                                    <td>
                                        <small>{{ Str::limit($history->keterangan ?? '-', 50) }}</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5">
                                        <i class="ri-inbox-line" style="font-size: 3rem; opacity: 0.3;"></i>
                                        <p class="text-muted mt-2">Tidak ada data history</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($histories->count() > 0)
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Menampilkan {{ $histories->count() }} dari {{ $histories->total() }} data
                        </small>
                        {{ $histories->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Print Template (Hidden) -->
<div id="printTemplate" style="display: none;"></div>

@endsection

@push('styles')
<style>
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        border: none;
        margin-bottom: 1.5rem;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0,123,255,0.05);
        cursor: pointer;
    }
    
    .badge {
        padding: 0.35em 0.65em;
        font-weight: 500;
    }
    
    @media print {
        body * { visibility: hidden; }
        #printTemplate, #printTemplate * { visibility: visible; }
        #printTemplate { 
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
    }
    
    .form-label.fw-bold {
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }
    
    .card-header {
        border-bottom: 2px solid #dee2e6;
    }
    
    thead.table-dark th {
        position: sticky;
        top: 0;
        z-index: 10;
    }
</style>
@endpush

@push('scripts')
<script>
const gudangId = '{{ $gudang->id }}';

// Filter History
function filterHistory() {
    const tanggalMulai = document.getElementById('tanggalMulai').value;
    const tanggalAkhir = document.getElementById('tanggalAkhir').value;
    const status = document.getElementById('filterStatus').value;
    
    // Show loading
    Swal.fire({
        title: 'Loading...',
        text: 'Memfilter data...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    fetch('{{ route("history-gudang.filter") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            gudang_id: gudangId,
            tanggal_mulai: tanggalMulai,
            tanggal_akhir: tanggalAkhir,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        if (data.success) {
            updateTable(data.histories);
            updateSummary(data.summary);
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: `Ditemukan ${data.histories.length} data`,
                timer: 1500,
                showConfirmButton: false
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat memfilter data'
        });
        console.error('Error:', error);
    });
}

// Reset Filter
function resetFilter() {
    document.getElementById('tanggalMulai').value = '{{ date("Y-m-01") }}';
    document.getElementById('tanggalAkhir').value = '{{ date("Y-m-d") }}';
    document.getElementById('filterStatus').value = '';
    document.getElementById('searchHistory').value = '';
    filterHistory();
}

// Update Table
function updateTable(histories) {
    const tbody = document.getElementById('historyTableBody');
    tbody.innerHTML = '';
    
    if (histories.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center py-5">
                    <i class="ri-inbox-line" style="font-size: 3rem; opacity: 0.3;"></i>
                    <p class="text-muted mt-2">Tidak ada data history</p>
                </td>
            </tr>
        `;
        document.getElementById('recordCount').textContent = 'Total: 0 Records';
        return;
    }
    
    histories.forEach((history, index) => {
        const statusBadge = history.status === 'penerimaan' 
            ? '<span class="badge bg-success"><i class="ri-arrow-down-line"></i> Penerimaan</span>'
            : '<span class="badge bg-warning text-dark"><i class="ri-arrow-up-line"></i> Pengiriman</span>';
            
        tbody.innerHTML += `
            <tr>
                <td class="text-center">${index + 1}</td>
                <td>
                    <i class="ri-calendar-line text-muted"></i>
                    ${history.waktu_proses_formatted}
                </td>
                <td class="text-center">${statusBadge}</td>
                <td><span class="badge bg-secondary">${history.referensi_type || '-'}</span></td>
                <td><strong>${history.no_referensi || '-'}</strong></td>
                <td><i class="ri-product-hunt-line text-primary"></i> ${history.barang_nama || '-'}</td>
                <td class="text-end"><strong class="text-primary">${history.jumlah_formatted}</strong></td>
                <td><i class="ri-user-line text-muted"></i> ${history.supplier_nama || '-'}</td>
                <td><code>${history.no_batch || '-'}</code></td>
                <td><small>${history.keterangan || '-'}</small></td>
            </tr>
        `;
    });
    
    document.getElementById('recordCount').textContent = `Total: ${histories.length.toLocaleString('id-ID')} Records`;
}

// Update Summary
function updateSummary(summary) {
    document.getElementById('totalPenerimaan').textContent = summary.total_penerimaan;
    document.getElementById('totalPengiriman').textContent = summary.total_pengiriman;
    document.getElementById('totalTransaksi').textContent = summary.total_transaksi || '0';
    
    const penerimaan = parseInt(summary.total_penerimaan.replace(/\./g, '')) || 0;
    const pengiriman = parseInt(summary.total_pengiriman.replace(/\./g, '')) || 0;
    document.getElementById('saldoStok').textContent = (penerimaan - pengiriman).toLocaleString('id-ID');
}

// Export Excel
function exportExcel() {
    const params = new URLSearchParams({
        gudang_id: gudangId,
        tanggal_mulai: document.getElementById('tanggalMulai').value,
        tanggal_akhir: document.getElementById('tanggalAkhir').value,
        status: document.getElementById('filterStatus').value
    });
    
    window.location.href = `{{ route('history-gudang.export-excel') }}?${params.toString()}`;
}

// Export PDF
function exportPDF() {
    const params = new URLSearchParams({
        gudang_id: gudangId,
        tanggal_mulai: document.getElementById('tanggalMulai').value,
        tanggal_akhir: document.getElementById('tanggalAkhir').value,
        status: document.getElementById('filterStatus').value
    });
    
    window.open(`{{ route('history-gudang.export-pdf') }}?${params.toString()}`, '_blank');
}

// Print Function
function printHistory() {
    const printContent = document.getElementById('historyTable').outerHTML;
    const gudangInfo = {
        nama: '{{ $gudang->nama ?? "" }}',
        kode: '{{ $gudang->kode ?? "" }}',
        lokasi: '{{ $gudang->lokasi ?? "" }}'
    };
    const tanggalMulai = document.getElementById('tanggalMulai').value;
    const tanggalAkhir = document.getElementById('tanggalAkhir').value;
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>History Gudang - ${gudangInfo.nama}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; font-size: 11px; }
                .print-header { text-align: center; margin-bottom: 20px; border-bottom: 3px solid #000; padding-bottom: 15px; }
                .print-header h1 { margin: 5px 0; font-size: 24px; }
                .print-header h2 { margin: 5px 0; font-size: 18px; color: #555; }
                .print-header p { margin: 3px 0; }
                .info-table { width: 100%; margin-bottom: 15px; font-size: 10px; }
                .info-table td { padding: 5px; }
                .info-table td:first-child { font-weight: bold; width: 30%; }
                table { width: 100%; border-collapse: collapse; font-size: 10px; }
                th, td { border: 1px solid #000; padding: 8px 5px; text-align: left; }
                th { background-color: #333; color: white; font-weight: bold; text-align: center; }
                .text-center { text-align: center; }
                .text-end { text-align: right; }
                .badge { padding: 2px 6px; border-radius: 3px; font-size: 9px; display: inline-block; }
                .bg-success { background-color: #28a745; color: white; }
                .bg-warning { background-color: #ffc107; color: #000; }
                .print-footer { margin-top: 50px; page-break-inside: avoid; }
                .signature-table { width: 100%; border: none; }
                .signature-table td { border: none; text-align: center; padding: 50px 20px 0; }
                @media print {
                    body { margin: 10mm; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="print-header">
                <h1>HISTORY GUDANG</h1>
                <h2>${gudangInfo.nama}</h2>
                <p>Kode: ${gudangInfo.kode} | Lokasi: ${gudangInfo.lokasi}</p>
                <p>Periode: ${new Date(tanggalMulai).toLocaleDateString('id-ID')} s/d ${new Date(tanggalAkhir).toLocaleDateString('id-ID')}</p>
                <p style="font-size: 9px; color: #888;">Dicetak pada: ${new Date().toLocaleString('id-ID')}</p>
            </div>
            ${printContent}
            <div class="print-footer">
                <table class="signature-table">
                    <tr>
                        <td>
                            <p>Mengetahui,</p>
                            <p><strong>Manager Gudang</strong></p>
                            <br><br><br>
                            <p>______________________</p>
                        </td>
                        <td>
                            <p>Dibuat oleh,</p>
                            <p><strong>Admin</strong></p>
                            <br><br><br>
                            <p>______________________</p>
                        </td>
                    </tr>
                </table>
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => {
        printWindow.print();
    }, 500);
}

// Search Function
document.getElementById('searchHistory')?.addEventListener('keyup', function(e) {
    const searchValue = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#historyTableBody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchValue)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    document.getElementById('recordCount').textContent = `Total: ${visibleCount.toLocaleString('id-ID')} Records`;
});
</script>
@endpush