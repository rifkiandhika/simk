@extends('layouts.app')

@section('title', 'Edit Retur')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('returs.index') }}">Retur</a></li>
    <li class="breadcrumb-item"><a href="{{ route('returs.show', $retur->id_retur) }}">{{ $retur->no_retur }}</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">Edit</li>
@endsection

@section('content')
<div class="app-body">
    {{-- Alert Messages --}}
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="ri-error-warning-line me-2"></i>
        <h5>Terdapat kesalahan:</h5>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="ri-checkbox-circle-line me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('returs.update', $retur->id_retur) }}" method="POST" id="returForm">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Left Column -->
            <div class="col-md-8">
                <!-- Informasi Retur -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="ri-information-line me-2"></i>Informasi Retur
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">No Retur</label>
                                <input type="text" class="form-control" value="{{ $retur->no_retur }}" readonly>
                                <small class="text-muted">No retur tidak dapat diubah</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Status</label>
                                <div>
                                    @php
                                        $statusBadge = [
                                            'draft' => 'secondary',
                                            'menunggu_persetujuan' => 'warning',
                                            'disetujui' => 'success',
                                            'ditolak' => 'danger',
                                            'diproses' => 'info',
                                            'selesai' => 'success',
                                            'dibatalkan' => 'dark'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusBadge[$retur->status] ?? 'secondary' }} fs-6 px-3 py-2">
                                        {{ ucwords(str_replace('_', ' ', $retur->status)) }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tipe Retur</label>
                                <input type="text" class="form-control" 
                                       value="{{ $retur->tipe_retur == 'po' ? 'Purchase Order' : 'Stock Apotik' }}" readonly>
                                <small class="text-muted">Tipe retur tidak dapat diubah</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kode Referensi</label>
                                <input type="text" class="form-control" value="{{ $retur->kode_referensi }}" readonly>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tanggal Retur <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_retur" class="form-control" 
                                       value="{{ $retur->tanggal_retur instanceof \Carbon\Carbon ? $retur->tanggal_retur->format('Y-m-d') : $retur->tanggal_retur }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Alasan Retur <span class="text-danger">*</span></label>
                                <select name="alasan_retur" class="form-select" required>
                                    <option value="">Pilih Alasan</option>
                                    <option value="barang_rusak" {{ $retur->alasan_retur == 'barang_rusak' ? 'selected' : '' }}>Barang Rusak</option>
                                    <option value="barang_kadaluarsa" {{ $retur->alasan_retur == 'barang_kadaluarsa' ? 'selected' : '' }}>Barang Kadaluarsa</option>
                                    <option value="barang_tidak_sesuai" {{ $retur->alasan_retur == 'barang_tidak_sesuai' ? 'selected' : '' }}>Barang Tidak Sesuai</option>
                                    <option value="kelebihan_pengiriman" {{ $retur->alasan_retur == 'kelebihan_pengiriman' ? 'selected' : '' }}>Kelebihan Pengiriman</option>
                                    <option value="kesalahan_order" {{ $retur->alasan_retur == 'kesalahan_order' ? 'selected' : '' }}>Kesalahan Order</option>
                                    <option value="kualitas_tidak_baik" {{ $retur->alasan_retur == 'kualitas_tidak_baik' ? 'selected' : '' }}>Kualitas Tidak Baik</option>
                                    <option value="lainnya" {{ $retur->alasan_retur == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Keterangan Alasan</label>
                                <textarea name="keterangan_alasan" class="form-control" rows="3" 
                                          placeholder="Jelaskan detail alasan retur...">{{ $retur->keterangan_alasan }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Item Retur -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="ri-box-3-line me-2"></i>Item Retur
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="ri-information-line me-2"></i>
                            <strong>Perhatian:</strong> Item retur tidak dapat diubah saat edit. Jika ingin mengubah item, silakan buat retur baru.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Produk</th>
                                        <th width="12%">Batch</th>
                                        <th width="12%">Qty Retur</th>
                                        <th width="15%">Harga Satuan</th>
                                        <th width="12%">Kondisi</th>
                                        <th width="15%">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($retur->returItems as $index => $item)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $item->nama_produk }}</strong>
                                            @if($item->batches && $item->batches->isNotEmpty())
                                            <br>
                                            <small class="text-muted">
                                                @foreach($item->batches as $batch)
                                                    <span class="badge bg-secondary me-1">
                                                        Batch: {{ $batch->batch_number }} - Qty: {{ $batch->qty_diretur }}
                                                    </span>
                                                @endforeach
                                            </small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->batches && $item->batches->isNotEmpty())
                                                @foreach($item->batches as $batch)
                                                    <span class="badge bg-secondary">{{ $batch->batch_number }}</span><br>
                                                @endforeach
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <strong>{{ number_format($item->qty_diretur, 0, ',', '.') }}</strong>
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
                                            <strong class="text-success">
                                                Rp {{ number_format($item->qty_diretur * $item->harga_satuan, 0, ',', '.') }}
                                            </strong>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="ri-inbox-line fs-3"></i>
                                            <p class="mb-0">Tidak ada item retur</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                @if($retur->returItems->isNotEmpty())
                                <tfoot class="table-secondary">
                                    <tr>
                                        <th colspan="6" class="text-end">TOTAL:</th>
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
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-4">
                <!-- Informasi Pelapor -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="ri-user-line me-2"></i>Informasi Pelapor
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Pelapor</label>
                            <input type="text" class="form-control" 
                                   value="{{ $retur->karyawanPelapor->nama_lengkap ?? 'N/A' }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">NIP</label>
                            <input type="text" class="form-control" 
                                   value="{{ $retur->karyawanPelapor->nip ?? 'N/A' }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Unit Pelapor</label>
                            <input type="text" class="form-control" 
                                   value="{{ ucfirst($retur->unit_pelapor) }}" readonly>
                        </div>

                        <small class="text-muted d-block">
                            <i class="ri-information-line me-1"></i>
                            Data pelapor tidak dapat diubah
                        </small>
                    </div>
                </div>

                <!-- Informasi Tujuan -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="ri-building-line me-2"></i>Informasi Tujuan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Unit Tujuan</label>
                            <select name="unit_tujuan" class="form-select">
                                <option value="">Pilih Tujuan</option>
                                <option value="gudang" {{ $retur->unit_tujuan == 'gudang' ? 'selected' : '' }}>Gudang</option>
                                <option value="supplier" {{ $retur->unit_tujuan == 'supplier' ? 'selected' : '' }}>Supplier</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Supplier (jika ke supplier)</label>
                            <select name="id_supplier" class="form-select" id="supplierSelect">
                                <option value="">Pilih Supplier</option>
                                @foreach($suppliers ?? [] as $supplier)
                                    <option value="{{ $supplier->id }}" 
                                            {{ $retur->id_supplier == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->nama_supplier ?? $supplier->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Catatan -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="ri-sticky-note-line me-2"></i>Catatan
                        </h5>
                    </div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control" rows="4" 
                                  placeholder="Catatan tambahan...">{{ $retur->catatan }}</textarea>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" id="btnSubmit">
                                <i class="ri-save-line me-1"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('returs.show', $retur->id_retur) }}" class="btn btn-outline-secondary">
                                <i class="ri-arrow-left-line me-1"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
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
        font-size: 0.85rem;
    }

    .table td {
        vertical-align: middle;
    }

    .form-control:disabled,
    .form-control[readonly] {
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Auto hide alerts
        setTimeout(function() {
            $('.alert:not(.alert-info)').fadeOut('slow');
        }, 5000);

        // Handle unit tujuan change
        $('select[name="unit_tujuan"]').on('change', function() {
            const value = $(this).val();
            const supplierSelect = $('#supplierSelect');
            
            if (value === 'supplier') {
                supplierSelect.prop('disabled', false);
                supplierSelect.closest('.mb-3').show();
            } else {
                supplierSelect.prop('disabled', true);
                supplierSelect.val('');
                if (value === 'gudang') {
                    supplierSelect.closest('.mb-3').hide();
                }
            }
        });

        // Trigger on load
        $('select[name="unit_tujuan"]').trigger('change');

        // Form validation
        $('#returForm').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const submitBtn = $('#btnSubmit');
            
            // Konfirmasi
            Swal.fire({
                title: 'Konfirmasi Perubahan',
                text: 'Apakah Anda yakin ingin menyimpan perubahan retur ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="ri-check-line me-1"></i> Ya, Simpan',
                cancelButtonText: '<i class="ri-close-line me-1"></i> Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Disable button dan ubah text
                    submitBtn.prop('disabled', true);
                    submitBtn.html('<i class="ri-loader-4-line ri-spin me-1"></i>Menyimpan...');
                    
                    // Submit form
                    form.off('submit').submit();
                }
            });
        });

        // Prevent accidental navigation
        let formChanged = false;
        
        $('#returForm input, #returForm select, #returForm textarea').on('change', function() {
            formChanged = true;
        });

        $(window).on('beforeunload', function() {
            if (formChanged) {
                return 'Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman?';
            }
        });

        $('#returForm').on('submit', function() {
            formChanged = false;
        });
    });
</script>
@endpush