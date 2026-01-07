{{-- Modal Resep dengan 2 Tab: Resep & Resep Luar --}}
<div class="modal fade" id="modalResep" tabindex="-1" aria-labelledby="modalResepLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalResepLabel">
                    <i class="ri-file-text-line me-2"></i>Kelola Resep Obat
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Tabs Navigation --}}
                <ul class="nav nav-tabs mb-4" id="resepTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="resep-tab" data-bs-toggle="tab" data-bs-target="#resep-content" type="button" role="tab">
                            <i class="ri-user-heart-line me-1"></i>Resep
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="resep-luar-tab" data-bs-toggle="tab" data-bs-target="#resep-luar-content" type="button" role="tab">
                            <i class="ri-hospital-line me-1"></i>Resep Luar
                        </button>
                    </li>
                </ul>

                {{-- Tab Content --}}
                <div class="tab-content" id="resepTabsContent">
                    {{-- TAB 1: RESEP (Pasien Terdaftar) --}}
                    <div class="tab-pane fade show active" id="resep-content" role="tabpanel">
                        <form id="formResep" action="{{ route('apotik.store-resep') }}" method="POST">
                            @csrf
                            <input type="hidden" name="pasien_id" id="pasien_id">
                            <input type="hidden" name="jenis_pembayaran" id="jenis_pembayaran_pasien">
                            <input type="hidden" name="jenis_resep" value="resep">

                            {{-- Info Pasien --}}
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p class="mb-1"><strong>Nama Pasien:</strong> <span id="pasien_nama_display" class="text-primary">-</span></p>
                                            <p class="mb-0"><strong>No RM:</strong> <span id="pasien_no_rm_display" class="text-muted">-</span></p>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="mb-1"><strong>Jenis Pembayaran:</strong> <span id="jenis_pembayaran_display">-</span></p>
                                        </div>
                                        <div class="col-md-4 text-md-end">
                                            <p class="mb-1"><strong>Tanggal:</strong> {{ date('d/m/Y H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Form Input --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Ruangan Tujuan <span class="text-danger">*</span></label>
                                    <select class="form-select" name="ruangan_id" id="ruangan_id" required>
                                        <option value="">Pilih Ruangan</option>
                                        @if(isset($ruangans))
                                            @foreach($ruangans as $ruangan)
                                                <option value="{{ $ruangan->id }}">{{ $ruangan->nama_ruangan }}</option>
                                            @endforeach
                                        @endif
                                                <option value="igd">IGD</option>
                                    </select>
                                    <div class="invalid-feedback">Ruangan harus dipilih</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Status Obat <span class="text-danger">*</span></label>
                                    <select class="form-select" name="status_obat" required>
                                        <option value="">Pilih Status</option>
                                        <option value="Racik">Racik</option>
                                        <option value="Non Racik" selected>Non Racik</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Jenis Racikan</label>
                                    <input type="text" class="form-control" name="jenis_racikan" placeholder="Contoh: Puyer">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Dosis/Signa</label>
                                    <input type="text" class="form-control" name="dosis_signa" placeholder="3 x 1">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Hasil Racikan</label>
                                    <select class="form-select" name="hasil_racikan">
                                        <option value="">Pilih Hasil</option>
                                        <option value="Kapsul">Kapsul</option>
                                        <option value="Tablet">Tablet</option>
                                        <option value="Sirup">Sirup</option>
                                        <option value="Puyer">Puyer</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Aturan Pakai</label>
                                    <select class="form-select" name="aturan_pakai">
                                        <option value="">Pilih Aturan Pakai</option>
                                        <option value="Sebelum Makan">Sebelum Makan</option>
                                        <option value="Sesudah Makan">Sesudah Makan</option>
                                        <option value="Saat Makan">Saat Makan</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Embalase</label>
                                    <input type="number" class="form-control" name="embalase" value="0" min="0" step="500">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Jasa Racik</label>
                                    <input type="number" class="form-control" name="jasa_racik" value="0" min="0" step="1000">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Keterangan</label>
                                <textarea class="form-control" name="keterangan" rows="2" placeholder="Catatan tambahan..."></textarea>
                            </div>

                            {{-- List Obat Racik --}}
                            <div class="card border-primary mb-3">
                                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                    <strong><i class="ri-capsule-line me-2"></i>Daftar Obat</strong>
                                    <button type="button" class="btn btn-sm btn-light" id="btnTambahObat">
                                        <i class="ri-add-circle-line me-1"></i>Tambah Obat
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm" id="tableObat">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="35%">Nama Obat</th>
                                                    <th width="15%">Jumlah</th>
                                                    <th width="15%">Satuan</th>
                                                    <th width="15%">Harga</th>
                                                    <th width="15%">Subtotal</th>
                                                    <th width="5%" class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="listObat">
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">
                                                        <em>Belum ada obat. Klik tombol "Tambah Obat" untuk menambahkan.</em>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-light">
                                                    <td colspan="4" class="text-end"><strong>Total Harga:</strong></td>
                                                    <td colspan="2">
                                                        <strong class="text-primary">Rp <span id="totalHarga">0</span></strong>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- Summary Pembayaran --}}
                            <div class="row">
                                <div class="col-md-6 ms-auto">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Total Harga Obat:</span>
                                                <strong>Rp <span id="summaryTotalObat">0,00</span></strong>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Embalase:</span>
                                                <strong>Rp <span id="summaryEmbalase">0,00</span></strong>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Jasa Racik:</span>
                                                <strong>Rp <span id="summaryJasaRacik">0,00</span></strong>
                                            </div>
                                            <hr>
                                            <div class="d-flex justify-content-between">
                                                <strong class="text-primary">Total Bayar:</strong>
                                                <h5 class="text-primary mb-0">Rp <span id="summaryTotalBayar">0,00</span></h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- TAB 2: RESEP LUAR (Tanpa Pasien Terdaftar) --}}
                    <div class="tab-pane fade" id="resep-luar-content" role="tabpanel">
                        <form id="formResepLuar" action="{{ route('apotik.store-resep-luar') }}" method="POST">
                            @csrf
                            <input type="hidden" name="jenis_resep" value="resep_luar">

                            {{-- Info Resep Luar --}}
                            <div class="alert alert-info mb-3">
                                <i class="ri-information-line me-2"></i>
                                <strong>Resep Luar</strong> untuk pasien yang tidak terdaftar di sistem atau resep dari luar rumah sakit.
                            </div>

                            {{-- Data Pasien Resep Luar --}}
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <strong><i class="ri-user-line me-2"></i>Data Pasien (Opsional)</strong>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Nama Pasien</label>
                                            <input type="text" class="form-control" name="nama_pasien_luar" placeholder="Nama pasien (opsional)">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold">Umur</label>
                                            <input type="number" class="form-control" name="umur" placeholder="Tahun" min="0">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold">Jenis Kelamin</label>
                                            <select class="form-select" name="jenis_kelamin">
                                                <option value="">Pilih</option>
                                                <option value="L">Laki-laki</option>
                                                <option value="P">Perempuan</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">Alamat</label>
                                            <textarea class="form-control" name="alamat" rows="2" placeholder="Alamat pasien (opsional)"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Form Input Resep Luar --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Dokter/Sumber Resep</label>
                                    <input type="text" class="form-control" name="dokter_resep" placeholder="Nama dokter / RS / Klinik">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Status Obat</label>
                                    <select class="form-select" name="status_obat_luar">
                                        <option value="">Pilih Status</option>
                                        <option value="Racik">Racik</option>
                                        <option value="Non Racik" selected>Non Racik</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Jenis Racikan</label>
                                    <input type="text" class="form-control" name="jenis_racikan_luar" placeholder="Contoh: Puyer">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Dosis/Signa</label>
                                    <input type="text" class="form-control" name="dosis_signa_luar" placeholder="3 x 1">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Hasil Racikan</label>
                                    <select class="form-select" name="hasil_racikan_luar">
                                        <option value="">Pilih Hasil</option>
                                        <option value="Kapsul">Kapsul</option>
                                        <option value="Tablet">Tablet</option>
                                        <option value="Sirup">Sirup</option>
                                        <option value="Puyer">Puyer</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Aturan Pakai</label>
                                    <select class="form-select" name="aturan_pakai_luar">
                                        <option value="">Pilih Aturan Pakai</option>
                                        <option value="Sebelum Makan">Sebelum Makan</option>
                                        <option value="Sesudah Makan">Sesudah Makan</option>
                                        <option value="Saat Makan">Saat Makan</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Embalase</label>
                                    <input type="number" class="form-control" name="embalase_luar" value="0" min="0" step="500">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Jasa Racik</label>
                                    <input type="number" class="form-control" name="jasa_racik_luar" value="0" min="0" step="1000">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Keterangan</label>
                                <textarea class="form-control" name="keterangan_luar" rows="2" placeholder="Catatan tambahan..."></textarea>
                            </div>

                            {{-- List Obat Racik Resep Luar --}}
                            <div class="card border-success mb-3">
                                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                    <strong><i class="ri-capsule-line me-2"></i>Daftar Obat</strong>
                                    <button type="button" class="btn btn-sm btn-light" id="btnTambahObatLuar">
                                        <i class="ri-add-circle-line me-1"></i>Tambah Obat
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm" id="tableObatLuar">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="30%">Nama Obat</th>
                                                    <th width="15%">Jumlah</th>
                                                    <th width="15%">Satuan</th>
                                                    <th width="20%">Harga</th>
                                                    <th width="15%">Subtotal</th>
                                                    <th width="5%" class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="listObatLuar">
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">
                                                        <em>Belum ada obat. Klik tombol "Tambah Obat" untuk menambahkan.</em>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-light">
                                                    <td colspan="4" class="text-end"><strong>Total Harga:</strong></td>
                                                    <td colspan="2">
                                                        <strong class="text-success">Rp <span id="totalHargaLuar">0</span></strong>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- Summary Pembayaran Resep Luar --}}
                            <div class="row">
                                <div class="col-md-6 ms-auto">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Total Harga Obat:</span>
                                                <strong>Rp <span id="summaryTotalObatLuar">0,00</span></strong>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Embalase:</span>
                                                <strong>Rp <span id="summaryEmbalaseLuar">0,00</span></strong>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Jasa Racik:</span>
                                                <strong>Rp <span id="summaryJasaRacikLuar">0,00</span></strong>
                                            </div>
                                            <hr>
                                            <div class="d-flex justify-content-between">
                                                <strong class="text-success">Total Bayar:</strong>
                                                <h5 class="text-success mb-0">Rp <span id="summaryTotalBayarLuar">0,00</span></h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i>Tutup
                </button>
                <button type="button" class="btn btn-primary" id="btnSimpanResep">
                    <i class="ri-save-line me-1"></i>Simpan Resep
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Counter untuk ID obat
let obatCounter = 0;
let obatLuarCounter = 0;

