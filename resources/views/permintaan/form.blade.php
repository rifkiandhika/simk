@if($errors->any())
    @foreach($errors->all() as $error)
        <div class="alert alert-danger">{{ $error }}</div>
    @endforeach
@endif

<style>
    .cursor {
        cursor: pointer;
    }
    .modal-dialog-scrollable {
        height: 90vh;
    }
    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
    .accordion-button:not(.collapsed) {
        background-color: #e7f1ff;
        color: #0d6efd;
    }
    .list-group-item {
        border: 1px solid #dee2e6;
        transition: all 0.2s ease;
    }
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
    .selected-count {
        background: #28a745;
        color: white;
        border-radius: 15px;
        padding: 2px 8px;
        font-size: 0.8em;
        margin-left: 10px;
    }
    .btn-add-products {
        background: linear-gradient(45deg, #007bff, #0056b3);
        border: none;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    .btn-add-products:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
        color: white;
    }
    .btn-add-products:disabled {
        background: #6c757d;
        transform: none;
        box-shadow: none;
    }
</style>

<div class="row mb-3">
    <div class="col-md-3">
        <label>No Requisition</label>
        <input type="text" name="no_requisition" class="form-control" placeholder="e.g. APT-202254"
            value="{{ old('no_requisition', $permintaans->no_requisition ?? '') }}">
    </div>

    <div class="col-md-3">
        <label>Tanggal</label>
        <input type="date" name="tanggal" class="form-control"
            value="{{ old('tanggal', $permintaans->tanggal ?? now()->format('Y-m-d')) }}">
    </div>

    <div class="col-md-3">
        <label>Department</label>
        <select name="department" class="form-control">
            <option value="" hidden>-- Pilih Department --</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->nama_department }}"
                    {{ (isset($detail) && $detail->department_id == $dept->id) ? 'selected' : '' }}>
                    {{ $dept->nama_department }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label>Pembuat</label>
        <input type="text" name="pembuat" class="form-control" placeholder="e.g. Jimi"
            value="{{ old('pembuat', $permintaans->pembuat ?? '') }}">
    </div>
</div>

{{-- Pilih supplier --}}
<div class="row mb-3">
    <div class="col-md-6">
        <label>Pilih Supplier</label>
        <select name="supplier_id" id="supplier_id" class="form-select cursor">
            <option value="" hidden>-- Pilih Supplier --</option>
            @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}">{{ $supplier->supplier }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 d-flex align-items-end">
        <button type="button" class="btn btn-add-products" id="btnOpenProductModal" disabled>
            <i class="ri-add-circle-line me-2"></i>
            Pilih Barang <span class="selected-count" id="selectedCount" style="display: none;">0</span>
        </button>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-bordered" id="tableBarang">
        <thead>
            <tr>
                <th>No Batch</th>
                <th>Nama</th>
                <th>Jenis</th>
                <th>Exp Date</th>
                <th>Stock Gudang</th>
                <th>Jumlah Permintaan</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            {{-- Jika edit, tampilkan data existing --}}
            @if(isset($permintaans) && $permintaans->detailPermintaan->count())
                @foreach($permintaans->detailPermintaan as $detail)
                    <tr data-no_batch="{{ $detail->no_batch }}">
                        <td>
                            <input type="hidden" name="barang_id[]" value="{{ $detail->barang_id }}">
                            <input type="text" name="no_batch[]" class="form-control" value="{{ $detail->no_batch }}" readonly>
                        </td>
                        <td><input type="text" name="nama[]" class="form-control" value="{{ $detail->nama }}" readonly></td>
                        <td><input type="text" name="jenis[]" class="form-control" value="{{ $detail->jenis }}" readonly></td>
                        <td><input type="date" name="exp_date[]" class="form-control" value="{{ $detail->exp_date }}" readonly></td>
                        <td><input type="number" name="stock_gudang[]" class="form-control" value="{{ $detail->stock_gudang }}" readonly></td>
                        <td><input type="number" name="jumlah_permintaan[]" class="form-control" value="{{ $detail->jumlah_permintaan }}"></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove">
                                <i class="ri-subtract-line"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

