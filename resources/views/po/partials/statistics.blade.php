<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded-circle bg-primary bg-soft">
                            <span class="avatar-title rounded-circle bg-primary bg-gradient">
                                <i class="ri-file-list-3-line fs-4 text-white"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-1">Total PO</p>
                        <h4 class="mb-0">{{ $purchaseOrders->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded-circle bg-warning bg-soft">
                            <span class="avatar-title rounded-circle bg-warning bg-gradient">
                                <i class="ri-time-line fs-4 text-white"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-1">Menunggu Approval</p>
                        <h4 class="mb-0">{{ $purchaseOrders->whereIn('status', ['menunggu_persetujuan_kepala_gudang', 'menunggu_persetujuan_kasir'])->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded-circle bg-success bg-soft">
                            <span class="avatar-title rounded-circle bg-success bg-gradient">
                                <i class="ri-checkbox-circle-line fs-4 text-white"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-1">Disetujui</p>
                        <h4 class="mb-0">{{ $purchaseOrders->where('status', 'disetujui')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded-circle bg-info bg-soft">
                            <span class="avatar-title rounded-circle bg-info bg-gradient">
                                <i class="ri-truck-line fs-4 text-white"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-1">Dalam Pengiriman</p>
                        <h4 class="mb-0">{{ $purchaseOrders->whereIn('status', ['dikirim_ke_supplier', 'dalam_pengiriman'])->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>