// Data obat dari stock apotik (nanti dari backend)
let stockObat = [];

$(document).ready(function() {
    // Load stock obat via AJAX
    loadStockObat();

    // Event: Tambah Obat (Resep)
    $('#btnTambahObat').on('click', function() {
        tambahBariObat();
    });

    // Event: Tambah Obat (Resep Luar)
    $('#btnTambahObatLuar').on('click', function() {
        tambahBariObatLuar();
    });

    // Event: Update summary saat input embalase/jasa racik berubah
    $('input[name="embalase"], input[name="jasa_racik"]').on('input', function() {
        hitungTotalResep();
    });

    $('input[name="embalase_luar"], input[name="jasa_racik_luar"]').on('input', function() {
        hitungTotalResepLuar();
    });

    // Event: Simpan Resep
    $('#btnSimpanResep').on('click', function() {
        const activeTab = $('.tab-pane.active').attr('id');
        
        if (activeTab === 'resep-content') {
            if (validateFormResep()) {
                submitResep();
            }
        } else {
            if (validateFormResepLuar()) {
                submitResepLuar();
            }
        }
    });

    // Reset modal saat ditutup
    $('#modalResep').on('hidden.bs.modal', function() {
        resetFormResep();
        resetFormResepLuar();
    });
});

// Load stock obat dari backend
function loadStockObat() {
    $.ajax({
        url: '{{ route("apotik.get-stock-obat") }}',
        method: 'GET',
        success: function(response) {
            stockObat = response.data;
        },
        error: function() {
            Swal.fire('Error', 'Gagal memuat data stock obat', 'error');
        }
    });
}

