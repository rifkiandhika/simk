<h5 class="mb-3">Data Karyawan</h5>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">NIP <span class="text-danger">*</span></label>
            <input type="text"
                   name="nip"
                   class="form-control @error('nip') is-invalid @enderror"
                   value="{{ old('nip', $karyawan->nip ?? '') }}"
                   placeholder="e.g. 1987654321"
                   required>
            @error('nip')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Status <span class="text-danger">*</span></label>
            <select name="status_aktif"
                    class="form-select @error('status_aktif') is-invalid @enderror"
                    required>
                <option value="">-- Pilih Status --</option>
                <option value="Aktif" {{ old('status_aktif', $karyawan->status_aktif ?? '') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Nonaktif" {{ old('status_aktif', $karyawan->status_aktif ?? '') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                <option value="Cuti" {{ old('status_aktif', $karyawan->status_aktif ?? '') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
            </select>
            @error('status_aktif')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="mb-3">
            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text"
                   name="nama_lengkap"
                   class="form-control @error('nama_lengkap') is-invalid @enderror"
                   value="{{ old('nama_lengkap', $karyawan->nama_lengkap ?? '') }}"
                   placeholder="Nama lengkap sesuai identitas"
                   required>
            @error('nama_lengkap')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<h5 class="mt-4 mb-3">Data Pribadi</h5>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Tempat Lahir</label>
            <input type="text"
                   name="tempat_lahir"
                   class="form-control @error('tempat_lahir') is-invalid @enderror"
                   value="{{ old('tempat_lahir', $karyawan->tempat_lahir ?? '') }}"
                   placeholder="e.g. Jakarta, Bandung">
            @error('tempat_lahir')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3">
            <label class="form-label">Tanggal Lahir</label>
            <input type="date"
                   name="tanggal_lahir"
                   class="form-control @error('tanggal_lahir') is-invalid @enderror"
                   value="{{ old('tanggal_lahir', $karyawan->tanggal_lahir ?? '') }}">
            @error('tanggal_lahir')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3">
            <label class="form-label">Jenis Kelamin</label>
            <select name="jenis_kelamin"
                    class="form-select @error('jenis_kelamin') is-invalid @enderror">
                <option value="">-- Pilih Jenis Kelamin --</option>
                <option value="L" {{ old('jenis_kelamin', $karyawan->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-Laki</option>
                <option value="P" {{ old('jenis_kelamin', $karyawan->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
            @error('jenis_kelamin')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Alamat</label>
    <textarea name="alamat"
              rows="3"
              class="form-control @error('alamat') is-invalid @enderror"
              placeholder="Alamat lengkap karyawan">{{ old('alamat', $karyawan->alamat ?? '') }}</textarea>
    @error('alamat')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<h5 class="mt-4 mb-3">Kontak</h5>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">No. Telepon</label>
            <input type="text"
                   name="no_telp"
                   class="form-control @error('no_telp') is-invalid @enderror"
                   value="{{ old('no_telp', $karyawan->no_telp ?? '') }}"
                   placeholder="e.g. 081234567890">
            @error('no_telp')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email"
                   name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', $karyawan->email ?? '') }}"
                   placeholder="e.g. karyawan@mail.com">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<h5 class="mt-4 mb-3">Keamanan & Audit</h5>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">PIN (6 digit) <span class="text-danger">*</span></label>
            <input type="text"
                name="pin"
                class="form-control @error('pin') is-invalid @enderror"
                value="{{ old('pin', $karyawan->pin ?? '') }}"
                placeholder="Masukkan 6 digit PIN"
                inputmode="numeric"
                pattern="[0-9]{6}"
                maxlength="6"
                required
                oninput="this.value = this.value.replace(/[^0-9]/g,'').slice(0,6)">
            @error('pin')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Tanggal Bergabung</label>
            <input type="date"
                   name="tanggal_bergabung"
                   class="form-control @error('tanggal_bergabung') is-invalid @enderror"
                   value="{{ old('tanggal_bergabung', $karyawan->tanggal_bergabung ?? '') }}">
            @error('tanggal_bergabung')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<h5 class="mt-4 mb-3">Foto Karyawan</h5>

<div class="mb-3">
    <label class="form-label">Upload Foto</label>
    <input type="file"
           name="foto"
           class="form-control"
           placeholder="Pilih foto karyawan">

    @error('foto')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    @if(isset($karyawan) && $karyawan->foto)
        <div class="mt-2">
            <img src="{{ asset('foto_karyawan/'.$karyawan->foto) }}"
                 class="img-thumbnail"
                 width="120">
        </div>
    @endif
</div>
