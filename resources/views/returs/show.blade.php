@extends('layouts.app')

@section('title', 'Detail Retur')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('returs.index') }}">Retur</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">{{ $retur->no_retur }}</li>
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
    
    @if (session('error') || $errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i>
            {{ session('error') ?? $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('returs.index') }}" class="btn btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i>Kembali
                </a>
                
                <div class="btn-group">
                    @if($retur->status == 'draft')
                        <a href="{{ route('returs.edit', $retur->id_retur) }}" class="btn btn-warning">
                            <i class="ri-pencil-fill me-1"></i>Edit
                        </a>
                        <button type="button" class="btn btn-primary" onclick="showPinModal('submit')">
                            <i class="ri-send-plane-line me-1"></i>Submit
                        </button>
                    @endif

                    @if($retur->status == 'menunggu_persetujuan')
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                            <i class="ri-checkbox-circle-line me-1"></i>Setujui
                        </button>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="ri-close-circle-line me-1"></i>Tolak
                        </button>
                    @endif

                    @if($retur->status == 'disetujui')
                        <button type="button" class="btn btn-info" onclick="showPinModal('process')">
                            <i class="ri-loader-line me-1"></i>Proses
                        </button>
                    @endif

                    @if($retur->status == 'diproses')
                        <button type="button" class="btn btn-success" onclick="showPinModal('complete')">
                            <i class="ri-check-double-line me-1"></i>Selesai
                        </button>
                    @endif

                    @if(!in_array($retur->status, ['selesai', 'dibatalkan']))
                        <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#cancelModal">
                            <i class="ri-forbid-line me-1"></i>Batalkan
                        </button>
                    @endif

                    <button onclick="window.print()" class="btn btn-outline-primary">
                        <i class="ri-printer-line me-1"></i>Cetak
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-md-8">
            <!-- Informasi Retur -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0">
                        <i class="ri-information-line me-2"></i>Informasi Retur
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="40%">No Retur</th>
                                    <td>: <code class="text-primary fw-bold">{{ $retur->no_retur }}</code></td>
                                </tr>
                                <tr>
                                    <th>Tipe Retur</th>
                                    <td>: 
                                        @if($retur->tipe_retur == 'po')
                                            <span class="badge bg-info">
                                                <i class="ri-file-list-3-line me-1"></i>Purchase Order
                                            </span>
                                        @else
                                            <span class="badge bg-primary">
                                                <i class="ri-store-3-line me-1"></i>Stock Apotik
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Kode Referensi</th>
                                    <td>: <strong>{{ $retur->kode_referensi }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Tanggal Retur</th>
                                    <td>: {{ \Carbon\Carbon::parse($retur->tanggal_retur)->format('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>: 
                                        @php
                                            $statusClass = [
                                                'draft' => 'secondary',
                                                'menunggu_persetujuan' => 'warning',
                                                'disetujui' => 'info',
                                                'ditolak' => 'danger',
                                                'diproses' => 'primary',
                                                'selesai' => 'success',
                                                'dibatalkan' => 'dark'
                                            ];
                                            $statusIcon = [
                                                'draft' => 'ri-draft-line',
                                                'menunggu_persetujuan' => 'ri-time-line',
                                                'disetujui' => 'ri-checkbox-circle-line',
                                                'ditolak' => 'ri-close-circle-line',
                                                'diproses' => 'ri-loader-line',
                                                'selesai' => 'ri-check-double-line',
                                                'dibatalkan' => 'ri-forbid-line'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusClass[$retur->status] ?? 'secondary' }} fs-6">
                                            <i class="{{ $statusIcon[$retur->status] ?? 'ri-information-line' }} me-1"></i>
                                            {{ str_replace('_', ' ', strtoupper($retur->status)) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="40%">Alasan Retur</th>
                                    <td>: {{ str_replace('_', ' ', ucwords($retur->alasan_retur)) }}</td>
                                </tr>
                                <tr>
                                    <th>Pelapor</th>
                                    <td>: {{ $retur->karyawanPelapor->nama_lengkap ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>NIP Pelapor</th>
                                    <td>: {{ $retur->karyawanPelapor->nip ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Unit Pelapor</th>
                                    <td>: {{ ucfirst($retur->unit_pelapor) }}</td>
                                </tr>
                                <tr>
                                    <th>Unit Tujuan</th>
                                    <td>: {{ $retur->unit_tujuan ? ucfirst($retur->unit_tujuan) : '-' }}</td>
                                </tr>
                                @if($retur->supplier)
                                <tr>
                                    <th>Supplier</th>
                                    <td>: {{ $retur->supplier->nama_supplier ?? $retur->supplier->nama }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                    
                    @if($retur->keterangan_alasan)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="alert alert-info mb-0">
                                <strong><i class="ri-information-line me-2"></i>Keterangan Alasan:</strong><br>
                                {{ $retur->keterangan_alasan }}
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($retur->catatan)
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="alert alert-secondary mb-0">
                                <strong><i class="ri-sticky-note-line me-2"></i>Catatan:</strong><br>
                                {{ $retur->catatan }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Item Retur -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0">
                        <i class="ri-box-3-line me-2"></i>Item Retur
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-s table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Produk</th>
                                    <th width="12%">Qty Retur</th>
                                    <th width="15%">Harga Satuan</th>
                                    <th width="12%">Kondisi</th>
                                    <th width="15%">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($retur->returItems as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $item->nama_produk }}</strong>
                                        @if($item->batches && $item->batches->isNotEmpty())
                                        <br>
                                        <small class="text-muted">
                                            @foreach($item->batches as $batch)
                                                <span class="badge bg-secondary me-1">
                                                    Batch: {{ $batch->batch_number }} 
                                                    @if($batch->tanggal_kadaluarsa)
                                                        (ED: {{ \Carbon\Carbon::parse($batch->tanggal_kadaluarsa)->format('d/m/Y') }})
                                                    @endif
                                                    - Qty: {{ $batch->qty_diretur }}
                                                </span>
                                            @endforeach
                                        </small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <strong>{{ number_format($item->qty_diretur, 0, ',', '.') }}</strong>
                                        @if(isset($item->qty_diterima_kembali) && $item->qty_diterima_kembali > 0)
                                        <br>
                                        <small class="text-success">
                                            <i class="ri-checkbox-circle-line"></i> Diterima: {{ $item->qty_diterima_kembali }}
                                        </small>
                                        @endif
                                    </td>
                                    <td class="text-end">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                    <td>
                                        @php
                                            $kondisiClass = [
                                                'baik' => 'success',
                                                'rusak' => 'danger',
                                                'kadaluarsa' => 'warning'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $kondisiClass[$item->kondisi_barang] ?? 'secondary' }}">
                                            {{ ucfirst($item->kondisi_barang) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        @php
                                            $subtotal = $item->qty_diretur * $item->harga_satuan;
                                        @endphp
                                        <strong class="text-success">Rp {{ number_format($subtotal, 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                                @if(isset($item->catatan_item) && $item->catatan_item)
                                <tr>
                                    <td colspan="6" class="bg-light">
                                        <small><i class="ri-information-line me-1"></i><strong>Catatan:</strong> {{ $item->catatan_item }}</small>
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <th colspan="5" class="text-end">TOTAL:</th>
                                    <th class="text-end">
                                        @php
                                            $total = $retur->returItems->sum(function($item) {
                                                return $item->qty_diretur * $item->harga_satuan;
                                            });
                                        @endphp
                                        <strong class="text-success fs-5">Rp {{ number_format($total, 0, ',', '.') }}</strong>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- History -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-info text-white py-3">
                    <h5 class="mb-0">
                        <i class="ri-history-line me-2"></i>History Perubahan Status
                    </h5>
                </div>
                <div class="card-body">
                    @if($retur->histories && $retur->histories->isNotEmpty())
                    <div class="timeline">
                        @foreach($retur->histories as $history)
                        <div class="timeline-item mb-3 d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <i class="ri-time-line"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1">
                                        {{ str_replace('_', ' ', ucwords($history->status_ke)) }}
                                    </h6>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($history->waktu_perubahan ?? $history->created_at)->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                                <p class="text-muted mb-1">
                                    <small>
                                        <i class="ri-user-line me-1"></i>
                                        Oleh: <strong>{{ $history->karyawan->nama_lengkap ?? '-' }}</strong>
                                    </small>
                                </p>
                                @if($history->catatan)
                                <p class="mb-0">
                                    <small>{{ $history->catatan }}</small>
                                </p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center text-muted py-3">
                        <i class="ri-history-line ri-3x mb-2 d-block"></i>
                        <p class="mb-0">Belum ada history</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-md-4">
            <!-- Status Approval -->
            @if(isset($retur->status_approval) && $retur->status_approval != 'pending')
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header {{ $retur->status_approval == 'disetujui' ? 'bg-success' : 'bg-danger' }} text-white py-3">
                    <h5 class="mb-0">
                        <i class="ri-{{ $retur->status_approval == 'disetujui' ? 'checkbox-circle' : 'close-circle' }}-line me-2"></i> 
                        Status Approval
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th>Status</th>
                            <td>: 
                                <span class="badge bg-{{ $retur->status_approval == 'disetujui' ? 'success' : 'danger' }}">
                                    {{ ucfirst($retur->status_approval) }}
                                </span>
                            </td>
                        </tr>
                        @if($retur->karyawanApproval)
                        <tr>
                            <th>Oleh</th>
                            <td>: {{ $retur->karyawanApproval->nama_lengkap }}</td>
                        </tr>
                        @endif
                        @if($retur->tanggal_approval)
                        <tr>
                            <th>Tanggal</th>
                            <td>: {{ \Carbon\Carbon::parse($retur->tanggal_approval)->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endif
                    </table>
                    @if($retur->catatan_approval)
                    <div class="alert alert-light mt-2 mb-0">
                        <strong>Catatan:</strong><br>
                        {{ $retur->catatan_approval }}
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Dokumen Pendukung -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="ri-attachment-line me-2"></i>Dokumen Pendukung
                    </h5>
                </div>
                <div class="card-body">
                    @if($retur->documents && $retur->documents->isEmpty())
                    <div class="text-center text-muted py-3">
                        <i class="ri-folder-open-line ri-3x mb-2 d-block"></i>
                        <p class="mb-0 small">Belum ada dokumen</p>
                    </div>
                    @elseif($retur->documents)
                    <div class="list-group">
                        @foreach($retur->documents as $doc)
                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank" 
                           class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="ri-file-{{ $doc->file_type == 'pdf' ? 'pdf' : 'image' }}-line me-2"></i>
                                    {{ $doc->nama_dokumen }}
                                    <br>
                                    <small class="text-muted">
                                        {{ ucfirst(str_replace('_', ' ', $doc->tipe_dokumen)) }}
                                    </small>
                                </div>
                                <i class="ri-download-line"></i>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @endif
                    
                    @if(!in_array($retur->status, ['selesai', 'dibatalkan']))
                    <button type="button" class="btn btn-sm btn-primary w-100 mt-3" 
                            data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i class="ri-upload-line me-1"></i>Upload Dokumen
                    </button>
                    @endif
                </div>
            </div>

            <!-- Processing Info -->
            @if(isset($retur->tanggal_diproses) && $retur->tanggal_diproses)
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark py-3">
                    <h5 class="mb-0">
                        <i class="ri-information-line me-2"></i>Info Pemrosesan
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th>Pemroses</th>
                            <td>: {{ $retur->karyawanPemroses->nama_lengkap ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Proses</th>
                            <td>: {{ \Carbon\Carbon::parse($retur->tanggal_diproses)->format('d/m/Y H:i') }}</td>
                        </tr>
                        @if(isset($retur->tanggal_selesai) && $retur->tanggal_selesai)
                        <tr>
                            <th>Tanggal Selesai</th>
                            <td>: {{ \Carbon\Carbon::parse($retur->tanggal_selesai)->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- PIN OTP Modal (Universal) --}}
<div class="modal fade" id="pinModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="ri-lock-password-line me-2"></i><span id="pinModalTitle">Verifikasi PIN</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-4 py-4">
                <p class="text-muted mb-4" id="pinModalDescription">Masukkan PIN 6 digit Anda untuk melanjutkan</p>
                
                <!-- OTP-style PIN Input -->
                <div class="otp-container d-flex justify-content-center gap-2 mb-4">
                    <input type="password" class="otp-input form-control text-center" maxlength="1" pattern="\d" inputmode="numeric" data-index="0" autocomplete="off">
                    <input type="password" class="otp-input form-control text-center" maxlength="1" pattern="\d" inputmode="numeric" data-index="1" autocomplete="off">
                    <input type="password" class="otp-input form-control text-center" maxlength="1" pattern="\d" inputmode="numeric" data-index="2" autocomplete="off">
                    <input type="password" class="otp-input form-control text-center" maxlength="1" pattern="\d" inputmode="numeric" data-index="3" autocomplete="off">
                    <input type="password" class="otp-input form-control text-center" maxlength="1" pattern="\d" inputmode="numeric" data-index="4" autocomplete="off">
                    <input type="password" class="otp-input form-control text-center" maxlength="1" pattern="\d" inputmode="numeric" data-index="5" autocomplete="off">
                </div>

                <div id="karyawanInfo" style="display: none;">
                    <div class="alert alert-success">
                        <i class="ri-user-line me-2"></i>
                        <strong id="karyawanNama"></strong>
                    </div>
                </div>

                <!-- Hidden field to store action type -->
                <input type="hidden" id="modalAction">
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-primary" id="confirmPinBtn" disabled>
                    <i class="ri-check-line me-1"></i> Konfirmasi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cancel -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="ri-forbid-line me-2"></i>Batalkan Retur</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin membatalkan retur ini?</p>
                <div class="mb-3">
                    <label class="form-label">Alasan Pembatalan <span class="text-danger">*</span></label>
                    <textarea id="cancelReason" class="form-control" rows="3" placeholder="Jelaskan alasan pembatalan..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-dark" onclick="submitCancelForm()">
                    <i class="ri-check-line me-1"></i>Ya, Batalkan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload Document -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="ri-upload-line me-2"></i>Upload Dokumen Pendukung</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">File <span class="text-danger">*</span></label>
                    <input type="file" id="uploadFile" class="form-control" 
                           accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" required>
                    <small class="text-muted">Format: JPG, PNG, PDF, DOC, DOCX (Max: 10MB)</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tipe Dokumen <span class="text-danger">*</span></label>
                    <select id="uploadType" class="form-select" required>
                        <option value="foto_barang">Foto Barang</option>
                        <option value="surat_jalan">Surat Jalan</option>
                        <option value="berita_acara">Berita Acara</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea id="uploadKeterangan" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="submitUploadForm()">
                    <i class="ri-upload-line me-1"></i>Upload
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Approve -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="ri-checkbox-circle-line me-2"></i>Setujui Retur</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menyetujui retur ini?</p>
                <div class="mb-3">
                    <label class="form-label">Catatan (Opsional)</label>
                    <textarea id="approveCatatan" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" onclick="submitApproveForm()">
                    <i class="ri-check-line me-1"></i>Ya, Setujui
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reject -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="ri-close-circle-line me-2"></i>Tolak Retur</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menolak retur ini?</p>
                <div class="mb-3">
                    <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                    <textarea id="rejectCatatan" class="form-control" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="submitRejectForm()">
                    <i class="ri-close-line me-1"></i>Ya, Tolak
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    @media print {
        .btn, .breadcrumb, .modal, .alert { display: none !important; }
        .card { border: 1px solid #ddd !important; page-break-inside: avoid; }
    }

    .card {
        border-radius: 0.5rem;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.4em 0.8em;
        font-size: 0.85rem;
    }

    code {
        background-color: #f0f7ff;
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
    }

    .timeline-item {
        position: relative;
        padding-left: 0;
    }

    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: 19px;
        top: 40px;
        bottom: -20px;
        width: 2px;
        background-color: #e9ecef;
    }
    
    .table-s {
        border: 1px solid #ced4da !important;
    }

    /* OTP-style PIN Input Styles */
    .otp-container {
        max-width: 400px;
        margin: 0 auto;
    }

    .otp-input {
        width: 50px;
        height: 60px;
        font-size: 24px;
        font-weight: bold;
        border: 2px solid #dee2e6;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .otp-input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        outline: none;
        transform: scale(1.05);
    }

    .otp-input.filled {
        background-color: #f8f9fa;
        border-color: #198754;
    }

    .otp-input.error {
        border-color: #dc3545;
        animation: shake 0.5s;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    #pinModal .modal-content {
        border-radius: 15px;
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    }

    #pinModal .modal-header {
        padding: 1.5rem;
    }

    #pinModal .modal-title {
        color: #0d6efd;
        font-weight: 600;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let verifiedKaryawan = null;
let currentAction = '';

// ============================================
// PIN OTP INITIALIZATION
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    initializePinOTP();
    
    // Auto hide alerts
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});

function initializePinOTP() {
    const otpInputs = document.querySelectorAll('.otp-input');
    
    otpInputs.forEach((input, index) => {
        // Handle input
        input.addEventListener('input', function(e) {
            const value = e.target.value;
            
            // Only allow numbers
            if (!/^\d$/.test(value)) {
                e.target.value = '';
                return;
            }
            
            // Add filled class
            e.target.classList.add('filled');
            e.target.classList.remove('error');
            
            // Move to next input
            if (value && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
            
            // Check if all inputs are filled
            checkPinComplete();
        });
        
        // Handle backspace
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace') {
                if (!e.target.value && index > 0) {
                    otpInputs[index - 1].focus();
                    otpInputs[index - 1].value = '';
                    otpInputs[index - 1].classList.remove('filled', 'error');
                } else {
                    e.target.value = '';
                    e.target.classList.remove('filled', 'error');
                }
            } else if (e.key === 'ArrowLeft' && index > 0) {
                e.preventDefault();
                otpInputs[index - 1].focus();
            } else if (e.key === 'ArrowRight' && index < otpInputs.length - 1) {
                e.preventDefault();
                otpInputs[index + 1].focus();
            } else if (e.key === 'Enter') {
                e.preventDefault();
                const confirmBtn = document.getElementById('confirmPinBtn');
                if (!confirmBtn.disabled) {
                    confirmBtn.click();
                }
            }
        });
        
        // Handle paste
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').trim();
            
            if (/^\d{6}$/.test(pastedData)) {
                pastedData.split('').forEach((char, i) => {
                    if (otpInputs[i]) {
                        otpInputs[i].value = char;
                        otpInputs[i].classList.add('filled');
                    }
                });
                otpInputs[5].focus();
                checkPinComplete();
            }
        });
        
        // Select all on focus
        input.addEventListener('focus', function() {
            this.select();
        });
    });
}

function checkPinComplete() {
    const otpInputs = document.querySelectorAll('.otp-input');
    const allFilled = Array.from(otpInputs).every(input => input.value !== '');
    const confirmBtn = document.getElementById('confirmPinBtn');
    
    if (allFilled) {
        confirmBtn.disabled = false;
        confirmBtn.classList.remove('disabled');
    } else {
        confirmBtn.disabled = true;
        confirmBtn.classList.add('disabled');
    }
}

function getPinValue() {
    const otpInputs = document.querySelectorAll('.otp-input');
    return Array.from(otpInputs).map(input => input.value).join('');
}

function resetPinInputs() {
    const otpInputs = document.querySelectorAll('.otp-input');
    otpInputs.forEach(input => {
        input.value = '';
        input.classList.remove('filled', 'error');
    });
    document.getElementById('confirmPinBtn').disabled = true;
    document.getElementById('confirmPinBtn').classList.add('disabled');
    document.getElementById('karyawanInfo').style.display = 'none';
    
    // Focus first input
    setTimeout(() => {
        if (otpInputs[0]) otpInputs[0].focus();
    }, 100);
}

function showPinError() {
    const otpInputs = document.querySelectorAll('.otp-input');
    otpInputs.forEach(input => {
        input.classList.add('error');
    });
    
    setTimeout(() => {
        otpInputs.forEach(input => {
            input.classList.remove('error');
        });
    }, 500);
}

// ============================================
// SHOW PIN MODAL
// ============================================
function showPinModal(action) {
    currentAction = action;
    const modal = new bootstrap.Modal(document.getElementById('pinModal'));
    
    // Set action type
    document.getElementById('modalAction').value = action;
    
    // Configure modal based on action
    const config = {
        submit: {
            title: 'Submit Retur',
            description: 'Masukkan PIN untuk submit retur ke approval',
            buttonText: 'Submit',
            buttonClass: 'btn-primary'
        },
        process: {
            title: 'Proses Retur',
            description: 'Masukkan PIN untuk memulai pemrosesan retur',
            buttonText: 'Proses',
            buttonClass: 'btn-info'
        },
        complete: {
            title: 'Selesaikan Retur',
            description: 'Masukkan PIN untuk menyelesaikan retur',
            buttonText: 'Selesai',
            buttonClass: 'btn-success'
        },
        cancel: {
            title: 'Batalkan Retur',
            description: 'Masukkan PIN untuk membatalkan retur',
            buttonText: 'Batalkan',
            buttonClass: 'btn-dark'
        },
        upload: {
            title: 'Upload Dokumen',
            description: 'Masukkan PIN untuk upload dokumen pendukung',
            buttonText: 'Upload',
            buttonClass: 'btn-primary'
        },
        approve: {
            title: 'Setujui Retur',
            description: 'Masukkan PIN untuk menyetujui retur',
            buttonText: 'Setujui',
            buttonClass: 'btn-success'
        },
        reject: {
            title: 'Tolak Retur',
            description: 'Masukkan PIN untuk menolak retur',
            buttonText: 'Tolak',
            buttonClass: 'btn-danger'
        }
    };
    
    const actionConfig = config[action];
    
    // Update modal content
    document.getElementById('pinModalTitle').textContent = actionConfig.title;
    document.getElementById('pinModalDescription').textContent = actionConfig.description;
    
    const confirmBtn = document.getElementById('confirmPinBtn');
    confirmBtn.className = 'btn ' + actionConfig.buttonClass;
    confirmBtn.innerHTML = `<i class="ri-check-line me-1"></i>${actionConfig.buttonText}`;
    
    resetPinInputs();
    modal.show();
}

// ============================================
// SUBMIT FORMS FROM MODALS (Cancel & Upload)
// ============================================
function submitCancelForm() {
    const reason = document.getElementById('cancelReason').value.trim();
    
    if (!reason) {
        Swal.fire({
            icon: 'warning',
            title: 'Alasan Wajib Diisi',
            text: 'Silakan masukkan alasan pembatalan',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Close cancel modal and show PIN modal
    bootstrap.Modal.getInstance(document.getElementById('cancelModal')).hide();
    
    // Wait for modal transition then show PIN modal
    setTimeout(() => {
        showPinModal('cancel');
    }, 300);
}

function submitUploadForm() {
    const file = document.getElementById('uploadFile').files[0];
    
    if (!file) {
        Swal.fire({
            icon: 'warning',
            title: 'File Belum Dipilih',
            text: 'Silakan pilih file yang akan diupload',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Validate file size (10MB)
    if (file.size > 10 * 1024 * 1024) {
        Swal.fire({
            icon: 'error',
            title: 'File Terlalu Besar',
            text: 'Ukuran file maksimal 10MB',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Close upload modal and show PIN modal
    bootstrap.Modal.getInstance(document.getElementById('uploadModal')).hide();
    
    // Wait for modal transition then show PIN modal
    setTimeout(() => {
        showPinModal('upload');
    }, 300);
}

function submitApproveForm() {
    // Close approve modal and show PIN modal
    bootstrap.Modal.getInstance(document.getElementById('approveModal')).hide();
    
    // Wait for modal transition then show PIN modal
    setTimeout(() => {
        showPinModal('approve');
    }, 300);
}

function submitRejectForm() {
    const catatan = document.getElementById('rejectCatatan').value.trim();
    
    if (!catatan) {
        Swal.fire({
            icon: 'warning',
            title: 'Alasan Wajib Diisi',
            text: 'Silakan masukkan alasan penolakan',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Close reject modal and show PIN modal
    bootstrap.Modal.getInstance(document.getElementById('rejectModal')).hide();
    
    // Wait for modal transition then show PIN modal
    setTimeout(() => {
        showPinModal('reject');
    }, 300);
}

// ============================================
// ACTION HANDLERS (Langsung tampilkan PIN modal)
// ============================================

// ============================================
// PIN CONFIRMATION & VERIFICATION
// ============================================
document.getElementById('confirmPinBtn').addEventListener('click', function() {
    const pin = getPinValue();
    const action = document.getElementById('modalAction').value;
    const btn = this;
    
    if (pin.length !== 6) {
        showPinError();
        Swal.fire({
            icon: 'error',
            title: 'PIN Tidak Lengkap',
            text: 'Silakan masukkan 6 digit PIN',
            timer: 2000,
            showConfirmButton: false
        });
        return;
    }
    
    // Add loading state
    btn.classList.add('btn-loading');
    btn.disabled = true;
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memverifikasi...';
    
    // Verify PIN
    fetch('/api/verify-pin', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ pin: pin })
    })
    .then(response => {
        // Check if response is JSON
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            throw new Error("Server tidak mengembalikan JSON. Mungkin terjadi error atau redirect.");
        }
        return response.json();
    })
    .then(data => {
        btn.classList.remove('btn-loading');
        btn.disabled = false;
        btn.innerHTML = originalHTML;
        
        if (data.success && data.karyawan) {
            verifiedKaryawan = data.karyawan;
            
            // Show karyawan info
            document.getElementById('karyawanNama').textContent = data.karyawan.nama_lengkap;
            document.getElementById('karyawanInfo').style.display = 'block';
            
            // Execute action after short delay
            setTimeout(() => {
                executeAction(action, pin, data.karyawan);
            }, 800);
        } else {
            showPinError();
            Swal.fire({
                icon: 'error',
                title: 'PIN Tidak Valid',
                text: data.message || 'PIN yang Anda masukkan tidak ditemukan',
                confirmButtonText: 'Coba Lagi'
            });
            resetPinInputs();
        }
    })
    .catch(error => {
        btn.classList.remove('btn-loading');
        btn.disabled = false;
        btn.innerHTML = originalHTML;
        showPinError();
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: error.message || 'Terjadi kesalahan sistem',
            confirmButtonText: 'OK'
        });
        console.error('PIN verification error:', error);
    });
});

// ============================================
// EXECUTE ACTION
// ============================================
function executeAction(action, pin, karyawan) {
    bootstrap.Modal.getInstance(document.getElementById('pinModal')).hide();
    
    if (action === 'submit') {
        submitRetur(pin, karyawan);
    } else if (action === 'process') {
        processRetur(pin, karyawan);
    } else if (action === 'complete') {
        completeRetur(pin, karyawan);
    } else if (action === 'cancel') {
        cancelRetur(pin, karyawan);
    } else if (action === 'upload') {
        uploadDocument(pin, karyawan);
    } else if (action === 'approve') {
        approveRetur(pin, karyawan);
    } else if (action === 'reject') {
        rejectRetur(pin, karyawan);
    }
}

function submitRetur(pin, karyawan) {
    Swal.fire({
        title: 'Memproses...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('{{ route("returs.submit", $retur->id_retur) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ 
            pin: pin,
            id_karyawan: karyawan.id_karyawan
        })
    })
    .then(response => {
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            throw new Error("Server tidak mengembalikan JSON");
        }
        return response.json();
    })
    .then(data => {
        if (data.success || data.message) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message || 'Retur berhasil disubmit',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.reload();
            });
        } else {
            throw new Error(data.error || 'Gagal submit retur');
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: error.message || 'Terjadi kesalahan saat submit retur',
            confirmButtonText: 'OK'
        });
    });
}

function processRetur(pin, karyawan) {
    Swal.fire({
        title: 'Memproses...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('{{ route("returs.process", $retur->id_retur) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ 
            pin: pin,
            id_karyawan: karyawan.id_karyawan
        })
    })
    .then(response => {
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            throw new Error("Server tidak mengembalikan JSON");
        }
        return response.json();
    })
    .then(data => {
        if (data.success || data.message) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message || 'Retur mulai diproses',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.reload();
            });
        } else {
            throw new Error(data.error || 'Gagal memproses retur');
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: error.message,
            confirmButtonText: 'OK'
        });
    });
}

function completeRetur(pin, karyawan) {
    Swal.fire({
        title: 'Memproses...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('{{ route("returs.complete", $retur->id_retur) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ 
            pin: pin,
            id_karyawan: karyawan.id_karyawan
        })
    })
    .then(response => {
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            throw new Error("Server tidak mengembalikan JSON");
        }
        return response.json();
    })
    .then(data => {
        if (data.success || data.message) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message || 'Retur berhasil diselesaikan',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.reload();
            });
        } else {
            throw new Error(data.error || 'Gagal menyelesaikan retur');
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: error.message,
            confirmButtonText: 'OK'
        });
    });
}

function cancelRetur(pin, karyawan) {
    const reason = document.getElementById('cancelReason').value.trim();
    
    Swal.fire({
        title: 'Memproses...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('{{ route("returs.cancel", $retur->id_retur) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ 
            pin: pin,
            id_karyawan: karyawan.id_karyawan,
            alasan_pembatalan: reason
        })
    })
    .then(response => {
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            throw new Error("Server tidak mengembalikan JSON");
        }
        return response.json();
    })
    .then(data => {
        if (data.success || data.message) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message || 'Retur berhasil dibatalkan',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.reload();
            });
        } else {
            throw new Error(data.error || 'Gagal membatalkan retur');
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: error.message,
            confirmButtonText: 'OK'
        });
    });
}

