<div class="row">
    <div class="col-md-6">
        <label>KFA Code</label>
        <input type="text" name="kfa_code" class="form-control mb-2"
            value="{{ old('kfa_code', $obat->kfa_code ?? '') }}" required
            placeholder="Contoh: KFA0001234 — Kode unik dari Satu Sehat">

        <label>Nama Obat</label>
        <input type="text" name="nama_obat" class="form-control mb-2"
            value="{{ old('nama_obat', $obat->nama_obat ?? '') }}" required
            placeholder="Contoh: Paracetamol 500 mg tablet">

        <label>Nama Generik</label>
        <input type="text" name="nama_generik" class="form-control mb-2"
            value="{{ old('nama_generik', $obat->nama_generik ?? '') }}"
            placeholder="Contoh: Paracetamol">

        <label>Bentuk Sediaan</label>
        <input type="text" name="bentuk_sediaan" class="form-control mb-2"
            value="{{ old('bentuk_sediaan', $obat->bentuk_sediaan ?? '') }}"
            placeholder="Contoh: Tablet, Sirup, Kapsul, Injeksi">

        <label>Kekuatan</label>
        <input type="text" name="kekuatan" class="form-control mb-2"
            value="{{ old('kekuatan', $obat->kekuatan ?? '') }}"
            placeholder="Contoh: 500 mg, 10 ml, 2%">

        <label>Satuan Kekuatan</label>
        <input type="text" name="satuan_kekuatan" class="form-control mb-2"
            value="{{ old('satuan_kekuatan', $obat->satuan_kekuatan ?? '') }}"
            placeholder="Contoh: mg, ml, IU, mcg">

        <label>Kemasan</label>
        <input type="text" name="kemasan" class="form-control mb-2"
            value="{{ old('kemasan', $obat->kemasan ?? '') }}"
            placeholder="Contoh: Strip, Botol, Box, Tube">

        <label>Isi Kemasan</label>
        <input type="text" name="isi_kemasan" class="form-control mb-2"
            value="{{ old('isi_kemasan', $obat->isi_kemasan ?? '') }}"
            placeholder="Contoh: 10 tablet/strip atau 60 ml/botol">

        <label>Manufacturer</label>
        <input type="text" name="manufacturer" class="form-control mb-2"
            value="{{ old('manufacturer', $obat->manufacturer ?? '') }}"
            placeholder="Contoh: PT Kimia Farma, PT Dexa Medica">

        <label>Nomor Izin Edar (NIE)</label>
        <input type="text" name="nie" class="form-control mb-2"
            value="{{ old('nie', $obat->nie ?? '') }}"
            placeholder="Contoh: DBL123456789A1">
        
        <label>Kategori</label>
        <select name="kategori" class="form-select mb-2">
            <option value="">-- Pilih Kategori --</option>
            @foreach(['Generik', 'Paten', 'OTC'] as $opt)
                <option value="{{ $opt }}" {{ old('kategori', $obat->kategori ?? '') == $opt ? 'selected' : '' }}>
                    {{ $opt }}
                </option>
            @endforeach
        </select>

        <label>Golongan</label>
        <select name="golongan" class="form-select mb-2">
            <option value="">-- Pilih Golongan --</option>
            @foreach(['Bebas', 'Bebas Terbatas', 'Keras', 'Narkotika', 'Psikotropika'] as $opt)
                <option value="{{ $opt }}" {{ old('golongan', $obat->golongan ?? '') == $opt ? 'selected' : '' }}>
                    {{ $opt }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label>Komposisi</label>
        <textarea name="komposisi" class="form-control mb-2" rows="2"
            placeholder="Contoh: Paracetamol 500mg per tablet">{{ old('komposisi', $obat->komposisi ?? '') }}</textarea>

        <label>Indikasi</label>
        <textarea name="indikasi" class="form-control mb-2" rows="2"
            placeholder="Contoh: Meredakan demam dan nyeri ringan hingga sedang">{{ old('indikasi', $obat->indikasi ?? '') }}</textarea>

        <label>Kontraindikasi</label>
        <textarea name="kontraindikasi" class="form-control mb-2" rows="2"
            placeholder="Contoh: Hipersensitivitas terhadap paracetamol">{{ old('kontraindikasi', $obat->kontraindikasi ?? '') }}</textarea>

        <label>Efek Samping</label>
        <textarea name="efek_samping" class="form-control mb-2" rows="2"
            placeholder="Contoh: Mual, ruam, gangguan fungsi hati">{{ old('efek_samping', $obat->efek_samping ?? '') }}</textarea>

        <label>Peringatan</label>
        <textarea name="peringatan" class="form-control mb-2" rows="2"
            placeholder="Contoh: Hati-hati pada pasien dengan gangguan hati atau alkoholik">{{ old('peringatan', $obat->peringatan ?? '') }}</textarea>

        <label>Dosis</label>
        <textarea name="dosis" class="form-control mb-2" rows="2"
            placeholder="Contoh: Dewasa: 500 mg tiap 4-6 jam, maks 4 g/hari">{{ old('dosis', $obat->dosis ?? '') }}</textarea>

        <label>Data API (JSON)</label>
        <textarea name="data_api" class="form-control mb-2" rows="2"
            placeholder='Contoh: {"kfa_code": "KFA0001234", "manufacturer": "PT Kimia Farma"}'>{{ old('data_api', $obat->data_api ?? '') }}</textarea>

        <label>Status</label>
        <select name="status" class="form-select mb-2">
            @foreach(['Aktif', 'Nonaktif'] as $opt)
                <option value="{{ $opt }}" {{ old('status', $obat->status ?? 'Aktif') == $opt ? 'selected' : '' }}>
                    {{ $opt }}
                </option>
            @endforeach
        </select>

        <label>Terakhir Sync</label>
        <input type="datetime-local" name="last_sync" class="form-control mb-3"
            value="{{ old('last_sync', isset($obat->last_sync) ? date('Y-m-d\TH:i', strtotime($obat->last_sync)) : '') }}"
            placeholder="Tanggal dan waktu terakhir sinkronisasi">
    </div>
</div>
