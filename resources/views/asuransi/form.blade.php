<h5 class="mb-3">Data Asuransi</h5>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="nama_asuransi" class="form-label">
                Nama Asuransi <span class="text-danger">*</span>
            </label>
            <input type="text" 
                   class="form-control @error('nama_asuransi') is-invalid @enderror" 
                   id="nama_asuransi" 
                   name="nama_asuransi" 
                   value="{{ old('nama_asuransi', $asuransi->nama_asuransi ?? '') }}" 
                   placeholder="e.g. BPJS Kesehatan, Prudential, Manulife" 
                   required>
            @error('nama_asuransi')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="tipe" class="form-label">Tipe Asuransi</label>
            <select class="form-select @error('tipe') is-invalid @enderror" 
                    id="tipe" 
                    name="tipe">
                <option value="">-- Pilih Tipe --</option>
                <option value="Pemerintah" {{ old('tipe', $asuransi->tipe ?? '') == 'Pemerintah' ? 'selected' : '' }}>
                    Pemerintah
                </option>
                <option value="Swasta" {{ old('tipe', $asuransi->tipe ?? '') == 'Swasta' ? 'selected' : '' }}>
                    Swasta
                </option>
                <option value="BPJS" {{ old('tipe', $asuransi->tipe ?? '') == 'BPJS' ? 'selected' : '' }}>
                    BPJS
                </option>
                <option value="Corporate" {{ old('tipe', $asuransi->tipe ?? '') == 'Corporate' ? 'selected' : '' }}>
                    Corporate
                </option>
                <option value="Asuransi Umum" {{ old('tipe', $asuransi->tipe ?? '') == 'Asuransi Umum' ? 'selected' : '' }}>
                    Asuransi Umum
                </option>
                <option value="Lainnya" {{ old('tipe', $asuransi->tipe ?? '') == 'Lainnya' ? 'selected' : '' }}>
                    Lainnya
                </option>
            </select>
            @error('tipe')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<h5 class="mb-3 mt-4">Informasi Kontrak</h5>

<div class="row">
    <div class="col-md-4">
        <div class="mb-3">
            <label for="no_kontrak" class="form-label">Nomor Kontrak</label>
            <input type="text" 
                   class="form-control @error('no_kontrak') is-invalid @enderror" 
                   id="no_kontrak" 
                   name="no_kontrak" 
                   value="{{ old('no_kontrak', $asuransi->no_kontrak ?? '') }}" 
                   placeholder="e.g. PKS/2024/001">
            @error('no_kontrak')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label for="tanggal_kontrak_mulai" class="form-label">Tanggal Kontrak Mulai</label>
            <input type="date" 
                   class="form-control @error('tanggal_kontrak_mulai') is-invalid @enderror" 
                   id="tanggal_kontrak_mulai" 
                   name="tanggal_kontrak_mulai" 
                   value="{{ old('tanggal_kontrak_mulai', $asuransi->tanggal_kontrak_mulai ?? '') }}">
            @error('tanggal_kontrak_mulai')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label for="tanggal_kontrak_selesai" class="form-label">Tanggal Kontrak Selesai</label>
            <input type="date" 
                   class="form-control @error('tanggal_kontrak_selesai') is-invalid @enderror" 
                   id="tanggal_kontrak_selesai" 
                   name="tanggal_kontrak_selesai" 
                   value="{{ old('tanggal_kontrak_selesai', $asuransi->tanggal_kontrak_selesai ?? '') }}">
            @error('tanggal_kontrak_selesai')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<h5 class="mb-3 mt-4">Informasi Kontak</h5>

<div class="row">
    <div class="col-md-12">
        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea class="form-control @error('alamat') is-invalid @enderror" 
                      id="alamat" 
                      name="alamat" 
                      rows="3" 
                      placeholder="Alamat lengkap kantor asuransi">{{ old('alamat', $asuransi->alamat ?? '') }}</textarea>
            @error('alamat')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="no_telp" class="form-label">Nomor Telepon</label>
            <input type="text" 
                   class="form-control @error('no_telp') is-invalid @enderror" 
                   id="no_telp" 
                   name="no_telp" 
                   value="{{ old('no_telp', $asuransi->no_telp ?? '') }}" 
                   placeholder="e.g. 021-1234567"
                   maxlength="15">
            @error('no_telp')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   id="email" 
                   name="email" 
                   value="{{ old('email', $asuransi->email ?? '') }}" 
                   placeholder="e.g. info@asuransi.com"
                   maxlength="100">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="status" class="form-label">
                Status <span class="text-danger">*</span>
            </label>
            <select class="form-select @error('status') is-invalid @enderror" 
                    id="status" 
                    name="status" 
                    required>
                <option value="Aktif" {{ old('status', $asuransi->status ?? 'Aktif') == 'Aktif' ? 'selected' : '' }}>
                    Aktif
                </option>
                <option value="Nonaktif" {{ old('status', $asuransi->status ?? '') == 'Nonaktif' ? 'selected' : '' }}>
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
                <li><strong>Nama asuransi</strong> dan <strong>status</strong> wajib diisi</li>
                <li>Field lainnya bersifat opsional</li>
                <li>Tanggal kontrak selesai harus lebih besar atau sama dengan tanggal kontrak mulai</li>
                <li>Nomor telepon maksimal 15 karakter</li>
                <li>Email maksimal 100 karakter</li>
            </ul>
        </div>
    </div>
</div>