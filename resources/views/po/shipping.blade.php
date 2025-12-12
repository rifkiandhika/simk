@extends('layouts.app')

@section('title', 'Shipping Tracking')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Tracking Pengiriman</li>
@endsection

@section('content')
<div class="app-body">
    
    {{-- Header --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-1 fw-bold">Tracking Pengiriman</h2>
                    <p class="text-muted mb-0">Monitor pengiriman PO dari supplier</p>
                </div>
                <div class="col-md-4 text-end">
                    <h1 class="text-primary mb-0 fw-bold">{{ $activeShipments }}</h1>
                    <p class="text-muted mb-0">Sedang Dikirim</p>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Shipping List --}}
    <div class="row">
        @forelse($purchaseOrders as $po)
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header {{ $po->status === 'dalam_pengiriman' ? 'bg-primary bg-opacity-10' : 'bg-light' }} py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <a href="{{ route('po.show', $po->id_po) }}" class="h5 mb-1 text-decoration-none text-dark fw-bold">
                                {{ $po->no_po }}
                            </a>
                            <p class="text-muted mb-0 small">{{ $po->supplier->nama_supplier }}</p>
                        </div>
                        @php
                            $statusColors = [
                                'dikirim_ke_supplier' => 'bg-info text-white',
                                'dalam_pengiriman' => 'bg-primary text-white',
                            ];
                        @endphp
                        <span class="badge {{ $statusColors[$po->status] ?? 'bg-secondary' }} px-3 py-2">
                            {{ $po->status }}
                        </span>
                    </div>
                </div>
                
                <div class="card-body">
                    {{-- Timeline --}}
                    <div class="timeline">
                        @foreach($po->shippingActivities->sortByDesc('tanggal_aktivitas')->take(3) as $activity)
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0 me-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center {{ $loop->first ? 'bg-primary' : 'bg-primary bg-opacity-25' }}" 
                                     style="width: 36px; height: 36px;">
                                    @if($loop->first)
                                    <i class="ri-record-circle-fill text-white"></i>
                                    @else
                                    <i class="ri-checkbox-circle-line text-primary"></i>
                                    @endif
                                </div>
                                @if(!$loop->last)
                                <div class="border-start border-2 border-primary ms-3" style="height: 40px; opacity: 0.3;"></div>
                                @endif
                            </div>
                            
                            <div class="flex-grow-1">
                                <p class="fw-semibold mb-1">{{ ucwords(str_replace('_', ' ', $activity->status_shipping)) }}</p>
                                <p class="text-muted small mb-1">{{ $activity->deskripsi_aktivitas }}</p>
                                <p class="text-muted small mb-0">
                                    <i class="ri-time-line me-1"></i>
                                    {{ \Carbon\Carbon::parse($activity->tanggal_aktivitas)->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                        
                        @if($po->shippingActivities->count() > 3)
                        <a href="{{ route('po.show', $po->id_po) }}" class="btn btn-link btn-sm p-0 text-decoration-none">
                            Lihat semua aktivitas ({{ $po->shippingActivities->count() }})
                            <i class="ri-arrow-right-line ms-1"></i>
                        </a>
                        @endif
                    </div>
                    
                    {{-- Actions --}}
                    <div class="row g-2 mt-3 pt-3 border-top">
                        <div class="col-6">
                            <button type="button" 
                                    class="btn btn-primary w-100"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#updateShippingModal"
                                    onclick="openUpdateShipping('{{ $po->id_po }}', '{{ $po->no_po }}')">
                                <i class="ri-add-line me-1"></i> Update Status
                            </button>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('po.show', $po->id_po) }}" class="btn btn-outline-secondary w-100">
                                <i class="ri-eye-line me-1"></i> Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <i class="ri-truck-line" style="font-size: 4rem; color: #dee2e6;"></i>
                    </div>
                    <h5 class="text-muted">Tidak ada pengiriman aktif</h5>
                    <p class="text-muted small">PO yang sedang dalam pengiriman akan muncul di sini</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
    
    {{-- Pagination --}}
    @if($purchaseOrders->hasPages())
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-body">
            {{ $purchaseOrders->links() }}
        </div>
    </div>
    @endif
</div>

