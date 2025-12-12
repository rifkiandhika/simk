<h5 class="mb-3">Data Reagensia</h5>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="kode_reagensia" class="form-label">
                Kode Reagensia <span class="text-danger">*</span>
            </label>
            <input type="text" 
                   class="form-control @error('kode_reagensia') is-invalid @enderror" 
                   id="kode_reagensia" 
                   name="kode_reagensia" 
                   value="{{ old('kode_reagensia', $reagen->kode_reagensia ?? '') }}" 
                   placeholder="e.g. REA-001" 
                   style="text-transform: uppercase;"
                   required>
            @error('kode_reagensia')
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
                <option value="Aktif" {{ old('status', $reagen->status ?? 'Aktif') == 'Aktif' ? 'selected' : '' }}>
                    Aktif
                </option>
                <option value="Nonaktif" {{ old('status', $reagen->status ?? '') == 'Nonaktif' ? 'selected' : '' }}>
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
    <div class="col-md-12">
        <div class="mb-3">
            <label for="nama_reagensia" class="form-label">
                Nama Reagensia <span class="text-danger">*</span>
            </label>
            <input type="text" 
                   class="form-control @error('nama_reagensia') is-invalid @enderror" 
                   id="nama_reagensia" 
                   name="nama_reagensia" 
                   value="{{ old('nama_reagensia', $reagen->nama_reagensia ?? '') }}" 
                   placeholder="e.g. Natrium Klorida, Glucose Test Strip" 
                   required>
            @error('nama_reagensia')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="merk" class="form-label">Merk</label>
            <input type="text" 
                   class="form-control @error('merk') is-invalid @enderror" 
                   id="merk" 
                   name="merk" 
                   value="{{ old('merk', $reagen->merk ?? '') }}" 
                   placeholder="e.g. Roche, Abbott, Siemens">
            @error('merk')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="no_katalog" class="form-label">No. Katalog</label>
            <input type="text" 
                   class="form-control @error('no_katalog') is-invalid @enderror" 
                   id="no_katalog" 
                   name="no_katalog" 
                   value="{{ old('no_katalog', $reagen->no_katalog ?? '') }}" 
                   placeholder="e.g. CAT-12345">
            @error('no_katalog')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="mb-3">
            <label for="komposisi" class="form-label">Komposisi</label>
            <textarea class="form-control @error('komposisi') is-invalid @enderror" 
                      id="komposisi" 
                      name="komposisi" 
                      rows="3" 
                      placeholder="Komposisi kimia atau bahan reagensia">{{ old('komposisi', $reagen->komposisi ?? '') }}</textarea>
            @error('komposisi')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<h5 class="mb-3 mt-4">Kemasan & Penyimpanan</h5>