<!-- Modal Pilih Barang -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="ri-package-line me-2"></i> Pilih Barang dari Gudang
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="supplier-products-modal" class="mb-3">
                    <div class="text-center text-muted py-5">
                        <i class="ri-loader-4-line ri-2x ri-spin"></i>
                        <div class="mt-2">Memuat data produk dari gudang...</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="me-auto">
                    <small class="text-muted">
                        <span id="modalSelectedCount">0</span> barang dipilih
                    </small>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    let selectedProducts = [];

    // 🔹 Load data dari Gudang berdasarkan Supplier
    function loadSupplierProductsModal(supplierId) {
        if (!supplierId) return;

        $('#supplier-products-modal').html(`
            <div class="text-center text-muted py-5">
                <i class="ri-loader-4-line ri-2x ri-spin"></i>
                <div class="mt-2">Memuat data produk dari gudang...</div>
            </div>
        `);

        $.get(`/permintaan/supplier/${supplierId}/details`, function (grouped) {
            let html = '<div class="accordion" id="accordionSupplierProductsModal">';
            let i = 0;

            for (const jenis in grouped) {
                const collapseId = `collapseModal${i}`;
                const headingId = `headingModal${i}`;

                html += `
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="${headingId}">
                            <button class="accordion-button ${i === 0 ? '' : 'collapsed'}" 
                                type="button" data-bs-toggle="collapse" 
                                data-bs-target="#${collapseId}" 
                                aria-expanded="${i === 0 ? 'true' : 'false'}" 
                                aria-controls="${collapseId}">
                                ${jenis}
                            </button>
                        </h2>
                        <div id="${collapseId}" class="accordion-collapse collapse ${i === 0 ? 'show' : ''}">
                            <div class="accordion-body p-2">
                `;

                for (const judul in grouped[jenis]) {
                    html += `<h6 class="mt-2 mb-2 text-primary">${judul}</h6><div class="list-group mb-3">`;

                    grouped[jenis][judul].forEach(d => {
                        const isAlreadyInTable = $(`#tableBarang tbody tr[data-no_batch="${d.no_batch}"]`).length > 0;
                        const isChecked = selectedProducts.some(p => p.no_batch === d.no_batch);

                        html += `
                            <label class="list-group-item cursor ${isAlreadyInTable ? 'bg-light text-muted' : ''}">
                                <input type="checkbox" class="form-check-input me-1 modal-supplier-product"
                                    data-id="${d.id}"
                                    data-no_batch="${d.no_batch}"
                                    data-nama="${d.nama}"
                                    data-judul="${d.judul}"
                                    data-jenis="${d.jenis}"
                                    data-exp_date="${d.exp_date}"
                                    data-stock_gudang="${d.stock_gudang}"
                                    ${isAlreadyInTable ? 'disabled' : ''}
                                    ${isChecked ? 'checked' : ''}>
                                ${d.nama} (Batch: ${d.no_batch}) 
                                <span class="text-success ms-2">Stock: ${d.stock_gudang}</span>
                                ${isAlreadyInTable ? '<span class="badge bg-success ms-2">Sudah ditambahkan</span>' : ''}
                            </label>
                        `;
                    });

                    html += `</div>`;
                }

                html += `</div></div></div>`;
                i++;
            }

            html += '</div>';
            $('#supplier-products-modal').html(html);
            updateSelectedCount();
        }).fail(function() {
            $('#supplier-products-modal').html(`
                <div class="text-center text-danger py-5">
                    <i class="ri-error-warning-line ri-2x"></i>
                    <div class="mt-2">Gagal memuat data produk</div>
                </div>
            `);
        });
    }

    // 🔹 Update counter
    function updateSelectedCount() {
        const count = selectedProducts.length;
        $('#modalSelectedCount').text(count);
        $('#selectedCount').text(count).toggle(count > 0);
    }

    // 🔹 Supplier change
    $('#supplier_id').on('change', function () {
        const supplierId = $(this).val();
        selectedProducts = [];
        $('#btnOpenProductModal').prop('disabled', !supplierId);
        updateSelectedCount();
    });

    // 🔹 Open modal
    $('#btnOpenProductModal').on('click', function () {
        const supplierId = $('#supplier_id').val();
        if (supplierId) {
            loadSupplierProductsModal(supplierId);
            $('#productModal').modal('show');
        }
    });

    // 🔹 Checkbox select
    $(document).on('change', '.modal-supplier-product', function () {
        const product = {
            id: $(this).data('id'),
            no_batch: $(this).data('no_batch'),
            nama: $(this).data('nama'),
            judul: $(this).data('judul'),
            jenis: $(this).data('jenis'),
            exp_date: $(this).data('exp_date'),
            stock_gudang: $(this).data('stock_gudang')
        };

        if ($(this).is(':checked')) {
            if (!selectedProducts.some(p => p.no_batch === product.no_batch)) {
                selectedProducts.push(product);
            }
        } else {
            selectedProducts = selectedProducts.filter(p => p.no_batch !== product.no_batch);
        }

        updateSelectedCount();
    });

    // 🔹 Tambahkan ke tabel
    $('#btnAddSelectedProducts').on('click', function () {
        selectedProducts.forEach(product => {
            if ($(`#tableBarang tbody tr[data-no_batch="${product.no_batch}"]`).length === 0) {
                let row = `
                    <tr data-no_batch="${product.no_batch}">
                        <td>
                            <input type="hidden" name="barang_id[]" value="${product.id}">
                            <input type="text" name="no_batch[]" class="form-control" value="${product.no_batch}" readonly>
                        </td>
                        <td><input type="text" name="nama[]" class="form-control" value="${product.nama}" readonly></td>
                        <td><input type="text" name="jenis[]" class="form-control" value="${product.jenis}" readonly></td>
                        <td><input type="date" name="exp_date[]" class="form-control" value="${product.exp_date}" readonly></td>
                        <td><input type="number" name="stock_gudang[]" class="form-control" value="${product.stock_gudang}" readonly></td>
                        <td><input type="number" name="jumlah_permintaan[]" class="form-control" placeholder="e.g. 10"></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove">
                                <i class="ri-subtract-line"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#tableBarang > tbody').append(row);
            }
        });

        selectedProducts = [];
        updateSelectedCount();
        $('#productModal').modal('hide');
    });

    // 🔹 Remove baris
    $(document).on('click', '.btn-remove', function () {
        $(this).closest('tr').remove();
    });
});
</script>
