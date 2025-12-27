@extends('layouts.app')

@section('title', 'Buat Purchase Order')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('po.index') }}">Purchase Order</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">Buat PO {{ ucfirst($type) }}</li>
@endsection

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
    }
    .select2-container {
        width: 100% !important;
    }
    .batch-info {
        font-size: 0.75rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }
    .stock-badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.5rem;
    }
    .item-row {
        animation: fadeIn 0.3s;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="app-body">
    {{-- Alert Messages --}}
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

    <form action="{{ route('po.store') }}" method="POST" id="formPO">
        @csrf
        <input type="hidden" name="tipe_po" value="{{ $type }}">
        <input type="hidden" name="unit_pemohon" value="{{ $type === 'internal' ? 'apotik' : 'gudang' }}">
        <input type="hidden" name="id_unit_pemohon" value="{{ auth()->user()->id_karyawan ?? '' }}">

        <div class="row">
            <!-- Left Column - Form -->
            <div class="col-xl-8">
                <!-- Informasi Umum -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0">
                            <i class="ri-information-line me-2"></i>Informasi Purchase Order
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="ri-information-line me-2"></i>
                            @if($type === 'internal')
                                <strong>PO Internal:</strong> Permintaan barang dari <strong>Apotik</strong> ke <strong>Gudang</strong><br>
                                <small class="text-muted">
                                    <i class="ri-checkbox-circle-line me-1"></i> Hanya memerlukan persetujuan Kepala Gudang<br>
                                    <i class="ri-checkbox-circle-line me-1"></i> Stok otomatis ditransfer ke Apotik setelah disetujui
                                </small>
                            @else
                                <strong>PO Eksternal:</strong> Permintaan barang dari <strong>Gudang</strong> ke <strong>Supplier</strong><br>
                                <small class="text-muted">
                                    <i class="ri-checkbox-circle-line me-1"></i> Memerlukan persetujuan Kepala Gudang dan Kasir
                                </small>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="ri-user-line me-1"></i> Pemohon
                                </label>
                                <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="ri-building-line me-1"></i> Unit Pemohon
                                </label>
                                <input type="text" class="form-control" value="{{ $type === 'internal' ? 'Apotik' : 'Gudang' }}" disabled>
                            </div>
                        </div>

                        @if($type === 'eksternal')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="ri-store-line me-1"></i> Supplier <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('id_supplier') is-invalid @enderror" 
                                        name="id_supplier" id="supplier" required>
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('id_supplier') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->nama_supplier }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_supplier')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="ri-percent-line me-1"></i> Pajak (Rp)
                                </label>
                                <input type="number" 
                                       class="form-control @error('pajak') is-invalid @enderror" 
                                       name="pajak" 
                                       id="pajak"
                                       value="{{ old('pajak', 0) }}" 
                                       min="0" 
                                       placeholder="0">
                                @error('pajak')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="ri-chat-3-line me-1"></i> Catatan
                            </label>
                            <textarea class="form-control @error('catatan_pemohon') is-invalid @enderror" 
                                      name="catatan_pemohon" 
                                      rows="3" 
                                      placeholder="Tambahkan catatan untuk PO ini...">{{ old('catatan_pemohon') }}</textarea>
                            @error('catatan_pemohon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Item PO -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="ri-shopping-cart-line me-2"></i>Item Purchase Order
                            </h5>
                            <button type="button" class="btn btn-sm btn-primary" onclick="addItem()">
                                <i class="ri-add-line me-1"></i>Tambah Item
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="itemTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Produk <span class="text-danger">*</span></th>
                                        <th width="120">Harga</th>
                                        <th width="100">Qty <span class="text-danger">*</span></th>
                                        <th width="150" class="text-end">Subtotal</th>
                                        <th width="80" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="itemTableBody">
                                    <!-- Items will be added here dynamically -->
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="4" class="text-end">Total:</th>
                                        <th class="text-end" id="totalHarga">Rp 0</th>
                                        <th></th>
                                    </tr>
                                    @if($type === 'eksternal')
                                    <tr>
                                        <th colspan="4" class="text-end">Pajak:</th>
                                        <th class="text-end" id="totalPajak">Rp 0</th>
                                        <th></th>
                                    </tr>
                                    @endif
                                    <tr class="table-primary">
                                        <th colspan="4" class="text-end">Grand Total:</th>
                                        <th class="text-end" id="grandTotal">Rp 0</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
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
                            <strong>Keamanan:</strong> Masukkan PIN 6 digit Anda untuk mengonfirmasi pembuatan PO ini.
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
            </div>

            <!-- Right Column - Summary & Actions -->
            <div class="col-xl-4">
                <!-- Summary Card -->
                <div class="card shadow-sm border-0 mb-4 position-sticky" style="top: 20px;">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="ri-calculator-line me-2"></i>Ringkasan</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Item:</span>
                            <strong id="summaryItemCount">0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Quantity:</span>
                            <strong id="summaryTotalQty">0</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <strong id="summarySubtotal">Rp 0</strong>
                        </div>
                        @if($type === 'eksternal')
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Pajak:</span>
                            <strong id="summaryPajak">Rp 0</strong>
                        </div>
                        @endif
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Grand Total:</span>
                            <h5 class="text-success mb-0" id="summaryGrandTotal">Rp 0</h5>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="ri-save-line me-1"></i> Simpan PO
                            </button>
                            <a href="{{ route('po.index') }}" class="btn btn-outline-secondary">
                                <i class="ri-arrow-left-line me-1"></i> Batal
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">
                            <i class="ri-information-line text-info me-2"></i>Informasi
                        </h6>
                        <ul class="small mb-0">
                            <li class="mb-2">Pastikan semua item yang dipilih sudah benar</li>
                            <li class="mb-2">Quantity yang diisi adalah jumlah yang diminta</li>
                            @if($type === 'internal')
                                <li class="mb-2"><strong class="text-success">Produk ditampilkan per batch dengan stock gudang</strong></li>
                                <li class="mb-2 text-success"><strong>PO Internal hanya memerlukan approval Kepala Gudang</strong></li>
                                <li class="mb-2 text-success"><strong>Stok otomatis ditransfer dari Gudang ke Apotik</strong></li>
                            @else
                                <li class="mb-2">Harga akan otomatis terisi dari master data produk</li>
                                <li class="mb-2">PO Eksternal memerlukan approval dari Kepala Gudang dan Kasir</li>
                            @endif
                            <li>PIN diperlukan untuk keamanan transaksi</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let itemCounter = 0;
    const produkData = @json($produkList);
    const isInternal = {{ $type === 'internal' ? 'true' : 'false' }};

    // Filter produk by supplier if eksternal
    @if($type === 'eksternal')
    const supplierSelect = document.getElementById('supplier');
    let filteredProduk = [];

    supplierSelect.addEventListener('change', function() {
        const supplierId = this.value;
        if (supplierId) {
            filteredProduk = produkData.filter(p => p.supplier_id === supplierId);
        } else {
            filteredProduk = [];
        }
        
        // Clear existing items
        document.getElementById('itemTableBody').innerHTML = '';
        itemCounter = 0;
        calculateTotal();
    });
    @else
    let filteredProduk = produkData;
    @endif

    function addItem() {
        @if($type === 'eksternal')
        if (!document.getElementById('supplier').value) {
            Swal.fire('Perhatian', 'Pilih supplier terlebih dahulu', 'warning');
            return;
        }
        @endif

        if (filteredProduk.length === 0) {
            Swal.fire('Perhatian', 'Tidak ada produk tersedia di gudang', 'warning');
            return;
        }

        itemCounter++;
        const tbody = document.getElementById('itemTableBody');
        const row = document.createElement('tr');
        row.className = 'item-row';
        row.id = `item-${itemCounter}`;

        row.innerHTML = `
            <td class="text-center">${itemCounter}</td>
            <td>
                <select class="form-select form-select-sm select2-produk" 
                        name="items[${itemCounter}][id_produk]" 
                        id="produk-${itemCounter}"
                        onchange="updatePrice(${itemCounter})" required>
                    <option value="">-- Pilih Produk --</option>
                </select>
                <div class="batch-info" id="batch-info-${itemCounter}"></div>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm text-end" 
                       id="harga-${itemCounter}" readonly value="0">
                <input type="hidden" name="items[${itemCounter}][harga]" id="harga-val-${itemCounter}" value="0">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm" 
                       name="items[${itemCounter}][qty_diminta]" 
                       id="qty-${itemCounter}"
                       min="1" value="1" 
                       onchange="calculateSubtotal(${itemCounter})" required>
                <small class="text-muted" id="max-qty-${itemCounter}"></small>
            </td>
            <td class="text-end">
                <strong id="subtotal-${itemCounter}">Rp 0</strong>
                <input type="hidden" id="subtotal-val-${itemCounter}" value="0">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${itemCounter})">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </td>
        `;

        tbody.appendChild(row);

        // Initialize Select2 for this item
        initializeSelect2(itemCounter);
    }

    function initializeSelect2(itemId) {
        const selectElement = $(`#produk-${itemId}`);
        
        // Populate options
        filteredProduk.forEach(p => {
            let optionText = '';
            let optionHtml = '';
            
            if (isInternal) {
                // Internal: Show batch info
                optionText = `${p.nama} - Batch: ${p.no_batch} (Stock: ${p.stock_gudang})`;
                optionHtml = `
                    <div>
                        <strong>${p.nama}</strong> ${p.merk ? `- ${p.merk}` : ''}
                        <br>
                        <small class="text-muted">
                            <span class="badge bg-info stock-badge">Batch: ${p.no_batch}</span>
                            <span class="badge bg-success stock-badge">Stock: ${p.stock_gudang}</span>
                            <span class="badge bg-warning stock-badge text-dark">Exp: ${p.tanggal_kadaluarsa}</span>
                        </small>
                    </div>
                `;
            } else {
                // External: Standard display
                optionText = `${p.nama} - ${p.merk || ''} (${p.satuan})`;
                optionHtml = `<div><strong>${p.nama}</strong> - ${p.merk || ''} (${p.satuan})</div>`;
            }
            
            const newOption = new Option(optionText, p.id, false, false);
            $(newOption).data('produk', p);
            selectElement.append(newOption);
        });

        // Initialize Select2
        selectElement.select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Pilih Produk --',
            allowClear: true,
            templateResult: formatProduk,
            templateSelection: formatProdukSelection
        });
    }

    function formatProduk(produk) {
        if (!produk.id) return produk.text;
        
        const data = $(produk.element).data('produk');
        if (!data) return produk.text;

        if (isInternal) {
            return $(`
                <div>
                    <strong>${data.nama}</strong> ${data.merk ? `- ${data.merk}` : ''}
                    <br>
                    <small class="text-muted">
                        <span class="badge bg-info stock-badge">Batch: ${data.no_batch}</span>
                        <span class="badge bg-success stock-badge">Stock: ${data.stock_gudang}</span>
                        <span class="badge bg-warning stock-badge text-dark">Exp: ${data.tanggal_kadaluarsa}</span>
                    </small>
                </div>
            `);
        } else {
            return $(`<div><strong>${data.nama}</strong> - ${data.merk || ''} (${data.satuan})</div>`);
        }
    }

    function formatProdukSelection(produk) {
        if (!produk.id) return produk.text;
        const data = $(produk.element).data('produk');
        if (!data) return produk.text;
        return `${data.nama} ${isInternal ? `- Batch: ${data.no_batch}` : ''}`;
    }

    function updatePrice(itemId) {
        const selectElement = $(`#produk-${itemId}`);
        const selectedOption = selectElement.find(':selected');
        const data = selectedOption.data('produk');
        
        if (!data) {
            document.getElementById(`harga-${itemId}`).value = '0';
            document.getElementById(`harga-val-${itemId}`).value = '0';
            document.getElementById(`batch-info-${itemId}`).innerHTML = '';
            document.getElementById(`max-qty-${itemId}`).textContent = '';
            calculateSubtotal(itemId);
            return;
        }

        const harga = parseFloat(data.harga_beli || 0);
        document.getElementById(`harga-${itemId}`).value = formatRupiah(harga);
        document.getElementById(`harga-val-${itemId}`).value = harga;

        // Show batch info for internal PO
        if (isInternal) {
            const batchInfo = document.getElementById(`batch-info-${itemId}`);
            batchInfo.innerHTML = `
                <span class="badge bg-info stock-badge">Batch: ${data.no_batch}</span>
                <span class="badge bg-success stock-badge">Stock Tersedia: ${data.stock_gudang}</span>
                <span class="badge bg-warning stock-badge text-dark">Exp: ${data.tanggal_kadaluarsa}</span>
            `;
            
            // Set max quantity
            const qtyInput = document.getElementById(`qty-${itemId}`);
            qtyInput.max = data.stock_gudang;
            document.getElementById(`max-qty-${itemId}`).textContent = `Max: ${data.stock_gudang}`;
            
            // Validate current quantity
            if (parseInt(qtyInput.value) > data.stock_gudang) {
                qtyInput.value = data.stock_gudang;
            }
        }

        calculateSubtotal(itemId);
    }

    function calculateSubtotal(itemId) {
        const harga = parseFloat(document.getElementById(`harga-val-${itemId}`).value || 0);
        const qty = parseInt(document.getElementById(`qty-${itemId}`).value || 0);
        
        // Validate quantity for internal PO
        if (isInternal) {
            const selectElement = $(`#produk-${itemId}`);
            const selectedOption = selectElement.find(':selected');
            const data = selectedOption.data('produk');
            
            if (data && qty > data.stock_gudang) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Quantity Melebihi Stock',
                    text: `Stock tersedia: ${data.stock_gudang}`,
                    confirmButtonText: 'OK'
                });
                document.getElementById(`qty-${itemId}`).value = data.stock_gudang;
                return calculateSubtotal(itemId);
            }
        }
        
        const subtotal = harga * qty;

        document.getElementById(`subtotal-${itemId}`).textContent = 'Rp ' + formatRupiah(subtotal);
        document.getElementById(`subtotal-val-${itemId}`).value = subtotal;

        calculateTotal();
    }

    function removeItem(itemId) {
        Swal.fire({
            title: 'Hapus Item?',
            text: 'Item akan dihapus dari PO',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const row = document.getElementById(`item-${itemId}`);
                row.remove();
                calculateTotal();
                renumberRows();
            }
        });
    }

    function renumberRows() {
        const rows = document.querySelectorAll('#itemTableBody tr');
        rows.forEach((row, index) => {
            row.querySelector('td:first-child').textContent = index + 1;
        });
    }

    function calculateTotal() {
        const subtotals = document.querySelectorAll('[id^="subtotal-val-"]');
        let total = 0;
        let itemCount = 0;
        let totalQty = 0;

        subtotals.forEach(input => {
            if (input.value) {
                total += parseFloat(input.value || 0);
                itemCount++;
                
                const itemId = input.id.split('-')[2];
                const qtyInput = document.getElementById(`qty-${itemId}`);
                if (qtyInput) {
                    totalQty += parseInt(qtyInput.value || 0);
                }
            }
        });

        const pajak = parseFloat(document.getElementById('pajak')?.value || 0);
        const grandTotal = total + pajak;

        // Update table footer
        document.getElementById('totalHarga').textContent = 'Rp ' + formatRupiah(total);
        @if($type === 'eksternal')
        document.getElementById('totalPajak').textContent = 'Rp ' + formatRupiah(pajak);
        @endif
        document.getElementById('grandTotal').textContent = 'Rp ' + formatRupiah(grandTotal);

        // Update summary
        document.getElementById('summaryItemCount').textContent = itemCount;
        document.getElementById('summaryTotalQty').textContent = totalQty;
        document.getElementById('summarySubtotal').textContent = 'Rp ' + formatRupiah(total);
        @if($type === 'eksternal')
        document.getElementById('summaryPajak').textContent = 'Rp ' + formatRupiah(pajak);
        @endif
        document.getElementById('summaryGrandTotal').textContent = 'Rp ' + formatRupiah(grandTotal);
    }

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }

    // Event listener for pajak
    @if($type === 'eksternal')
    document.getElementById('pajak')?.addEventListener('input', calculateTotal);
    @endif

    // Form validation
    document.getElementById('formPO').addEventListener('submit', function(e) {
        const itemCount = document.querySelectorAll('#itemTableBody tr').length;
        
        if (itemCount === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Tambahkan minimal 1 item untuk melanjutkan'
            });
            return false;
        }

        const pin = document.getElementById('pin').value;
        if (!pin || pin.length !== 6) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'PIN harus 6 digit'
            });
            return false;
        }

        // Show loading
        Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });

    // Add first item on load
    document.addEventListener('DOMContentLoaded', function() {
        @if($type === 'internal')
        addItem();
        @endif
    });
</script>
@endpush