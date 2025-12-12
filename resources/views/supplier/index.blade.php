@extends('layouts.app')

@section('title', 'Supplier')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Supplier</li>
@endsection

@section('page-actions')
    <div class="d-flex flex-row gap-1 day-sorting">
        <button class="btn btn-sm btn-primary">Today</button>
        <button class="btn btn-sm">7d</button>
        <button class="btn btn-sm">2w</button>
        <button class="btn btn-sm">1m</button>
        <button class="btn btn-sm">3m</button>
        <button class="btn btn-sm">6m</button>
        <button class="btn btn-sm">1y</button>
    </div>
@endsection

@section('content')
   <div class="app-body">
    <div class="row">
                <div class="col-xl-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="d-flex justify-content-between">
                                @if (session('error'))
                                    <p class="alert alert-danger">{{ session('error') }}</p>
                                @endif
                                
                                <a class="btn btn-outline-primary" href="{{ route('suppliers.create') }}">+ Add Supplier</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-borderless table-responsive w-100 d-block d-md-table" id="myTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>NPWP</th>
                                        <th>Supplier</th>
                                        <th>Alamat</th>
                                        <th>Catatan</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($suppliers as $x => $data)
                                        <tr>
                                            <td>{{ $x + 1 }}</td>
                                            <td>{{ $data->npwp }}</td>
                                            <td>{{ $data->nama_supplier }}</td>
                                            <td>{{ $data->alamat }}</td>
                                            <td>{{ $data->note ?? '' }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn shadow" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="ri-more-2-fill"></i></button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow">
                                                       @if ($data->file)
                                                            <li class="text-center">
                                                                <button type="button" 
                                                                        class="dropdown-item viewPdfBtn" 
                                                                        data-file="{{ asset($data->file) }}"
                                                                        data-file2="{{ $data->file2 ? asset($data->file2) : '' }}"
                                                                        data-supplier="{{ $data->nama_supplier }}">
                                                                    <i class="ri-file-pdf-2-line text-danger"></i> Lihat File
                                                                </button>
                                                            </li>
                                                        @endif

                                                        <li class="text-center">
                                                            <a class="dropdown-item editBtn" 
                                                            href="{{ route('suppliers.edit', $data->id) }}">
                                                                <i class="ri-pencil-fill"></i> Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('suppliers.destroy', $data->id) }}" method="POST" class="d-inline delete-confirm">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn w-100 btn-outline-secondary">
                                                                    <i class="ri-delete-bin-6-line"></i> Hapus
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-info">
                                            <h5 class="modal-title text-white" id="pdfModalLabel">Lihat File Supplier</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body" id="pdfViewerWrapper" style="min-height: 80vh;">
                                            <div class="text-center text-muted mt-5" id="loadingText">
                                                <i class="ri-loader-4-line ri-spin"></i> Memuat file...
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
@endsection

@push('styles')
    <!-- Custom styles for dashboard -->
    <style>
        .bg-2 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
@endpush

@push('scripts')
    <!-- Dashboard specific scripts -->
    <script>
        // Dashboard initialization
        $(document).ready(function() {
        });
    </script>

    {{-- Script Modal pdf --}}
    <script>
        $(document).ready(function () {
            let pdfModal = new bootstrap.Modal(document.getElementById('pdfModal'));

            $('.viewPdfBtn').on('click', function () {
                let fileUrl = $(this).data('file');
                let file2Url = $(this).data('file2'); 
                let supplierName = $(this).data('supplier');

                
                $('#pdfModalLabel').text('File Supplier: ' + supplierName);
                
                
                $('#pdfViewerWrapper').html(`
                    <div class="text-center text-muted mt-5">
                        <i class="ri-loader-4-line ri-spin"></i> Memuat file...
                    </div>
                `);

                
                pdfModal.show();

                
                setTimeout(function() {
                    let viewerHTML = '';

                    if (fileUrl && file2Url) {
                        
                        viewerHTML = `
                            <ul class="nav nav-tabs" id="pdfTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="file1-tab" data-bs-toggle="tab" data-bs-target="#file1" type="button" role="tab">File 1</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="file2-tab" data-bs-toggle="tab" data-bs-target="#file2" type="button" role="tab">File 2</button>
                                </li>
                            </ul>
                            <div class="tab-content border border-top-0 rounded-bottom p-2" id="pdfTabContent">
                                <div class="tab-pane fade show active" id="file1" role="tabpanel">
                                    <iframe src="${fileUrl}" style="width:100%; height:75vh; border:none;" title="File 1 PDF"></iframe>
                                </div>
                                <div class="tab-pane fade" id="file2" role="tabpanel">
                                    <iframe src="${file2Url}" style="width:100%; height:75vh; border:none;" title="File 2 PDF"></iframe>
                                </div>
                            </div>
                        `;
                    } else if (fileUrl) {
                        
                        viewerHTML = `
                            <iframe src="${fileUrl}" style="width:100%; height:80vh; border:none;" title="File PDF"></iframe>
                        `;
                    } else if (file2Url) {
                        
                        viewerHTML = `
                            <iframe src="${file2Url}" style="width:100%; height:80vh; border:none;" title="File2 PDF"></iframe>
                        `;
                    } else {
                        
                        viewerHTML = `
                            <div class="text-center text-danger mt-5">
                                <i class="ri-alert-line"></i> Tidak ada file yang tersedia.
                            </div>
                        `;
                    }

                    $('#pdfViewerWrapper').html(viewerHTML);
                }, 300); 
            });

            
            $('#pdfModal').on('hidden.bs.modal', function () {
                $('#pdfViewerWrapper').html('');
            });
        });
    </script>

@endpush