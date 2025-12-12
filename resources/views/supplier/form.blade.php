<h5 class="mb-3">Data Supplier</h5>

<div class="row mb-3">
    <div class="col-md-4">
        <label>NPWP</label>
        <input type="text" name="npwp" class="form-control" placeholder="e.g. 000.00-01-000-222"
            value="{{ old('npwp', $supplier->npwp ?? '') }}">
    </div>
    <div class="col-md-4">
        <label>Supplier</label>
        <input type="text" name="nama_supplier" class="form-control" placeholder="e.g. PT Premiere Alkes Nusindo"
            value="{{ old('supplier', $supplier->nama_supplier ?? '') }}">
    </div>
    <div class="col-md-4">
        <label>Alamat</label>
        <input type="text" name="alamat" class="form-control" placeholder="e.g. Randuagung, Singosari"
            value="{{ old('alamat', $supplier->alamat ?? '') }}">
    </div>
    <div class="col-md-4 mt-2">
        <label>Upload Dokumen 1 (PDF)</label>
        <input type="file" name="file" accept="application/pdf" class="form-control">
        @if(isset($supplier) && $supplier->file)
            <small class="text-primary btn btn-sm btn-outline-primary mt-1">
                <a href="{{ asset($supplier->file) }}" target="_blank">Lihat Dokumen 1</a>
            </small>
        @endif
    </div>
    <div class="col-md-4 mt-2">
        <label>Upload Dokumen 2 (PDF)</label>
        <input type="file" name="file2" accept="application/pdf" class="form-control">
        @if(isset($supplier) && $supplier->file2)
            <small class="text-primary btn btn-sm btn-outline-primary mt-1">
                <a href="{{ asset($supplier->file2) }}" target="_blank">Lihat Dokumen 2</a>
            </small>
        @endif
    </div>
    <div class="col-md-4 mt-2">
        <label>Catatan</label>
        <textarea 
            class="form-control" 
            name="note" 
            cols="30" 
            rows="1" 
            placeholder="e.g. kurang KTP supplier">{{ old('note', $supplier->note ?? '') }}</textarea>
    </div>
</div>

<h5 class="mb-3">Detail Barang</h5>

<table class="table table-bordered table-striped" id="detail-table">
    <thead>
        <tr>
            <th width="85%">Data Detail</th>
            <th width="15%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($supplier->detailSuppliers ?? [null] as $detail)
        <tr class="supplier-detail-item" data-detail-id="{{ $detail->id ?? '' }}">
            <td>
                <input type="hidden" name="detail_id[]" value="{{ $detail->id ?? '' }}">
                <div class="row g-2">
                    <div class="col-md-6">
                        <label>No Batch</label>
                        <input type="text" name="no_batch[]" class="form-control"
                               value="{{ $detail->no_batch ?? '' }}" placeholder="e.g. BTC-36523">
                    </div>
                    <div class="col-md-6">
                        <label>Judul</label>
                        <input type="text" name="judul[]" class="form-control"
                               value="{{ $detail->judul ?? '' }}" placeholder="e.g. Obat Sakit Kepala">
                    </div>
                    <div class="col-md-6">
                        <label>Jenis <span class="text-danger">*</span></label>
                        <select name="jenis[]" class="form-select jenis-select" required>
                            <option value="" hidden>-- Pilih Jenis --</option>
                            @foreach($jenis as $j)
                                <option value="{{ $j->nama_jenis }}" {{ ($detail->jenis ?? '') == $j->nama_jenis ? 'selected' : '' }}>
                                    {{ $j->nama_jenis }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Nama <span class="text-danger">*</span></label>
                        <!-- Select2 untuk Obat/Alkes/Reagensia -->
                        <select name="nama[]" class="form-control nama-select select2-nama" 
                                style="{{ in_array($detail->jenis ?? '', ['Obat', 'Alkes', 'Reagensia']) ? '' : 'display: none;' }}"
                                {{ in_array($detail->jenis ?? '', ['Obat', 'Alkes', 'Reagensia']) ? 'required' : 'disabled' }}>
                            @if($detail && $detail->nama && in_array($detail->jenis ?? '', ['Obat', 'Alkes', 'Reagensia']))
                                <option value="{{ $detail->nama }}" selected>{{ $detail->nama }}</option>
                            @else
                                <option value="">-- Pilih atau ketik untuk mencari --</option>
                            @endif
                        </select>
                        <!-- Input Manual untuk jenis lainnya -->
                        <input type="text" name="nama_manual[]" class="form-control nama-manual" 
                            value="{{ !in_array($detail->jenis ?? '', ['Obat', 'Alkes', 'Reagensia']) && ($detail->jenis ?? '') ? ($detail->nama ?? '') : '' }}"
                            style="{{ !in_array($detail->jenis ?? '', ['Obat', 'Alkes', 'Reagensia']) && ($detail->jenis ?? '') ? '' : 'display: none;' }}" 
                            placeholder="Masukkan nama barang"
                            {{ !in_array($detail->jenis ?? '', ['Obat', 'Alkes', 'Reagensia']) && ($detail->jenis ?? '') ? 'required' : 'disabled' }}>
                    </div>
                    <div class="col-md-6">
                        <label>Merk</label>
                        <input type="text" name="merk[]" class="form-control merk-input"
                               value="{{ $detail->merk ?? '' }}" placeholder="e.g. Kimia Farma">
                    </div>
                    <div class="col-md-6">
                        <label>Satuan <span class="text-danger">*</span></label>
                        <select name="satuan[]" class="form-select" required>
                            <option value="" hidden>-- Pilih Satuan --</option>
                            @foreach($satuans as $data)
                                <option value="{{ $data->nama_satuan }}" {{ ($detail->satuans ?? '') == $data->nama_satuan ? 'selected' : '' }}>
                                    {{ $data->nama_satuan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Exp Date</label>
                        <input type="date" name="exp_date[]" class="form-control"
                               value="{{ $detail->exp_date ?? '' }}">
                    </div>
                    <div class="col-md-6">
                        <label>Stock Live</label>
                        <input type="number" name="stock_live[]" class="form-control"
                               value="{{ $detail->stock_live ?? '' }}" placeholder="e.g. 50">
                    </div>
                    <div class="col-md-6">
                        <label>Stock PO</label>
                        <input type="number" name="stock_po[]" class="form-control"
                               value="{{ $detail->stock_po ?? '' }}" placeholder="e.g. 20">
                    </div>
                    <div class="col-md-6">
                        <label>Min. Persediaan</label>
                        <input type="number" name="min_persediaan[]" class="form-control"
                               value="{{ $detail->min_persediaan ?? '' }}" placeholder="e.g. 10">
                    </div>
                    <div class="col-md-6">
                        <label>Harga Beli</label>
                        <input type="number" name="harga_beli[]" class="form-control format-rupiah"
                               value="{{ number_format($detail->harga_beli ?? 0, 0, ',', '.') }}" placeholder="e.g. 5000">
                    </div>
                    <div class="col-md-6">
                        <label>Department</label>
                        <select name="department_id[]" class="form-control">
                            <option value="" hidden>-- Pilih Department --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}"
                                    {{ ($detail->department_id ?? '') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->nama_department }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Kode Rak</label>
                        <input type="text" name="kode_rak[]" class="form-control"
                               value="{{ $detail->kode_rak ?? '' }}" placeholder="e.g. A12">
                    </div>
                </div>
            </td>
            <td class="align-middle text-center">
                <div class="d-flex flex-column justify-content-center align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-success btn-add" title="Tambah Baris">
                        <i class="ri-add-line"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove" title="Hapus Baris">
                        <i class="ri-subtract-line"></i>
                    </button>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@push('styles')
<style>
    .select2-container .select2-selection--single {
        height: 37px !important;
        padding-top: 3px;
        border: 1px solid #ced4da;
        border-radius: 6px;
    }
    .select2-selection__rendered {
        line-height: 26px !important;
    }
    .table {
        border: 1px solid #ced4da !important;
    }
</style>
@endpush

@push('scripts')
{{-- Helper Api --}}
<script>
    // Daftar jenis yang menggunakan API (Select2)
    window.jenisWithApi = ['Obat', 'Alkes', 'Reagensia'];
    
    // Mapping API untuk jenis tertentu saja
    window.jenisApiMapping = {
        'Obat': "{{ route('api.obat.search') }}",
        'Alkes': "{{ route('api.alkes.search') }}",
        'Reagensia': "{{ route('api.reagensia.search') }}"
    };

    // Opsi jenis untuk dynamic row
    window.jenisOptions = `
        <option value="" hidden>-- Pilih Jenis --</option>
        @foreach($jenis as $j)
            <option value="{{ $j->nama_jenis }}">{{ $j->nama_jenis }}</option>
        @endforeach
    `;

    // Opsi department untuk dynamic row
    window.departmentOptions = `
        <option value="" hidden>-- Pilih Department --</option>
        @foreach($departments as $dept)
            <option value="{{ $dept->id }}">{{ $dept->nama_department }}</option>
        @endforeach
    `;
</script>
{{-- Format Rupiah --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rupiahInputs = document.querySelectorAll('.format-rupiah');

        rupiahInputs.forEach(input => {
            input.addEventListener('input', function (e) {
                // Hapus semua karakter non-digit
                let value = this.value.replace(/\D/g, '');

                // Format angka jadi ribuan
                this.value = new Intl.NumberFormat('id-ID').format(value);
            });

            // Saat form disubmit, ubah ke angka murni agar backend tidak bingung
            input.form?.addEventListener('submit', function () {
                rupiahInputs.forEach(inp => {
                    inp.value = inp.value.replace(/\./g, '');
                });
            });
        });
    });
</script>
<script>
$(document).ready(function() {

    // Daftar jenis yang menggunakan API
    const jenisWithApi = window.jenisWithApi || ['Obat', 'Alkes', 'Reagensia'];

    // Helper: Cek apakah jenis menggunakan API
    function isApiJenis(jenis) {
        return jenisWithApi.includes(jenis);
    }

    // --- Inisialisasi Select2 ---
    function initSelect2(el) {
        const row = el.closest('.supplier-detail-item');
        const jenis = row.find('.jenis-select').val();
        
        // Skip jika jenis belum dipilih atau tidak menggunakan API
        if (!jenis || !isApiJenis(jenis)) {
            return;
        }
        
        // Ambil mapping API berdasarkan jenis
        const apiMapping = window.jenisApiMapping || {};
        const apiUrl = apiMapping[jenis];
        
        if (!apiUrl) return;
        
        el.select2({
            placeholder: '-- Pilih atau ketik untuk mencari --',
            allowClear: true,
            width: '100%',
            ajax: {
                url: apiUrl,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.items || [],
                        pagination: {
                            more: data.pagination ? data.pagination.more : false
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 1
        });
    }

    // Inisialisasi Select2 pada semua element yang sudah ada
    $('.select2-nama').each(function() {
        const row = $(this).closest('.supplier-detail-item');
        const jenis = row.find('.jenis-select').val();
        const namaSelect = $(this);
        const namaManual = row.find('.nama-manual');
        
        if (!jenis) {
            // Belum ada jenis dipilih - semua hidden
            namaSelect.hide().prop('disabled', true).prop('required', false);
            namaManual.hide().prop('disabled', true).prop('required', false);
        } else if (isApiJenis(jenis)) {
            // Jenis menggunakan API - tampilkan select2, hide manual
            namaSelect.show().prop('disabled', false).prop('required', true);
            namaManual.hide().prop('disabled', true).prop('required', false);
            initSelect2(namaSelect);
        } else {
            // Jenis menggunakan input manual - hide select2, tampilkan manual
            namaSelect.hide().prop('disabled', true).prop('required', false);
            namaManual.show().prop('disabled', false).prop('required', true);
        }
    });

    // --- Handle Perubahan Jenis ---
    $('#detail-table').on('change', '.jenis-select', function() {
        const row = $(this).closest('.supplier-detail-item');
        const jenis = $(this).val();
        const namaSelect = row.find('.nama-select');
        const namaManual = row.find('.nama-manual');
        
        // Reset field
        if (namaSelect.hasClass('select2-hidden-accessible')) {
            namaSelect.val(null).trigger('change');
            namaSelect.select2('destroy');
        }
        namaSelect.val('');
        namaManual.val('');
        
        if (!jenis) {
            // Belum pilih jenis - semua hidden
            namaSelect.hide().prop('disabled', true).prop('required', false);
            namaSelect.html('<option value="">-- Pilih Jenis Terlebih Dahulu --</option>');
            namaManual.hide().prop('disabled', true).prop('required', false);
        } else if (isApiJenis(jenis)) {
            // Jenis menggunakan API (Select2) - tampilkan select2, hide manual
            namaSelect.show().prop('disabled', false).prop('required', true);
            namaSelect.html('<option value="">-- Pilih atau ketik untuk mencari --</option>');
            namaManual.hide().prop('disabled', true).prop('required', false);
            
            // Initialize Select2
            initSelect2(namaSelect);
        } else {
            // Jenis menggunakan input manual - hide select2, tampilkan manual
            namaSelect.hide().prop('disabled', true).prop('required', false);
            namaManual.show().prop('disabled', false).prop('required', true);
            namaManual.focus();
        }
    });

    // --- Handle Select2 Selection ---
    $('#detail-table').on('select2:select', '.nama-select', function(e) {
        const data = e.params.data;
        const row = $(this).closest('.supplier-detail-item');
        
        // Auto-fill merk dan satuan jika ada
        if (data.merk) {
            row.find('.merk-input').val(data.merk);
        }
        if (data.satuan) {
            row.find('.satuan-input').val(data.satuan);
        }
    });

    // --- Tambah Baris Baru ---
    $('#detail-table').on('click', '.btn-add', function() {
        let $tableBody = $('#detail-table tbody');
        
        // Ambil opsi jenis dari variable global
        let jenisOptions = window.jenisOptions || '<option value="">-- Pilih Jenis --</option>';
        
        // Template baris baru
        let $newRow = $('<tr class="supplier-detail-item" data-detail-id="">' +
            '<td>' +
                '<input type="hidden" name="detail_id[]" value="">' +
                '<div class="row g-2">' +
                    '<div class="col-md-6">' +
                        '<label>No Batch</label>' +
                        '<input type="text" name="no_batch[]" class="form-control" placeholder="e.g. BTC-36523">' +
                    '</div>' +
                    '<div class="col-md-6">' +
                        '<label>Judul</label>' +
                        '<input type="text" name="judul[]" class="form-control" placeholder="e.g. Obat Sakit Kepala">' +
                    '</div>' +
                    '<div class="col-md-6">' +
                        '<label>Jenis <span class="text-danger">*</span></label>' +
                        '<select name="jenis[]" class="form-select jenis-select" required>' +
                            jenisOptions +
                        '</select>' +
                    '</div>' +
                    '<div class="col-md-6">' +
                        '<label>Nama <span class="text-danger">*</span></label>' +
                        '<select name="nama[]" class="form-control nama-select select2-nama" style="display: none;" disabled>' +
                            '<option value="">-- Pilih Jenis Terlebih Dahulu --</option>' +
                        '</select>' +
                        '<input type="text" name="nama_manual[]" class="form-control nama-manual" style="display: none;" placeholder="Masukkan nama barang" disabled>' +
                    '</div>' +
                    '<div class="col-md-6">' +
                        '<label>Merk</label>' +
                        '<input type="text" name="merk[]" class="form-control merk-input" placeholder="e.g. Kimia Farma">' +
                    '</div>' +
                    '<div class="col-md-6">' +
                        '<label>Satuan</label>' +
                        '<input type="text" name="satuan[]" class="form-control satuan-input" placeholder="e.g. Box">' +
                    '</div>' +
                    '<div class="col-md-6">' +
                        '<label>Exp Date</label>' +
                        '<input type="date" name="exp_date[]" class="form-control">' +
                    '</div>' +
                    '<div class="col-md-6">' +
                        '<label>Stock Live</label>' +
                        '<input type="number" name="stock_live[]" class="form-control" placeholder="e.g. 50">' +
                    '</div>' +
                    '<div class="col-md-6">' +
                        '<label>Stock PO</label>' +
                        '<input type="number" name="stock_po[]" class="form-control" placeholder="e.g. 20">' +
                    '</div>' +
                    '<div class="col-md-6">' +
                        '<label>Min. Persediaan</label>' +
                        '<input type="number" name="min_persediaan[]" class="form-control" placeholder="e.g. 10">' +
                    '</div>' +
                    '<div class="col-md-6">' +
                        '<label>Harga Beli</label>' +
                        '<input type="number" name="harga_beli[]" class="form-control" placeholder="e.g. 5000">' +
                    '</div>' +
                    '<div class="col-md-6">' +
                        '<label>Department</label>' +
                        '<select name="department_id[]" class="form-control">' +
                            (window.departmentOptions || '<option value="">-- Pilih Department --</option>') +
                        '</select>' +
                    '</div>' +
                    '<div class="col-md-6">' +
                        '<label>Kode Rak</label>' +
                        '<input type="text" name="kode_rak[]" class="form-control" placeholder="e.g. A12">' +
                    '</div>' +
                '</div>' +
            '</td>' +
            '<td class="align-middle text-center">' +
                '<div class="d-flex flex-column justify-content-center align-items-center gap-2">' +
                    '<button type="button" class="btn btn-sm btn-outline-success btn-add" title="Tambah Baris">' +
                        '<i class="ri-add-line"></i>' +
                    '</button>' +
                    '<button type="button" class="btn btn-sm btn-outline-danger btn-remove" title="Hapus Baris">' +
                        '<i class="ri-subtract-line"></i>' +
                    '</button>' +
                '</div>' +
            '</td>' +
        '</tr>');

        // Tambahkan baris baru
        $tableBody.append($newRow);
    });

    // --- Hapus Baris ---
    $('#detail-table').on('click', '.btn-remove', function() {
        const rowCount = $('#detail-table tbody tr').length;
        
        if (rowCount > 1) {
            let $row = $(this).closest('tr');
            
            // Hancurkan Select2 instance sebelum menghapus row
            let $select = $row.find('.select2-nama');
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }
            
            // Hapus baris
            $row.remove();
        } else {
            alert('Minimal satu baris detail harus ada.');
        }
    });

});
</script>
@endpush