<!-- Modal Buat Resep -->
<div class="modal fade" id="modalResep" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl-custom" style="max-width: 80%">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="ri-file-add-line me-2"></i>Buat Resep Obat
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Info Pasien -->
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <h6 class="card-title text-primary mb-3">
                            <i class="ri-user-line me-2"></i>Informasi Pasien
                        </h6>
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-muted d-block">No. RM</small>
                                <strong id="display_no_rm">-</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Nama Pasien</small>
                                <strong id="display_nama_pasien">-</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Jenis Pembayaran</small>
                                <span id="display_jenis_pembayaran" class="badge bg-secondary">-</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Jenis Ruangan</small>
                                <strong id="display_jenis_ruangan">-</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Nama Ruangan</small>
                                <span id="display_nama_ruangan" class="badge bg-info">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-info mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">
                                        <i class="ri-stethoscope-line me-1"></i>Pilih Dokter Penanggung Jawab
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="dokter_id" name="dokter_resep" required>
                                        <option value="">-- Pilih Dokter --</option>
                                    </select>
                                    <small class="text-muted">Dokter yang bertanggung jawab atas resep ini</small>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- Form Resep -->
                <form id="formResep">
                    <input type="hidden" id="pasien_id" name="pasien_id">
                    <input type="hidden" id="jenis_pembayaran" name="jenis_pembayaran">
                    <input type="hidden" name="status_obat" id="status_obat" value="non_racik">
                    <input type="hidden" id="diskon_type" name="diskon_type" value="percent">
                    <input type="hidden" id="pajak_type" name="pajak_type" value="percent">

                    <!-- Tabs untuk Non-Racik dan Racik -->
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-nonracik" data-bs-toggle="tab" onclick="setJenisResep('non_racik')" 
                                data-bs-target="#content-nonracik" type="button">
                                <i class="ri-medicine-bottle-line me-1"></i>Obat Non Racik
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-racik" data-bs-toggle="tab" onclick="setJenisResep('racik')" 
                                data-bs-target="#content-racik" type="button">
                                <i class="ri-flask-line me-1"></i>Obat Racikan
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- TAB NON RACIK -->
                        <div class="tab-pane fade show active" id="content-nonracik">
                            <div class="card border-secondary mb-3">
                                <div class="card-header bg-secondary text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="ri-medicine-bottle-line me-2"></i>Daftar Obat Non Racik
                                        </h6>
                                        <button type="button" class="btn btn-light btn-sm" id="btnTambahObatNonRacik">
                                            <i class="ri-add-line me-1"></i>Tambah Obat
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="25%">Nama Obat</th>
                                                    <th width="10%">Satuan</th>
                                                    <th width="8%">Stock</th>
                                                    <th width="8%">Jumlah</th>
                                                    <th width="12%">Harga Satuan</th>
                                                    <th width="12%">Subtotal</th>
                                                    <th width="15%">Dosis</th>
                                                    <th width="10%">Signa</th>
                                                    <th width="5%"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyObatNonRacik">
                                                <tr>
                                                    <td colspan="9" class="text-center text-muted py-4">
                                                        <i class="ri-medicine-bottle-line ri-2x d-block mb-2"></i>
                                                        Belum ada obat non racik
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB RACIKAN -->
                        <div class="tab-pane fade" id="content-racik">
                            <div class="mb-3">
                                <button type="button" class="btn btn-success btn-sm" id="btnTambahRacikan">
                                    <i class="ri-add-circle-line me-1"></i>Tambah Racikan Baru
                                </button>
                            </div>
                            
                            <!-- Container untuk list racikan -->
                            <div id="containerRacikan"></div>
                        </div>
                    </div>

                    <!-- Total & Biaya -->
                    <div class="row mt-4">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Keterangan</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3" 
                                    placeholder="Keterangan tambahan (opsional)"></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Total Obat:</span>
                                        <strong id="display_total_obat">Rp 0</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2 align-items-center">
                                        <span>Diskon:</span>
                                        <div class="input-group" style="width: 150px;">
                                            <input type="number" class="form-control form-control-sm" 
                                                id="diskon" name="diskon" value="0" min="0">
                                            <button class="btn btn-outline-secondary btn-sm px-2" type="button" id="btnToggleDiskon" title="Toggle antara % dan IDR">
                                                %
                                            </button>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted">Nilai Diskon:</small>
                                        <small class="text-muted" id="display_nilai_diskon">Rp 0</small>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2 align-items-center">
                                        <span>Pajak:</span>
                                        <div class="input-group" style="width: 150px;">
                                            <input type="number" class="form-control form-control-sm" 
                                                id="pajak" name="pajak" value="0" min="0">
                                            <button class="btn btn-outline-secondary btn-sm px-2" type="button" id="btnTogglePajak" title="Toggle antara % dan IDR">
                                                %
                                            </button>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted">Nilai Pajak:</small>
                                        <small class="text-muted" id="display_nilai_pajak">Rp 0</small>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <strong>Total Bayar:</strong>
                                        <strong class="text-primary" id="display_total_bayar">Rp 0</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i>Batal
                </button>
                <button type="button" class="btn btn-primary" id="btnSimpanResep">
                    <i class="ri-save-line me-1"></i>Simpan Resep
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pilih Obat -->
<div class="modal fade" id="modalPilihObat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-medicine-bottle-line me-2"></i>Pilih Obat
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="searchObat" placeholder="Cari nama obat...">
                </div>
                <div class="table-responsive" style="max-height: 400px;">
                    <table class="table table-hover table-sm">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Nama Obat</th>
                                <th>Satuan</th>
                                <th>Stock</th>
                                <th>Harga</th>
                                <th width="80" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyPilihObat"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('style')
