{{-- Sub Tabs untuk External: PO, GR, Invoice --}}
<ul class="nav nav-tabs nav-tabs-sub mb-3" id="externalSubTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="external-po-tab" data-bs-toggle="tab" data-bs-target="#external-po-content" 
                type="button" role="tab">
            <i class="ri-file-list-line me-2"></i>Purchase Order
            <span class="badge bg-primary ms-2">{{ $purchaseOrders->whereNull('no_gr')->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="external-gr-tab" data-bs-toggle="tab" data-bs-target="#external-gr-content" 
                type="button" role="tab">
            <i class="ri-inbox-line me-2"></i>Goods Receipt (GR)
            <span class="badge bg-info ms-2">{{ $purchaseOrders->whereNotNull('no_gr')->whereNull('no_invoice')->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="external-invoice-tab" data-bs-toggle="tab" data-bs-target="#external-invoice-content" 
                type="button" role="tab">
            <i class="ri-file-text-line me-2"></i>Invoice
            <span class="badge bg-success ms-2">{{ $purchaseOrders->whereNotNull('no_invoice')->count() }}</span>
        </button>
    </li>
</ul>

{{-- Filter Section --}}
@include('po.partials.filter-form', ['type' => 'external'])

{{-- Tab Content --}}
<div class="tab-content" id="externalSubTabsContent">
    {{-- Tab PO --}}
    <div class="tab-pane fade show active" id="external-po-content" role="tabpanel">
        @include('po.partials.po-table', [
            'purchaseOrders' => $purchaseOrders->whereNull('no_gr'),
            'tableId' => 'externalPoTable'
        ])
    </div>

    {{-- Tab GR --}}
    <div class="tab-pane fade" id="external-gr-content" role="tabpanel">
        @include('po.partials.gr-table-external', [
            'purchaseOrders' => $purchaseOrders->whereNotNull('no_gr')->whereNull('no_invoice'),
            'tableId' => 'externalGrTable'
        ])
    </div>

    {{-- Tab Invoice --}}
    <div class="tab-pane fade" id="external-invoice-content" role="tabpanel">
        @include('po.partials.invoice-table', [
            'purchaseOrders' => $purchaseOrders->whereNotNull('no_invoice'),
            'tableId' => 'externalInvoiceTable'
        ])
    </div>
</div>