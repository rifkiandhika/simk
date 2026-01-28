@extends('layouts.app')

@section('title', 'Daftar Purchase Order')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Purchase Order</li>
@endsection

@section('content')
<div class="app-body">
    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-checkbox-circle-line me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    @include('po.partials.statistics', ['purchaseOrders' => $purchaseOrders])

    <!-- Main Table with Tabs -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="ri-list-check-2 me-2"></i>Daftar Purchase Order
                        </h5>
                        <div class="btn-group">
                            <a class="btn btn-primary btn-sm" href="{{ route('po.create', ['type' => 'internal']) }}">
                                <i class="ri-add-circle-line me-1"></i>PO Internal
                            </a>
                            <a class="btn btn-success btn-sm" href="{{ route('po.create', ['type' => 'eksternal']) }}">
                                <i class="ri-add-circle-line me-1"></i>PO Eksternal
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Main Tabs: Internal / External --}}
                <div class="card-body border-bottom p-0">
                    <ul class="nav nav-tabs nav-tabs-custom" id="mainTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="internal-tab" data-bs-toggle="tab" data-bs-target="#internal-content" 
                                    type="button" role="tab" aria-controls="internal-content" aria-selected="true">
                                <i class="ri-home-line me-2"></i>Internal
                                <span class="badge bg-primary ms-2">{{ $purchaseOrders->where('tipe_po', 'internal')->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="external-tab" data-bs-toggle="tab" data-bs-target="#external-content" 
                                    type="button" role="tab" aria-controls="external-content" aria-selected="false">
                                <i class="ri-building-line me-2"></i>External
                                <span class="badge bg-success ms-2">{{ $purchaseOrders->where('tipe_po', 'eksternal')->count() }}</span>
                            </button>
                        </li>
                    </ul>
                </div>

                {{-- Tab Content --}}
                <div class="card-body">
                    <div class="tab-content" id="mainTabsContent">
                        {{-- Tab Internal --}}
                        <div class="tab-pane fade show active" id="internal-content" role="tabpanel" aria-labelledby="internal-tab">
                            @include('po.partials.internal-tabs', ['purchaseOrders' => $purchaseOrders->where('tipe_po', 'internal')])
                        </div>

                        {{-- Tab External --}}
                        <div class="tab-pane fade" id="external-content" role="tabpanel" aria-labelledby="external-tab">
                            @include('po.partials.external-tabs', ['purchaseOrders' => $purchaseOrders->where('tipe_po', 'eksternal')])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modals --}}
@include('po.partials.modals')

@endsection

@push('styles')
@include('po.partials.styles')
@endpush

@push('scripts')
@include('po.partials.scripts')
@endpush