<style>
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

    /* Main Tabs Styling (Internal/External) */
    .nav-tabs-custom {
        border-bottom: 2px solid #dee2e6;
    }

    .nav-tabs-custom .nav-link {
        border: none;
        color: #6c757d;
        padding: 1rem 1.5rem;
        font-weight: 600;
        background: transparent;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
        font-size: 1rem;
    }

    .nav-tabs-custom .nav-link:hover {
        color: #0d6efd;
        border-bottom-color: #0d6efd;
        background: rgba(13, 110, 253, 0.05);
    }

    .nav-tabs-custom .nav-link.active {
        color: #0d6efd;
        border-bottom-color: #0d6efd;
        background: rgba(13, 110, 253, 0.05);
    }

    .nav-tabs-custom .nav-link .badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }

    /* Sub Tabs Styling (PO/GR/Invoice) */
    .nav-tabs-sub {
        border-bottom: 1px solid #dee2e6;
        background: #f8f9fa;
        padding: 0.5rem 1rem 0;
        margin: -1rem -1rem 1rem -1rem;
        border-radius: 0.5rem 0.5rem 0 0;
    }

    .nav-tabs-sub .nav-link {
        border: none;
        color: #6c757d;
        padding: 0.75rem 1.25rem;
        font-weight: 500;
        background: transparent;
        border-bottom: 2px solid transparent;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .nav-tabs-sub .nav-link:hover {
        color: #495057;
        border-bottom-color: #adb5bd;
        background: rgba(0, 0, 0, 0.03);
    }

    .nav-tabs-sub .nav-link.active {
        color: #212529;
        border-bottom-color: #0d6efd;
        background: #fff;
        font-weight: 600;
    }

    .nav-tabs-sub .nav-link .badge {
        font-size: 0.65rem;
        padding: 0.2rem 0.4rem;
    }

    /* Card Body Padding for Sub Tabs */
    #internal-content .tab-content,
    #external-content .tab-content {
        padding-top: 1rem;
    }
</style>