<!-- Modal Tolak Resep -->
<div class="modal fade" id="modalTolak" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="ri-close-circle-line me-2"></i>Tolak Resep
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="ri-alert-line me-2"></i>
                    Resep yang ditolak tidak dapat diproses kembali!
                </div>

                <input type="hidden" id="resep_id_tolak">
                
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        Alasan Penolakan <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control" id="rejection_reason" rows="4" 
                        placeholder="Contoh: Stock obat XYZ habis, tidak dapat memenuhi resep"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-arrow-left-line me-1"></i>Batal
                </button>
                <button type="button" class="btn btn-danger" id="btnSubmitTolak">
                    <i class="ri-close-circle-line me-1"></i>Tolak Resep
                </button>
            </div>
        </div>
    </div>
</div>