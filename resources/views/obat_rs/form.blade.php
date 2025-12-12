<div class="row mb-3">
    <div class="col-md-6">
        <label>Nama Obat</label>
        <input type="text" name="nama_obat" class="form-control"
               value="{{ old('nama_obat', $obat->nama_obat ?? '') }}" required
               placeholder="Contoh: Amlodipine 10mg">
    </div>
    <div class="col-md-6">
        <label>Nama Obat Internasional</label>
        <input type="text" name="nama_obat_internasional" class="form-control"
               value="{{ old('nama_obat_internasional', $obat->nama_obat_internasional ?? '') }}" required
               placeholder="Contoh: Amlodipine Besylate">
    </div>
</div>

<hr>
<h5>Detail Obat RS</h5>

<table class="table table-striped align-middle" id="detail-table">
    <thead class="table-light">
        <tr>
            <th style="width: 30%">Obat Master (Satu Sehat)</th>
            <th style="width: 11%">Kode Obat RS</th>
            <th style="width: 10%">Nama Obat RS</th>
            <th style="width: 9%">Stok Min</th>
            <th style="width: 9%">Stok Max</th>
            <th style="width: 10%">Lokasi</th>
            <th style="width: 10%">Status</th>
            <th style="width: 10%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($obat->detailObats ?? [null] as $detail)
        <tr data-detail-id="{{ $detail->id ?? '' }}">
            <td>
                <input type="hidden" name="id_detail_obat_rs[]" value="{{ $detail->id_detail_obat_rs ?? null }}">

                <select name="id_obat_master[]" class="form-select select2-obat-master" required>
                    @if($detail)
                        <option value="{{ $detail->id_obat_master }}" selected>
                            {{ $detail->obatMaster->nama_obat ?? '' }}
                        </option>
                    @endif
                </select>
            </td>
            <td><input type="text" name="kode_obat_rs[]" class="form-control" value="{{ $detail->kode_obat_rs ?? '' }}" placeholder="Contoh: KDRS001"></td>
            <td><input type="text" name="nama_obat_rs[]" class="form-control" value="{{ $detail->nama_obat_rs ?? '' }}" placeholder="Contoh: Paracetamol"></td>
            <td><input type="number" name="stok_minimal[]" class="form-control" min="0" value="{{ $detail->stok_minimal ?? 0 }}"></td>
            <td><input type="number" name="stok_maksimal[]" class="form-control" min="0" value="{{ $detail->stok_maksimal ?? 0 }}"></td>
            <td><input type="text" name="lokasi_penyimpanan[]" class="form-control" value="{{ $detail->lokasi_penyimpanan ?? '' }}" placeholder="Contoh: Lemari A1"></td>
            <td>
                <select name="status_aktif[]" class="form-select">
                    @foreach(['Aktif', 'Nonaktif', 'Diskontinyu'] as $opt)
                        <option value="{{ $opt }}" {{ ($detail->status_aktif ?? '') == $opt ? 'selected' : '' }}>
                            {{ $opt }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td class="text-center">
                {{-- Button Detail (hanya muncul jika data sudah tersimpan) --}}
                @if($detail && $detail->id_detail_obat_rs)
                    <a href="{{ route('obat.detail.edit', [$obat->id_obat_rs, $detail->id_detail_obat_rs]) }}" 
                       class="btn btn-sm btn-info" 
                       title="Edit Detail">
                        <i class="ri-file-edit-line"></i>
                    </a>
                @endif
                
                <button type="button" class="btn btn-sm btn-success btn-add" title="Tambah Baris">
                    <i class="ri-add-line"></i>
                </button>
                <button type="button" class="btn btn-sm btn-danger btn-remove" title="Hapus Baris">
                    <i class="ri-subtract-line"></i>
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@push('styles')
<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
        padding: 6px 12px;
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
<script>
$(document).ready(function() {

    // --- Inisialisasi Select2 ---
    function initSelect2(el) {
        el.select2({
            theme: 'bootstrap-5',
            placeholder: 'Cari obat master...',
            allowClear: true,
            ajax: {
                url: '{{ route("api.obat-master.search") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { term: params.term };
                },
                processResults: function(data) {
                    return { results: data };
                },
                cache: true
            }
        });
    }

    // Inisialisasi Select2 pada semua element yang sudah ada
    $('.select2-obat-master').each(function() {
        initSelect2($(this));
    });

    // --- Tambah Baris Baru ---
    $('#detail-table').on('click', '.btn-add', function() {
        let $tableBody = $('#detail-table tbody');
        
        // Buat baris baru dari HTML template (tanpa button detail karena belum tersimpan)
        let $newRow = $('<tr data-detail-id="">' +
            '<td>' +
                '<select name="id_obat_master[]" class="form-select select2-obat-master" required>' +
                    '<option value=""></option>' +
                '</select>' +
            '</td>' +
            '<td><input type="text" name="kode_obat_rs[]" class="form-control" placeholder="Contoh: KDRS001"></td>' +
            '<td><input type="text" name="nama_obat_rs[]" class="form-control" placeholder="Contoh: Paracetamol"></td>' +
            '<td><input type="number" name="stok_minimal[]" class="form-control" min="0" value="0"></td>' +
            '<td><input type="number" name="stok_maksimal[]" class="form-control" min="0" value="0"></td>' +
            '<td><input type="text" name="lokasi_penyimpanan[]" class="form-control" placeholder="Contoh: Lemari A1"></td>' +
            '<td>' +
                '<select name="status_aktif[]" class="form-select">' +
                    '<option value="Aktif" selected>Aktif</option>' +
                    '<option value="Nonaktif">Nonaktif</option>' +
                    '<option value="Diskontinyu">Diskontinyu</option>' +
                '</select>' +
            '</td>' +
            '<td class="text-center">' +
                '<button type="button" class="btn btn-sm btn-success btn-add" title="Tambah Baris"><i class="ri-add-line"></i></button> ' +
                '<button type="button" class="btn btn-sm btn-danger btn-remove" title="Hapus Baris"><i class="ri-subtract-line"></i></button>' +
            '</td>' +
        '</tr>');

        // Tambahkan baris baru ke tabel
        $tableBody.append($newRow);

        // Inisialisasi Select2 pada baris yang baru ditambahkan
        initSelect2($newRow.find('.select2-obat-master'));
    });

    // --- Hapus Baris ---
    $('#detail-table').on('click', '.btn-remove', function() {
        const rowCount = $('#detail-table tbody tr').length;
        
        if (rowCount > 1) {
            let $row = $(this).closest('tr');
            
            // Hancurkan Select2 instance sebelum menghapus row
            let $select = $row.find('.select2-obat-master');
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