// Tambah baris obat (Resep)
// Tambah baris obat (Resep) - TANPA DROPDOWN HARGA
function tambahBariObat() {
    obatCounter++;
    const row = `
        <tr data-id="${obatCounter}">
            <td>
                <select class="form-select form-select-sm obat-select" name="obat[${obatCounter}][detail_supplier_id]" data-id="${obatCounter}" required>
                    <option value="">Pilih Obat</option>
                    ${stockObat.map(obat => `
                        <option value="${obat.id}" 
                            data-harga-obat="${obat.harga_obat || 0}" 
                            data-harga-khusus="${obat.harga_khusus || 0}" 
                            data-harga-bpjs="${obat.harga_bpjs || 0}" 
                            data-satuan="${obat.satuan || '-'}" 
                            data-stock="${obat.stock || 0}">
                            ${obat.nama} - Stock: ${obat.stock}
                        </option>
                    `).join('')}
                </select>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm jumlah-obat" name="obat[${obatCounter}][jumlah]" data-id="${obatCounter}" min="1" value="1" required>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm satuan-obat" data-id="${obatCounter}" readonly>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm harga-obat" name="obat[${obatCounter}][harga]" data-id="${obatCounter}" readonly>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm subtotal-obat" name="obat[${obatCounter}][subtotal]" data-id="${obatCounter}" readonly>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger btn-hapus-obat" data-id="${obatCounter}">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </td>
        </tr>
    `;
    
    if ($('#listObat tr td[colspan]').length > 0) {
        $('#listObat').empty();
    }
    
    $('#listObat').append(row);
    
    // Event: Select obat berubah
    $(`.obat-select[data-id="${obatCounter}"]`).on('change', function() {
        const selected = $(this).find('option:selected');
        const id = $(this).data('id');
        const satuan = selected.data('satuan') || '-';
        
        // Ambil jenis pembayaran pasien
        const jenisPembayaran = $('#jenis_pembayaran_pasien').val() || 'Umum';
        
        let harga = 0;
        // Tentukan harga berdasarkan jenis pembayaran
        if (jenisPembayaran.toLowerCase() === 'bpjs') {
            harga = parseFloat(selected.data('harga-bpjs')) || 0;
        } else if (jenisPembayaran.toLowerCase() === 'khusus') {
            harga = parseFloat(selected.data('harga-khusus')) || 0;
        } else {
            // Default: Umum
            harga = parseFloat(selected.data('harga-obat')) || 0;
        }
        
        $(`.satuan-obat[data-id="${id}"]`).val(satuan);
        $(`.harga-obat[data-id="${id}"]`).val(harga);
        
        hitungSubtotal(id);
    });
    
    // Event: Jumlah berubah
    $(`.jumlah-obat[data-id="${obatCounter}"]`).on('input', function() {
        const id = $(this).data('id');
        hitungSubtotal(id);
    });
    
    // Event: Hapus obat
    $(`.btn-hapus-obat[data-id="${obatCounter}"]`).on('click', function() {
        const id = $(this).data('id');
        $(`#listObat tr[data-id="${id}"]`).remove();
        
        if ($('#listObat tr').length === 0) {
            $('#listObat').html(`
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        <em>Belum ada obat. Klik tombol "Tambah Obat" untuk menambahkan.</em>
                    </td>
                </tr>
            `);
        }
        
        hitungTotalResep();
    });
}

