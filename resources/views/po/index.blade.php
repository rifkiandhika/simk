@extends('layouts.app')

@section('title', 'Daftar Purchase Order')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Purchase Order</li>
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
                                    <i class="ri-file-list-3-line fs-4 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Total PO</p>
                            <h4 class="mb-0">{{ $purchaseOrders->total() }}</h4>
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
                                    <i class="ri-time-line fs-4 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Menunggu Approval</p>
                            <h4 class="mb-0">{{ $purchaseOrders->whereIn('status', ['menunggu_persetujuan_kepala_gudang', 'menunggu_persetujuan_kasir'])->count() }}</h4>
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
                            <p class="text-muted mb-1">Disetujui</p>
                            <h4 class="mb-0">{{ $purchaseOrders->where('status', 'disetujui')->count() }}</h4>
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
                                    <i class="ri-truck-line fs-4 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Dalam Pengiriman</p>
                            <h4 class="mb-0">{{ $purchaseOrders->whereIn('status', ['dikirim_ke_supplier', 'dalam_pengiriman'])->count() }}</h4>
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
                            <i class="ri-list-check-2 me-2"></i>Daftar Purchase Order
                        </h5>
                        <div class="btn-group">
                            <a class="btn btn-primary btn-sm" href="{{ route('po.create', ['type' => 'internal']) }}">
                                <i class="ri-add-circle-line me-1"></i>PO Internal
                            </a>
                            <a class="btn btn-success btn-sm" href="{{ route('po.create', ['type' => 'eksternal']) }}">
                                <i class="ri-add-circle-line me-1"></i>PO Eksternal
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="card-body border-bottom bg-light">
                    <form method="GET" action="{{ route('po.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Filter Role</label>
                                <select class="form-select form-select-sm" name="role" id="filterRole">
                                    <option value="">Semua Role</option>
                                    <option value="apotik" {{ request('role') == 'apotik' ? 'selected' : '' }}>Apotik</option>
                                    <option value="gudang" {{ request('role') == 'gudang' ? 'selected' : '' }}>Gudang</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Filter Status</label>
                                <select class="form-select form-select-sm" name="status" id="filterStatus">
                                    <option value="">Semua Status</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="menunggu_persetujuan_kepala_gudang" {{ request('status') == 'menunggu_persetujuan_kepala_gudang' ? 'selected' : '' }}>Menunggu Kepala Gudang</option>
                                    <option value="menunggu_persetujuan_kasir" {{ request('status') == 'menunggu_persetujuan_kasir' ? 'selected' : '' }}>Menunggu Kasir</option>
                                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="dikirim_ke_supplier" {{ request('status') == 'dikirim_ke_supplier' ? 'selected' : '' }}>Dikirim ke Supplier</option>
                                    <option value="dalam_pengiriman" {{ request('status') == 'dalam_pengiriman' ? 'selected' : '' }}>Dalam Pengiriman</option>
                                    <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Cari</label>
                                <input type="text" class="form-control form-control-sm" name="search" id="searchBox" 
                                       placeholder="Cari No PO atau Supplier..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold d-block">&nbsp;</label>
                                <button type="submit" class="btn btn-sm btn-info w-100">
                                    <i class="ri-filter-line me-1"></i>Terapkan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle" id="poTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>No PO</th>
                                    <th>Tanggal</th>
                                    <th>Pemohon</th>
                                    <th>Tujuan</th>
                                    <th width="150">Total</th>
                                    <th width="150">Status</th>
                                    <th width="100" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($purchaseOrders as $x => $po)
                                    <tr>
                                        <td class="text-center">{{ $purchaseOrders->firstItem() + $x }}</td>
                                        <td>
                                            <strong class="text-primary">{{ $po->no_po }}</strong>
                                            <br>
                                            @if($po->tipe_po == 'internal')
                                                <span class="badge badge-sm bg-info">Internal</span>
                                            @else
                                                <span class="badge badge-sm bg-warning text-dark">Eksternal</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <i class="ri-calendar-line"></i> 
                                                {{ $po->tanggal_permintaan->format('d/m/Y') }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-2">
                                                    <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                        <i class="ri-user-line"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <strong>{{ $po->karyawanPemohon->nama_lengkap }}</strong>
                                                    <br><small class="text-muted">{{ ucfirst($po->unit_pemohon) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($po->supplier)
                                                <span class="badge bg-secondary">{{ $po->supplier->nama_supplier }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($po->unit_tujuan) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong class="text-success">Rp {{ number_format($po->grand_total, 0, ',', '.') }}</strong>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'draft' => 'bg-secondary',
                                                    'menunggu_persetujuan_kepala_gudang' => 'bg-warning text-dark',
                                                    'menunggu_persetujuan_kasir' => 'bg-warning text-dark',
                                                    'disetujui' => 'bg-success',
                                                    'dikirim_ke_supplier' => 'bg-info',
                                                    'dalam_pengiriman' => 'bg-primary',
                                                    'diterima' => 'bg-success',
                                                    'ditolak' => 'bg-danger',
                                                ];
                                                $statusLabels = [
                                                    'draft' => 'Draft',
                                                    'menunggu_persetujuan_kepala_gudang' => 'Menunggu Kepala Gudang',
                                                    'menunggu_persetujuan_kasir' => 'Menunggu Kasir',
                                                    'disetujui' => 'Disetujui',
                                                    'dikirim_ke_supplier' => 'Dikirim ke Supplier',
                                                    'dalam_pengiriman' => 'Dalam Pengiriman',
                                                    'diterima' => 'Diterima',
                                                    'ditolak' => 'Ditolak',
                                                ];
                                            @endphp
                                            <span class="badge {{ $statusColors[$po->status] ?? 'bg-secondary' }}">
                                                <i class="ri-record-circle-line"></i> {{ $statusLabels[$po->status] ?? $po->status }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-2-fill"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('po.show', $po->id_po) }}">
                                                            <i class="ri-eye-fill me-2"></i>Detail
                                                        </a>
                                                    </li>
                                                    @if($po->status === 'draft')
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('po.edit', $po->id_po) }}">
                                                            <i class="ri-pencil-fill me-2"></i>Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <button class="dropdown-item text-success" onclick="submitPO('{{ $po->id_po }}')">
                                                            <i class="ri-send-plane-fill me-2"></i>Submit
                                                        </button>
                                                    </li>
                                                    @endif
                                                    @if($po->status === 'dikirim_ke_supplier' || $po->status === 'dalam_pengiriman')
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('shipping.by-po', $po->id_po) }}">
                                                            <i class="ri-truck-line me-2"></i>Shipping
                                                        </a>
                                                    </li>
                                                    @endif
                                                    @if(in_array($po->status, ['draft', 'ditolak']))
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button class="dropdown-item text-danger" onclick="deletePO('{{ $po->id_po }}')">
                                                            <i class="ri-delete-bin-6-line me-2"></i>Hapus
                                                        </button>
                                                    </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="ri-file-list-3-line ri-3x text-muted d-block mb-3"></i>
                                            <p class="text-muted mb-0">Belum ada data Purchase Order</p>
                                            <a href="{{ route('po.create', ['type' => 'internal']) }}" class="btn btn-primary btn-sm mt-3">
                                                <i class="ri-add-circle-line me-1"></i>Buat PO Baru
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($purchaseOrders->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted small">
                            Menampilkan {{ $purchaseOrders->firstItem() }} - {{ $purchaseOrders->lastItem() }} 
                            dari {{ $purchaseOrders->total() }} data
                        </div>
                        <div>
                            {{ $purchaseOrders->links() }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- PIN Modal for Submit --}}
