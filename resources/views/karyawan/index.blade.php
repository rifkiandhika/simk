@extends('layouts.app')

@section('title', 'Data Karyawan')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary">Karyawan</li>
@endsection

@section('content')
<div class="app-body">

    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="ri-user-line me-2"></i>Data Karyawan</h5>
            <a href="{{ route('karyawans.create') }}" class="btn btn-primary btn-sm">
                <i class="ri-add-circle-line me-1"></i>Tambah Karyawan
            </a>
        </div>

        <div class="card-body border-bottom bg-light">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Status</label>
                    <select id="filterStatus" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Nonaktif">Nonaktif</option>
                        <option value="Cuti">Cuti</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-bold">Cari</label>
                    <input type="text" id="searchBox" class="form-control form-control-sm"
                           placeholder="Cari NIP atau nama...">
                </div>
            </div>
        </div>

        <div class="card-body">
            <table class="table table-hover table-striped" id="karyawanTable">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Status</th>
                        <th width="100" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($karyawans as $i => $k)
                    <tr>
                        <td>{{ $i+1 }}</td>

                        <td>
                            <strong>{{ $k->nip }}</strong>
                        </td>

                        <td>{{ $k->nama_lengkap }}</td>

                        <td>
                            @if($k->status_aktif == 'Aktif')
                                <span class="badge bg-success">Aktif</span>
                            @elseif($k->status_aktif == 'Cuti')
                                <span class="badge bg-warning text-dark">Cuti</span>
                            @else
                                <span class="badge bg-danger">Nonaktif</span>
                            @endif
                        </td>

                        <td class="text-center">
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle"
                                        data-bs-toggle="dropdown">
                                    <i class="ri-more-2-fill"></i>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end shadow">

                                    <li>
                                        <a class="dropdown-item"
                                        href="{{ route('karyawans.edit', $k->id_karyawan) }}">
                                            <i class="ri-pencil-fill me-2"></i>Edit
                                        </a>
                                    </li>

                                    <li><hr class="dropdown-divider"></li>

                                    <li>
                                        <form action="{{ route('karyawans.destroy', $k->id_karyawan) }}"
                                            method="POST"
                                            class="delete-confirm">
                                            @csrf
                                            @method('DELETE')
                                            <button class="dropdown-item text-danger">
                                                <i class="ri-delete-bin-6-line me-2"></i>Hapus
                                            </button>
                                        </form>
                                    </li>

                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <i class="ri-user-line ri-3x text-muted"></i>
                            <p class="text-muted mt-3">Belum ada karyawan.</p>
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {

    let dataTable = null;
    const $table = $('#karyawanTable');

    // Cek apakah tabel kosong (ada <td colspan="...">)
    const isEmpty = $table.find('tbody tr td[colspan]').length > 0;

    // Jika DataTables sudah pernah aktif → hancurkan dulu
    if ($.fn.DataTable.isDataTable('#karyawanTable')) {
        $table.DataTable().destroy();
    }

    // ⚡ Inisialisasi DataTables hanya jika tabel TIDAK kosong
    if (!isEmpty) {
        dataTable = $table.DataTable({
            pageLength: 10,
            dom: 'rtip',
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
            },
            columnDefs: [
                { orderable: false, targets: [4] } 
            ]
        });

        // Search
        $('#searchBox').on('keyup', function () {
            dataTable.search(this.value).draw();
        });

        // Filter Status
        $('#filterStatus').on('change', function () {
            dataTable.column(3).search(this.value).draw();
        });

    } else {
        console.log("⚠️ Tabel kosong → DataTables tidak diinisialisasi.");
    }


    // SweetAlert Delete Handler
    $(document).on('submit', '.delete-confirm', function (e) {
        e.preventDefault();
        let form = this;

        Swal.fire({
            title: 'Hapus Karyawan?',
            text: "Data akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    });

});
</script>
@endpush

