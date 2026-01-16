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
            <small class="form-text text-muted">Kode unik identifikasi dokter. Contoh: <code>DR001</code>, <code>DR002</code></small>
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
            <small class="form-text text-muted">Nama lengkap beserta gelar. Contoh: <code>dr. Ahmad Santoso, Sp.PD</code></small>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="spesialisasi" class="form-label">Spesialisasi</label>
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
            <small class="form-text text-muted">Bidang keahlian. Contoh: <code>Penyakit Dalam</code>, <code>Anak</code>, <code>Bedah</code></small>
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="status" class="form-label">
                Status <span class="text-danger">*</span>
            </label>
            <select class="form-control form-select @error('status') is-invalid @enderror"
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
            <small class="form-text text-muted">Status keaktifan dokter di rumah sakit</small>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="no_str" class="form-label">No. STR</label>
            <input type="text" 
                   class="form-control @error('no_str') is-invalid @enderror" 
                   id="no_str" 
                   name="no_str" 
                   value="{{ old('no_str', $dokter->no_str ?? '') }}" 
                   placeholder="e.g. KKI.00001.2024" 
                   maxlength="255">
            @error('no_str')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">Surat Tanda Registrasi. Contoh: <code>KKI.00001.2024</code></small>
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="no_sip" class="form-label">No. SIP</label>
            <input type="text" 
                   class="form-control @error('no_sip') is-invalid @enderror" 
                   id="no_sip" 
                   name="no_sip" 
                   value="{{ old('no_sip', $dokter->no_sip ?? '') }}" 
                   placeholder="e.g. SIP/DR/001/2024" 
                   maxlength="255">
            @error('no_sip')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">Surat Izin Praktik. Contoh: <code>SIP/DR/001/2024</code></small>
        </div>
    </div>
</div>

<hr class="my-4">

<h5 class="mb-3">
    <i class="ri-hospital-line me-2"></i>Ruangan Praktek
</h5>

<div class="row">
    <div class="col-12">
        @if(isset($ruangans) && $ruangans->count() > 0)
            <div class="card bg-light border-0 mb-3">
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
                                    <label class="form-check-label" for="ruangan_{{ $ruangan->id }}">
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
            <div class="alert alert-warning mb-3">
                <i class="ri-alert-line me-2"></i>
                Belum ada ruangan yang tersedia. Silakan <a href="{{ route('ruangans.create') }}" target="_blank" class="alert-link">tambah ruangan</a> terlebih dahulu.
            </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="alert alert-info mb-3">
            <i class="ri-information-line"></i>
            <strong>Informasi:</strong>
            <ul class="mb-0 mt-2">
                <li>Field yang ditandai <span class="text-danger">*</span> wajib diisi</li>
                <li><strong>Kode Dokter:</strong> Kode unik untuk identifikasi dokter (akan otomatis kapital)</li>
                <li><strong>Nama Dokter:</strong> Nama lengkap dokter beserta gelarnya</li>
                <li><strong>Spesialisasi:</strong> Bidang keahlian dokter - opsional (kosongkan jika dokter umum)</li>
                <li><strong>Status:</strong> Status aktif/nonaktif dokter di rumah sakit</li>
                <li><strong>No. STR:</strong> Nomor Surat Tanda Registrasi - opsional</li>
                <li><strong>No. SIP:</strong> Nomor Surat Izin Praktik - opsional</li>
                <li><strong>Ruangan Praktek:</strong> Pilih ruangan tempat dokter berpraktik (bisa lebih dari satu)</li>
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
                    <table class="table table-sm table-bordered table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="100">Kode</th>
                                <th>Nama Dokter</th>
                                <th width="150">Spesialisasi</th>
                                <th width="140">No. STR</th>
                                <th width="140">No. SIP</th>
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
                <div class="mt-3">
                    <p class="small mb-2"><strong>Tips Pengisian:</strong></p>
                    <ul class="small mb-0">
                        <li><strong>Kode Dokter:</strong> Gunakan format "DR" diikuti nomor urut (DR001, DR002, dst)</li>
                        <li><strong>Nama Dokter:</strong> Tulis lengkap dengan gelar dokter dan spesialisnya jika ada</li>
                        <li><strong>Spesialisasi:</strong> Kosongkan untuk dokter umum atau isi sesuai bidang keahlian</li>
                        <li><strong>No. STR/SIP:</strong> Salin dari dokumen resmi atau kosongkan jika belum ada</li>
                        <li><strong>Status:</strong> Pilih Aktif untuk dokter yang sedang praktik</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto uppercase kode dokter
        const kodeDokterInput = document.getElementById('kode_dokter');
        if (kodeDokterInput) {
            kodeDokterInput.addEventListener('input', function(e) {
                e.target.value = e.target.value.toUpperCase();
            });
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
                    document.getElementById('kode_dokter').value = kode;
                    document.getElementById('nama_dokter').value = nama;
                    document.getElementById('spesialisasi').value = spesialisasi;
                    document.getElementById('no_str').value = str;
                    document.getElementById('no_sip').value = sip;
                    document.getElementById('status').value = 'Aktif';
                    
                    // Scroll to top of form
                    document.getElementById('kode_dokter').scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        });
    });
</script>
@endpush