<div class="modal fade" id="submitModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Submit PO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Masukkan PIN Anda untuk submit Purchase Order</p>
                <div class="mb-3">
                    <label class="form-label">PIN (6 digit)</label>
                    <input type="password" class="form-control" id="pinSubmit" maxlength="6" placeholder="Masukkan PIN">
                </div>
                <input type="hidden" id="poIdSubmit">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="confirmSubmit()">Submit PO</button>
            </div>
        </div>
    </div>
</div>

{{-- PIN Modal for Delete --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Konfirmasi Hapus PO</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Apakah Anda yakin ingin menghapus Purchase Order ini?</p>
                <div class="alert alert-warning">
                    <i class="ri-alert-line"></i> Tindakan ini tidak dapat dibatalkan!
                </div>
                <div class="mb-3">
                    <label class="form-label">PIN (6 digit)</label>
                    <input type="password" class="form-control" id="pinDelete" maxlength="6" placeholder="Masukkan PIN">
                </div>
                <input type="hidden" id="poIdDelete">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Hapus PO</button>
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
    let submitModalInstance, deleteModalInstance;

    document.addEventListener('DOMContentLoaded', function() {
        submitModalInstance = new bootstrap.Modal(document.getElementById('submitModal'));
        deleteModalInstance = new bootstrap.Modal(document.getElementById('deleteModal'));

        // Auto dismiss alerts
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });

    function submitPO(poId) {
        document.getElementById('poIdSubmit').value = poId;
        document.getElementById('pinSubmit').value = '';
        submitModalInstance.show();
    }

    function confirmSubmit() {
        const pin = document.getElementById('pinSubmit').value;
        const poId = document.getElementById('poIdSubmit').value;

        if (!pin || pin.length !== 6) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'PIN harus 6 digit'
            });
            return;
        }

        fetch(`/po/${poId}/submit`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ pin: pin })
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.error || 'Terjadi kesalahan'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan sistem'
            });
        });

        submitModalInstance.hide();
    }

    function deletePO(poId) {
        document.getElementById('poIdDelete').value = poId;
        document.getElementById('pinDelete').value = '';
        deleteModalInstance.show();
    }

    function confirmDelete() {
        const pin = document.getElementById('pinDelete').value;
        const poId = document.getElementById('poIdDelete').value;

        if (!pin || pin.length !== 6) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'PIN harus 6 digit'
            });
            return;
        }

        fetch(`/po/${poId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ pin: pin })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.message || 'Terjadi kesalahan'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan sistem'
            });
        });

        deleteModalInstance.hide();
    }
</script>
@endpush