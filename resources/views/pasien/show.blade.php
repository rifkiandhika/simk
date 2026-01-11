@extends('layouts.app')

@section('title', 'Detail Pasien')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Detail Pasien</h4>
                <div>
                    <a href="{{ route('pasiens.edit', $pasien->id_pasien) }}" class="btn btn-warning">
                        <i class="ri-edit-line"></i> Edit
                    </a>
                    <a href="{{ route('pasiens.index') }}" class="btn btn-secondary">
                        <i class="ri-arrow-left-line"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Foto Pasien -->
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    @if($pasien->foto)
                        <img src="{{ Storage::url($pasien->foto) }}" alt="Foto Pasien" class="img-fluid rounded mb-3">
                    @else
                        <div class="bg-light rounded mb-3 d-flex align-items-center justify-content-center" style="height: 250px;">
                            <i class="ri-user-line" style="font-size: 100px; color: #ccc;"></i>
                        </div>
                    @endif
                    <h5 class="mb-1">{{ $pasien->nama_lengkap }}</h5>
                    <p class="text-muted mb-2">{{ $pasien->no_rm }}</p>
                    @if($pasien->status_aktif == 'Aktif')
                        <span class="badge bg-success">Aktif</span>
                    @else
                        <span class="badge bg-secondary">Nonaktif</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Detail Data -->
        <div class="col-md-9">
            <!-- Data Identitas -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="ri-user-line"></i> Data Identitas</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">No. Rekam Medis</label>
                            <p class="fw-bold mb-0">{{ $pasien->no_rm }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">NIK</label>
                            <p class="fw-bold mb-0">{{ $pasien->nik ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Nama Lengkap</label>
                            <p class="fw-bold mb-0">{{ $pasien->nama_lengkap }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Jenis Kelamin</label>
                            <p class="fw-bold mb-0">
                                @if($pasien->jenis_kelamin == 'L')
                                    <span class="badge bg-primary">Laki-laki</span>
                                @else
                                    <span class="badge bg-danger">Perempuan</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Tempat, Tanggal Lahir</label>
                            <p class="fw-bold mb-0">
                                {{ $pasien->tempat_lahir ?? '-' }}, 
                                {{ $pasien->tanggal_lahir ? \Carbon\Carbon::parse($pasien->tanggal_lahir)->format('d F Y') : '-' }}
                                @if($pasien->tanggal_lahir)
                                    ({{ \Carbon\Carbon::parse($pasien->tanggal_lahir)->age }} tahun)
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Golongan Darah</label>
                            <p class="fw-bold mb-0">{{ $pasien->golongan_darah ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Status Perkawinan</label>
                            <p class="fw-bold mb-0">{{ $pasien->status_perkawinan ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Pekerjaan</label>
                            <p class="fw-bold mb-0">{{ $pasien->pekerjaan ?? '-' }}</p>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="text-muted small">Alamat</label>
                            <p class="fw-bold mb-0">{{ $pasien->alamat ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Tanggal Registrasi</label>
                            <p class="fw-bold mb-0">
                                {{ $pasien->tanggal ? \Carbon\Carbon::parse($pasien->tanggal)->format('d F Y') : '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Kontak -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="ri-phone-line"></i> Data Kontak</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">No. Telepon</label>
                            <p class="fw-bold mb-0">{{ $pasien->no_telp ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">No. Telepon Darurat</label>
                            <p class="fw-bold mb-0">{{ $pasien->no_telp_darurat ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Nama Kontak Darurat</label>
                            <p class="fw-bold mb-0">{{ $pasien->nama_kontak_darurat ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Hubungan</label>
                            <p class="fw-bold mb-0">{{ $pasien->hubungan_kontak_darurat ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Pembayaran -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="ri-bank-card-line"></i> Data Pembayaran</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Jenis Pembayaran</label>
                            <p class="fw-bold mb-0">
                                @if($pasien->jenis_pembayaran == 'BPJS')
                                    <span class="badge bg-success">{{ $pasien->jenis_pembayaran }}</span>
                                @elseif($pasien->jenis_pembayaran == 'Asuransi')
                                    <span class="badge bg-info">{{ $pasien->jenis_pembayaran }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $pasien->jenis_pembayaran }}</span>
                                @endif
                            </p>
                        </div>

                        @if($pasien->jenis_pembayaran == 'BPJS')
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">No. BPJS</label>
                                <p class="fw-bold mb-0">{{ $pasien->no_bpjs ?? '-' }}</p>
                            </div>
                        @endif

                        @if($pasien->jenis_pembayaran == 'Asuransi')
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">Nama Asuransi</label>
                                <p class="fw-bold mb-0">{{ $pasien->asuransi->nama_asuransi ?? '-' }}</p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">No. Polis</label>
                                <p class="fw-bold mb-0">{{ $pasien->no_polis_asuransi ?? '-' }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection