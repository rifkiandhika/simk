<!-- Modal Buat Resep -->
<div class="modal fade" id="modalResep" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
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
                        </div>
                    </div>
                </div>

                <!-- Form Resep -->
                <form id="formResep">
                    <input type="hidden" id="pasien_id" name="pasien_id">
                    <input type="hidden" id="jenis_pembayaran" name="jenis_pembayaran">
                    <input type="hidden" name="status_obat" id="status_obat" value="non_racik">

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
                                                    <th width="30%">Nama Obat</th>
                                                    <th width="12%">Satuan</th>
                                                    <th width="8%">Stock</th>
                                                    <th width="8%">Jumlah</th>
                                                    <th width="12%">Harga Satuan</th>
                                                    <th width="12%">Subtotal</th>
                                                    <th width="8%">Dosis</th>
                                                    <th width="5%"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyObatNonRacik">
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted py-4">
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
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Embalase:</span>
                                        <input type="number" class="form-control form-control-sm w-50" 
                                            id="embalase" name="embalase" value="0" min="0">
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Total Jasa Racik:</span>
                                        <strong id="display_total_jasa_racik">Rp 0</strong>
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
</style>
@endpush
@push('scripts')
    
<script>
        let stockObatData = [];
        let obatNonRacikList = [];
        let racikanList = [];
        let currentJenisPembayaran = 'Umum';
        let currentModalType = '';
        let currentRacikanIndex = null;

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

            $(document).on('input', '#embalase', hitungTotal);
            $('#btnSimpanResep').on('click', simpanResep);
            $('#modalResep').on('hidden.bs.modal', resetForm);
        });

        function setJenisResep(jenis) {
            document.getElementById('status_obat').value = jenis;

            // optional debug
            // console.log('Jenis resep:', jenis);
        }

        function loadDataPasien(pasienId) {
            $.ajax({
                url: `/pasien/${pasienId}/ajax`,
                method: 'GET',
                beforeSend: function() {
                    $('#display_no_rm, #display_nama_pasien, #display_jenis_pembayaran')
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
                        
                        let badgeClass = p.jenis_pembayaran === 'BPJS' ? 'bg-success' : 
                                        p.jenis_pembayaran === 'Asuransi' ? 'bg-info' : 'bg-secondary';
                        $('#display_jenis_pembayaran').removeClass('bg-secondary bg-success bg-info')
                            .addClass(badgeClass).text(p.jenis_pembayaran);
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
                        console.log('Stock Data Loaded:', stockObatData); // ✅ Debug
                        renderPilihObat();
                    }
                },
                error: function(xhr) {
                    console.error('Error loading stock:', xhr); // ✅ Debug
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
                dosis: ''
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
                html = `<tr><td colspan="8" class="text-center text-muted py-4">
                    <i class="ri-medicine-bottle-line ri-2x d-block mb-2"></i>Belum ada obat non racik</td></tr>`;
            } else {
                obatNonRacikList.forEach((obat, idx) => {
                    html += `
                        <tr>
                            <td><strong>${obat.nama}</strong><br><small class="text-muted">${obat.judul || ''}</small></td>
                            <td>${obat.satuan}</td>
                            <td><span class="badge bg-info">${obat.stock}</span></td>
                            <td><input type="number" class="form-control form-control-sm jumlah-nonracik" 
                                data-index="${idx}" value="${obat.jumlah}" min="1" max="${obat.stock}"></td>
                            <td>Rp ${formatRupiah(obat.harga)}</td>
                            <td><strong>Rp ${formatRupiah(obat.subtotal)}</strong></td>
                            <td><input type="text" class="form-control form-control-sm dosis-nonracik" 
                                data-index="${idx}" value="${obat.dosis}" placeholder="3x1"></td>
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
        }

        $(document).on('input', '.jumlah-nonracik', function() {
            const idx = $(this).data('index');
            const jumlah = parseInt($(this).val()) || 1;
            obatNonRacikList[idx].jumlah = jumlah;
            obatNonRacikList[idx].subtotal = jumlah * obatNonRacikList[idx].harga;
            renderObatNonRacik();
            hitungTotal();
        });

        $(document).on('input', '.dosis-nonracik', function() {
            const idx = $(this).data('index');
            obatNonRacikList[idx].dosis = $(this).val();
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
                dosis: '',
                aturan_pakai: '',
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
        }

        function generateRacikanHTML(racikan, idx) {
            let obatHTML = '';
            if (racikan.obat.length === 0) {
                obatHTML = '<tr><td colspan="6" class="text-center text-muted">Belum ada obat dalam racikan ini</td></tr>';
            } else {
                racikan.obat.forEach((obat, obatIdx) => {
                    obatHTML += `
                        <tr>
                            <td><strong>${obat.nama}</strong></td>
                            <td>${obat.satuan}</td>
                            <td><span class="badge bg-info">${obat.stock}</span></td>
                            <td><input type="number" class="form-control form-control-sm jumlah-racik" 
                                data-racik="${idx}" data-obat="${obatIdx}" value="${obat.jumlah}" min="1" max="${obat.stock}"></td>
                            <td>Rp ${formatRupiah(obat.harga)}</td>
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
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Nama Racikan</label>
                                <input type="text" class="form-control form-control-sm nama-racikan" 
                                    data-index="${idx}" value="${racikan.nama_racikan}" placeholder="Racikan Batuk">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Hasil Racikan</label>
                                <select class="form-select form-select-sm hasil-racikan" data-index="${idx}">
                                    <option value="">Pilih</option>
                                    <option value="Kapsul" ${racikan.hasil_racikan === 'Kapsul' ? 'selected' : ''}>Kapsul</option>
                                    <option value="Tablet" ${racikan.hasil_racikan === 'Tablet' ? 'selected' : ''}>Tablet</option>
                                    <option value="Puyer" ${racikan.hasil_racikan === 'Puyer' ? 'selected' : ''}>Puyer</option>
                                    <option value="Sirup" ${racikan.hasil_racikan === 'Sirup' ? 'selected' : ''}>Sirup</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold small">Jumlah</label>
                                <input type="number" class="form-control form-control-sm jumlah-racikan" 
                                    data-index="${idx}" value="${racikan.jumlah_racikan}" min="1">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold small">Dosis</label>
                                <input type="text" class="form-control form-control-sm dosis-racikan" 
                                    data-index="${idx}" value="${racikan.dosis}" placeholder="3x1">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold small">Jasa Racik</label>
                                <input type="number" class="form-control form-control-sm jasa-racik" 
                                    data-index="${idx}" value="${racikan.jasa_racik}" min="0">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">Aturan Pakai</label>
                                <select class="form-select form-select-sm aturan-pakai-racikan" data-index="${idx}">
                                    <option value="">Pilih</option>
                                    <option value="Sebelum Makan" ${racikan.aturan_pakai === 'Sebelum Makan' ? 'selected' : ''}>Sebelum Makan</option>
                                    <option value="Sesudah Makan" ${racikan.aturan_pakai === 'Sesudah Makan' ? 'selected' : ''}>Sesudah Makan</option>
                                    <option value="Saat Makan" ${racikan.aturan_pakai === 'Saat Makan' ? 'selected' : ''}>Saat Makan</option>
                                </select>
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
                                    <th>Nama Obat</th>
                                    <th width="12%">Satuan</th>
                                    <th width="10%">Stock</th>
                                    <th width="10%">Jumlah</th>
                                    <th width="15%">Harga</th>
                                    <th width="8%"></th>
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

        $(document).on('input', '.dosis-racikan', function() {
            racikanList[$(this).data('index')].dosis = $(this).val();
        });

        $(document).on('change', '.aturan-pakai-racikan', function() {
            racikanList[$(this).data('index')].aturan_pakai = $(this).val();
        });

        $(document).on('input', '.jasa-racik', function() {
            racikanList[$(this).data('index')].jasa_racik = parseFloat($(this).val()) || 0;
            hitungTotal();
        });

        $(document).on('click', '.tambah-obat-racik', function() {
            currentModalType = 'racikan';
            currentRacikanIndex = $(this).data('index');
            loadStockObat();
            $('#modalPilihObat').modal('show');
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

        // ===== HITUNG TOTAL =====
        function hitungTotal() {
            let totalObat = 0;
            let totalJasaRacik = 0;
            
            // Total non racik
            obatNonRacikList.forEach(obat => {
                totalObat += obat.subtotal;
            });
            
            // Total racikan
            racikanList.forEach(racikan => {
                racikan.obat.forEach(obat => {
                    totalObat += obat.subtotal;
                });
                totalJasaRacik += racikan.jasa_racik;
            });
            
            const embalase = parseFloat($('#embalase').val()) || 0;
            const totalBayar = totalObat + embalase + totalJasaRacik;
            
            $('#display_total_obat').text('Rp ' + formatRupiah(totalObat));
            $('#display_total_jasa_racik').text('Rp ' + formatRupiah(totalJasaRacik));
            $('#display_total_bayar').text('Rp ' + formatRupiah(totalBayar));
        }

        // ===== SIMPAN RESEP =====
        function simpanResep() {
            if (!$('#pasien_id').val()) {
                Swal.fire('Error', 'Data pasien tidak ditemukan', 'error');
                return;
            }
            
            if (obatNonRacikList.length === 0 && racikanList.length === 0) {
                Swal.fire('Error', 'Minimal harus ada 1 obat atau racikan', 'error');
                return;
            }
            
            // Validasi racikan
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

            let statusObat = 'non_racik';
            if (racikanList.length > 0) {
                statusObat = 'racik';
            }
            
            const formData = {
                pasien_id: $('#pasien_id').val(),
                obat_non_racik: obatNonRacikList,
                status_obat: statusObat,
                racikan: racikanList,
                embalase: parseFloat($('#embalase').val()) || 0,
                keterangan: $('#keterangan').val()
            };
            
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

        // ===== SUBMIT RESEP =====
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

        // ===== RESET FORM =====
        function resetForm() {
            $('#formResep')[0].reset();
            obatNonRacikList = [];
            racikanList = [];
            renderObatNonRacik();
            renderAllRacikan();
            hitungTotal();
        }

        // ===== SET DATA PASIEN (dipanggil dari luar) =====
        function setDataPasienResep(pasienId) {
            $('#modalResep').modal('show');
            loadDataPasien(pasienId);
        }

        // ===== FORMAT RUPIAH =====
        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID').format(angka);
        }
</script>

@endpush