<div class="row">
    <div class="col-md-4">
        <div class="mb-3">
            <label for="satuan" class="form-label">
                Satuan <span class="text-danger">*</span>
            </label>
            <select class="form-select @error('satuan') is-invalid @enderror" 
                    id="satuan" 
                    name="satuan" 
                    required>
                <option value="">-- Pilih Satuan --</option>
                @foreach ($satuans as $satuan)
                    <option value="{{ $satuan->nama_satuan }}" 
                        {{ old('satuan', $reagen->satuan_id ?? '') == $satuan->id ? 'selected' : '' }}>
                        {{ $satuan->nama_satuan }}
                    </option>
                @endforeach
            </select>
            @error('satuan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label for="volume_kemasan" class="form-label">Volume Kemasan</label>
            <input type="text" 
                   class="form-control @error('volume_kemasan') is-invalid @enderror" 
                   id="volume_kemasan" 
                   name="volume_kemasan" 
                   value="{{ old('volume_kemasan', $reagen->volume_kemasan ?? '') }}" 
                   placeholder="e.g. 500 mL, 1 L">
            @error('volume_kemasan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label for="stok_minimal" class="form-label">
                Stok Minimal <span class="text-danger">*</span>
            </label>
            <input type="number" 
                   class="form-control @error('stok_minimal') is-invalid @enderror" 
                   id="stok_minimal" 
                   name="stok_minimal" 
                   value="{{ old('stok_minimal', $reagen->stok_minimal ?? '') }}" 
                   min="0" 
                   required>
            @error('stok_minimal')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="mb-3">
            <label for="suhu_penyimpanan_min" class="form-label">Suhu Minimal (°C)</label>
            <input type="number" 
                   step="0.01" 
                   class="form-control @error('suhu_penyimpanan_min') is-invalid @enderror" 
                   id="suhu_penyimpanan_min" 
                   name="suhu_penyimpanan_min" 
                   value="{{ old('suhu_penyimpanan_min', $reagen->suhu_penyimpanan_min ?? '') }}" 
                   placeholder="e.g. 2">
            @error('suhu_penyimpanan_min')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label for="suhu_penyimpanan_max" class="form-label">Suhu Maksimal (°C)</label>
            <input type="number" 
                   step="0.01" 
                   class="form-control @error('suhu_penyimpanan_max') is-invalid @enderror" 
                   id="suhu_penyimpanan_max" 
                   name="suhu_penyimpanan_max" 
                   value="{{ old('suhu_penyimpanan_max', $reagen->suhu_penyimpanan_max ?? '') }}" 
                   placeholder="e.g. 8">
            @error('suhu_penyimpanan_max')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label for="stabilitas_hari" class="form-label">Stabilitas (Hari)</label>
            <input type="number" 
                   class="form-control @error('stabilitas_hari') is-invalid @enderror" 
                   id="stabilitas_hari" 
                   name="stabilitas_hari" 
                   value="{{ old('stabilitas_hari', $reagen->stabilitas_hari ?? '') }}" 
                   min="0" 
                   placeholder="e.g. 30">
            @error('stabilitas_hari')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="lokasi_penyimpanan" class="form-label">Lokasi Penyimpanan</label>
            <input type="text" 
                   class="form-control @error('lokasi_penyimpanan') is-invalid @enderror" 
                   id="lokasi_penyimpanan" 
                   name="lokasi_penyimpanan" 
                   value="{{ old('lokasi_penyimpanan', $reagen->lokasi_penyimpanan ?? '') }}" 
                   placeholder="e.g. Rak A-1, Kulkas B">
            @error('lokasi_penyimpanan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="kondisi_penyimpanan" class="form-label">Kondisi Penyimpanan</label>
            <input type="text" 
                   class="form-control @error('kondisi_penyimpanan') is-invalid @enderror" 
                   id="kondisi_penyimpanan" 
                   name="kondisi_penyimpanan" 
                   value="{{ old('kondisi_penyimpanan', $reagen->kondisi_penyimpanan ?? '') }}" 
                   placeholder="e.g. Simpan di tempat kering dan sejuk">
            @error('kondisi_penyimpanan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<h5 class="mb-3 mt-4">Prosedur & Keselamatan</h5>

<div class="row">
    <div class="col-md-12">
        <div class="mb-3">
            <label for="prosedur_penggunaan" class="form-label">Prosedur Penggunaan</label>
            <textarea class="form-control @error('prosedur_penggunaan') is-invalid @enderror" 
                      id="prosedur_penggunaan" 
                      name="prosedur_penggunaan" 
                      rows="4" 
                      placeholder="Prosedur atau instruksi penggunaan reagensia">{{ old('prosedur_penggunaan', $reagen->prosedur_penggunaan ?? '') }}</textarea>
            @error('prosedur_penggunaan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="mb-3">
            <label for="bahaya_keselamatan" class="form-label">
                Bahaya Keselamatan
                <span class="badge bg-warning text-dark ms-2">
                    <i class="ri-alert-line"></i> Opsional
                </span>
            </label>
            <textarea class="form-control @error('bahaya_keselamatan') is-invalid @enderror" 
                      id="bahaya_keselamatan" 
                      name="bahaya_keselamatan" 
                      rows="3" 
                      placeholder="Informasi bahaya dan cara penanganan (kosongkan jika tidak berbahaya)">{{ old('bahaya_keselamatan', $reagen->bahaya_keselamatan ?? '') }}</textarea>
            @error('bahaya_keselamatan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<h5 class="mb-3 mt-4">Informasi Harga</h5>

<div class="alert alert-info">
    <i class="ri-information-line"></i>
    <strong>Harga Reagensia Saat Ini</strong>
    <p class="mb-1 mt-2">
        Anda dapat memperbarui harga di bawah ini langsung — sistem akan menyimpannya di tabel reagensias.
    </p>
</div>

<div id="harga_section">
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="harga_beli" class="form-label">Harga Beli (Rp)</label>
                <input type="number"
                       class="form-control @error('harga_beli') is-invalid @enderror format-rupiah"
                       id="harga_beli"
                       name="harga_beli"
                       value="{{ old('harga_beli', $reagen->harga_beli ?? '') }}"
                       min="0"
                       placeholder="e.g. 500000">
                @error('harga_beli')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label for="harga_per_test" class="form-label">Harga Per Test (Rp)</label>
                <input type="number"
                       class="form-control @error('harga_per_test') is-invalid @enderror format-rupiah"
                       id="harga_per_test"
                       name="harga_per_test"
                       value="{{ old('harga_per_test', $reagen->harga_per_test ?? '') }}"
                       min="0"
                       placeholder="e.g. 25000">
                @error('harga_per_test')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="tanggal_mulai" class="form-label fw-semibold">
                    <i class="ri-calendar-line me-1"></i> Tanggal Mulai Berlaku
                </label>
                <input type="date"
                    class="form-control @error('tanggal_mulai') is-invalid @enderror"
                    id="tanggal_mulai"
                    name="tanggal_mulai"
                    value="{{ old('tanggal_mulai', optional($reagen)->tanggal_mulai ? date('Y-m-d', strtotime($reagen->tanggal_mulai)) : '') }}">
                @error('tanggal_mulai')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label for="tanggal_selesai" class="form-label fw-semibold">
                    <i class="ri-calendar-event-line me-1"></i> Tanggal Selesai Berlaku
                </label>
                <input type="date"
                    class="form-control @error('tanggal_selesai') is-invalid @enderror"
                    id="tanggal_selesai"
                    name="tanggal_selesai"
                    value="{{ old('tanggal_selesai', optional($reagen)->tanggal_selesai ? date('Y-m-d', strtotime($reagen->tanggal_selesai)) : '') }}"
                    min="{{ old('tanggal_mulai', optional($reagen)->tanggal_mulai ? date('Y-m-d', strtotime($reagen->tanggal_mulai)) : '') }}">
                @error('tanggal_selesai')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="alert alert-info">
            <i class="ri-information-line"></i>
            <strong>Informasi:</strong>
            <ul class="mb-0 mt-2">
                <li><strong>Kode reagensia</strong>, <strong>nama reagensia</strong>, <strong>satuan</strong>, <strong>stok minimal</strong>, dan <strong>status</strong> wajib diisi</li>
                <li>Field lainnya bersifat opsional</li>
                <li>Suhu maksimal harus lebih besar atau sama dengan suhu minimal</li>
                <li>Tanggal selesai harga harus lebih besar atau sama dengan tanggal mulai</li>
                <li>Jika mengisi harga, harap isi <strong>Harga Beli</strong> dan <strong>Harga Per Test</strong></li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const rupiahInputs = document.querySelectorAll('.format-rupiah');

    
    rupiahInputs.forEach(input => {
        input.addEventListener('input', function () {
            let value = this.value.replace(/\D/g, '');
            this.value = new Intl.NumberFormat('id-ID').format(value);
        });
    });

    
    document.querySelector('form')?.addEventListener('submit', function () {
        rupiahInputs.forEach(inp => inp.value = inp.value.replace(/\./g, '').replace(/,/g, ''));
    });

    // Validasi harga: jika salah satu diisi, keduanya harus diisi
    document.querySelector('form')?.addEventListener('submit', function (e) {
        const beli = document.getElementById('harga_beli').value;
        const perTest = document.getElementById('harga_per_test').value;

        if ((beli && !perTest) || (!beli && perTest)) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Jika mengisi harga, harap isi Harga Beli dan Harga Per Test',
            });
        }
    });

    
    document.getElementById('suhu_penyimpanan_max')?.addEventListener('change', function () {
        const min = parseFloat(document.getElementById('suhu_penyimpanan_min').value);
        const max = parseFloat(this.value);
        if (min && max && max < min) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Suhu maksimal harus lebih besar atau sama dengan suhu minimal',
            });
            this.value = '';
        }
    });

    
    document.addEventListener('DOMContentLoaded', function () {
        const inputMulai = document.getElementById('tanggal_mulai');
        const inputSelesai = document.getElementById('tanggal_selesai');

        
        if (inputMulai && inputSelesai && inputMulai.value) {
            inputSelesai.min = inputMulai.value;
        }

        
        inputMulai?.addEventListener('change', function () {
            if (inputSelesai) {
                inputSelesai.min = this.value;
                if (inputSelesai.value && new Date(inputSelesai.value) < new Date(this.value)) {
                    inputSelesai.value = '';
                }
            }
        });

        
        inputSelesai?.addEventListener('blur', function () {
            const start = inputMulai?.value;
            const end = this.value;

            
            if (start && end && start.length === 10 && end.length === 10) {
                if (new Date(end) < new Date(start)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Tanggal selesai harus sama atau setelah tanggal mulai!',
                    });
                    this.value = '';
                    this.focus();
                }
            }
        });
    });
});
</script>
@endpush