function uploadDocument(pin, karyawan) {
    const file = document.getElementById('uploadFile').files[0];
    const type = document.getElementById('uploadType').value;
    const keterangan = document.getElementById('uploadKeterangan').value;
    
    const formData = new FormData();
    formData.append('file', file);
    formData.append('tipe_dokumen', type);
    formData.append('keterangan', keterangan);
    formData.append('pin', pin);
    formData.append('id_karyawan', karyawan.id_karyawan);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    
    Swal.fire({
        title: 'Uploading...',
        text: 'Mohon tunggu',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('{{ route("returs.documents.upload", $retur->id_retur) }}', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            throw new Error("Server tidak mengembalikan JSON");
        }
        return response.json();
    })
    .then(data => {
        if (data.success || data.message) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message || 'Dokumen berhasil diupload',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.reload();
            });
        } else {
            throw new Error(data.error || 'Gagal upload dokumen');
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: error.message,
            confirmButtonText: 'OK'
        });
    });
}

function approveRetur(pin, karyawan) {
    const catatan = document.getElementById('approveCatatan').value.trim();
    
    Swal.fire({
        title: 'Memproses...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('{{ route("returs.approve", $retur->id_retur) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ 
            action: 'approve',
            pin: pin,
            id_karyawan: karyawan.id_karyawan,
            catatan: catatan
        })
    })
    .then(response => {
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            throw new Error("Server tidak mengembalikan JSON");
        }
        return response.json();
    })
    .then(data => {
        if (data.success || data.message) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message || 'Retur berhasil disetujui',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.reload();
            });
        } else {
            throw new Error(data.error || 'Gagal menyetujui retur');
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: error.message,
            confirmButtonText: 'OK'
        });
    });
}

function rejectRetur(pin, karyawan) {
    const catatan = document.getElementById('rejectCatatan').value.trim();
    
    Swal.fire({
        title: 'Memproses...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('{{ route("returs.approve", $retur->id_retur) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ 
            action: 'reject',
            pin: pin,
            id_karyawan: karyawan.id_karyawan,
            catatan: catatan
        })
    })
    .then(response => {
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            throw new Error("Server tidak mengembalikan JSON");
        }
        return response.json();
    })
    .then(data => {
        if (data.success || data.message) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message || 'Retur berhasil ditolak',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.reload();
            });
        } else {
            throw new Error(data.error || 'Gagal menolak retur');
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: error.message,
            confirmButtonText: 'OK'
        });
    });
}

// Clear PIN modal when hidden
document.getElementById('pinModal').addEventListener('hidden.bs.modal', function() {
    if (!verifiedKaryawan) {
        resetPinInputs();
    }
    verifiedKaryawan = null;
    currentAction = '';
});

// Focus first input when modal is shown
document.getElementById('pinModal').addEventListener('shown.bs.modal', function() {
    const firstInput = document.querySelector('.otp-input');
    if (firstInput) {
        firstInput.focus();
    }
});
</script>
@endpush