<style>
.racikan-card {
    border-left: 4px solid #0d6efd;
    margin-bottom: 1.5rem;
}
.racikan-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}
.table {
    border: 1px solid #ced4da !important;
}
</style>
@endpush

@push('scripts')
    
<script>
let stockObatData = [];
let dosisData = [];
let signaData = [];
let obatNonRacikList = [];
let racikanList = [];
let currentJenisPembayaran = 'Umum';
let currentModalType = '';
let currentRacikanIndex = null;
let diskonType = 'percent'; // 'percent' atau 'idr'
let pajakType = 'percent'; // 'percent' atau 'idr'

$(document).ready(function() {
    $('#btnTambahObatNonRacik').on('click', function() {
        currentModalType = 'nonracik';
        loadStockObat();
        $('#modalPilihObat').modal('show');
    });

    $('#btnTambahRacikan').on('click', function() {
        tambahRacikanBaru();
    });

    $('#searchObat').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('#tbodyPilihObat tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(searchTerm) > -1);
        });
    });

    // Toggle Diskon Type
    $('#btnToggleDiskon').on('click', function() {
        if (diskonType === 'percent') {
            diskonType = 'idr';
            $(this).html('<i class="ri-money-dollar-circle-line"></i>');
            $(this).attr('title', 'Mode: IDR (klik untuk % )');
            $('#diskon').attr('placeholder', 'Nominal IDR');
        } else {
            diskonType = 'percent';
            $(this).html('%');
            $(this).attr('title', 'Mode: % (klik untuk IDR)');
            $('#diskon').attr('placeholder', 'Persentase');
        }
        $('#diskon_type').val(diskonType);
        hitungTotal();
    });

    // Toggle Pajak Type
    $('#btnTogglePajak').on('click', function() {
        if (pajakType === 'percent') {
            pajakType = 'idr';
            $(this).html('<i class="ri-money-dollar-circle-line"></i>');
            $(this).attr('title', 'Mode: IDR (klik untuk %)');
            $('#pajak').attr('placeholder', 'Nominal IDR');
        } else {
            pajakType = 'percent';
            $(this).html('%');
            $(this).attr('title', 'Mode: % (klik untuk IDR)');
            $('#pajak').attr('placeholder', 'Persentase');
        }
        $('#pajak_type').val(pajakType);
        hitungTotal();
    });

    $(document).on('input', '#diskon, #pajak', hitungTotal);
    $('#btnSimpanResep').on('click', simpanResep);
    $('#modalResep').on('hidden.bs.modal', resetForm);
});

