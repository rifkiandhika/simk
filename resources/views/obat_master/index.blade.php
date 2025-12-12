@extends('layouts.app')

@section('title', 'Obat')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Obat</li>
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
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-primary">Daftar Master Obat</h5>
                        <a class="btn btn-outline-primary" href="{{ route('obat-masters.create') }}">+ Tambah Obat</a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-borderless align-middle" id="obatTable">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>KFA Code</th>
                                    <th>Nama Obat</th>
                                    <th>Kategori</th>
                                    <th>Golongan</th>
                                    <th>Status</th>
                                    <th width="120">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($obats as $index => $obat)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $obat->kfa_code }}</td>
                                        <td>{{ $obat->nama_obat }}</td>
                                        <td>{{ $obat->kategori ?? '-' }}</td>
                                        <td>{{ $obat->golongan ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $obat->status == 'Aktif' ? 'bg-success' : 'bg-danger' }}">
                                                {{ $obat->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn shadow-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-2-fill"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('obat-masters.edit', $obat->id_obat_master) }}">
                                                            <i class="ri-pencil-fill me-2"></i>Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('obat-masters.destroy', $obat->id_obat_master) }}" method="POST" class="d-inline delete-confirm">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="ri-delete-bin-6-line me-2"></i>Hapus
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-2 {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#obatTable')) {
        $('#obatTable').DataTable().clear().destroy();
    }

    $('#obatTable').DataTable({
        pageLength: 10,
        ordering: false,
        language: {
            search: "Cari Obat:",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            zeroRecords: "Tidak ditemukan data yang cocok",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(disaring dari total _MAX_ data)"
        }
    });
});

</script>
@endpush
