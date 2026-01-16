<!-- Modal Detail Resep -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="ri-file-list-3-line me-2"></i>Detail Resep
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Info Resep & Pasien -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white py-2">
                                <h6 class="mb-0"><i class="ri-file-text-line me-2"></i>Informasi Resep</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td width="140"><strong>No. Resep</strong></td>
                                        <td>: <span id="detail_no_resep" class="text-primary fw-bold">-</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal</strong></td>
                                        <td>: <span id="detail_tanggal">-</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status</strong></td>
                                        <td>: <span id="detail_status">-</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status Obat</strong></td>
                                        <td>: <span id="detail_status_obat">-</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Dokter Pengirim</strong></td>
                                        <td>: <span id="detail_dokter">-</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white py-2">
                                <h6 class="mb-0"><i class="ri-user-line me-2"></i>Informasi Pasien</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td width="140"><strong>No. RM</strong></td>
                                        <td>: <span id="detail_no_rm">-</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nama Pasien</strong></td>
                                        <td>: <span id="detail_nama_pasien">-</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Jenis Pembayaran</strong></td>
                                        <td>: <span id="detail_jenis_pembayaran">-</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Jenis Ruangan</strong></td>
                                        <td>: <span id="detail_jenis_ruangan">-</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nama Ruangan</strong></td>
                                        <td>: <span id="detail_nama_ruangan">-</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Non Racik -->
                <div class="card border-success mb-3" id="detail_non_racik_info" style="display: none;">
                    <div class="card-header bg-success text-white py-2">
                        <h6 class="mb-0">
                            <i class="ri-medicine-bottle-line me-2"></i>Informasi Obat (Non Racik)
                        </h6>
                    </div>
                    <div class="card-body" id="detail_non_racik_body">
                        <!-- Diisi via JS -->
                    </div>
                </div>


                {{-- <div class="card mb-3" id="detail_pembayaran_info">
                    <div class="card-header py-2" id="pembayaran_header">
                        <h6 class="mb-0"><i class="ri-wallet-line me-2"></i>Status Pembayaran</h6>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <small class="text-muted d-block">Status</small>
                                <strong id="detail_status_pembayaran" class="fs-5">-</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Total Tagihan</small>
                                <strong id="detail_total_tagihan">Rp 0</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Sudah Dibayar</small>
                                <strong class="text-success" id="detail_total_dibayar">Rp 0</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Sisa Tagihan</small>
                                <strong class="text-danger" id="detail_sisa_tagihan">Rp 0</strong>
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="alert mb-0 py-2" id="pembayaran_alert">
                            <small id="pembayaran_keterangan">-</small>
                        </div>
                    </div>
                </div> --}}

                <!-- Info Racikan (jika ada) -->
                <div class="card border-warning mb-3" id="detail_racik_info" style="display: none;">
                    <div class="card-header bg-warning text-white py-2">
                        <h6 class="mb-0"><i class="ri-flask-line me-2"></i>Informasi Racikan</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <small class="text-muted d-block">Jenis Racikan</small>
                                <strong id="detail_jenis_racikan">-</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Hasil Racikan</small>
                                <strong id="detail_hasil_racikan">-</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Dosis/Signa</small>
                                <strong id="detail_dosis_signa">-</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Aturan Pakai</small>
                                <strong id="detail_aturan_pakai">-</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daftar Obat -->
                <div class="card border-secondary mb-3">
                    <div class="card-header bg-secondary text-white py-2">
                        <h6 class="mb-0"><i class="ri-medicine-bottle-line me-2"></i>Daftar Obat</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Nama Obat</th>
                                        <th width="100">Satuan</th>
                                        <th width="100" class="text-center">Jumlah</th>
                                        <th width="100" class="text-center">Stock</th>
                                        <th width="140" class="text-end">Harga</th>
                                        <th width="140" class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody_detail_obat">
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-3">
                                            Memuat data obat...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Warning Stock -->
                {{-- <div class="alert alert-danger d-flex align-items-center" id="stock_warning" style="display: none;">
                    <i class="ri-error-warning-line me-2 fs-4"></i>
                    <div>
                        <strong>Perhatian!</strong> Ada obat yang stock-nya tidak mencukupi. 
                        Resep tidak dapat diverifikasi sampai stock tersedia.
                    </div>
                </div> --}}

                <!-- Total -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="card bg-light">
                            <div class="card-body">
                                <strong>Keterangan:</strong>
                                <p class="mb-0" id="detail_keterangan">-</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">

                                {{-- <div class="d-flex justify-content-between mb-2">
                                    <span>Embalase:</span>
                                    <strong id="summary_embalase">Rp 0</strong>
                                </div>

                                <div class="d-flex justify-content-between mb-2" id="summary_jasa_racik_row" style="display:none;">
                                    <span>Jasa Racik:</span>
                                    <strong id="summary_jasa_racik">Rp 0</strong>
                                </div> --}}

                                <div class="d-flex justify-content-between mb-2 text-danger" id="detail_diskon_row" style="display:none;">
                                    <span id="detail_diskon_label">Diskon:</span>
                                    <strong id="detail_diskon">Rp 0</strong>
                                </div>

                                <div class="d-flex justify-content-between mb-2 text-success" id="detail_pajak_row" style="display:none;">
                                    <span id="detail_pajak_label">Pajak:</span>
                                    <strong id="detail_pajak">Rp 0</strong>
                                </div>

                                <hr>

                                <div class="d-flex justify-content-between">
                                    <strong class="text-primary">Total Bayar:</strong>
                                    <strong class="text-primary fs-5" id="detail_total">Rp 0</strong>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i>Tutup
                </button>
                <button type="button" class="btn btn-success" id="btn_verifikasi_modal" style="display: none;">
                    <i class="ri-checkbox-line me-1"></i>Verifikasi Resep
                </button>
                <button type="button" class="btn btn-primary" id="btn_serahkan_modal" style="display: none;">
                    <i class="ri-hand-heart-line me-1"></i>Serahkan Obat
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Button verifikasi dari modal
    $(document).on('click', '#btn_verifikasi_modal', function() {
        const resepId = $(this).attr('data-resep-id');
        $('#modalDetail').modal('hide');
        verifikasiResep(resepId);
    });

    // Button serahkan dari modal
    $(document).on('click', '#btn_serahkan_modal', function() {
        const resepId = $(this).attr('data-resep-id');
        $('#modalDetail').modal('hide');
        serahkanObat(resepId);
    });
});
</script>
@endpush