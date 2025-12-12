<h5 class="mb-3">Data Department</h5>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="kode_department" class="form-label">
                Kode Department <span class="text-danger">*</span>
            </label>
            <input type="text" 
                   class="form-control text-uppercase @error('kode_department') is-invalid @enderror" 
                   id="kode_department" 
                   name="kode_department" 
                   value="{{ old('kode_department', $department->kode_department ?? '') }}" 
                   placeholder="e.g. FRM, LAB, RAD" 
                   maxlength="20"
                   required>
            @error('kode_department')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Kode unik untuk department (maks. 20 karakter)</small>
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="nama_department" class="form-label">
                Nama Department <span class="text-danger">*</span>
            </label>
            <input type="text" 
                   class="form-control @error('nama_department') is-invalid @enderror" 
                   id="nama_department" 
                   name="nama_department" 
                   value="{{ old('nama_department', $department->nama_department ?? '') }}" 
                   placeholder="e.g. Farmasi, Laboratorium, Radiologi" 
                   maxlength="100"
                   required>
            @error('nama_department')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="jenis" class="form-label">
                Jenis Department <span class="text-danger">*</span>
            </label>
            <select class="form-select @error('jenis') is-invalid @enderror" 
                    id="jenis" 
                    name="jenis"
                    required>
                <option value="">-- Pilih Jenis --</option>
                <option value="Medis" {{ old('jenis', $department->jenis ?? '') == 'Medis' ? 'selected' : '' }}>
                    <i class="ri-hospital-line"></i> Medis
                </option>
                <option value="Non-Medis" {{ old('jenis', $department->jenis ?? '') == 'Non-Medis' ? 'selected' : '' }}>
                    Non-Medis
                </option>
                <option value="Penunjang" {{ old('jenis', $department->jenis ?? '') == 'Penunjang' ? 'selected' : '' }}>
                    Penunjang
                </option>
            </select>
            @error('jenis')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="status" class="form-label">
                Status <span class="text-danger">*</span>
            </label>
            <select class="form-select @error('status') is-invalid @enderror" 
                    id="status" 
                    name="status" 
                    required>
                <option value="Aktif" {{ old('status', $department->status ?? 'Aktif') == 'Aktif' ? 'selected' : '' }}>
                    Aktif
                </option>
                <option value="Nonaktif" {{ old('status', $department->status ?? '') == 'Nonaktif' ? 'selected' : '' }}>
                    Nonaktif
                </option>
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<h5 class="mb-3 mt-4">Informasi Tambahan</h5>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="lokasi" class="form-label">Lokasi</label>
            <input type="text" 
                   class="form-control @error('lokasi') is-invalid @enderror" 
                   id="lokasi" 
                   name="lokasi" 
                   value="{{ old('lokasi', $department->lokasi ?? '') }}" 
                   placeholder="e.g. Gedung A Lantai 2, Lantai 3 Wing B">
            @error('lokasi')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Lokasi fisik department di rumah sakit</small>
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="kepala_department" class="form-label">Kepala Department</label>
            <input type="text" 
                   class="form-control @error('kepala_department') is-invalid @enderror" 
                   id="kepala_department" 
                   name="kepala_department" 
                   value="{{ old('kepala_department', $department->kepala_department ?? '') }}" 
                   placeholder="e.g. dr. John Doe, Sp.PD">
            @error('kepala_department')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Nama penanggung jawab department</small>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="alert alert-info">
            <i class="ri-information-line"></i>
            <strong>Informasi:</strong>
            <ul class="mb-0 mt-2">
                <li><strong>Kode Department</strong>, <strong>Nama Department</strong>, <strong>Jenis</strong>, dan <strong>Status</strong> wajib diisi</li>
                <li>Kode department akan otomatis diubah menjadi huruf kapital</li>
                <li>Kode department harus unik (tidak boleh duplikat)</li>
                <li>Field Lokasi dan Kepala Department bersifat opsional</li>
                <li><strong>Jenis Department:</strong>
                    <ul>
                        <li><strong>Medis:</strong> Unit pelayanan medis langsung (e.g. IGD, Poliklinik, Rawat Inap)</li>
                        <li><strong>Non-Medis:</strong> Unit administratif dan manajemen (e.g. Keuangan, SDM, IT)</li>
                        <li><strong>Penunjang:</strong> Unit penunjang medis (e.g. Laboratorium, Radiologi, Farmasi)</li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto uppercase kode department
    document.getElementById('kode_department').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });
</script>
@endpush