@extends('layouts.app')

@section('title', 'Daftar Tagihan Purchase Order')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Tagihan PO</li>
@endsection

@section('content')
<div class="container-fluid">
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

    <!-- Tab Navigation -->
    <ul class="nav nav-tabs nav-tabs-custom mb-4" id="tagihanTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="aktif-tab" data-bs-toggle="tab" data-bs-target="#aktif" type="button" role="tab">
                <i class="ri-file-list-3-line me-2"></i>Tagihan Aktif
                <span class="badge bg-primary ms-2">{{ $tagihanAktif->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="draft-tab" data-bs-toggle="tab" data-bs-target="#draft" type="button" role="tab">
                <i class="ri-draft-line me-2"></i>Draft
                <span class="badge bg-secondary ms-2">{{ $tagihanDraft->count() }}</span>
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="tagihanTabContent">
        
        <!-- TAGIHAN AKTIF TAB -->
        <div class="tab-pane fade show active" id="aktif" role="tabpanel">
            <!-- Total Outstanding Card -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="card-body text-white">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="text-white mb-2">
                                        <i class="ri-money-dollar-circle-line me-2"></i>Total Outstanding
                                    </h5>
                                    <h2 class="text-white mb-0">
                                        Rp {{ number_format($tagihanAktif->whereNotIn('status', ['lunas', 'dibatalkan'])->sum('sisa_tagihan'), 0, ',', '.') }}
                                    </h2>
                                    <small class="text-white-50">Dari {{ $tagihanAktif->whereNotIn('status', ['lunas', 'dibatalkan'])->count() }} tagihan aktif</small>
                                </div>
                                <div class="col-md-4 text-end">
                                    <i class="ri-wallet-3-line" style="font-size: 5rem; opacity: 0.3;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Tagihan Aktif -->
            @include('tagihan.partials.table', ['tagihan' => $tagihanAktif, 'tabType' => 'aktif', 'suppliers' => $suppliers])
        </div>

        <!-- DRAFT TAB -->
        <div class="tab-pane fade {{ request('tab') == 'draft' ? 'show active' : '' }}" id="draft" role="tabpanel">
            <!-- Draft Statistics -->
            <div class="row mb-4">
                <div class="col-xl-4 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm rounded-circle bg-secondary bg-soft">
                                        <span class="avatar-title rounded-circle bg-secondary bg-gradient">
                                            <i class="ri-draft-line fs-4 text-white"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Total Draft</p>
                                    <h4 class="mb-0">{{ $tagihanDraft->count() }}</h4>
                                    <small class="text-muted">Belum diproses</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #868e96 0%, #6c757d 100%);">
                        <div class="card-body text-white">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="text-white mb-2">
                                        <i class="ri-money-dollar-circle-line me-2"></i>Total Nilai Draft
                                    </h5>
                                    <h2 class="text-white mb-0">
                                        Rp {{ number_format($tagihanDraft->sum('grand_total'), 0, ',', '.') }}
                                    </h2>
                                    <small class="text-white-50">Dari {{ $tagihanDraft->count() }} tagihan draft</small>
                                </div>
                                <div class="col-md-4 text-end">
                                    <i class="ri-file-draft-line" style="font-size: 5rem; opacity: 0.3;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Tagihan Draft -->
            @include('tagihan.partials.table', ['tagihan' => $tagihanDraft, 'tabType' => 'draft', 'suppliers' => $suppliers])
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .nav-tabs-custom {
        border-bottom: 2px solid #e9ecef;
    }
    
    .nav-tabs-custom .nav-link {
        border: none;
        color: #6c757d;
        padding: 1rem 1.5rem;
        font-weight: 500;
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
    }
    
    .nav-tabs-custom .nav-link:hover {
        color: #495057;
        border-bottom-color: #dee2e6;
    }
    
    .nav-tabs-custom .nav-link.active {
        color: #0d6efd;
        border-bottom-color: #0d6efd;
        background: transparent;
    }
    
    .nav-tabs-custom .nav-link .badge {
        font-size: 0.75rem;
    }

    .badge {
        font-weight: 500;
        padding: 0.4em 0.8em;
        font-size: 0.85rem;
    }
    
    .badge-sm {
        font-size: 0.75rem;
        padding: 0.25em 0.6em;
    }
    
    .avatar-xs {
        height: 2rem;
        width: 2rem;
    }
    
    .avatar-sm {
        width: 3rem;
        height: 3rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .avatar-title {
        align-items: center;
        display: flex;
        font-weight: 600;
        height: 100%;
        justify-content: center;
        width: 100%;
    }
    
    .bg-soft {
        opacity: 0.1;
    }
    
    .bg-soft-primary {
        background-color: rgba(13, 110, 253, 0.15) !important;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
    
    .card {
        border-radius: 0.5rem;
    }

    .dropdown-item i {
        width: 20px;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .table {
        border: 1px solid #ced4da !important;
    }

    .progress {
        background-color: #e9ecef;
    }

    .table-responsive {
        min-height: auto !important;
        max-height: none !important;
    }

    .tab-content {
        min-height: auto !important;
    }

    .tab-pane {
        min-height: auto !important;
    }

    /* Print Styles */
    @media print {
        .btn, .nav-tabs, .card-header, .filter-section {
            display: none !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        body {
            background: white !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $("#tagihanTable").DataTable({
            ordering: false,
            searching: false,
            lengthChange: false
        });
    });

    // Export Excel Function
    function exportExcel(tab) {
        const params = new URLSearchParams(window.location.search);
        params.set('export', 'excel');
        params.set('tab', tab);
        window.location.href = '{{ route("tagihan.index") }}?' + params.toString();
    }

    // Export PDF Function
    function exportPDF(tab) {
        const params = new URLSearchParams(window.location.search);
        params.set('export', 'pdf');
        params.set('tab', tab);
        window.open('{{ route("tagihan.index") }}?' + params.toString(), '_blank');
    }

    // Print Function
    function printTagihan(tab) {
        const params = new URLSearchParams(window.location.search);
        params.set('print', 'true');
        params.set('tab', tab);
        
        const printWindow = window.open('{{ route("tagihan.index") }}?' + params.toString(), '_blank');
        printWindow.onload = function() {
            printWindow.print();
        };
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Auto dismiss alerts
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Handle tab from URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || 'aktif';
        
        if (activeTab === 'draft') {
            const tabTrigger = new bootstrap.Tab(document.querySelector('#draft-tab'));
            tabTrigger.show();
        }

        // Save active tab to URL when switching tabs
        document.querySelectorAll('#tagihanTabs button').forEach(button => {
            button.addEventListener('shown.bs.tab', function (e) {
                const tabId = e.target.getAttribute('data-bs-target').replace('#', '');
                
                const url = new URL(window.location);
                url.searchParams.set('tab', tabId);
                window.history.pushState({}, '', url);
            });
        });
    });
</script>
@endpush