<h5 class="mb-3">Informasi Dosis Obat</h5>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="jumlah" class="form-label">
                Jumlah <span class="text-danger">*</span>
            </label>
            <input type="text" 
                   class="form-control @error('jumlah') is-invalid @enderror" 
                   id="jumlah" 
                   name="jumlah" 
                   value="{{ old('jumlah', $dosis->jumlah ?? '') }}" 
                   placeholder="e.g. 500mg, 2 tablet, 5ml" 
                   maxlength="100"
                   required>
            @error('jumlah')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Dosis per sekali minum. Contoh: <code>500mg</code>, <code>2 tablet</code>, <code>5ml</code></small>
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="frekuensi" class="form-label">
                Frekuensi <span class="text-danger">*</span>
            </label>
            <input type="text" 
                   class="form-control @error('frekuensi') is-invalid @enderror" 
                   id="frekuensi" 
                   name="frekuensi" 
                   value="{{ old('frekuensi', $dosis->frekuensi ?? '') }}" 
                   placeholder="e.g. 3x sehari, 2x sehari, Setiap 6 jam" 
                   maxlength="100"
                   required>
            @error('frekuensi')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Berapa kali dalam sehari. Contoh: <code>3x sehari</code>, <code>Setiap 6 jam</code>, <code>Bila perlu</code></small>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="durasi" class="form-label">Durasi</label>
            <input type="text" 
                   class="form-control @error('durasi') is-invalid @enderror" 
                   id="durasi" 
                   name="durasi" 
                   value="{{ old('durasi', $dosis->durasi ?? '') }}" 
                   placeholder="e.g. 7 hari, 2 minggu, Sampai habis" 
                   maxlength="100">
            @error('durasi')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Lama pengobatan. Contoh: <code>7 hari</code>, <code>2 minggu</code>, <code>Sampai habis</code>, <code>Terus menerus</code></small>
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="rute" class="form-label">Rute Pemberian</label>
            <input type="text" 
                   class="form-control @error('rute') is-invalid @enderror" 
                   id="rute" 
                   name="rute" 
                   value="{{ old('rute', $dosis->rute ?? '') }}" 
                   placeholder="e.g. Oral, IV, IM, Topikal" 
                   maxlength="100">
            @error('rute')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Cara pemberian obat. Contoh: <code>Oral</code>, <code>IV</code>, <code>IM</code>, <code>Topikal</code>, <code>Inhalasi</code></small>
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
                <li><strong>Jumlah:</strong> Dosis per sekali minum (contoh: 500mg, 2 tablet, 5ml, 1 kapsul)</li>
                <li><strong>Frekuensi:</strong> Berapa kali obat diminum dalam sehari (contoh: 3x sehari, 2x sehari, Setiap 8 jam)</li>
                <li><strong>Durasi:</strong> Lama pengobatan - opsional (contoh: 7 hari, 2 minggu, Sampai habis)</li>
                <li><strong>Rute:</strong> Cara pemberian obat - opsional (contoh: Oral, IV, IM, SC, Topikal)</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card bg-light border-0">
            <div class="card-body">
                <h6 class="card-title"><i class="ri-lightbulb-line text-warning"></i> Contoh Pengisian</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>Jumlah</th>
                                <th>Frekuensi</th>
                                <th>Durasi</th>
                                <th>Rute</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <tr>
                                <td><code>500mg</code></td>
                                <td><code>3x sehari</code></td>
                                <td><code>7 hari</code></td>
                                <td><code>Oral</code></td>
                            </tr>
                            <tr>
                                <td><code>2 tablet</code></td>
                                <td><code>2x sehari</code></td>
                                <td><code>Sampai habis</code></td>
                                <td><code>Oral</code></td>
                            </tr>
                            <tr>
                                <td><code>5ml</code></td>
                                <td><code>Setiap 6 jam</code></td>
                                <td><code>5 hari</code></td>
                                <td><code>Oral</code></td>
                            </tr>
                            <tr>
                                <td><code>1 ampul</code></td>
                                <td><code>1x sehari</code></td>
                                <td><code>3 hari</code></td>
                                <td><code>IV</code></td>
                            </tr>
                            <tr>
                                <td><code>250mg</code></td>
                                <td><code>Bila perlu</code></td>
                                <td><code>-</code></td>
                                <td><code>Oral</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <p class="small mb-2"><strong>Tips Pengisian:</strong></p>
                    <ul class="small mb-0">
                        <li><strong>Jumlah:</strong> Selalu sertakan satuan (mg, tablet, ml, kapsul, ampul, tetes, dll)</li>
                        <li><strong>Frekuensi:</strong> Gunakan format "Nx sehari" atau "Setiap X jam" atau "Bila perlu"</li>
                        <li><strong>Durasi:</strong> Kosongkan jika tidak ada batas waktu atau tulis "Terus menerus"</li>
                        <li><strong>Rute:</strong> Singkatan umum: Oral (diminum), IV (intravena), IM (intramuskular), SC (subkutan), Topikal (oles)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto capitalize first letter for better formatting
    document.getElementById('rute').addEventListener('blur', function(e) {
        if (e.target.value) {
            e.target.value = e.target.value.charAt(0).toUpperCase() + e.target.value.slice(1);
        }
    });
</script>
@endpush