    @extends('layouts.app')

@section('title', isset($stock) ? 'Edit Stock Apotik' : 'Tambah Stock Apotik')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('stock_apotiks.index') }}">Stock Apotik</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">{{ isset($stock) ? 'Edit' : 'Tambah' }}</li>
@endsection

@section('content')
<div class="app-body">
    <div class="row">
        <div class="col-xl-12">
            @if($errors->any())
                @foreach($errors->all() as $error)
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ $error }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endforeach
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ isset($stock) ? route('stock_apotiks.update', $stock->id) : route('stock_apotiks.store') }}" 
                method="POST" id="formStockApotik">
                @csrf
                @if(isset($stock))
                    @method('PUT')
                @endif

                <!-- Header Information -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="ri-file-list-3-line me-2"></i>Informasi Stock</h5>
                    </div>
                    <div class="card-body">
                        @if(isset($stock))
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Kode Transaksi</label>
                                <input type="text" class="form-control bg-light" value="{{ $stock->kode_transaksi }}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Gudang <span class="text-danger">*</span></label>
                                <input type="text" class="form-control bg-light" 
                                    value="{{ $stock->gudang->nama_gudang }}" readonly>
                                <input type="hidden" name="gudang_id" value="{{ $stock->gudang_id }}">
                                <input type="hidden" id="gudang_id" value="{{ $stock->gudang_id }}">
                                <small class="text-muted">Gudang tidak dapat diubah</small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Supplier</label>
                                <input type="text" id="supplier_name" class="form-control bg-light" readonly 
                                    value="{{ $stock->gudang->supplier->nama_supplier ?? '-' }}">
                                <small class="text-muted">Informasi saja, tidak disimpan</small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Tanggal Penerimaan <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="tanggal_penerimaan" class="form-control"
                                    value="{{ old('tanggal_penerimaan', \Carbon\Carbon::parse($stock->tanggal_penerimaan)->format('Y-m-d\TH:i')) }}" required>
                            </div>
                        </div>
                        @else
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Pilih Gudang <span class="text-danger">*</span></label>
                                <select name="gudang_id" id="gudang_id" class="form-select" required>
                                    <option value="" hidden>-- Pilih Gudang --</option>
                                    @foreach($gudangs as $gudang)
                                        <option value="{{ $gudang->id }}" 
                                                data-supplier="{{ $gudang->supplier->nama_supplier ?? '-' }}"
                                                {{ old('gudang_id') == $gudang->id ? 'selected' : '' }}>
                                            {{ $gudang->nama_gudang }} - {{ $gudang->supplier->nama_supplier ?? 'Tanpa Supplier' }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Supplier akan ditampilkan otomatis sesuai gudang</small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Supplier</label>
                                <input type="text" id="supplier_name" class="form-control bg-light" readonly 
                                    placeholder="Pilih gudang terlebih dahulu">
                                <small class="text-muted">Informasi saja, tidak disimpan</small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Tanggal Penerimaan <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="tanggal_penerimaan" class="form-control"
                                    value="{{ old('tanggal_penerimaan', now()->format('Y-m-d\TH:i')) }}" required>
                            </div>
                        </div>
                        @endif
                        
                        <div class="row mb-3">
                            <div class="col-md-9">
                                <label class="form-label fw-bold">Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="2" 
                                        placeholder="Keterangan tambahan (opsional)">{{ old('keterangan', $stock->keterangan ?? '') }}</textarea>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="button" class="btn {{ isset($stock) ? 'btn-success' : 'btn-primary' }} w-100" 
                                        id="btnOpenProductModal" {{ !isset($stock) ? 'disabled' : '' }}>
                                    <i class="ri-add-circle-line me-2"></i>{{ isset($stock) ? 'Tambah Barang Baru' : 'Pilih Barang' }}
                                    <span class="badge {{ isset($stock) ? 'bg-light text-success' : 'bg-light text-primary' }} ms-2" 
                                          id="selectedCount" style="display: none;">0</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daftar Barang yang Sudah Ada (Edit Mode) -->
                @if(isset($stock))
                <div class="card mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="ri-archive-line me-2"></i>Barang yang Sudah Ada</h5>
                        <span class="badge bg-primary">{{ $stock->details->count() }} item</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">No</th>
                                        <th width="100">No Batch</th>
                                        <th>Nama Barang</th>
                                        <th width="100">Jenis</th>
                                        <th width="100">Tipe Barang</th>
                                        <th width="80">Satuan</th>
                                        <th width="120">Exp Date</th>
                                        <th width="130">Stock Apotik</th>
                                        <th width="100">Min Stock</th>
                                        <th width="100">Retur</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stock->details as $index => $detail)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td><span class="badge bg-info">{{ $detail->no_batch }}</span></td>
                                        <td>
                                            <strong>{{ $detail->detailSupplier->nama ?? '-' }}</strong>
                                            @if(isset($detail->detailSupplier->judul) && $detail->detailSupplier->judul != '-')
                                                <br><small class="text-muted">{{ $detail->detailSupplier->judul }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $detail->detailSupplier->jenis ?? '-' }}</td>
                                        <td><span class="badge bg-secondary">{{ strtolower($detail->detailSupplier->jenis ?? 'obat') }}</span></td>
                                        <td>{{ $detail->detailSupplier->satuan ?? '-' }}</td>
                                        <td>{{ $detail->tanggal_kadaluarsa ? \Carbon\Carbon::parse($detail->tanggal_kadaluarsa)->format('d/m/Y') : '-' }}</td>
                                        <td class="text-center"><strong class="text-primary">{{ number_format($detail->stock_apotik) }}</strong></td>
                                        <td class="text-center">{{ number_format($detail->min_persediaan) }}</td>
                                        <td class="text-center">{{ number_format($detail->retur) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Daftar Barang yang Dipilih (untuk Create dan Edit - Barang Baru) -->
                <div class="card mb-4">
                    <div class="card-header {{ isset($stock) ? 'bg-success text-white' : 'bg-light' }}">
                        <h5 class="mb-0">
                            <i class="ri-shopping-cart-line me-2"></i>
                            {{ isset($stock) ? 'Barang Baru yang Ditambahkan' : 'Daftar Barang yang Dipilih' }}
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0" id="tableBarang">
                                <thead class="{{ isset($stock) ? 'table-success' : 'table-light' }}">
                                    <tr>
                                        <th width="50">No</th>
                                        <th width="100">No Batch</th>
                                        <th>Nama Barang</th>
                                        <th width="100">Jenis</th>
                                        <th width="100">Tipe Barang</th>
                                        <th width="80">Satuan</th>
                                        <th width="120">Exp Date</th>
                                        <th width="130">Stock Gudang</th>
                                        <th width="130">Stock Apotik <span class="text-danger">*</span></th>
                                        <th width="100">Min Stock</th>
                                        <th width="100">Retur</th>
                                        <th width="80" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="emptyRow">
                                        <td colspan="12" class="text-center py-5 text-muted">
                                            <i class="ri-inbox-line ri-3x d-block mb-3"></i>
                                            <p class="mb-0">{{ isset($stock) ? 'Belum ada barang baru yang ditambahkan' : 'Belum ada barang yang dipilih' }}</p>
                                            <small>{{ isset($stock) ? 'Klik tombol "Tambah Barang Baru"' : 'Pilih gudang dan klik tombol "Pilih Barang"' }}</small>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('stock_apotiks.index') }}" class="btn btn-secondary">
                                <i class="ri-arrow-left-line me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary" id="btnSubmit">
                                <i class="ri-save-line me-2"></i>{{ isset($stock) ? 'Update Stock Apotik' : 'Simpan Stock Apotik' }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Pilih Barang -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="ri-package-line me-2"></i>Pilih Barang dari Gudang</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="ri-information-line me-2"></i>
                    <strong>Petunjuk:</strong> Pilih barang yang ingin ditambahkan ke stock apotik. 
                    Hanya barang dengan kondisi <strong>Baik</strong> dan stock > 0 yang ditampilkan.
                </div>
                
                <!-- Filter/Search -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" id="searchProduct" class="form-control" placeholder="🔍 Cari barang...">
                    </div>
                    <div class="col-md-3">
                        <select id="filterJenis" class="form-select">
                            <option value="">Semua Jenis</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="filterTipeBarang" class="form-select">
                            <option value="">Semua Tipe</option>
                            <option value="obat">Obat</option>
                            <option value="alkes">Alkes</option>
                            <option value="reagensia">Reagensia</option>
                        </select>
                    </div>
                </div>
                
                <!-- Product List -->
                <div id="productsContainer">
                    <div class="text-center text-muted py-5">
                        <i class="ri-loader-4-line ri-2x ri-spin"></i>
                        <div class="mt-2">Memuat data produk...</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <div class="me-auto">
                    <strong class="text-primary">
                        <span id="modalSelectedCount">0</span> barang dipilih
                    </strong>
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i>Batal
                </button>
                <button type="button" class="btn btn-primary" id="btnAddSelectedProducts">
                    <i class="ri-check-line me-1"></i>Tambahkan Barang
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .product-item {
        padding: 12px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        margin-bottom: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .product-item:hover:not(.disabled) {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }
    .product-item.disabled {
        background-color: #e9ecef;
        cursor: not-allowed;
        opacity: 0.6;
    }
    .product-item.selected {
        background-color: #e7f1ff;
        border-color: #0d6efd;
    }
    .badge-type {
        font-size: 0.75rem;
        padding: 4px 8px;
    }
    .empty-state {
        text-align: center;
        padding: 40px;
        color: #6c757d;
    }
    .empty-state i {
        font-size: 3rem;
        opacity: 0.3;
    }
    .table {
        border: 1px solid #ced4da !important;
    }
</style>
@endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
document.addEventListener('DOMContentLoaded', function () {
    let selectedProducts = [];
    let allProducts = [];
    
    // Update supplier name when gudang changes
    $('#gudang_id').on('change', function() {
        const supplierName = $(this).find(':selected').data('supplier') || '-';
        $('#supplier_name').val(supplierName);
        
        const gudangId = $(this).val();
        $('#btnOpenProductModal').prop('disabled', !gudangId);
        
        if (!gudangId) {
            selectedProducts = [];
            updateSelectedCount();
        }
    });
    
    // Trigger on page load if gudang already selected
    if ($('#gudang_id').val()) {
        $('#gudang_id').trigger('change');
    }
    
    // Open modal
    $('#btnOpenProductModal').on('click', function() {
        const gudangId = $('#gudang_id').val();
        if (gudangId) {
            selectedProducts = [];
            loadGudangProducts(gudangId);
            $('#productModal').modal('show');
        }
    });
    
    // Load products from gudang
    function loadGudangProducts(gudangId) {
        $('#productsContainer').html(`
            <div class="text-center text-muted py-5">
                <i class="ri-loader-4-line ri-2x ri-spin"></i>
                <div class="mt-2">Memuat data produk...</div>
            </div>
        `);
        
        console.log('Loading products for gudang:', gudangId);
        
        $.get(`/stock-apotik/gudang/${gudangId}/details`, function(response) {
            console.log('Raw Response:', response);
            
            // PERBAIKAN: Handle response format baru dengan key 'data'
            if (response.success === false) {
                console.error('API returned error:', response.error || response.message);
                $('#productsContainer').html(`
                    <div class="text-center text-danger py-5">
                        <i class="ri-error-warning-line ri-3x"></i>
                        <div class="mt-3"><strong>Error</strong></div>
                        <small class="text-muted">${response.error || response.message || 'Gagal memuat data'}</small>
                    </div>
                `);
                return;
            }
            
            // PERBAIKAN: Langsung assign dari response.data (format flat array)
            allProducts = response.data || [];
            
            console.log('Total products loaded:', allProducts.length);
            console.log('Sample product:', allProducts[0]);
            
            if (allProducts.length === 0) {
                $('#productsContainer').html(`
                    <div class="text-center text-warning py-5">
                        <i class="ri-inbox-line ri-3x"></i>
                        <div class="mt-3"><strong>Tidak ada barang tersedia</strong></div>
                        <small class="text-muted">Belum ada barang di gudang ini dengan kondisi baik dan stock > 0</small>
                    </div>
                `);
                return;
            }
            
            // Populate filter jenis
            const jenisSet = new Set(allProducts.map(p => p.jenis).filter(Boolean));
            $('#filterJenis').html('<option value="">Semua Jenis</option>');
            jenisSet.forEach(j => {
                $('#filterJenis').append(`<option value="${j}">${j}</option>`);
            });
            
            renderProducts();
        }).fail(function(xhr, status, error) {
            console.error('AJAX Error:', error);
            console.error('Status:', xhr.status);
            console.error('Response Text:', xhr.responseText);
            
            let errorMessage = 'Silakan coba lagi';
            try {
                const errorObj = JSON.parse(xhr.responseText);
                errorMessage = errorObj.error || errorObj.message || errorMessage;
            } catch(e) {
                errorMessage = xhr.responseText || errorMessage;
            }
            
            $('#productsContainer').html(`
                <div class="text-center text-danger py-5">
                    <i class="ri-error-warning-line ri-3x"></i>
                    <div class="mt-3"><strong>Gagal memuat data</strong></div>
                    <small class="text-muted">${errorMessage}</small>
                </div>
            `);
        });
    }
    
    // Render products
    function renderProducts() {
        const search = $('#searchProduct').val().toLowerCase();
        const filterJenis = $('#filterJenis').val();
        const filterTipeBarang = $('#filterTipeBarang').val();
        
        let filtered = allProducts.filter(p => {
            // PERBAIKAN: Tambahkan null check
            const matchSearch = (p.nama && p.nama.toLowerCase().includes(search)) || 
                            (p.no_batch && p.no_batch.toLowerCase().includes(search));
            const matchJenis = !filterJenis || (p.jenis && p.jenis === filterJenis);
            const matchTipe = !filterTipeBarang || (p.barang_type && p.barang_type === filterTipeBarang);
            const notInTable = !$(`#tableBarang tbody tr[data-detail-id="${p.id}"]`).length;
            
            return matchSearch && matchJenis && matchTipe && notInTable;
        });
        
        console.log('Filtered products:', filtered.length);
        
        if (filtered.length === 0) {
            $('#productsContainer').html(`
                <div class="text-center text-muted py-4">
                    <i class="ri-search-line ri-2x"></i>
                    <div class="mt-2">Tidak ada barang yang sesuai filter</div>
                </div>
            `);
            return;
        }
        
        let html = '';
        filtered.forEach(product => {
            const isSelected = selectedProducts.some(p => p.id === product.id);
            const expDate = product.exp_date ? new Date(product.exp_date).toLocaleDateString('id-ID') : '-';
            
            html += `
                <div class="product-item ${isSelected ? 'selected' : ''}" data-product-id="${product.id}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-check flex-grow-1">
                            <input class="form-check-input product-checkbox" type="checkbox" 
                                data-id="${product.id}" ${isSelected ? 'checked' : ''}>
                            <label class="form-check-label">
                                <strong>${product.nama || 'Nama tidak tersedia'}</strong>
                                <div class="small text-muted mt-1">
                                    <span class="badge badge-type bg-secondary">${product.barang_type || '-'}</span>
                                    <span class="badge badge-type bg-info">${product.jenis || '-'}</span>
                                    Batch: ${product.no_batch || '-'}
                                </div>
                            </label>
                        </div>
                        <div class="text-end">
                            <div class="small">
                                <strong class="text-primary">Stock: ${product.stock_gudang || 0}</strong>
                            </div>
                            <div class="small text-muted">Exp: ${expDate}</div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        $('#productsContainer').html(html);
    }
    
    // Search and filter
    $('#searchProduct, #filterJenis, #filterTipeBarang').on('input change', function() {
        renderProducts();
    });
    
    // Select product
    $(document).on('change', '.product-checkbox', function(e) {
        // Stop propagation agar tidak trigger click pada parent
        e.stopPropagation();
        
        const productId = $(this).data('id');
        const product = allProducts.find(p => p.id === productId);
        
        if ($(this).is(':checked')) {
            if (!selectedProducts.some(p => p.id === productId)) {
                selectedProducts.push(product);
            }
            $(this).closest('.product-item').addClass('selected');
        } else {
            selectedProducts = selectedProducts.filter(p => p.id !== productId);
            $(this).closest('.product-item').removeClass('selected');
        }
        
        updateSelectedCount();
    });

    // NEW: Click pada product-item untuk toggle checkbox
    $(document).on('click', '.product-item', function(e) {
        // Jangan toggle jika user klik langsung pada checkbox atau label
        if ($(e.target).hasClass('product-checkbox') || 
            $(e.target).hasClass('form-check-input') ||
            $(e.target).closest('.form-check-input').length) {
            return;
        }
        
        const checkbox = $(this).find('.product-checkbox');
        const productId = checkbox.data('id');
        const product = allProducts.find(p => p.id === productId);
        
        // Toggle checkbox
        checkbox.prop('checked', !checkbox.is(':checked'));
        
        // Update selected products
        if (checkbox.is(':checked')) {
            if (!selectedProducts.some(p => p.id === productId)) {
                selectedProducts.push(product);
            }
            $(this).addClass('selected');
        } else {
            selectedProducts = selectedProducts.filter(p => p.id !== productId);
            $(this).removeClass('selected');
        }
        
        updateSelectedCount();
    });
    
    // Update selected count
    function updateSelectedCount() {
        const count = selectedProducts.length;
        $('#modalSelectedCount').text(count);
        $('#selectedCount').text(count).toggle(count > 0);
    }
    
    // Add selected products to table
    $('#btnAddSelectedProducts').on('click', function() {
        if (selectedProducts.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Silakan pilih minimal 1 barang'
            });
            return;
        }
        
        $('#emptyRow').remove();
        
        selectedProducts.forEach((product, index) => {
            const rowNumber = $('#tableBarang tbody tr:not(#emptyRow)').length + 1;
            const expDate = product.exp_date || '';
            const expDateFormatted = expDate ? new Date(expDate).toLocaleDateString('id-ID') : '-';
            
            const row = `
                <tr data-detail-id="${product.id}">
                    <td class="text-center">${rowNumber}</td>
                    <td>
                        <input type="hidden" name="detail_gudang_id[]" value="${product.id}">
                        <span class="badge bg-info">${product.no_batch || '-'}</span>
                    </td>
                    <td><strong>${product.nama || 'Nama tidak tersedia'}</strong></td>
                    <td>${product.jenis || '-'}</td>
                    <td><span class="badge bg-secondary">${product.barang_type || '-'}</span></td>
                    <td>${product.satuan || 'Unit'}</td>
                    <td>${expDateFormatted}</td>
                    <td class="text-center"><strong>${product.stock_gudang || 0}</strong></td>
                    <td>
                        <input type="number" name="stock_apotik[]" class="form-control form-control-sm" 
                            placeholder="Jumlah" min="1" max="${product.stock_gudang || 0}" required>
                    </td>
                    <td>
                        <input type="number" name="min_persediaan[]" class="form-control form-control-sm" 
                            placeholder="Min" value="${product.min_persediaan || 0}" min="0">
                    </td>
                    <td>
                        <input type="number" name="retur[]" class="form-control form-control-sm" 
                            placeholder="Retur" value="0" min="0">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger btn-remove">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </td>
                </tr>
            `;
            
            $('#tableBarang tbody').append(row);
        });
        
        selectedProducts = [];
        updateSelectedCount();
        $('#productModal').modal('hide');
        
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Barang berhasil ditambahkan',
            timer: 2000,
            showConfirmButton: false
        });
    });
    
    // Remove item
    $(document).on('click', '.btn-remove', function() {
        $(this).closest('tr').remove();
        
        // Renumber rows
        $('#tableBarang tbody tr:not(#emptyRow)').each(function(index) {
            $(this).find('td:first').text(index + 1);
        });
        
        if ($('#tableBarang tbody tr:not(#emptyRow)').length === 0) {
            $('#tableBarang tbody').html(`
                <tr id="emptyRow">
                    <td colspan="12" class="text-center py-5 text-muted">
                        <i class="ri-inbox-line ri-3x d-block mb-3"></i>
                        <p class="mb-0">Belum ada barang yang dipilih</p>
                        <small>Pilih gudang dan klik tombol "Pilih Barang"</small>
                    </td>
                </tr>
            `);
        }
    });
    
    // Form validation
    $('#formStockApotik').on('submit', function(e) {
        const rowCount = $('#tableBarang tbody tr:not(#emptyRow)').length;
        
        if (rowCount === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Silakan pilih minimal 1 barang'
            });
            return false;
        }
        
        // Validate stock_apotik
        let hasError = false;
        let errorMessages = [];
        
        $('#tableBarang tbody tr:not(#emptyRow)').each(function(index) {
            const $row = $(this);
            const stockApotikInput = $row.find('input[name="stock_apotik[]"]');
            const stockApotik = parseInt(stockApotikInput.val()) || 0;
            const stockGudang = parseInt($row.find('td:eq(7)').text().replace(/[^0-9]/g, '')) || 0;
            const namaBarang = $row.find('td:eq(2)').text().trim();
            
            // Reset validation state
            stockApotikInput.removeClass('is-invalid');
            
            if (stockApotik <= 0) {
                hasError = true;
                stockApotikInput.addClass('is-invalid');
                errorMessages.push(`Baris ${index + 1} (${namaBarang}): Stock apotik harus lebih dari 0`);
            } else if (stockApotik > stockGudang) {
                hasError = true;
                stockApotikInput.addClass('is-invalid');
                errorMessages.push(`Baris ${index + 1} (${namaBarang}): Stock apotik (${stockApotik}) tidak boleh melebihi stock gudang (${stockGudang})`);
            }
        });
        
        if (hasError) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: errorMessages.join('<br>'),
                confirmButtonText: 'OK'
            });
            return false;
        }
        
        // Disable submit button to prevent double submission
        $('#btnSubmit').prop('disabled', true).html('<i class="ri-loader-4-line ri-spin me-2"></i>Menyimpan...');
        
        return true;
    });
});
</script>
    @endpush