@extends('layouts.app')

@section('title', 'Permintaan')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Permintaan</li>
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
                                
                                <a class="btn btn-outline-primary" href="{{ route('permintaans.create') }}">+ Add Permintaan</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-borderless table-responsive w-100 d-block d-md-table" id="myTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No Requisition</th>
                                        <th>Supplier</th>
                                        <th>Tanggal</th>
                                        <th>Department</th>
                                        <th>Pembuat</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($permintaan as $x => $data)
                                    <tr>
                                        <td>{{ $x+1 }}</td>
                                        <td>{{ $data->no_requisition }}</td>
                                        <td>{{ $data->supplier->supplier ?? '' }}</td>
                                        <td>{{ $data->tanggal }}</td>
                                        <td>{{ $data->department }}</td>
                                        <td>{{ $data->pembuat }}</td>
                                        <td>
                                            @if($data->status == 'request')
                                                <span class="badge bg-warning">Request</span>
                                            @elseif($data->status == 'nonaktif')
                                                <span class="badge bg-danger">Nonaktif</span>
                                            @elseif($data->status == 'pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $data->status }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn shadow" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-2-fill"></i></button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                                    <li>
                                                        <form action="{{ route('permintaans.edit', $data->id) }}">
                                                            @csrf
                                                            <button type="submit" class="btn w-100 btn-outline-secondary">
                                                                <i class="ri-pencil-fill"></i> Edit
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <!-- Trigger Modal -->
                                                        <button type="button" class="btn w-100 btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#sendModal-{{ $data->id }}">
                                                           <i class="ri-send-plane-line"></i> Kirim
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button type="button" class="btn w-100 btn-outline-secondary" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#pengirimanModal"
                                                                data-supplier="{{ $data->id }}">
                                                            <i class="ri-export-line"></i> Pengiriman
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('permintaans.destroy', $data->id) }}" method="POST" class="d-inline delete-confirm">
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
                                    {{-- Modal Kirim Permintaan --}}
                                    <div class="modal fade" id="sendModal-{{ $data->id }}" tabindex="-1" aria-labelledby="sendModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('permintaans.send', $data->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="sendModalLabel">Kirim Permintaan</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <label for="supplier_id">Pilih Supplier</label>
                                                        <select name="supplier_id" id="supplier_id" class="form-select" required>
                                                            <option value="" selected hidden>-- Pilih Supplier --</option>
                                                            @foreach($suppliers as $supplier)
                                                                <option value="{{ $supplier->id }}">{{ $supplier->supplier }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-outline-primary">Kirim</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </tbody>
                            </table>
                            {{-- Modal Pengiriman Barang --}}
                            <div class="modal fade" id="pengirimanModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title">
                                                <i class="ri-truck-line me-2"></i>
                                                Pengiriman Barang - {{ $data->supplier->supplier ?? 'Tanpa Supplier' }}
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="pengirimanForm">
                                                <input type="hidden" id="pengiriman_supplier_id" name="supplier_id">
                                                
                                                {{-- Pilih Permintaan --}}
                                                <div class="mb-4">
                                                    <h6 class="mb-3">1. Pilih Permintaan dari Department</h6>
                                                    <div id="pengiriman-permintaan-modal" class="mb-3">
                                                        <div class="text-center text-muted py-5">
                                                            <i class="ri-loader-4-line ri-2x ri-spin"></i>
                                                            <div class="mt-2">Memuat data permintaan...</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                {{-- Tabel Input Jumlah Pengiriman --}}
                                                <div class="mb-4" id="pengiriman-section" style="display: none;">
                                                    <h6 class="mb-3">2. Masukkan Jumlah Barang yang Dikirim</h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered" id="tablePengiriman">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Nama Barang</th>
                                                                    <th>Jenis</th>
                                                                    <th>No Batch</th>
                                                                    <th>Exp Date</th>
                                                                    <th>Stock Gudang</th>
                                                                    <th>Jumlah Diminta</th>
                                                                    <th>Jumlah Dikirim</th>
                                                                    <th>Aksi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody></tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="me-auto">
                                                <small class="text-muted">
                                                    <span id="pengirimanSelectedCount">0</span> barang dipilih
                                                </small>
                                            </div>
                                            <button class="btn btn-secondary" data-bs-dismiss="modal">
                                                <i class="ri-close-line me-1"></i> Batal
                                            </button>
                                            <button class="btn btn-primary" id="btnProsesPengiriman" disabled>
                                                <i class="ri-truck-line me-1"></i> Proses Pengiriman
                                            </button>
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
@endpush