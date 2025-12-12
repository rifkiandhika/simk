@extends('layouts.app')

@section('title', 'Data Obat RS')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Obat RS</h5>
        <a href="{{ route('obatrs.create') }}" class="btn btn-primary btn-sm">+ Tambah Obat</a>
    </div>
    <div class="card-body">
        <table id="myTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Obat</th>
                    <th>Nama Internasional</th>
                    <th>Jumlah Detail</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($obats as $i => $obat)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $obat->nama_obat }}</td>
                        <td>{{ $obat->nama_obat_internasional }}</td>
                        <td>{{ $obat->detailObats->count() }}</td>
                        <td class="text-center">
                            <a href="{{ route('obatrs.edit', $obat->id_obat_rs) }}" class="btn btn-sm btn-outline-primary">
                                <i class="ri-pencil-line"></i> Edit
                            </a>
                            <form action="{{ route('obatrs.destroy', $obat->id_obat_rs) }}" method="POST" class="d-inline delete-confirm">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="ri-delete-bin-line"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