{{-- Update Shipping Modal --}}
<div class="modal fade" id="updateShippingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <div>
                    <h5 class="modal-title">Update Status Pengiriman</h5>
                    <p class="mb-0 small opacity-75" id="modalPoInfo">Tambahkan informasi terbaru pengiriman</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <form id="updateShippingForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="modalPoId" name="id_po">
                <input type="hidden" name="id_karyawan_input" value="{{ auth()->user()->id_karyawan ?? '' }}">
                
                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Status --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Status Pengiriman <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" name="status_shipping" id="modalStatus" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="persiapan">📦 Persiapan</option>
                                <option value="dikemas">📮 Dikemas</option>
                                <option value="dalam_perjalanan">🚚 Dalam Perjalanan</option>
                                <option value="tiba_di_tujuan">📍 Tiba di Tujuan</option>
                                <option value="diterima">✅ Diterima</option>
                                <option value="selesai">🎉 Selesai</option>
                            </select>
                        </div>
                        
                        {{-- Deskripsi --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Deskripsi Aktivitas <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" name="deskripsi_aktivitas" id="modalDeskripsi" rows="4" required
                                      placeholder="Contoh: Barang telah dikirim melalui ekspedisi JNE dengan nomor resi..."></textarea>
                            <div class="form-text">Jelaskan detail aktivitas pengiriman secara lengkap</div>
                        </div>
                        
                        {{-- Catatan --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Catatan Tambahan</label>
                            <input type="text" class="form-control" name="catatan" id="modalCatatan"
                                   placeholder="No resi, nama kurir, estimasi tiba, dll (opsional)">
                        </div>
                        
                        {{-- Tanggal Aktivitas --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Tanggal & Waktu Aktivitas</label>
                            <input type="datetime-local" class="form-control" name="tanggal_aktivitas" id="modalTanggal">
                            <div class="form-text">Kosongkan untuk menggunakan waktu saat ini</div>
                        </div>
                        
                        {{-- Foto Bukti --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Foto Bukti Pengiriman</label>
                            <input type="file" class="form-control" name="foto_bukti" id="modalFoto" accept="image/jpeg,image/jpg,image/png">
                            <div class="form-text">Format: JPG, PNG • Ukuran maksimal: 2MB</div>
                            <div id="fotoPreview" class="mt-2" style="display: none;">
                                <img id="previewImage" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                            </div>
                        </div>
                        
                        {{-- PIN --}}
                        <div class="col-12">
                            <div class="border-top pt-3">
                                <label class="form-label fw-semibold">
                                    <i class="ri-lock-line me-1"></i> PIN Konfirmasi <span class="text-danger">*</span>
                                </label>
                                <input type="password" class="form-control" name="pin" id="modalPin" 
                                       maxlength="6" required placeholder="••••••" inputmode="numeric">
                                <div class="form-text">Masukkan 6 digit PIN untuk verifikasi</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                        <i class="ri-save-line me-1"></i> Simpan Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentPoId = null;
let currentPoNo = null;

function openUpdateShipping(poId, poNo) {
    currentPoId = poId;
    currentPoNo = poNo;
    
    document.getElementById('modalPoId').value = poId;
    document.getElementById('modalPoInfo').textContent = `PO: ${poNo}`;
    
    // Set default datetime to now
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    document.getElementById('modalTanggal').value = now.toISOString().slice(0, 16);
    
    // Reset form
    document.getElementById('updateShippingForm').reset();
    document.getElementById('modalPoId').value = poId;
    
    // Hide preview
    document.getElementById('fotoPreview').style.display = 'none';
}

// Preview image before upload
document.getElementById('modalFoto')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    
    if (file) {
        // Validate file size
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'File Terlalu Besar',
                text: 'Ukuran file maksimal 2MB'
            });
            e.target.value = '';
            document.getElementById('fotoPreview').style.display = 'none';
            return;
        }
        
        // Validate file type
        if (!['image/jpeg', 'image/jpg', 'image/png'].includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Format File Tidak Valid',
                text: 'Format file harus JPG atau PNG'
            });
            e.target.value = '';
            document.getElementById('fotoPreview').style.display = 'none';
            return;
        }
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImage').src = e.target.result;
            document.getElementById('fotoPreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        document.getElementById('fotoPreview').style.display = 'none';
    }
});

// PIN input only numbers
document.getElementById('modalPin')?.addEventListener('input', function(e) {
    this.value = this.value.replace(/\D/g, '').substring(0, 6);
});

// Form submission
document.getElementById('updateShippingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const pin = document.getElementById('modalPin').value;
    const status = document.getElementById('modalStatus').value;
    const deskripsi = document.getElementById('modalDeskripsi').value;
    const idKaryawan = document.querySelector('input[name="id_karyawan_input"]').value;
    
    // Validasi
    if (!idKaryawan) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Session user tidak ditemukan. Silakan login ulang.'
        });
        return;
    }
    
    if (!pin || pin.length !== 6) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'PIN harus 6 digit angka'
        });
        return;
    }
    
    if (!status) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Status pengiriman harus dipilih'
        });
        return;
    }
    
    if (!deskripsi || deskripsi.trim() === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Deskripsi aktivitas harus diisi'
        });
        return;
    }
    
    const formData = new FormData(this);
    
    // Debug: log form data
    console.log('Form Data:');
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
    
    // Disable submit button
    const btnSubmit = document.getElementById('btnSubmit');
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';
    
    // Show loading
    Swal.fire({
        title: 'Menyimpan...',
        text: 'Mohon tunggu',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('{{ route("shipping.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        // Clone response untuk debugging
        return response.json().then(data => {
            return {
                ok: response.ok,
                status: response.status,
                data: data
            };
        });
    })
    .then(result => {
        Swal.close();
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = '<i class="ri-save-line me-1"></i> Simpan Update';
        
        console.log('Response:', result);
        
        if (result.ok && result.data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('updateShippingModal'));
            modal.hide();
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: result.data.message || 'Status pengiriman berhasil diupdate',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        } else {
            let errorMessage = 'Gagal update status pengiriman';
            
            if (result.data.errors) {
                // Validation errors
                const errors = Object.values(result.data.errors).flat();
                errorMessage = errors.join('\n');
            } else if (result.data.message) {
                errorMessage = result.data.message;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: errorMessage,
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        Swal.close();
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = '<i class="ri-save-line me-1"></i> Simpan Update';
        
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan pada server. Silakan coba lagi atau hubungi administrator.',
            confirmButtonText: 'OK'
        });
    });
});

// Reset form when modal is closed
document.getElementById('updateShippingModal')?.addEventListener('hidden.bs.modal', function () {
    document.getElementById('updateShippingForm').reset();
    document.getElementById('fotoPreview').style.display = 'none';
});
</script>
@endpush