// Tambah baris obat luar (Resep Luar)
function tambahBariObatLuar() {
    obatLuarCounter++;
    const row = `
        <tr data-id="${obatLuarCounter}">
            <td>
                <select class="form-select form-select-sm obat-select-luar" name="obat_luar[${obatLuarCounter}][detail_supplier_id]" data-id="${obatLuarCounter}" required>
                    <option value="">Pilih Obat</option>
                    ${stockObat.map(obat => `
                        <option value="${obat.id}" 
                            data-harga-obat="${obat.harga_obat || 0}" 
                            data-harga-khusus="${obat.harga_khusus || 0}" 
                            data-harga-bpjs="${obat.harga_bpjs || 0}" 
                            data-satuan="${obat.satuan || '-'}" 
                            data-stock="${obat.stock || 0}">
                            ${obat.nama} - Stock: ${obat.stock}
                        </option>
                    `).join('')}
                </select>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm jumlah-obat-luar" name="obat_luar[${obatLuarCounter}][jumlah]" data-id="${obatLuarCounter}" min="1" value="1" required>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm satuan-obat-luar" data-id="${obatLuarCounter}" readonly>
            </td>
            <td>
                <div class="d-flex gap-1">
                    <select class="form-select form-select-sm jenis-harga-luar" name="obat_luar[${obatLuarCounter}][jenis_harga]" data-id="${obatLuarCounter}" style="max-width: 90px;">
                        <option value="harga_obat">Umum</option>
                        <option value="harga_khusus">Khusus</option>
                        <option value="harga_bpjs">BPJS</option>
                    </select>
                    <input type="number" class="form-control form-control-sm harga-obat-luar flex-grow-1" name="obat_luar[${obatLuarCounter}][harga]" data-id="${obatLuarCounter}" readonly>
                </div>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm subtotal-obat-luar" name="obat_luar[${obatLuarCounter}][subtotal]" data-id="${obatLuarCounter}" readonly>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger btn-hapus-obat-luar" data-id="${obatLuarCounter}">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </td>
        </tr>
    `;
    
    if ($('#listObatLuar tr td[colspan]').length > 0) {
        $('#listObatLuar').empty();
    }
    
    $('#listObatLuar').append(row);
    
    // Event: Select obat luar berubah
    $(`.obat-select-luar[data-id="${obatLuarCounter}"]`).on('change', function() {
        const selected = $(this).find('option:selected');
        const id = $(this).data('id');
        const satuan = selected.data('satuan') || '-';
        
        // Simpan data harga ke row
        $(`tr[data-id="${id}"]`).data('harga-obat', selected.data('harga-obat'));
        $(`tr[data-id="${id}"]`).data('harga-khusus', selected.data('harga-khusus'));
        $(`tr[data-id="${id}"]`).data('harga-bpjs', selected.data('harga-bpjs'));
        
        $(`.satuan-obat-luar[data-id="${id}"]`).val(satuan);
        
        // Trigger update harga berdasarkan jenis yang dipilih
        $(`.jenis-harga-luar[data-id="${id}"]`).trigger('change');
    });
    
    // Event: Jenis harga luar berubah
    $(`.jenis-harga-luar[data-id="${obatLuarCounter}"]`).on('change', function() {
        const id = $(this).data('id');
        const jenisHarga = $(this).val();
        const row = $(`tr[data-id="${id}"]`);
        
        let harga = 0;
        if (jenisHarga === 'harga_obat') {
            harga = parseFloat(row.data('harga-obat')) || 0;
        } else if (jenisHarga === 'harga_khusus') {
            harga = parseFloat(row.data('harga-khusus')) || 0;
        } else if (jenisHarga === 'harga_bpjs') {
            harga = parseFloat(row.data('harga-bpjs')) || 0;
        }
        
        $(`.harga-obat-luar[data-id="${id}"]`).val(harga);
        hitungSubtotalLuar(id);
    });
    
    // Event: Jumlah berubah
    $(`.jumlah-obat-luar[data-id="${obatLuarCounter}"]`).on('input', function() {
        const id = $(this).data('id');
        hitungSubtotalLuar(id);
    });
    
    // Event: Hapus obat luar
    $(`.btn-hapus-obat-luar[data-id="${obatLuarCounter}"]`).on('click', function() {
        const id = $(this).data('id');
        $(`#listObatLuar tr[data-id="${id}"]`).remove();
        
        if ($('#listObatLuar tr').length === 0) {
            $('#listObatLuar').html(`
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        <em>Belum ada obat. Klik tombol "Tambah Obat" untuk menambahkan.</em>
                    </td>
                </tr>
            `);
        }
        
        hitungTotalResepLuar();
    });
}

// Hitung subtotal per obat (Resep)
function hitungSubtotal(id) {
    const jumlah = parseFloat($(`.jumlah-obat[data-id="${id}"]`).val()) || 0;
    const harga = parseFloat($(`.harga-obat[data-id="${id}"]`).val()) || 0;
    const subtotal = jumlah * harga;
    
    $(`.subtotal-obat[data-id="${id}"]`).val(subtotal);
    
    hitungTotalResep();
}

// Hitung subtotal per obat luar (Resep Luar)
function hitungSubtotalLuar(id) {
    const jumlah = parseFloat($(`.jumlah-obat-luar[data-id="${id}"]`).val()) || 0;
    const harga = parseFloat($(`.harga-obat-luar[data-id="${id}"]`).val()) || 0;
    const subtotal = jumlah * harga;
    
    $(`.subtotal-obat-luar[data-id="${id}"]`).val(subtotal);
    
    hitungTotalResepLuar();
}

// Hitung total resep
function hitungTotalResep() {
    let totalObat = 0;
    $('.subtotal-obat').each(function() {
        totalObat += parseFloat($(this).val()) || 0;
    });
    
    const embalase = parseFloat($('input[name="embalase"]').val()) || 0;
    const jasaRacik = parseFloat($('input[name="jasa_racik"]').val()) || 0;
    const totalBayar = totalObat + embalase + jasaRacik;
    
    $('#totalHarga').text(formatRupiah(totalObat));
    $('#summaryTotalObat').text(formatRupiah(totalObat));
    $('#summaryEmbalase').text(formatRupiah(embalase));
    $('#summaryJasaRacik').text(formatRupiah(jasaRacik));
    $('#summaryTotalBayar').text(formatRupiah(totalBayar));
}

