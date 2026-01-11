@if($errors->any())
    @foreach($errors->all() as $error)
        <div class="alert alert-danger">{{ $error }}</div>
    @endforeach
@endif

<style>
    .cursor { cursor: pointer; }
    .modal-dialog-scrollable { height: 90vh; }
    .modal-body { max-height: 70vh; overflow-y: auto; }
    .accordion-button:not(.collapsed) {
        background-color: #e7f1ff;
        color: #0d6efd;
    }
    .list-group-item { border: 1px solid #dee2e6; transition: all 0.2s ease; }
    .list-group-item:hover { background-color: #f8f9fa; }
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
    <div class="col-md-6">
        <label>Kode Gudang <span class="text-danger">*</span></label>
        <input type="text" name="kode_gudang" class="form-control"
               value="{{ old('kode_gudang', $gudang->kode_gudang ?? '') }}"
               placeholder="e.g. GDG-001" required>
    </div>

    <div class="col-md-6">
        <label>Nama Gudang <span class="text-danger">*</span></label>
        <input type="text" name="nama_gudang" class="form-control"
               value="{{ old('nama_gudang', $gudang->nama_gudang ?? '') }}"
               placeholder="e.g. Gudang Pusat" required>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label>Pilih Supplier <span class="text-danger">*</span></label>
        <select name="supplier_id" id="supplier_id" class="form-select cursor">
            <option value="" hidden>-- Pilih Supplier --</option>
            @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}"
                    {{ old('supplier_id', $gudang->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                    {{ $supplier->nama_supplier }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label>Lokasi</label>
        <input type="text" name="lokasi" class="form-control"
               value="{{ old('lokasi', $gudang->lokasi ?? '') }}"
               placeholder="e.g. Lantai 1, Gedung A">
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label>Penanggung Jawab</label>
        <input type="text" name="penanggung_jawab" class="form-control"
               value="{{ old('penanggung_jawab', $gudang->penanggung_jawab ?? '') }}"
               placeholder="Nama penanggung jawab">
    </div>

    <div class="col-md-6">
        <label>Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select" required>
            <option value="Aktif" {{ old('status', $gudang->status ?? 'Aktif') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="Nonaktif" {{ old('status', $gudang->status ?? '') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
        </select>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label>Keterangan</label>
        <textarea name="keterangan" class="form-control" rows="3" 
                  placeholder="Keterangan tambahan tentang gudang">{{ old('keterangan', $gudang->keterangan ?? '') }}</textarea>
    </div>
</div>

<hr>

<div class="row mb-3">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detail Barang</h5>
        <button type="button" class="btn btn-add-products" id="btnOpenProductModal" 
                {{ isset($gudang) && $gudang->supplier_id ? '' : 'disabled' }}>
            <i class="ri-add-circle-line me-2"></i>
            Pilih Barang
            <span class="selected-count" id="selectedCount" style="display: none;">0</span>
        </button>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-bordered" id="tableGudang">
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Tipe</th>
                <th>Exp Date</th>
                <th>No Batch</th>
                <th>Stock Gudang</th>
                <th>Min Persediaan</th>
                <th>Tanggal Masuk</th>
                <th>Lokasi Rak</th>
                <th>Kondisi</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            {{-- Data edit --}}
            @if(isset($gudang) && $gudang->details->count())
                @foreach($gudang->details as $detail)
                    <tr data-id="{{ $detail->barang_id }}" data-type="{{ $detail->barang_type }}">
                      @php
                        $barang = null;
                        $nama   = '-';
                        $jenis  = '-';
                        $exp    = null;

                        if ($detail->barang_type === 'obat') {
                            $barang = $detail->barangObat;
                            $nama   = $barang->nama_obat_rs ?? '-';
                            $jenis  = 'Obat';
                            $exp    = $detail->tanggal_kadaluarsa ?? null;

                        } elseif ($detail->barang_type === 'alkes') {
                            $barang = $detail->alkes;
                            $nama   = $barang->nama_alkes ?? '-';
                            $jenis  = 'Alkes';
                            $exp    = $barang->exp_date ?? null;

                        } elseif ($detail->barang_type === 'reagensia') {
                            $barang = $detail->reagensia;
                            $nama   = $barang->nama_reagensia ?? '-';
                            $jenis  = 'Reagensia';
                            $exp    = $barang->exp_date ?? null;

                        } else {
                            $jenis = ucfirst($detail->barang_type); // fallback
                        }
                    @endphp
                        <td>
                            <input type="hidden" name="barang_id[]" value="{{ $detail->barang_id }}">
                            <input type="hidden" name="barang_type[]" value="{{ $detail->barang_type }}">
                            <input type="text" class="form-control" value="{{ $nama }}" readonly>
                        </td>
                        <td><input type="text" class="form-control" value="{{ $jenis }}" readonly></td>
                        <td><input type="date" class="form-control" value="{{ $exp }}" readonly></td>
                        <td><input type="text" name="no_batch[]" class="form-control" 
                                   value="{{ $detail->no_batch }}" placeholder="No Batch"></td>
                        <td><input type="number" name="stock_gudang[]" class="form-control" 
                                   value="{{ $detail->stock_gudang }}" min="0" required></td>
                        <td><input type="number" name="min_persediaan[]" class="form-control" 
                                   value="{{ $detail->min_persediaan }}" min="0" required></td>
                        <td><input type="date" name="tanggal_masuk[]" class="form-control" 
                                   value="{{ $detail->tanggal_masuk }}"></td>
                        <td><input type="text" name="lokasi_rak[]" class="form-control" 
                                   value="{{ $detail->lokasi_rak }}" placeholder="A1-B2"></td>
                        <td>
                            <select name="kondisi[]" class="form-select">
                                <option value="Baik" {{ $detail->kondisi == 'Baik' ? 'selected' : '' }}>Baik</option>
                                <option value="Rusak" {{ $detail->kondisi == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                                <option value="Kadaluarsa" {{ $detail->kondisi == 'Kadaluarsa' ? 'selected' : '' }}>Kadaluarsa</option>
                            </select>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove">
                                <i class="ri-subtract-line"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr id="emptyStateRow">
                    <td colspan="10" class="text-center py-5">
                        <div class="empty-state">
                            <i class="ri-inbox-line" style="font-size: 3rem; color: #6c757d;"></i>
                            <h5 class="mt-3 text-muted">Belum Ada Detail Barang</h5>
                            <p class="text-muted mb-3">Silakan pilih supplier terlebih dahulu, kemudian klik tombol "Pilih Barang" untuk menambahkan barang ke gudang.</p>
                            <div class="d-flex justify-content-center align-items-center gap-2">
                                <i class="ri-arrow-up-line text-primary"></i>
                                <small class="text-primary fw-bold">Klik tombol "Pilih Barang" di atas</small>
                            </div>
                        </div>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

<!-- Modal Pilih Barang -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="productModalLabel">
          <i class="ri-search-line me-2"></i> Cari Barang dari Supplier
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">

        <!-- Search Box -->
        <div class="input-group mb-3">
          <input type="text" id="searchProductInput" class="form-control" placeholder="Ketik nama / jenis / judul barang...">
          <button class="btn btn-outline-light bg-primary" type="button" id="btnSearchProduct">
            <i class="ri-search-line"></i>
          </button>
        </div>

        <!-- Hasil Pencarian -->
        <div id="searchResultsContainer" class="list-group" style="max-height:60vh;overflow:auto;">
          <div class="text-center text-muted py-5">
            <i class="ri-information-line"></i> Ketik minimal 2 huruf untuk mencari barang...
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <div class="me-auto">
          <small class="text-muted"><span id="modalSelectedCount">0</span> barang dipilih</small>
        </div>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="ri-close-line me-1"></i> Batal
        </button>
        <button type="button" class="btn btn-primary" id="btnAddSelectedProducts">
          <i class="ri-check-line me-1"></i> Tambahkan Barang
        </button>
      </div>
    </div>
  </div>
</div>

@push('styles')
    <style>
        .table {
            border: 1px solid #ced4da !important;
        }
    </style>
@endpush


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  let selectedProducts = [];
  let supplierId = null;

  // Aktifkan tombol "Pilih Barang" hanya jika supplier dipilih
  $('#supplier_id').on('change', function () {
    supplierId = $(this).val();
    $('#btnOpenProductModal').prop('disabled', !supplierId);
  });

  // Buka modal pencarian
  $('#btnOpenProductModal').on('click', function () {
    supplierId = $('#supplier_id').val();
    $('#searchProductInput').val('');
    $('#searchResultsContainer').html(`
      <div class="text-center text-muted py-5">
        <i class="ri-information-line"></i> Ketik minimal 2 huruf untuk mencari barang...
      </div>
    `);
    $('#productModal').modal('show');
  });

  // Fungsi untuk memperbarui jumlah terpilih
  function updateSelectedCount() {
    const count = selectedProducts.length;
    $('#modalSelectedCount').text(count);
    $('#selectedCount').text(count).toggle(count > 0);
  }

  // Fungsi untuk mencari barang dari supplier
  function searchSupplierProducts(query) {
    if (!supplierId) return;

    $('#searchResultsContainer').html(`
      <div class="text-center text-muted py-5">
        <i class="ri-loader-4-line ri-spin"></i> Mencari data...
      </div>
    `);

    $.get(`/api/supplier/${supplierId}/search-products?q=${query}`, function (data) {
        // console.log("HASIL API:", data);
      if (data.length === 0) {
        $('#searchResultsContainer').html(`
          <div class="text-center text-muted py-5">
            <i class="ri-error-warning-line"></i> Tidak ditemukan hasil.
          </div>
        `);
        return;
      }

      let html = data.map(d => {
        const isChecked = selectedProducts.some(p => p.id === d.id && p.type === d.jenis);
        const isAlreadyInTable = $(`#tableGudang tbody tr[data-id="${d.id}"][data-type="${d.jenis}"]`).length > 0;
        return `
          <label class="list-group-item d-flex justify-content-between align-items-center cursor ${isAlreadyInTable ? 'bg-light text-muted' : ''}">
            <div>
              <input type="checkbox" class="form-check-input me-2 search-product-checkbox"
                data-id="${d.id}" data-nama="${d.nama}"
                data-judul="${d.judul}" data-jenis="${d.jenis}"
                data-exp_date="${d.exp_date}"
                data-type="${d.jenis}"
                ${isChecked ? 'checked' : ''} ${isAlreadyInTable ? 'disabled' : ''}>
              <strong>${d.nama}</strong>
              <small class="text-muted">(${d.jenis} • ${d.judul})</small>
            </div>
            ${isAlreadyInTable ? '<span class="badge bg-success">Sudah ditambahkan</span>' : ''}
          </label>
        `;
      }).join('');

      $('#searchResultsContainer').html(html);
    });
  }

  // Jalankan pencarian ketika user mengetik
  $('#searchProductInput').on('input', function () {
    const query = $(this).val().trim();
    if (query.length < 2) {
      $('#searchResultsContainer').html(`
        <div class="text-center text-muted py-5">
          <i class="ri-information-line"></i> Ketik minimal 2 huruf untuk mencari barang...
        </div>
      `);
      return;
    }
    searchSupplierProducts(query);
  });

  // Checkbox pilih barang
  $(document).on('change', '.search-product-checkbox', function () {
    const productData = {
      id: $(this).data('id'),
      nama: $(this).data('nama'),
      judul: $(this).data('judul'),
      jenis: $(this).data('jenis'),
      exp_date: $(this).data('exp_date'),
      type: $(this).data('type')
    };

    if ($(this).is(':checked')) {
      if (!selectedProducts.some(p => p.id === productData.id && p.type === productData.type)) {
        selectedProducts.push(productData);
      }
    } else {
      selectedProducts = selectedProducts.filter(p => !(p.id === productData.id && p.type === productData.type));
    }
    updateSelectedCount();
  });

  // Tambahkan produk terpilih ke tabel
  $('#btnAddSelectedProducts').on('click', function () {
    $('#emptyStateRow').remove();
    selectedProducts.forEach(p => {
      if ($(`#tableGudang tbody tr[data-id="${p.id}"][data-type="${p.jenis}"]`).length === 0) {
        const today = new Date().toISOString().split('T')[0];
        $('#tableGudang tbody').append(`
          <tr data-id="${p.id}" data-type="${p.jenis}">
            <td>
              <input type="hidden" name="barang_id[]" value="${p.id}">
              <input type="hidden" name="barang_type[]" value="${p.jenis}">
              <input type="text" class="form-control" value="${p.nama}" readonly>
            </td>
            <td><input type="text" class="form-control" value="${p.jenis}" readonly></td>
            <td><input type="date" class="form-control" value="${p.exp_date}" readonly></td>
            <td><input type="text" name="no_batch[]" class="form-control" placeholder="No Batch"></td>
            <td><input type="number" name="stock_gudang[]" class="form-control" min="0" value="0" required></td>
            <td><input type="number" name="min_persediaan[]" class="form-control" min="0" value="0" required></td>
            <td><input type="date" name="tanggal_masuk[]" class="form-control" value="${today}"></td>
            <td><input type="text" name="lokasi_rak[]" class="form-control" placeholder="A1-B2"></td>
            <td>
              <select name="kondisi[]" class="form-select">
                <option value="Baik" selected>Baik</option>
                <option value="Rusak">Rusak</option>
                <option value="Kadaluarsa">Kadaluarsa</option>
              </select>
            </td>
            <td class="text-center">
              <button type="button" class="btn btn-sm btn-outline-danger btn-remove">
                <i class="ri-delete-bin-2-line"></i>
              </button>
            </td>
          </tr>
        `);
      }
    });
    selectedProducts = [];
    updateSelectedCount();
    $('#productModal').modal('hide');
  });

  // Hapus baris di tabel
  $(document).on('click', '.btn-remove', function () {
  $(this).closest('tr').remove();

  if ($('#tableGudang tbody tr').length === 0) {
    $('#tableGudang tbody').append(`
      <tr id="emptyStateRow">
        <td colspan="10" class="text-center py-5">
          <div class="empty-state">
            <i class="ri-inbox-line" style="font-size: 3rem; color: #6c757d;"></i>
            <h5 class="mt-3 text-muted">Belum Ada Detail Barang</h5>
            <p class="text-muted mb-3">Silakan pilih supplier terlebih dahulu, kemudian klik tombol "Pilih Barang" untuk menambahkan barang ke gudang.</p>
            <div class="d-flex justify-content-center align-items-center gap-2">
              <i class="ri-arrow-up-line text-primary"></i>
              <small class="text-primary fw-bold">Klik tombol "Pilih Barang" di atas</small>
            </div>
          </div>
        </td>
      </tr>
    `);
  }
});

  // Reset ketika modal ditutup
  $('#productModal').on('hidden.bs.modal', function () {
    selectedProducts = [];
    updateSelectedCount();
  });
});
</script>
@endpush