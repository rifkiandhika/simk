@extends('layouts.app')

@section('title', 'Input Invoice/Faktur')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('po.index') }}">Purchase Order</a></li>
    <li class="breadcrumb-item"><a href="{{ route('po.show', $po->id_po) }}">Detail PO</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">Input Invoice</li>
@endsection

@section('content')
<div class="app-body">
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i>
            <strong>Terdapat kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-8">
            <form action="{{ route('po.store-invoice', $po->id_po) }}" method="POST" id="formInvoice">
                @csrf
                
                <!-- PO & GR Info -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-success text-white py-3">
                        <h5 class="mb-0">
                            <i class="ri-file-list-3-line me-2"></i>Informasi PO & Good Receive
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="150"><strong>No. PO</strong></td>
                                        <td>: {{ $po->no_po }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>No. GR</strong></td>
                                        <td>: <span class="badge bg-primary">{{ $po->no_gr }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Supplier</strong></td>
                                        <td>: <strong class="text-success">{{ $po->supplier->nama_supplier }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal Diterima</strong></td>
                                        <td>: {{ $po->tanggal_diterima ? \Carbon\Carbon::parse($po->tanggal_diterima)->format('d/m/Y H:i') : '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="150"><strong>Total Item</strong></td>
                                        <td>: {{ $po->items->count() }} produk</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Quantity</strong></td>
                                        <td>: {{ $po->items->sum('qty_diterima') }} unit</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Grand Total</strong></td>
                                        <td>: <strong class="text-primary">Rp {{ number_format($po->grand_total, 0, ',', '.') }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Form -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="ri-file-text-line me-2"></i>Data Invoice/Faktur
                        </h5>
                        <small class="text-muted">Masukkan data invoice yang diterima dari supplier</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    No. Invoice/Faktur <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('no_invoice') is-invalid @enderror" 
                                       name="no_invoice" 
                                       placeholder="Contoh: INV-SUP-001"
                                       value="{{ old('no_invoice') }}"
                                       required>
                                @error('no_invoice')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Nomor invoice dari supplier</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    Tanggal Invoice <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('tanggal_invoice') is-invalid @enderror" 
                                       name="tanggal_invoice" 
                                       value="{{ old('tanggal_invoice', date('Y-m-d')) }}"
                                       required>
                                @error('tanggal_invoice')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Tanggal invoice dari supplier</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    Tanggal Jatuh Tempo <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('tanggal_jatuh_tempo') is-invalid @enderror" 
                                       name="tanggal_jatuh_tempo" 
                                       id="tanggalJatuhTempo"
                                       value="{{ old('tanggal_jatuh_tempo') }}"
                                       required>
                                @error('tanggal_jatuh_tempo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Tanggal pembayaran harus dilakukan</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    Nomor Faktur Pajak
                                </label>
                                <input type="text" 
                                       class="form-control @error('nomor_faktur_pajak') is-invalid @enderror" 
                                       name="nomor_faktur_pajak" 
                                       placeholder="Contoh: 010.000-25.00000001"
                                       value="{{ old('nomor_faktur_pajak') }}">
                                @error('nomor_faktur_pajak')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Opsional, jika ada faktur pajak</small>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="ri-information-line me-2"></i>
                            <strong>Informasi:</strong> Data invoice ini akan digunakan untuk proses pembayaran dan pencatatan keuangan.
                        </div>
                    </div>
                </div>

                <!-- PIN Confirmation -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-warning py-3">
                        <h5 class="mb-0">
                            <i class="ri-lock-line me-2"></i>Konfirmasi PIN
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning mb-3">
                            <i class="ri-shield-check-line me-2"></i>
                            <strong>Keamanan:</strong> Masukkan PIN 6 digit Anda untuk mengonfirmasi data invoice ini.
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">PIN (6 digit) <span class="text-danger">*</span></label>
                            <input type="password" 
                                   class="form-control @error('pin') is-invalid @enderror" 
                                   name="pin" 
                                   id="pin"
                                   maxlength="6" 
                                   placeholder="Masukkan PIN 6 digit"
                                   required>
                            @error('pin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-3">
                    <button type="submit" class="btn btn-success btn-lg" id="btnSubmit">
                        <i class="ri-save-line me-1"></i> Simpan Invoice
                    </button>
                    <a href="{{ route('po.show', $po->id_po) }}" class="btn btn-outline-secondary">
                        <i class="ri-arrow-left-line me-1"></i> Kembali
                    </a>
                </div>
            </form>
        </div>

        <!-- Right Column - Info -->
        <div class="col-xl-4">
            <!-- Items Summary -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="ri-shopping-cart-line me-2"></i>Ringkasan Item</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($po->items as $item)
                                <tr>
                                    <td>
                                        <small>{{ $item->nama_produk }}</small>
                                        @if($item->batches->count() > 0)
                                            <br><span class="badge badge-sm bg-secondary">{{ $item->batches->count() }} batch</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ $item->qty_diterima }}</span>
                                    </td>
                                    <td class="text-end">
                                        <small>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="2">Total</th>
                                    <th class="text-end">Rp {{ number_format($po->total_harga, 0, ',', '.') }}</th>
                                </tr>
                                @if($po->pajak > 0)
                                <tr>
                                    <th colspan="2">Pajak</th>
                                    <th class="text-end">Rp {{ number_format($po->pajak, 0, ',', '.') }}</th>
                                </tr>
                                @endif
                                <tr class="table-success">
                                    <th colspan="2">Grand Total</th>
                                    <th class="text-end">Rp {{ number_format($po->grand_total, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="ri-information-line text-info me-2"></i>Informasi Penting
                    </h6>
                    <ul class="small mb-0">
                        <li class="mb-2">Pastikan nomor invoice sesuai dengan dokumen dari supplier</li>
                        <li class="mb-2">Tanggal jatuh tempo digunakan untuk reminder pembayaran</li>
                        <li class="mb-2">Faktur pajak diisi jika supplier mengeluarkan faktur pajak</li>
                        <li class="mb-2">Data invoice dapat diedit sebelum pembayaran dilakukan</li>
                        <li class="text-success"><strong>Setelah disimpan, PO ini siap untuk proses pembayaran</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Auto calculate due date (default 14 days, can be customized per supplier terms)
    $('input[name="tanggal_invoice"]').on('change', function() {
        let invoiceDate = new Date($(this).val());
        if (invoiceDate && !$('#tanggalJatuhTempo').val()) {
            invoiceDate.setDate(invoiceDate.getDate() + 14); // Default 14 days terms
            let dueDate = invoiceDate.toISOString().split('T')[0];
            $('#tanggalJatuhTempo').val(dueDate);
        }
    });
    
    // Validate due date must be after invoice date
    $('#tanggalJatuhTempo').on('change', function() {
        let invoiceDate = new Date($('input[name="tanggal_invoice"]').val());
        let dueDate = new Date($(this).val());
        
        if (dueDate < invoiceDate) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Tanggal jatuh tempo tidak boleh lebih awal dari tanggal invoice',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            $(this).val('');
        }
    });
    
    // Form validation
    $('#formInvoice').on('submit', function(e) {
        e.preventDefault();
        
        const pin = $('#pin').val();
        
        if (!pin || pin.length !== 6) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'PIN harus 6 digit'
            });
            return false;
        }
        
        const noInvoice = $('input[name="no_invoice"]').val();
        const tanggalInvoice = $('input[name="tanggal_invoice"]').val();
        const tanggalJatuhTempo = $('input[name="tanggal_jatuh_tempo"]').val();
        
        // Show confirmation
        const form = this;
        
        Swal.fire({
            title: 'Konfirmasi Data Invoice',
            html: `
                <div class="text-start">
                    <p>Pastikan data invoice sudah benar:</p>
                    <ul>
                        <li><strong>No. Invoice:</strong> ${noInvoice}</li>
                        <li><strong>Tanggal Invoice:</strong> ${tanggalInvoice}</li>
                        <li><strong>Jatuh Tempo:</strong> ${tanggalJatuhTempo}</li>
                        <li><strong>Grand Total:</strong> Rp {{ number_format($po->grand_total, 0, ',', '.') }}</li>
                    </ul>
                    <p class="text-success"><strong>Data akan disimpan dan PO siap untuk pembayaran</strong></p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Periksa Lagi'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Mohon tunggu, sedang menyimpan data invoice',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                form.submit();
            }
        });
    });
});
</script>
@endpush