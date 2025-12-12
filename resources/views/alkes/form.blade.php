<h5 class="mb-3">Informasi Dasar</h5>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="kode_alkes" class="form-label">
                Kode Alkes <span class="text-danger">*</span>
            </label>
            <input type="text" 
                   class="form-control text-uppercase @error('kode_alkes') is-invalid @enderror" 
                   id="kode_alkes" 
                   name="kode_alkes" 
                   value="{{ old('kode_alkes', $alkes->kode_alkes ?? '') }}" 
                   placeholder="e.g. ALK001, TENS001" 
                   maxlength="50"
                   required>
            @error('kode_alkes')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="nama_alkes" class="form-label">
                Nama Alat Kesehatan <span class="text-danger">*</span>
            </label>
            <input type="text" 
                   class="form-control @error('nama_alkes') is-invalid @enderror" 
                   id="nama_alkes" 
                   name="nama_alkes" 
                   value="{{ old('nama_alkes', $alkes->nama_alkes ?? '') }}" 
                   placeholder="e.g. Tensimeter Digital, Stetoskop" 
                   maxlength="200"
                   required>
            @error('nama_alkes')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="mb-3">
            <label for="merk" class="form-label">Merk</label>
            <input type="text" 
                   class="form-control @error('merk') is-invalid @enderror" 
                   id="merk" 
                   name="merk" 
                   value="{{ old('merk', $alkes->merk ?? '') }}" 
                   placeholder="e.g. Omron, Littmann" 
                   maxlength="100">
            @error('merk')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label for="model" class="form-label">Model</label>
            <input type="text" 
                   class="form-control @error('model') is-invalid @enderror" 
                   id="model" 
                   name="model" 
                   value="{{ old('model', $alkes->model ?? '') }}" 
                   placeholder="e.g. HEM-7130, Classic III" 
                   maxlength="100">
            @error('model')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label for="no_batch" class="form-label">No. Batch</label>
            <input type="text" 
                   class="form-control @error('no_batch') is-invalid @enderror" 
                   id="no_batch" 
                   name="no_batch" 
                   value="{{ old('no_batch', $alkes->no_batch ?? '') }}" 
                   placeholder="e.g. BATCH2024001" 
                   maxlength="50">
            @error('no_batch')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="mb-3">
            <label for="kategori" class="form-label">
                Kategori <span class="text-danger">*</span>
            </label>
            <select class="form-select @error('kategori') is-invalid @enderror" 
                    id="kategori" 
                    name="kategori"
                    required>
                <option value="">-- Pilih Kategori --</option>
                <option value="Alat Medis" {{ old('kategori', $alkes->kategori ?? '') == 'Alat Medis' ? 'selected' : '' }}>
                    Alat Medis
                </option>
                <option value="Alat Lab" {{ old('kategori', $alkes->kategori ?? '') == 'Alat Lab' ? 'selected' : '' }}>
                    Alat Lab
                </option>
            </select>
            @error('kategori')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label for="satuan" class="form-label">
                Satuan <span class="text-danger">*</span>
            </label>
            <input type="text" 
                   class="form-control @error('satuan') is-invalid @enderror" 
                   id="satuan" 
                   name="satuan" 
                   value="{{ old('satuan', $alkes->satuan ?? '') }}" 
                   placeholder="e.g. Unit, Pcs, Set" 
                   maxlength="50"
                   required>
            @error('satuan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label for="lokasi_penyimpanan" class="form-label">Lokasi Penyimpanan</label>
            <input type="text" 
                   class="form-control @error('lokasi_penyimpanan') is-invalid @enderror" 
                   id="lokasi_penyimpanan" 
                   name="lokasi_penyimpanan" 
                   value="{{ old('lokasi_penyimpanan', $alkes->lokasi_penyimpanan ?? '') }}" 
                   placeholder="e.g. Gudang A-1, Ruang Penyimpanan" 
                   maxlength="100">
            @error('lokasi_penyimpanan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="mb-3">
            <label for="spesifikasi" class="form-label">Spesifikasi</label>
            <textarea class="form-control @error('spesifikasi') is-invalid @enderror" 
                      id="spesifikasi" 
                      name="spesifikasi" 
                      rows="3" 
                      placeholder="Masukkan spesifikasi teknis alat">{{ old('spesifikasi', $alkes->spesifikasi ?? '') }}</textarea>
            @error('spesifikasi')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<h5 class="mb-3 mt-4">Stok & Kondisi</h5>

<div class="row">
    <div class="col-md-3">
        <div class="mb-3">
            <label for="jumlah_stok" class="form-label">
                Jumlah Stok <span class="text-danger">*</span>
            </label>
            <input type="number" 
                   class="form-control @error('jumlah_stok') is-invalid @enderror" 
                   id="jumlah_stok" 
                   name="jumlah_stok" 
                   value="{{ old('jumlah_stok', $alkes->jumlah_stok ?? 0) }}" 
                   min="0"
                   required>
            @error('jumlah_stok')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3">
            <label for="stok_minimal" class="form-label">
                Stok Minimal <span class="text-danger">*</span>
            </label>
            <input type="number" 
                   class="form-control @error('stok_minimal') is-invalid @enderror" 
                   id="stok_minimal" 
                   name="stok_minimal" 
                   value="{{ old('stok_minimal', $alkes->stok_minimal ?? 0) }}" 
                   min="0"
                   required>
            @error('stok_minimal')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3">
            <label for="kondisi" class="form-label">
                Kondisi <span class="text-danger">*</span>
            </label>
            <select class="form-select @error('kondisi') is-invalid @enderror" 
                    id="kondisi" 
                    name="kondisi"
                    required>
                <option value="">-- Pilih Kondisi --</option>
                <option value="Baik" {{ old('kondisi', $alkes->kondisi ?? '') == 'Baik' ? 'selected' : '' }}>
                    Baik
                </option>
                <option value="Rusak" {{ old('kondisi', $alkes->kondisi ?? '') == 'Rusak' ? 'selected' : '' }}>
                    Rusak
                </option>
                <option value="Perlu Maintenance" {{ old('kondisi', $alkes->kondisi ?? '') == 'Perlu Maintenance' ? 'selected' : '' }}>
                    Perlu Maintenance
                </option>
            </select>
            @error('kondisi')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3">
            <label for="status" class="form-label">
                Status <span class="text-danger">*</span>
            </label>
            <select class="form-select @error('status') is-invalid @enderror" 
                    id="status" 
                    name="status"
                    required>
                <option value="Aktif" {{ old('status', $alkes->status ?? 'Aktif') == 'Aktif' ? 'selected' : '' }}>
                    Aktif
                </option>
                <option value="Nonaktif" {{ old('status', $alkes->status ?? '') == 'Nonaktif' ? 'selected' : '' }}>
                    Nonaktif
                </option>
                <option value="Rusak" {{ old('status', $alkes->status ?? '') == 'Rusak' ? 'selected' : '' }}>
                    Rusak
                </option>
                <option value="Dalam Perbaikan" {{ old('status', $alkes->status ?? '') == 'Dalam Perbaikan' ? 'selected' : '' }}>
                    Dalam Perbaikan
                </option>
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="mb-3">
            <label for="tanggal_kadaluarsa" class="form-label">Tanggal Kadaluarsa</label>
            <input type="date" 
                   class="form-control @error('tanggal_kadaluarsa') is-invalid @enderror" 
                   id="tanggal_kadaluarsa" 
                   name="tanggal_kadaluarsa" 
                   value="{{ old('tanggal_kadaluarsa', $alkes->tanggal_kadaluarsa?->format('Y-m-d') ?? '') }}">
            @error('tanggal_kadaluarsa')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Untuk alat yang memiliki masa berlaku</small>
        </div>
    </div>
</div>

<h5 class="mb-3 mt-4">Kalibrasi & Maintenance</h5>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="tanggal_kalibrasi_terakhir" class="form-label">Tanggal Kalibrasi Terakhir</label>
            <input type="date" 
                   class="form-control @error('tanggal_kalibrasi_terakhir') is-invalid @enderror" 
                   id="tanggal_kalibrasi_terakhir" 
                   name="tanggal_kalibrasi_terakhir" 
                   value="{{ old('tanggal_kalibrasi_terakhir', $alkes->tanggal_kalibrasi_terakhir?->format('Y-m-d') ?? '') }}">
            @error('tanggal_kalibrasi_terakhir')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="tanggal_kalibrasi_berikutnya" class="form-label">Tanggal Kalibrasi Berikutnya</label>
            <input type="date" 
                   class="form-control @error('tanggal_kalibrasi_berikutnya') is-invalid @enderror" 
                   id="tanggal_kalibrasi_berikutnya" 
                   name="tanggal_kalibrasi_berikutnya" 
                   value="{{ old('tanggal_kalibrasi_berikutnya', $alkes->tanggal_kalibrasi_berikutnya?->format('Y-m-d') ?? '') }}">
            @error('tanggal_kalibrasi_berikutnya')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="mb-3">
            <label for="maintenance_schedule" class="form-label">Jadwal Maintenance</label>
            <textarea class="form-control @error('maintenance_schedule') is-invalid @enderror" 
                      id="maintenance_schedule" 
                      name="maintenance_schedule" 
                      rows="3" 
                      placeholder="e.g. Setiap 3 bulan sekali, Cek rutin setiap minggu">{{ old('maintenance_schedule', $alkes->maintenance_schedule ?? '') }}</textarea>
            @error('maintenance_schedule')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<h5 class="mb-3 mt-4">Informasi Harga</h5>

<div class="row">
    <div class="col-md-4">
        <div class="mb-3">
            <label for="harga_beli" class="form-label">
                Harga Beli <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="number" 
                       class="form-control @error('harga_beli') is-invalid @enderror" 
                       id="harga_beli" 
                       name="harga_beli" 
                       value="{{ old('harga_beli', $alkes->harga_beli ?? 0) }}" 
                       min="0"
                       required>
            </div>
            @error('harga_beli')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label for="harga_jual_umum" class="form-label">
                Harga Jual Umum <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="number" 
                       class="form-control @error('harga_jual_umum') is-invalid @enderror" 
                       id="harga_jual_umum" 
                       name="harga_jual_umum" 
                       value="{{ old('harga_jual_umum', $alkes->harga_jual_umum ?? 0) }}" 
                       min="0"
                       required>
            </div>
            @error('harga_jual_umum')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label for="harga_jual_bpjs" class="form-label">
                Harga Jual BPJS <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="number" 
                       class="form-control @error('harga_jual_bpjs') is-invalid @enderror" 
                       id="harga_jual_bpjs" 
                       name="harga_jual_bpjs" 
                       value="{{ old('harga_jual_bpjs', $alkes->harga_jual_bpjs ?? 0) }}" 
                       min="0"
                       required>
            </div>
            @error('harga_jual_bpjs')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="tanggal_mulai" class="form-label">Tanggal Mulai Harga</label>
            <input type="date" 
                   class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                   id="tanggal_mulai" 
                   name="tanggal_mulai" 
                   value="{{ old('tanggal_mulai', $alkes->tanggal_mulai?->format('Y-m-d') ?? '') }}">
            @error('tanggal_mulai')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="tanggal_selesai" class="form-label">Tanggal Selesai Harga</label>
            <input type="date" 
                   class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                   id="tanggal_selesai" 
                   name="tanggal_selesai" 
                   value="{{ old('tanggal_selesai', $alkes->tanggal_selesai?->format('Y-m-d') ?? '') }}">
            @error('tanggal_selesai')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<h5 class="mb-3 mt-4">Catatan Tambahan</h5>

<div class="row">
    <div class="col-md-12">
        <div class="mb-3">
            <label for="catatan" class="form-label">Catatan</label>
            <textarea class="form-control @error('catatan') is-invalid @enderror" 
                      id="catatan" 
                      name="catatan" 
                      rows="4" 
                      placeholder="Catatan tambahan tentang alat kesehatan ini">{{ old('catatan', $alkes->catatan ?? '') }}</textarea>
            @error('catatan')
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
                <li>Kode alkes akan otomatis diubah menjadi huruf kapital</li>
                <li>Stok minimal digunakan sebagai peringatan stok rendah</li>
                <li>Tanggal kalibrasi berikutnya harus setelah tanggal kalibrasi terakhir</li>
                <li>Harga beli, harga jual umum, dan harga jual BPJS wajib diisi</li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto uppercase kode alkes
    document.getElementById('kode_alkes').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });

    // Format currency on input
    ['harga_beli', 'harga_jual_umum', 'harga_jual_bpjs'].forEach(function(id) {
        document.getElementById(id).addEventListener('blur', function(e) {
            const value = parseInt(e.target.value) || 0;
            e.target.value = value;
        });
    });
</script>
@endpush