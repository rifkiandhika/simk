<div class="card mb-3 border">
    <div class="card-body bg-light">
        <form method="GET" action="{{ route('po.index') }}" id="filterForm{{ ucfirst($type) }}">
            <input type="hidden" name="type" value="{{ $type }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Filter Role</label>
                    <select class="form-select form-select-sm" name="role">
                        <option value="">Semua Role</option>
                        <option value="apotik" {{ request('role') == 'apotik' ? 'selected' : '' }}>Apotik</option>
                        <option value="gudang" {{ request('role') == 'gudang' ? 'selected' : '' }}>Gudang</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Filter Status</label>
                    <select class="form-select form-select-sm" name="status">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="menunggu_persetujuan_kepala_gudang" {{ request('status') == 'menunggu_persetujuan_kepala_gudang' ? 'selected' : '' }}>Menunggu Kepala Gudang</option>
                        <option value="menunggu_persetujuan_kasir" {{ request('status') == 'menunggu_persetujuan_kasir' ? 'selected' : '' }}>Menunggu Kasir</option>
                        <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="dikirim_ke_supplier" {{ request('status') == 'dikirim_ke_supplier' ? 'selected' : '' }}>Dikirim ke Supplier</option>
                        <option value="dalam_pengiriman" {{ request('status') == 'dalam_pengiriman' ? 'selected' : '' }}>Dalam Pengiriman</option>
                        <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Cari</label>
                    <input type="text" class="form-control form-control-sm" name="search" 
                           placeholder="Cari No PO, GR, Invoice atau Supplier..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold d-block">&nbsp;</label>
                    <button type="submit" class="btn btn-sm btn-info w-100">
                        <i class="ri-filter-line me-1"></i>Terapkan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>