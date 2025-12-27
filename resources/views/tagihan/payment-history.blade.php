@extends('layouts.app')

@section('title', 'History Pembayaran - ' . $tagihan->no_tagihan)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tagihan.index') }}">Tagihan PO</a></li>
    <li class="breadcrumb-item"><a href="{{ route('tagihan.show', $tagihan->id_tagihan) }}">{{ $tagihan->no_tagihan }}</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">History Pembayaran</li>
@endsection

@section('content')
<div class="app-body">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="ri-history-line me-2"></i>History Pembayaran
                </h4>
                <div>
                    <a href="{{ route('tagihan.show', $tagihan->id_tagihan) }}" class="btn btn-secondary btn-sm">
                        <i class="ri-arrow-left-line me-1"></i>Kembali
                    </a>
                    @if($tagihan->canBePaid())
                    <a href="{{ route('tagihan.payment.form', $tagihan->id_tagihan) }}" class="btn btn-success btn-sm">
                        <i class="ri-add-line me-1"></i>Bayar Lagi
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Payment History Table -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="ri-file-list-line me-2"></i>Daftar Pembayaran
                    </h5>
                </div>
                <div class="card-body">
                    @if($tagihan->pembayaran->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>No Pembayaran</th>
                                    <th>Tanggal</th>
                                    <th>Metode</th>
                                    <th>Referensi</th>
                                    <th class="text-end">Jumlah</th>
                                    <th>Status</th>
                                    <th width="150" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tagihan->pembayaran as $x => $payment)
                                <tr>
                                    <td class="text-center">{{ $x + 1 }}</td>
                                    <td>
                                        <strong>{{ $payment->no_pembayaran }}</strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="ri-calendar-line"></i>
                                            {{ $payment->tanggal_bayar->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ ucfirst(str_replace('_', ' ', $payment->metode_pembayaran)) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $payment->nomor_referensi ?? '-' }}
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-success">Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}</strong>
                                    </td>
                                    <td>
                                        @if($payment->status_pembayaran == 'diverifikasi')
                                            <span class="badge bg-success">
                                                <i class="ri-checkbox-circle-line"></i> Verified
                                            </span>
                                            @if($payment->tanggal_approve)
                                                <br><small class="text-muted">{{ $payment->tanggal_approve->format('d/m/Y H:i') }}</small>
                                            @endif
                                        @elseif($payment->status_pembayaran == 'pending')
                                            <span class="badge bg-warning text-dark">
                                                <i class="ri-time-line"></i> Pending
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="ri-close-circle-line"></i> Ditolak
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            @if($payment->bukti_pembayaran)
                                            <a href="{{ route('tagihan.payment.download', $payment->id_pembayaran) }}" 
                                               class="btn btn-sm btn-outline-primary"
                                               title="Download Bukti">
                                                <i class="ri-download-line"></i>
                                            </a>
                                            @endif
                                            
                                            @if($payment->status_pembayaran == 'pending')
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-success" 
                                                    onclick="verifyPayment('{{ $payment->id_pembayaran }}', 'diverifikasi')"
                                                    title="Verifikasi">
                                                <i class="ri-check-line"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="verifyPayment('{{ $payment->id_pembayaran }}', 'ditolak')"
                                                    title="Tolak">
                                                <i class="ri-close-line"></i>
                                            </button>
                                            @endif
                                            
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-info" 
                                                    onclick="showDetail('{{ $payment->id_pembayaran }}')"
                                                    title="Detail">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="5" class="text-end fw-bold">Total Dibayar (Verified):</td>
                                    <td class="text-end fw-bold text-success">
                                        Rp {{ number_format($tagihan->pembayaran->where('status_pembayaran', 'diverifikasi')->sum('jumlah_bayar'), 0, ',', '.') }}
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="ri-file-list-line ri-3x text-muted d-block mb-3"></i>
                        <p class="text-muted">Belum ada history pembayaran</p>
                        @if($tagihan->canBePaid())
                        <a href="{{ route('tagihan.payment.form', $tagihan->id_tagihan) }}" class="btn btn-success btn-sm mt-2">
                            <i class="ri-add-line me-1"></i>Bayar Sekarang
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Summary Sidebar -->
        <div class="col-lg-4">
            <!-- Payment Summary -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h6 class="mb-0 text-white">
                        <i class="ri-pie-chart-line me-2"></i>Ringkasan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">No Tagihan:</small>
                        <br><strong>{{ $tagihan->no_tagihan }}</strong>
                    </div>
                    <hr>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Grand Total:</span>
                            <strong>Rp {{ number_format($tagihan->grand_total, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Total Dibayar:</span>
                            <strong class="text-success">Rp {{ number_format($tagihan->total_dibayar, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted fw-bold">Sisa Tagihan:</span>
                            <strong class="fs-5 {{ $tagihan->sisa_tagihan > 0 ? 'text-danger' : 'text-success' }}">
                                Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}
                            </strong>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    @if($tagihan->grand_total > 0)
                    <div class="mb-3">
                        <small class="text-muted">Progress Pembayaran:</small>
                        <div class="progress mt-2" style="height: 20px;">
                            <div class="progress-bar {{ $tagihan->status == 'lunas' ? 'bg-success' : 'bg-info' }}" 
                                 style="width: {{ ($tagihan->total_dibayar / $tagihan->grand_total) * 100 }}%">
                                {{ number_format(($tagihan->total_dibayar / $tagihan->grand_total) * 100, 1) }}%
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="mb-0">
                        <span class="badge {{ $tagihan->status == 'lunas' ? 'bg-success' : 'bg-warning text-dark' }} w-100 py-2">
                            {{ ucwords(str_replace('_', ' ', $tagihan->status)) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="ri-bar-chart-line me-2"></i>Statistik Pembayaran
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
                        <div>
                            <small class="text-muted">Total Transaksi</small>
                            <br><strong class="fs-5">{{ $tagihan->pembayaran->count() }}</strong>
                        </div>
                        <div class="text-end">
                            <i class="ri-file-list-line fs-1 text-muted" style="opacity: 0.3;"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
                        <div>
                            <small class="text-muted">Verified</small>
                            <br><strong class="fs-5 text-success">{{ $tagihan->pembayaran->where('status_pembayaran', 'diverifikasi')->count() }}</strong>
                        </div>
                        <div class="text-end">
                            <i class="ri-checkbox-circle-line fs-1 text-success" style="opacity: 0.3;"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
                        <div>
                            <small class="text-muted">Pending</small>
                            <br><strong class="fs-5 text-warning">{{ $tagihan->pembayaran->where('status_pembayaran', 'pending')->count() }}</strong>
                        </div>
                        <div class="text-end">
                            <i class="ri-time-line fs-1 text-warning" style="opacity: 0.3;"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-0">
                        <div>
                            <small class="text-muted">Ditolak</small>
                            <br><strong class="fs-5 text-danger">{{ $tagihan->pembayaran->where('status_pembayaran', 'ditolak')->count() }}</strong>
                        </div>
                        <div class="text-end">
                            <i class="ri-close-circle-line fs-1 text-danger" style="opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <div class="text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Verify Modal -->
<div class="modal fade" id="verifyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Verifikasi Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="verifyMessage"></p>
                <div class="mb-3">
                    <label class="form-label fw-bold">PIN (6 digit) <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="verifyPin" maxlength="6" placeholder="Masukkan PIN">
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-control" id="verifyCatatan" rows="2" placeholder="Catatan verifikasi (opsional)"></textarea>
                </div>
                <input type="hidden" id="verifyPaymentId">
                <input type="hidden" id="verifyStatus">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="confirmVerify()">Konfirmasi</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        border-radius: 0.5rem;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.4em 0.8em;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .progress {
        background-color: #e9ecef;
    }
</style>
@endpush

@push('scripts')
<script>
    let detailModalInstance, verifyModalInstance;

    document.addEventListener('DOMContentLoaded', function() {
        detailModalInstance = new bootstrap.Modal(document.getElementById('detailModal'));
        verifyModalInstance = new bootstrap.Modal(document.getElementById('verifyModal'));
    });

    function showDetail(paymentId) {
        // TODO: Implement AJAX to fetch payment detail
        detailModalInstance.show();
    }

    function verifyPayment(paymentId, status) {
        const message = status === 'diverifikasi' 
            ? 'Apakah Anda yakin ingin memverifikasi pembayaran ini?' 
            : 'Apakah Anda yakin ingin menolak pembayaran ini?';
        
        document.getElementById('verifyMessage').textContent = message;
        document.getElementById('verifyPaymentId').value = paymentId;
        document.getElementById('verifyStatus').value = status;
        document.getElementById('verifyPin').value = '';
        document.getElementById('verifyCatatan').value = '';
        
        verifyModalInstance.show();
    }

    function confirmVerify() {
        const pin = document.getElementById('verifyPin').value;
        const paymentId = document.getElementById('verifyPaymentId').value;
        const status = document.getElementById('verifyStatus').value;
        const catatan = document.getElementById('verifyCatatan').value;

        if (!pin || pin.length !== 6) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'PIN harus 6 digit'
            });
            return;
        }

        // Show loading
        Swal.fire({
            title: 'Memproses...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch(`/tagihan/payment/${paymentId}/verify`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ 
                pin: pin,
                status: status,
                catatan: catatan
            })
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

        verifyModalInstance.hide();
    }
</script>
@endpush