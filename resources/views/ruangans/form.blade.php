@extends('layouts.app')

@section('title', isset($ruangan) ? 'Edit Ruangan' : 'Tambah Ruangan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('ruangans.index') }}">Ruangan</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">
        {{ isset($ruangan) ? 'Edit' : 'Tambah' }}
    </li>
@endsection

@section('content')
<div class="app-body">
    <div class="row">
        <div class="col-xl-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="ri-building-4-line me-2"></i>
                            {{ isset($ruangan) ? 'Edit Ruangan' : 'Tambah Ruangan Baru' }}
                        </h5>
                        <a href="{{ route('ruangans.index') }}" class="btn btn-secondary btn-sm">
                            <i class="ri-arrow-left-line me-1"></i>Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ isset($ruangan) ? route('ruangans.update', $ruangan->id) : route('ruangans.store') }}" 
                          method="POST">
                        @csrf
                        @if(isset($ruangan))
                            @method('PUT')
                        @endif

                        <h5 class="mb-3">Informasi Ruangan</h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kode_ruangan" class="form-label">
                                        Kode Ruangan <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control text-uppercase @error('kode_ruangan') is-invalid @enderror" 
                                           id="kode_ruangan" 
                                           name="kode_ruangan" 
                                           value="{{ old('kode_ruangan', $ruangan->kode_ruangan ?? '') }}" 
                                           placeholder="e.g. RJ001, RI001, IGD001" 
                                           maxlength="50"
                                           required>
                                    @error('kode_ruangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Kode akan otomatis diubah menjadi huruf kapital</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_ruangan" class="form-label">
                                        Nama Ruangan <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('nama_ruangan') is-invalid @enderror" 
                                           id="nama_ruangan" 
                                           name="nama_ruangan" 
                                           value="{{ old('nama_ruangan', $ruangan->nama_ruangan ?? '') }}" 
                                           placeholder="e.g. Poli Umum, VIP A, Ruang ICU" 
                                           maxlength="255"
                                           required>
                                    @error('nama_ruangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="jenis" class="form-label">
                                        Jenis Ruangan <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('jenis') is-invalid @enderror" 
                                            id="jenis" 
                                            name="jenis" 
                                            required>
                                        <option value="">-- Pilih Jenis --</option>
                                        <option value="rawat_jalan" {{ old('jenis', $ruangan->jenis ?? '') == 'rawat_jalan' ? 'selected' : '' }}>
                                            Rawat Jalan
                                        </option>
                                        <option value="rawat_inap" {{ old('jenis', $ruangan->jenis ?? '') == 'rawat_inap' ? 'selected' : '' }}>
                                            Rawat Inap
                                        </option>
                                        <option value="igd" {{ old('jenis', $ruangan->jenis ?? '') == 'igd' ? 'selected' : '' }}>
                                            IGD
                                        </option>
                                        <option value="penunjang" {{ old('jenis', $ruangan->jenis ?? '') == 'penunjang' ? 'selected' : '' }}>
                                            Penunjang
                                        </option>
                                    </select>
                                    @error('jenis')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="kapasitas" class="form-label">
                                        Kapasitas <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control @error('kapasitas') is-invalid @enderror" 
                                           id="kapasitas" 
                                           name="kapasitas" 
                                           value="{{ old('kapasitas', $ruangan->kapasitas ?? 0) }}" 
                                           min="0"
                                           placeholder="0"
                                           required>
                                    @error('kapasitas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Jumlah maksimal pasien/tempat tidur</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="status" class="form-label">
                                        Status <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status" 
                                            required>
                                        <option value="">-- Pilih Status --</option>
                                        <option value="1" {{ old('status', $ruangan->status ?? '') == '1' ? 'selected' : '' }}>
                                            Aktif
                                        </option>
                                        <option value="0" {{ old('status', $ruangan->status ?? '') == '0' ? 'selected' : '' }}>
                                            Nonaktif
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="ri-information-line"></i>
                                    <strong>Informasi:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Field yang ditandai <span class="text-danger">*</span> wajib diisi</li>
                                        <li><strong>Kode Ruangan:</strong> Kode unik untuk identifikasi ruangan (akan otomatis kapital)</li>
                                        <li><strong>Nama Ruangan:</strong> Nama lengkap ruangan</li>
                                        <li><strong>Jenis Ruangan:</strong> Kategori ruangan (Rawat Jalan, Rawat Inap, IGD, atau Penunjang)</li>
                                        <li><strong>Kapasitas:</strong> Jumlah maksimal pasien atau tempat tidur yang tersedia</li>
                                        <li><strong>Status:</strong> Status operasional ruangan (Aktif/Nonaktif)</li>
                                        <li>Kode ruangan harus unik dan tidak boleh sama dengan yang sudah ada</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="ri-lightbulb-line text-warning"></i> Contoh Kode Ruangan
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead>
                                                    <tr>
                                                        <th width="150">Kode</th>
                                                        <th>Nama Ruangan</th>
                                                        <th width="150">Jenis</th>
                                                        <th width="100">Kapasitas</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="small">
                                                    <tr class="example-row" data-kode="RJ001" data-nama="Poli Umum" data-jenis="rawat_jalan" data-kapasitas="1">
                                                        <td><code>RJ001</code></td>
                                                        <td>Poli Umum</td>
                                                        <td>Rawat Jalan</td>
                                                        <td>1</td>
                                                    </tr>
                                                    <tr class="example-row" data-kode="RI001" data-nama="Ruang VIP A" data-jenis="rawat_inap" data-kapasitas="1">
                                                        <td><code>RI001</code></td>
                                                        <td>Ruang VIP A</td>
                                                        <td>Rawat Inap</td>
                                                        <td>1</td>
                                                    </tr>
                                                    <tr class="example-row" data-kode="RI002" data-nama="Ruang Kelas 1" data-jenis="rawat_inap" data-kapasitas="2">
                                                        <td><code>RI002</code></td>
                                                        <td>Ruang Kelas 1</td>
                                                        <td>Rawat Inap</td>
                                                        <td>2</td>
                                                    </tr>
                                                    <tr class="example-row" data-kode="IGD001" data-nama="Ruang IGD" data-jenis="igd" data-kapasitas="10">
                                                        <td><code>IGD001</code></td>
                                                        <td>Ruang IGD</td>
                                                        <td>IGD</td>
                                                        <td>10</td>
                                                    </tr>
                                                    <tr class="example-row" data-kode="LAB001" data-nama="Laboratorium" data-jenis="penunjang" data-kapasitas="5">
                                                        <td><code>LAB001</code></td>
                                                        <td>Laboratorium</td>
                                                        <td>Penunjang</td>
                                                        <td>5</td>
                                                    </tr>
                                                    <tr class="example-row" data-kode="RAD001" data-nama="Radiologi" data-jenis="penunjang" data-kapasitas="3">
                                                        <td><code>RAD001</code></td>
                                                        <td>Radiologi</td>
                                                        <td>Penunjang</td>
                                                        <td>3</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <small class="text-muted mt-2 d-block">
                                            <i class="ri-cursor-line"></i> Klik baris untuk mengisi form dengan contoh
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('ruangans.index') }}" class="btn btn-secondary">
                                        <i class="ri-close-line me-1"></i>Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-save-line me-1"></i>
                                        {{ isset($ruangan) ? 'Perbarui' : 'Simpan' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto uppercase kode ruangan
    document.getElementById('kode_ruangan').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });

    // Quick fill from examples
    function quickFill(kode, nama, jenis, kapasitas) {
        document.getElementById('kode_ruangan').value = kode;
        document.getElementById('nama_ruangan').value = nama;
        document.getElementById('jenis').value = jenis;
        document.getElementById('kapasitas').value = kapasitas;
        document.getElementById('status').value = '1';
    }

    // Add click handlers to example table rows
    document.querySelectorAll('.example-row').forEach(row => {
        row.style.cursor = 'pointer';
        row.title = 'Klik untuk mengisi form dengan contoh ini';
        
        row.addEventListener('click', function() {
            const kode = this.dataset.kode;
            const nama = this.dataset.nama;
            const jenis = this.dataset.jenis;
            const kapasitas = this.dataset.kapasitas;
            
            if (confirm('Isi form dengan contoh ini?')) {
                quickFill(kode, nama, jenis, kapasitas);
            }
        });
    });
</script>
@endpush