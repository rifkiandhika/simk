{{-- PIN Modal for Submit --}}
<div class="modal fade" id="submitModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Submit PO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Masukkan PIN Anda untuk submit Purchase Order</p>
                <div class="mb-3">
                    <label class="form-label">PIN (6 digit)</label>
                    <input type="password" class="form-control" id="pinSubmit" maxlength="6" placeholder="Masukkan PIN">
                </div>
                <input type="hidden" id="poIdSubmit">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="confirmSubmit()">Submit PO</button>
            </div>
        </div>
    </div>
</div>

{{-- PIN Modal for Delete --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Konfirmasi Hapus PO</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Apakah Anda yakin ingin menghapus Purchase Order ini?</p>
                <div class="alert alert-warning">
                    <i class="ri-alert-line"></i> Tindakan ini tidak dapat dibatalkan!
                </div>
                <div class="mb-3">
                    <label class="form-label">PIN (6 digit)</label>
                    <input type="password" class="form-control" id="pinDelete" maxlength="6" placeholder="Masukkan PIN">
                </div>
                <input type="hidden" id="poIdDelete">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Hapus PO</button>
            </div>
        </div>
    </div>
</div>