function renderDokterSelect(dokters) {
    let options = '<option value="">-- Pilih Dokter --</option>';

    dokters.forEach(d => {
        options += `<option value="${d.nama_dokter}">
            ${d.nama_dokter}
        </option>`;
    });

    $('#dokter_id').html(options).select2({
        dropdownParent: $('#modalResep'),
        width: '100%',
        placeholder: '-- Pilih Dokter --'
    });
}


function setJenisResep(jenis) {
    document.getElementById('status_obat').value = jenis;
}
function formatJenisRuangan(jenis) {
    const map = {
        rawat_jalan: 'Rawat Jalan',
        rawat_inap: 'Rawat Inap',
        igd: 'IGD',
        penunjang: 'Penunjang'
    };
    return map[jenis] ?? '-';
}


function loadDataPasien(pasienId) {
    $.ajax({
        url: `/pasien/${pasienId}/ajax`,
        method: 'GET',
        beforeSend: function() {
            $('#display_no_rm, #display_nama_pasien, #display_jenis_pembayaran, #display_jenis_ruangan, #display_nama_ruangan')
                .html('<span class="spinner-border spinner-border-sm"></span>');
        },
        success: function(response) {
            if (response.success && response.data) {
                const p = response.data;
                $('#pasien_id').val(p.id_pasien);
                $('#jenis_pembayaran').val(p.jenis_pembayaran);
                currentJenisPembayaran = p.jenis_pembayaran;
                
                $('#display_no_rm').text(p.no_rm);
                $('#display_nama_pasien').text(p.nama_lengkap);
                $('#display_jenis_ruangan').text(formatJenisRuangan(p.jenis_ruangan));
                $('#display_nama_ruangan').text(p.ruangan?.nama_ruangan ?? '-');
                
                let badgeClass = p.jenis_pembayaran === 'BPJS' ? 'bg-success' : 
                                p.jenis_pembayaran === 'Asuransi' ? 'bg-info' : 'bg-secondary';
                $('#display_jenis_pembayaran').removeClass('bg-secondary bg-success bg-info')
                    .addClass(badgeClass).text(p.jenis_pembayaran);
                if (p.ruangan && p.ruangan.dokters) {
                        renderDokterSelect(p.ruangan.dokters);
                    }
            }
            
        },
        error: function() {
            Swal.fire('Error', 'Gagal memuat data pasien', 'error')
                .then(() => $('#modalResep').modal('hide'));
        }
    });
}

function loadStockObat() {
    $.ajax({
        url: '{{ route("pasiens.getStockObat") }}',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                stockObatData = response.data;
                dosisData = response.dosis || [];
                signaData = response.signa || [];
                console.log('Stock Data Loaded:', stockObatData, dosisData, signaData);
                renderPilihObat();
            }
        },
        error: function(xhr) {
            console.error('Error loading stock:', xhr);
            Swal.fire('Error', 'Gagal memuat data obat', 'error');
        }
    });
}

function renderPilihObat() {
    let html = '';
    stockObatData.forEach(obat => {
        let harga = obat.harga_obat;
        if (currentJenisPembayaran === 'BPJS') harga = obat.harga_bpjs || obat.harga_obat;
        if (currentJenisPembayaran === 'Asuransi') harga = obat.harga_khusus || obat.harga_obat;

        html += `
            <tr>
                <td><strong>${obat.nama}</strong><br><small class="text-muted">${obat.judul || ''}</small></td>
                <td>${obat.satuan}</td>
                <td><span class="badge bg-info">${obat.stock}</span></td>
                <td>Rp ${formatRupiah(harga)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-primary btn-pilih-obat" 
                        data-obat='${JSON.stringify(obat)}' data-harga="${harga}">
                        <i class="ri-add-line"></i> Pilih
                    </button>
                </td>
            </tr>
        `;
    });
    $('#tbodyPilihObat').html(html);
}

