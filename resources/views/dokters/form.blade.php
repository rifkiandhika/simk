@extends('layouts.app')

@section('title', isset($dokter) ? 'Edit Dokter' : 'Tambah Dokter')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dokters.index') }}">Dokter</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">
        {{ isset($dokter) ? 'Edit' : 'Tambah' }}
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
                            <i class="ri-user-heart-line me-2"></i>
                            {{ isset($dokter) ? 'Edit Dokter' : 'Tambah Dokter Baru' }}
                        </h5>
                        <a href="{{ route('dokters.index') }}" class="btn btn-secondary btn-sm">
                            <i class="ri-arrow-left-line me-1"></i>Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                        <h5 class="mb-3">Informasi Dokter</h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kode_dokter" class="form-label">
                                        Kode Dokter <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control text-uppercase @error('kode_dokter') is-invalid @enderror" 
                                           id="kode_dokter" 
                                           name="kode_dokter" 
                                           value="{{ old('kode_dokter', $dokter->kode_dokter ?? '') }}" 
                                           placeholder="e.g. DR001, DR002" 
                                           maxlength="50"
                                           required>
                                    @error('kode_dokter')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Kode akan otomatis diubah menjadi huruf kapital</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_dokter" class="form-label">
                                        Nama Dokter <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('nama_dokter') is-invalid @enderror" 
                                           id="nama_dokter" 
                                           name="nama_dokter" 
                                           value="{{ old('nama_dokter', $dokter->nama_dokter ?? '') }}" 
                                           placeholder="e.g. dr. Ahmad Santoso, Sp.PD" 
                                           maxlength="255"
                                           required>
                                    @error('nama_dokter')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="spesialisasi" class="form-label">
                                        Spesialisasi
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('spesialisasi') is-invalid @enderror" 
                                           id="spesialisasi" 
                                           name="spesialisasi" 
                                           value="{{ old('spesialisasi', $dokter->spesialisasi ?? '') }}" 
                                           placeholder="e.g. Penyakit Dalam, Anak, Bedah" 
                                           maxlength="255">
                                    @error('spesialisasi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Kosongkan jika dokter umum</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="no_str" class="form-label">
                                        No. STR
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('no_str') is-invalid @enderror" 
                                           id="no_str" 
                                           name="no_str" 
                                           value="{{ old('no_str', $dokter->no_str ?? '') }}" 
                                           placeholder="e.g. 1234567890" 
                                           maxlength="255">
                                    @error('no_str')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Surat Tanda Registrasi</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="no_sip" class="form-label">
                                        No. SIP
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('no_sip') is-invalid @enderror" 
                                           id="no_sip" 
                                           name="no_sip" 
                                           value="{{ old('no_sip', $dokter->no_sip ?? '') }}" 
                                           placeholder="e.g. SIP/001/2024" 
                                           maxlength="255">
                                    @error('no_sip')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Surat Izin Praktik</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="status" class="form-label">
                                        Status <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('status') is-invalid @enderror"
                                            id="status"
                                            name="status"
                                            required>
                                        <option value="">-- Pilih Status --</option>
                                        <option value="Aktif" {{ old('status', $dokter->status ?? '') == 'Aktif' ? 'selected' : '' }}>
                                            Aktif
                                        </option>
                                        <option value="Nonaktif" {{ old('status', $dokter->status ?? '') == 'Nonaktif' ? 'selected' : '' }}>
                                            Nonaktif
                                        </option>
                                        <option value="Cuti" {{ old('status', $dokter->status ?? '') == 'Cuti' ? 'selected' : '' }}>
                                            Cuti
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3">
                            <i class="ri-hospital-line me-2"></i>Ruangan Praktek
                        </h5>

                        <div class="row">
                            <div class="col-12">
                                @if($ruangans->count() > 0)
                                    <div class="card bg-light border-0">
                                        <div class="card-body">
                                            <p class="text-muted mb-3 small">
                                                <i class="ri-information-line me-1"></i>
                                                Pilih satu atau lebih ruangan tempat dokter praktek. Anda bisa memilih lebih dari satu ruangan.
                                            </p>
                                            
                                            <div class="row g-3">
                                                @foreach($ruangans as $ruangan)
                                                    <div class="col-md-6 col-lg-4">
                                                        <div class="form-check">
                                                            <input class="form-check-input" 
                                                                   type="checkbox" 
                                                                   name="ruangans[]" 
                                                                   value="{{ $ruangan->id }}" 
                                                                   id="ruangan_{{ $ruangan->id }}"
                                                                   {{ isset($dokter) && $dokter->ruangans->contains($ruangan->id) ? 'checked' : '' }}
                                                                   {{ in_array($ruangan->id, old('ruangans', [])) ? 'checked' : '' }}>
                                                            <label class="form-check-label d-flex align-items-center" for="ruangan_{{ $ruangan->id }}">
                                                                <div>
                                                                    <strong class="d-block">{{ $ruangan->nama_ruangan }}</strong>
                                                                    <small class="text-muted">
                                                                        <code class="me-1">{{ $ruangan->kode_ruangan }}</code>
                                                                        @php
                                                                            $jenisConfig = [
                                                                                'rawat_jalan' => ['label' => 'Rawat Jalan', 'class' => 'primary'],
                                                                                'rawat_inap' => ['label' => 'Rawat Inap', 'class' => 'success'],
                                                                                'igd' => ['label' => 'IGD', 'class' => 'danger'],
                                                                                'penunjang' => ['label' => 'Penunjang', 'class' => 'info']
                                                                            ];
                                                                            $config = $jenisConfig[$ruangan->jenis] ?? ['label' => $ruangan->jenis, 'class' => 'secondary'];
                                                                        @endphp
                                                                        <span class="badge bg-{{ $config['class'] }}" style="font-size: 0.65rem;">
                                                                            {{ $config['label'] }}
                                                                        </span>
                                                                    </small>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            @error('ruangans')
                                                <div class="text-danger mt-2 small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="ri-alert-line me-2"></i>
                                        Belum ada ruangan yang tersedia. Silakan <a href="{{ route('ruangans.create') }}" target="_blank">tambah ruangan</a> terlebih dahulu.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="ri-information-line"></i>
                                    <strong>Informasi:</strong>
                                    <div class="mt-2">
                                        Field yang ditandai <span class="text-danger">*</span> wajib diisi. Berikut penjelasan singkat untuk setiap field:
                                    </div>
                                    <div class="mt-2">
                                        <strong>Kode Dokter:</strong> Kode unik untuk identifikasi dokter (akan otomatis kapital).
                                    </div>
                                    <div>
                                        <strong>Nama Dokter:</strong> Nama lengkap dokter beserta gelarnya.
                                    </div>
                                    <div>
                                        <strong>Spesialisasi:</strong> Bidang keahlian dokter (kosongkan jika dokter umum).
                                    </div>
                                    <div>
                                        <strong>No. STR:</strong> Nomor Surat Tanda Registrasi dari konsil kedokteran.
                                    </div>
                                    <div>
                                        <strong>No. SIP:</strong> Nomor Surat Izin Praktik dari dinas kesehatan.
                                    </div>
                                    <div>
                                        <strong>Status:</strong> Status aktif/nonaktif dokter di rumah sakit.
                                    </div>
                                    <div>
                                        <strong>Ruangan Praktek:</strong> Ruangan tempat dokter berpraktik (bisa lebih dari satu).
                                    </div>
                                    <div class="mt-2">
                                        Kode dokter harus unik dan tidak boleh sama dengan yang sudah ada.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="ri-lightbulb-line text-warning"></i> Contoh Data Dokter
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead>
                                                    <tr>
                                                        <th width="100">Kode</th>
                                                        <th>Nama Dokter</th>
                                                        <th width="150">Spesialisasi</th>
                                                        <th width="120">No. STR</th>
                                                        <th width="120">No. SIP</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="small">
                                                    <tr class="example-row" style="cursor: pointer;" 
                                                        data-kode="DR001" 
                                                        data-nama="dr. Ahmad Santoso, Sp.PD" 
                                                        data-spesialisasi="Penyakit Dalam"
                                                        data-str="KKI.00001.2024"
                                                        data-sip="SIP/DR/001/2024"
                                                        title="Klik untuk mengisi form dengan contoh ini">
                                                        <td><code>DR001</code></td>
                                                        <td>dr. Ahmad Santoso, Sp.PD</td>
                                                        <td>Penyakit Dalam</td>
                                                        <td>KKI.00001.2024</td>
                                                        <td>SIP/DR/001/2024</td>
                                                    </tr>
                                                    <tr class="example-row" style="cursor: pointer;" 
                                                        data-kode="DR002" 
                                                        data-nama="dr. Siti Aminah, Sp.A" 
                                                        data-spesialisasi="Anak"
                                                        data-str="KKI.00002.2024"
                                                        data-sip="SIP/DR/002/2024"
                                                        title="Klik untuk mengisi form dengan contoh ini">
                                                        <td><code>DR002</code></td>
                                                        <td>dr. Siti Aminah, Sp.A</td>
                                                        <td>Anak</td>
                                                        <td>KKI.00002.2024</td>
                                                        <td>SIP/DR/002/2024</td>
                                                    </tr>
                                                    <tr class="example-row" style="cursor: pointer;" 
                                                        data-kode="DR003" 
                                                        data-nama="dr. Budi Hartono, Sp.B" 
                                                        data-spesialisasi="Bedah"
                                                        data-str="KKI.00003.2024"
                                                        data-sip="SIP/DR/003/2024"
                                                        title="Klik untuk mengisi form dengan contoh ini">
                                                        <td><code>DR003</code></td>
                                                        <td>dr. Budi Hartono, Sp.B</td>
                                                        <td>Bedah</td>
                                                        <td>KKI.00003.2024</td>
                                                        <td>SIP/DR/003/2024</td>
                                                    </tr>
                                                    <tr class="example-row" style="cursor: pointer;" 
                                                        data-kode="DR004" 
                                                        data-nama="dr. Rina Wulandari" 
                                                        data-spesialisasi=""
                                                        data-str="KKI.00004.2024"
                                                        data-sip="SIP/DR/004/2024"
                                                        title="Klik untuk mengisi form dengan contoh ini">
                                                        <td><code>DR004</code></td>
                                                        <td>dr. Rina Wulandari</td>
                                                        <td><span class="text-muted">Umum</span></td>
                                                        <td>KKI.00004.2024</td>
                                                        <td>SIP/DR/004/2024</td>
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
                                    <a href="{{ route('dokters.index') }}" class="btn btn-secondary">
                                        <i class="ri-close-line me-1"></i>Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-save-line me-1"></i>
                                        {{ isset($dokter) ? 'Perbarui' : 'Simpan' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto uppercase kode dokter
    document.getElementById('kode_dokter').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });

    // Quick fill from examples
    function quickFill(kode, nama, spesialisasi, str, sip) {
        document.getElementById('kode_dokter').value = kode;
        document.getElementById('nama_dokter').value = nama;
        document.getElementById('spesialisasi').value = spesialisasi;
        document.getElementById('no_str').value = str;
        document.getElementById('no_sip').value = sip;
        document.getElementById('is_active').value = '1';
    }

    // Add click handlers to example table rows
    document.querySelectorAll('.example-row').forEach(row => {
        row.addEventListener('click', function() {
            const kode = this.dataset.kode;
            const nama = this.dataset.nama;
            const spesialisasi = this.dataset.spesialisasi;
            const str = this.dataset.str;
            const sip = this.dataset.sip;
            
            if (confirm('Isi form dengan contoh ini?')) {
                quickFill(kode, nama, spesialisasi, str, sip);
            }
        });
    });
</script>
@endpush