// Hitung total resep luar
function hitungTotalResepLuar() {
    let totalObat = 0;
    $('.subtotal-obat-luar').each(function() {
        totalObat += parseFloat($(this).val()) || 0;
    });
    
    const embalase = parseFloat($('input[name="embalase_luar"]').val()) || 0;
    const jasaRacik = parseFloat($('input[name="jasa_racik_luar"]').val()) || 0;
    const totalBayar = totalObat + embalase + jasaRacik;
    
    $('#totalHargaLuar').text(formatRupiah(totalObat));
    $('#summaryTotalObatLuar').text(formatRupiah(totalObat));
    $('#summaryEmbalaseLuar').text(formatRupiah(embalase));
    $('#summaryJasaRacikLuar').text(formatRupiah(jasaRacik));
    $('#summaryTotalBayarLuar').text(formatRupiah(totalBayar));
}

// Format rupiah
function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(angka);
}

// Validasi form resep
function validateFormResep() {
    const pasienId = $('#pasien_id').val();
    const ruanganId = $('#ruangan_id').val();
    const obatCount = $('#listObat tr:not(:has(td[colspan]))').length;
    
    if (!pasienId) {
        Swal.fire('Perhatian', 'Data pasien tidak ditemukan', 'warning');
        return false;
    }
    
    if (!ruanganId) {
        Swal.fire('Perhatian', 'Ruangan harus dipilih', 'warning');
        $('#ruangan_id').focus();
        return false;
    }
    
    if (obatCount === 0) {
        Swal.fire('Perhatian', 'Minimal harus ada 1 obat', 'warning');
        return false;
    }
    
    return true;
}