$(document).on('click', '.btn-pilih-obat', function() {
    const obatData = JSON.parse($(this).attr('data-obat'));
    const harga = parseFloat($(this).attr('data-harga'));
    
    const obat = {
        id: obatData.id,
        nama: obatData.nama,
        judul: obatData.judul,
        satuan: obatData.satuan,
        stock: obatData.stock,
        jumlah: 1,
        harga: harga,
        subtotal: harga,
        dosis_signa: '',
        aturan_pakai: ''
    };
    
    if (currentModalType === 'nonracik') {
        obatNonRacikList.push(obat);
        renderObatNonRacik();
    } else if (currentModalType === 'racikan' && currentRacikanIndex !== null) {
        racikanList[currentRacikanIndex].obat.push(obat);
        renderRacikan(currentRacikanIndex);
    }
    
    hitungTotal();
    $('#modalPilihObat').modal('hide');
});

function renderObatNonRacik() {
    let html = '';
    if (obatNonRacikList.length === 0) {
        html = `<tr><td colspan="9" class="text-center text-muted py-4">
            <i class="ri-medicine-bottle-line ri-2x d-block mb-2"></i>Belum ada obat non racik</td></tr>`;
    } else {
        obatNonRacikList.forEach((obat, idx) => {
            let dosisOptions = '<option value="">Pilih Dosis</option>';
            dosisData.forEach(d => {
                const dosisText = `${d.jumlah} - ${d.frekuensi}${d.durasi ? ' - ' + d.durasi : ''}`;
                const selected = obat.dosis_signa == dosisText ? 'selected' : '';
                dosisOptions += `<option value="${dosisText}" ${selected}>${dosisText}</option>`;
            });
            
            let signaOptions = '<option value="">Pilih Signa</option>';
            signaData.forEach(s => {
                const signaText = `${s.kode_signa} - ${s.deskripsi}`;
                const selected = obat.aturan_pakai == signaText ? 'selected' : '';
                signaOptions += `<option value="${signaText}" ${selected}>${signaText}</option>`;
            });

            html += `
                <tr>
                    <td><strong>${obat.nama}</strong><br><small class="text-muted">${obat.judul || ''}</small></td>
                    <td>${obat.satuan}</td>
                    <td><span class="badge bg-info">${obat.stock}</span></td>
                    <td><input type="number" class="form-control form-control-sm jumlah-nonracik" 
                        data-index="${idx}" value="${obat.jumlah}" min="1" max="${obat.stock}"></td>
                    <td>Rp ${formatRupiah(obat.harga)}</td>
                    <td><strong>Rp ${formatRupiah(obat.subtotal)}</strong></td>
                    <td>
                        <select class="form-select form-select-sm select2-dosis dosis-nonracik" data-index="${idx}">
                            ${dosisOptions}
                        </select>
                    </td>
                    <td>
                        <select class="form-select form-select-sm select2-signa signa-nonracik" data-index="${idx}">
                            ${signaOptions}
                        </select>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger hapus-nonracik" data-index="${idx}">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    $('#tbodyObatNonRacik').html(html);
    
    $('.select2-dosis, .select2-signa').select2({
        width: '100%',
        dropdownParent: $('#modalResep'),
        placeholder: 'Pilih...'
    });
}

$(document).on('change', '.dosis-nonracik', function() {
    const idx = $(this).data('index');
    const dosisText = $(this).val();
    obatNonRacikList[idx].dosis_signa = dosisText;
});

$(document).on('change', '.signa-nonracik', function() {
    const idx = $(this).data('index');
    const signaText = $(this).val();
    obatNonRacikList[idx].aturan_pakai = signaText;
});

$(document).on('input', '.jumlah-nonracik', function() {
    const idx = $(this).data('index');
    const jumlah = parseInt($(this).val()) || 1;
    obatNonRacikList[idx].jumlah = jumlah;
    obatNonRacikList[idx].subtotal = jumlah * obatNonRacikList[idx].harga;
    renderObatNonRacik();
    hitungTotal();
});

$(document).on('click', '.hapus-nonracik', function() {
    const idx = $(this).data('index');
    obatNonRacikList.splice(idx, 1);
    renderObatNonRacik();
    hitungTotal();
});

function tambahRacikanBaru() {
    const racikan = {
        nama_racikan: '',
        hasil_racikan: '',
        jumlah_racikan: 1,
        jasa_racik: 0,
        obat: []
    };
    racikanList.push(racikan);
    renderAllRacikan();
}

function renderAllRacikan() {
    let html = '';
    if (racikanList.length === 0) {
        html = '<div class="alert alert-info"><i class="ri-information-line me-2"></i>Belum ada racikan</div>';
    } else {
        racikanList.forEach((racikan, idx) => {
            html += generateRacikanHTML(racikan, idx);
        });
    }
    $('#containerRacikan').html(html);
    
    $('.select2-dosis-racik, .select2-signa-racik').select2({
        width: '100%',
        dropdownParent: $('#modalResep'),
        placeholder: 'Pilih...'
    });
}

function generateRacikanHTML(racikan, idx) {
    let obatHTML = '';
    if (racikan.obat.length === 0) {
        obatHTML = '<tr><td colspan="8" class="text-center text-muted">Belum ada obat dalam racikan ini</td></tr>';
    } else {
        racikan.obat.forEach((obat, obatIdx) => {
            let dosisOptions = '<option value="">Pilih Dosis</option>';
            dosisData.forEach(d => {
                const dosisText = `${d.jumlah} - ${d.frekuensi}${d.durasi ? ' - ' + d.durasi : ''}`;
                const selected = obat.dosis_signa == dosisText ? 'selected' : '';
                dosisOptions += `<option value="${dosisText}" ${selected}>${dosisText}</option>`;
            });
            
            let signaOptions = '<option value="">Pilih Signa</option>';
            signaData.forEach(s => {
                const signaText = `${s.kode_signa} - ${s.deskripsi}`;
                const selected = obat.aturan_pakai == signaText ? 'selected' : '';
                signaOptions += `<option value="${signaText}" ${selected}>${signaText}</option>`;
            });

            obatHTML += `
                <tr>
                    <td><strong>${obat.nama}</strong><br><small class="text-muted">${obat.judul || ''}</small></td>
                    <td>${obat.satuan}</td>
                    <td><span class="badge bg-info">${obat.stock}</span></td>
                    <td><input type="number" class="form-control form-control-sm jumlah-racik" 
                        data-racik="${idx}" data-obat="${obatIdx}" value="${obat.jumlah}" min="1" max="${obat.stock}"></td>
                    <td>Rp ${formatRupiah(obat.harga)}</td>
                    <td>
                        <select class="form-select form-select-sm select2-dosis-racik dosis-racik" 
                            data-racik="${idx}" data-obat="${obatIdx}">
                            ${dosisOptions}
                        </select>
                    </td>
                    <td>
                        <select class="form-select form-select-sm select2-signa-racik signa-racik" 
                            data-racik="${idx}" data-obat="${obatIdx}">
                            ${signaOptions}
                        </select>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger hapus-obat-racik" 
                            data-racik="${idx}" data-obat="${obatIdx}">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    
    return `
        <div class="card racikan-card">
            <div class="card-header racikan-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="ri-flask-line me-2"></i>Racikan #${idx + 1}</h6>
                    <button type="button" class="btn btn-sm btn-danger hapus-racikan" data-index="${idx}">
                        <i class="ri-delete-bin-line me-1"></i>Hapus Racikan
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Nama Racikan</label>
                        <input type="text" class="form-control form-control-sm nama-racikan" 
                            data-index="${idx}" value="${racikan.nama_racikan}" placeholder="Racikan Batuk">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Hasil Racikan</label>
                        <select class="form-select form-select-sm hasil-racikan" data-index="${idx}">
                            <option value="">Pilih</option>
                            <option value="Kapsul" ${racikan.hasil_racikan === 'Kapsul' ? 'selected' : ''}>Kapsul</option>
                            <option value="Tablet" ${racikan.hasil_racikan === 'Tablet' ? 'selected' : ''}>Tablet</option>
                            <option value="Puyer" ${racikan.hasil_racikan === 'Puyer' ? 'selected' : ''}>Puyer</option>
                            <option value="Sirup" ${racikan.hasil_racikan === 'Sirup' ? 'selected' : ''}>Sirup</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Jumlah</label>
                        <input type="number" class="form-control form-control-sm jumlah-racikan" 
                            data-index="${idx}" value="${racikan.jumlah_racikan}" min="1">
                    </div>
                </div>
                
                <div class="mb-2">
                    <button type="button" class="btn btn-sm btn-success tambah-obat-racik" data-index="${idx}">
                        <i class="ri-add-line me-1"></i>Tambah Obat ke Racikan
                    </button>
                </div>
                
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th width="25%">Nama Obat</th>
                            <th width="10%">Satuan</th>
                            <th width="8%">Stock</th>
                            <th width="8%">Jumlah</th>
                            <th width="12%">Harga</th>
                            <th width="15%">Dosis</th>
                            <th width="15%">Signa</th>
                            <th width="7%"></th>
                        </tr>
                    </thead>
                    <tbody>${obatHTML}</tbody>
                </table>
            </div>
        </div>
    `;
}

function renderRacikan(idx) {
    const racikan = racikanList[idx];
    const newHTML = generateRacikanHTML(racikan, idx);
    $(`[data-index="${idx}"]`).closest('.card').replaceWith(newHTML);
    
    $('.select2-dosis-racik, .select2-signa-racik').select2({
        width: '100%',
        dropdownParent: $('#modalResep'),
        placeholder: 'Pilih...'
    });
}

$(document).on('input', '.nama-racikan', function() {
    racikanList[$(this).data('index')].nama_racikan = $(this).val();
});

$(document).on('change', '.hasil-racikan', function() {
    racikanList[$(this).data('index')].hasil_racikan = $(this).val();
});

$(document).on('input', '.jumlah-racikan', function() {
    racikanList[$(this).data('index')].jumlah_racikan = parseInt($(this).val()) || 1;
});

$(document).on('click', '.tambah-obat-racik', function() {
    currentModalType = 'racikan';
    currentRacikanIndex = $(this).data('index');
    loadStockObat();
    $('#modalPilihObat').modal('show');
});

$(document).on('change', '.dosis-racik', function() {
    const ridx = $(this).data('racik');
    const oidx = $(this).data('obat');
    const dosisText = $(this).val();
    racikanList[ridx].obat[oidx].dosis_signa = dosisText;
});

$(document).on('change', '.signa-racik', function() {
    const ridx = $(this).data('racik');
    const oidx = $(this).data('obat');
    const signaText = $(this).val();
    racikanList[ridx].obat[oidx].aturan_pakai = signaText;
});

$(document).on('input', '.jumlah-racik', function() {
    const ridx = $(this).data('racik');
    const oidx = $(this).data('obat');
    const jumlah = parseInt($(this).val()) || 1;
    racikanList[ridx].obat[oidx].jumlah = jumlah;
    racikanList[ridx].obat[oidx].subtotal = jumlah * racikanList[ridx].obat[oidx].harga;
    renderRacikan(ridx);
    hitungTotal();
});

$(document).on('click', '.hapus-obat-racik', function() {
    const ridx = $(this).data('racik');
    const oidx = $(this).data('obat');
    racikanList[ridx].obat.splice(oidx, 1);
    renderRacikan(ridx);
    hitungTotal();
});

$(document).on('click', '.hapus-racikan', function() {
    const idx = $(this).data('index');
    racikanList.splice(idx, 1);
    renderAllRacikan();
    hitungTotal();
});

function hitungTotal() {
    let totalObat = 0;
    
    obatNonRacikList.forEach(obat => {
        totalObat += obat.subtotal;
    });
    
    racikanList.forEach(racikan => {
        racikan.obat.forEach(obat => {
            totalObat += obat.subtotal;
        });
    });
    
    const diskonInput = parseFloat($('#diskon').val()) || 0;
    let nilaiDiskon = 0;
    
    if (diskonType === 'percent') {
        nilaiDiskon = (totalObat * diskonInput) / 100;
    } else {
        nilaiDiskon = diskonInput;
    }
    
    const subtotalSetelahDiskon = totalObat - nilaiDiskon;
    
    const pajakInput = parseFloat($('#pajak').val()) || 0;
    let nilaiPajak = 0;
    
    if (pajakType === 'percent') {
        nilaiPajak = (subtotalSetelahDiskon * pajakInput) / 100;
    } else {
        nilaiPajak = pajakInput;
    }
    
    const totalBayar = subtotalSetelahDiskon + nilaiPajak;
    
    $('#display_total_obat').text('Rp ' + formatRupiah(totalObat));
    $('#display_nilai_diskon').text('Rp ' + formatRupiah(nilaiDiskon));
    $('#display_nilai_pajak').text('Rp ' + formatRupiah(nilaiPajak));
    $('#display_total_bayar').text('Rp ' + formatRupiah(totalBayar));
}

function simpanResep() {
    if (!$('#pasien_id').val()) {
        Swal.fire('Error', 'Data pasien tidak ditemukan', 'error');
        return;
    }
    
    if (obatNonRacikList.length === 0 && racikanList.length === 0) {
        Swal.fire('Error', 'Minimal harus ada 1 obat atau racikan', 'error');
        return;
    }
    
    for (let i = 0; i < racikanList.length; i++) {
        if (!racikanList[i].nama_racikan) {
            Swal.fire('Error', `Nama racikan #${i+1} harus diisi`, 'error');
            return;
        }
        if (racikanList[i].obat.length === 0) {
            Swal.fire('Error', `Racikan #${i+1} belum ada obat`, 'error');
            return;
        }
    }
    
    for (let i = 0; i < obatNonRacikList.length; i++) {
        if (!obatNonRacikList[i].dosis_signa) {
            Swal.fire('Error', `Dosis untuk obat "${obatNonRacikList[i].nama}" belum dipilih`, 'error');
            return;
        }
    }

    let statusObat = 'non_racik';
    if (racikanList.length > 0) {
        statusObat = 'racik';
    }
    
    const formData = {
        pasien_id: $('#pasien_id').val(),
        obat_non_racik: obatNonRacikList,
        dokter_resep: $('#dokter_id').val(),
        status_obat: statusObat,
        racikan: racikanList,
        diskon: parseFloat($('#diskon').val()) || 0,
        diskon_type: diskonType,
        pajak: parseFloat($('#pajak').val()) || 0,
        pajak_type: pajakType,
        keterangan: $('#keterangan').val()
    };
    
    console.log('Data yang akan dikirim:', formData);
    
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin menyimpan resep ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            submitResep(formData);
        }
    });
}

function submitResep(formData) {
    $.ajax({
        url: '{{ route("pasiens.storeResep") }}',
        method: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            Swal.fire({
                title: 'Menyimpan Resep...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    html: response.message,
                    confirmButtonText: 'OK'
                }).then(() => {
                    $('#modalResep').modal('hide');
                    location.reload();
                });
            }
        },
        error: function(xhr) {
            let errorMsg = 'Terjadi kesalahan saat menyimpan resep';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: errorMsg
            });
        }
    });
}

function resetForm() {
    $('#formResep')[0].reset();
    obatNonRacikList = [];
    racikanList = [];
    diskonType = 'percent';
    pajakType = 'percent';
    $('#btnToggleDiskon').html('%');
    $('#btnTogglePajak').html('%');
    renderObatNonRacik();
    renderAllRacikan();
    hitungTotal();
}

function setDataPasienResep(pasienId) {
    $('#modalResep').modal('show');
    loadDataPasien(pasienId);
}

function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID').format(angka);
}
</script>

@endpush