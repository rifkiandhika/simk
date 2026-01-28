{{-- Sub Tabs untuk Internal: PO, GR, Sukses --}}
<ul class="nav nav-tabs nav-tabs-sub mb-3" id="internalSubTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="internal-po-tab" data-bs-toggle="tab" data-bs-target="#internal-po-content" 
                type="button" role="tab">
            <i class="ri-file-list-line me-2"></i>Purchase Order
            <span class="badge bg-primary ms-2">{{ $purchaseOrders->whereNull('no_gr')->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="internal-gr-tab" data-bs-toggle="tab" data-bs-target="#internal-gr-content" 
                type="button" role="tab">
            <i class="ri-inbox-line me-2"></i>Goods Receipt (GR)
            <span class="badge bg-info ms-2">{{ $purchaseOrders->whereNotNull('no_gr')->where('status', '!=', 'selesai')->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="internal-success-tab" data-bs-toggle="tab" data-bs-target="#internal-success-content" 
                type="button" role="tab">
            <i class="ri-checkbox-circle-line me-2"></i>Sukses
            <span class="badge bg-success ms-2">{{ $purchaseOrders->where('status', 'selesai')->count() }}</span>
        </button>
    </li>
</ul>

{{-- Filter Section --}}
@include('po.partials.filter-form', ['type' => 'internal'])

{{-- Tab Content --}}
<div class="tab-content" id="internalSubTabsContent">
    {{-- Tab PO --}}
    <div class="tab-pane fade show active" id="internal-po-content" role="tabpanel">
        @include('po.partials.po-table', [
            'purchaseOrders' => $purchaseOrders->whereNull('no_gr'),
            'tableId' => 'internalPoTable'
        ])
    </div>

    {{-- Tab GR --}}
    <div class="tab-pane fade" id="internal-gr-content" role="tabpanel">
        @include('po.partials.gr-table-internal', [
            'purchaseOrders' => $purchaseOrders->whereNotNull('no_gr')->where('status', '!=', 'selesai'),
            'tableId' => 'internalGrTable'
        ])
    </div>

    {{-- Tab Sukses --}}
    <div class="tab-pane fade" id="internal-success-content" role="tabpanel">
        @include('po.partials.success-table', [
            'purchaseOrders' => $purchaseOrders->where('status', 'selesai'),
            'tableId' => 'internalSuccessTable'
        ])
    </div>
</div>