// Validasi form resep luar
function validateFormResepLuar() {
    const obatCount = $('#listObatLuar tr:not(:has(td[colspan]))').length;
    
    if (obatCount === 0) {
        Swal.fire('Perhatian', 'Minimal harus ada 1 obat', 'warning');
        return false;
    }
    
    return true;
}

// Reset form resep
function resetFormResep() {
    $('#formResep')[0].reset();
    $('#pasien_id').val('');
    $('#pasien_nama_display').text('-');
    $('#pasien_no_rm_display').text('-');
    $('#jenis_pembayaran_display').text('-');
    $('#listObat').html(`
        <tr>
            <td colspan="6" class="text-center text-muted">
                <em>Belum ada obat. Klik tombol "Tambah Obat" untuk menambahkan.</em>
            </td>
        </tr>
    `);
    obatCounter = 0;
    hitungTotalResep();
}

// Reset form resep luar
function resetFormResepLuar() {
    $('#formResepLuar')[0].reset();
    $('#listObatLuar').html(`
        <tr>
            <td colspan="6" class="text-center text-muted">
                <em>Belum ada obat. Klik tombol "Tambah Obat" untuk menambahkan.</em>
            </td>
        </tr>
    `);
    obatLuarCounter = 0;
    hitungTotalResepLuar();
}
function submitResep() {
    const formData = new FormData($('#formResep')[0]);
    const btnSimpan = $('#btnSimpanResep');
    
    // Disable button dan show loading
    btnSimpan.prop('disabled', true);
    btnSimpan.html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');
    
    $.ajax({
        url: $('#formResep').attr('action'),
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                // Close modal
                $('#modalResep').modal('hide');
                
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    // Reload table tanpa refresh halaman
                    window.location.href = '{{ route("apotik.index") }}';
                });
            } else {
                Swal.fire('Error', response.message || 'Gagal menyimpan resep', 'error');
            }
        },
        error: function(xhr) {
            let errorMessage = 'Terjadi kesalahan saat menyimpan resep';
            
            if (xhr.responseJSON) {
                if (xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join('<br>');
                }
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: errorMessage
            });
        },
        complete: function() {
            // Re-enable button
            btnSimpan.prop('disabled', false);
            btnSimpan.html('<i class="ri-save-line me-1"></i>Simpan Resep');
        }
    });
}

// Submit Resep Luar (AJAX)
function submitResepLuar() {
    const formData = new FormData($('#formResepLuar')[0]);
    const btnSimpan = $('#btnSimpanResep');
    
    // Disable button dan show loading
    btnSimpan.prop('disabled', true);
    btnSimpan.html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');
    
    $.ajax({
        url: $('#formResepLuar').attr('action'),
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                // Close modal
                $('#modalResep').modal('hide');
                
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    // Reload table tanpa refresh halaman
                    window.location.href = '{{ route("apotik.index") }}';
                });
            } else {
                Swal.fire('Error', response.message || 'Gagal menyimpan resep luar', 'error');
            }
        },
        error: function(xhr) {
            let errorMessage = 'Terjadi kesalahan saat menyimpan resep luar';
            
            if (xhr.responseJSON) {
                if (xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join('<br>');
                }
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: errorMessage
            });
        },
        complete: function() {
            // Re-enable button
            btnSimpan.prop('disabled', false);
            btnSimpan.html('<i class="ri-save-line me-1"></i>Simpan Resep');
        }
    });
}
</script>
@endpush