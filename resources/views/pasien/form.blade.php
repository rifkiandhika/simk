<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">{{ isset($pasien) ? 'Edit' : 'Tambah' }} Data Pasien</h5>
    </div>
    <div class="card-body">
            <!-- Data Identitas -->
            <div class="row mb-4">
                <div class="col-12">
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="ri-user-line"></i> Data Identitas
                    </h6>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">No. Rekam Medis <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="text" name="no_rm" id="no_rm" class="form-control @error('no_rm') is-invalid @enderror"
                               value="{{ old('no_rm', $pasien->no_rm ?? '') }}" 
                               placeholder="Contoh: RM000001" 
                               required
                               {{ isset($pasien) ? 'readonly' : '' }}>
                        @if(!isset($pasien))
                        <button type="button" class="btn btn-outline-secondary" id="btn-generate-rm">
                            <i class="ri-refresh-line"></i> Generate
                        </button>
                        @endif
                    </div>
                    @error('no_rm')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">NIK</label>
                    <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror"
                           value="{{ old('nik', $pasien->nik ?? '') }}" 
                           placeholder="16 digit NIK" 
                           maxlength="16">
                    @error('nik')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Tanggal Registrasi</label>
                    <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror"
                           value="{{ old('tanggal', $pasien->tanggal ?? date('Y-m-d')) }}">
                    @error('tanggal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror"
                           value="{{ old('nama_lengkap', $pasien->nama_lengkap ?? '') }}" 
                           placeholder="Nama lengkap pasien" 
                           required>
                    @error('nama_lengkap')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                    <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                        <option value="">Pilih</option>
                        <option value="L" {{ old('jenis_kelamin', $pasien->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin', $pasien->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('jenis_kelamin')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Golongan Darah</label>
                    <select name="golongan_darah" class="form-select @error('golongan_darah') is-invalid @enderror">
                        <option value="">Pilih</option>
                        @foreach(['A', 'B', 'AB', 'O', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $gol)
                            <option value="{{ $gol }}" {{ old('golongan_darah', $pasien->golongan_darah ?? '') == $gol ? 'selected' : '' }}>
                                {{ $gol }}
                            </option>
                        @endforeach
                    </select>
                    @error('golongan_darah')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror"
                           value="{{ old('tempat_lahir', $pasien->tempat_lahir ?? '') }}" 
                           placeholder="Contoh: Jakarta">
                    @error('tempat_lahir')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror"
                           value="{{ old('tanggal_lahir', $pasien->tanggal_lahir ?? '') }}">
                    @error('tanggal_lahir')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Status Perkawinan</label>
                    <select name="status_perkawinan" class="form-select @error('status_perkawinan') is-invalid @enderror">
                        <option value="">Pilih</option>
                        @foreach(['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'] as $status)
                            <option value="{{ $status }}" {{ old('status_perkawinan', $pasien->status_perkawinan ?? '') == $status ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                    @error('status_perkawinan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Pekerjaan</label>
                    <input type="text" name="pekerjaan" class="form-control @error('pekerjaan') is-invalid @enderror"
                           value="{{ old('pekerjaan', $pasien->pekerjaan ?? '') }}" 
                           placeholder="Contoh: Pegawai Swasta">
                    @error('pekerjaan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" 
                              rows="3" 
                              placeholder="Alamat lengkap">{{ old('alamat', $pasien->alamat ?? '') }}</textarea>
                    @error('alamat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Data Kontak -->
            <div class="row mb-4">
                <div class="col-12">
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="ri-phone-line"></i> Data Kontak
                    </h6>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="no_telp" class="form-control @error('no_telp') is-invalid @enderror"
                           value="{{ old('no_telp', $pasien->no_telp ?? '') }}" 
                           placeholder="Contoh: 08123456789">
                    @error('no_telp')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">No. Telepon Darurat</label>
                    <input type="text" name="no_telp_darurat" class="form-control @error('no_telp_darurat') is-invalid @enderror"
                           value="{{ old('no_telp_darurat', $pasien->no_telp_darurat ?? '') }}" 
                           placeholder="Contoh: 08123456789">
                    @error('no_telp_darurat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Kontak Darurat</label>
                    <input type="text" name="nama_kontak_darurat" class="form-control @error('nama_kontak_darurat') is-invalid @enderror"
                           value="{{ old('nama_kontak_darurat', $pasien->nama_kontak_darurat ?? '') }}" 
                           placeholder="Nama lengkap kontak darurat">
                    @error('nama_kontak_darurat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Hubungan Kontak Darurat</label>
                    <input type="text" name="hubungan_kontak_darurat" class="form-control @error('hubungan_kontak_darurat') is-invalid @enderror"
                           value="{{ old('hubungan_kontak_darurat', $pasien->hubungan_kontak_darurat ?? '') }}" 
                           placeholder="Contoh: Suami/Istri/Anak">
                    @error('hubungan_kontak_darurat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Data Ruangan & Pembayaran -->
            <div class="row mb-4">
                <div class="col-12">
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="ri-building-line"></i> Data Ruangan & Pembayaran
                    </h6>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Jenis Ruangan</label>
                    <select name="jenis_ruangan" id="jenis_ruangan" class="form-select @error('jenis_ruangan') is-invalid @enderror">
                        <option value="">Pilih Jenis Ruangan</option>
                        <option value="rawat_jalan" {{ old('jenis_ruangan', $pasien->jenis_ruangan ?? '') == 'rawat_jalan' ? 'selected' : '' }}>Rawat Jalan</option>
                        <option value="rawat_inap" {{ old('jenis_ruangan', $pasien->jenis_ruangan ?? '') == 'rawat_inap' ? 'selected' : '' }}>Rawat Inap</option>
                        <option value="igd" {{ old('jenis_ruangan', $pasien->jenis_ruangan ?? '') == 'igd' ? 'selected' : '' }}>IGD</option>
                        <option value="penunjang" {{ old('jenis_ruangan', $pasien->jenis_ruangan ?? '') == 'penunjang' ? 'selected' : '' }}>Penunjang</option>
                    </select>
                    @error('jenis_ruangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Filter ruangan berdasarkan jenis</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Ruangan</label>
                    <select name="ruangan_id" id="ruangan_id" class="form-select @error('ruangan_id') is-invalid @enderror">
                        <option value="">Pilih Ruangan</option>
                        @foreach($ruangans as $ruangan)
                            <option value="{{ $ruangan->id }}" 
                                    data-jenis="{{ $ruangan->jenis }}"
                                    data-kapasitas="{{ $ruangan->kapasitas }}"
                                    data-kode="{{ $ruangan->kode_ruangan }}"
                                    {{ old('ruangan_id', $pasien->ruangan_id ?? '') == $ruangan->id ? 'selected' : '' }}>
                                [{{ $ruangan->kode_ruangan }}] {{ $ruangan->nama_ruangan }} 
                                @if($ruangan->jenis == 'rawat_inap')
                                    (Kapasitas: {{ $ruangan->kapasitas }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('ruangan_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="ruangan-info" class="mt-2" style="display: none;">
                        <div class="alert alert-info py-2 mb-0">
                            <small>
                                <i class="ri-information-line"></i>
                                <strong>Info Ruangan:</strong>
                                <span id="info-kode"></span> - 
                                <span id="info-jenis"></span>
                                <span id="info-kapasitas"></span>
                            </small>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Jenis Pembayaran <span class="text-danger">*</span></label>
                    <select name="jenis_pembayaran" id="jenis_pembayaran" class="form-select @error('jenis_pembayaran') is-invalid @enderror" required>
                        <option value="">Pilih</option>
                        <option value="BPJS" {{ old('jenis_pembayaran', $pasien->jenis_pembayaran ?? '') == 'BPJS' ? 'selected' : '' }}>BPJS</option>
                        <option value="Umum" {{ old('jenis_pembayaran', $pasien->jenis_pembayaran ?? '') == 'Umum' ? 'selected' : '' }}>Umum</option>
                        <option value="Asuransi" {{ old('jenis_pembayaran', $pasien->jenis_pembayaran ?? '') == 'Asuransi' ? 'selected' : '' }}>Asuransi</option>
                    </select>
                    @error('jenis_pembayaran')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3" id="field_bpjs" style="display: none;">
                    <label class="form-label">No. BPJS</label>
                    <input type="text" name="no_bpjs" class="form-control @error('no_bpjs') is-invalid @enderror"
                           value="{{ old('no_bpjs', $pasien->no_bpjs ?? '') }}" 
                           placeholder="13 digit nomor BPJS">
                    @error('no_bpjs')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3" id="field_asuransi" style="display: none;">
                    <label class="form-label">Asuransi</label>
                    <select name="asuransi_id" class="form-select @error('asuransi_id') is-invalid @enderror">
                        <option value="">Pilih Asuransi</option>
                        @foreach($asuransis as $asuransi)
                            <option value="{{ $asuransi->id }}" {{ old('asuransi_id', $pasien->asuransi_id ?? '') == $asuransi->id ? 'selected' : '' }}>
                                {{ $asuransi->nama_asuransi }}
                            </option>
                        @endforeach
                    </select>
                    @error('asuransi_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3" id="field_polis" style="display: none;">
                    <label class="form-label">No. Polis Asuransi</label>
                    <input type="text" name="no_polis_asuransi" class="form-control @error('no_polis_asuransi') is-invalid @enderror"
                           value="{{ old('no_polis_asuransi', $pasien->no_polis_asuransi ?? '') }}" 
                           placeholder="Nomor polis asuransi">
                    @error('no_polis_asuransi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Data Lainnya -->
            <div class="row mb-4">
                <div class="col-12">
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="ri-settings-line"></i> Data Lainnya
                    </h6>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Foto Pasien</label>
                    <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/*">
                    @error('foto')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if(isset($pasien) && $pasien->foto)
                        <div class="mt-2">
                            <img src="{{ Storage::url($pasien->foto) }}" alt="Foto Pasien" class="img-thumbnail" style="max-width: 150px;">
                        </div>
                    @endif
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Status Aktif <span class="text-danger">*</span></label>
                    <select name="status_aktif" class="form-select @error('status_aktif') is-invalid @enderror" required>
                        <option value="Aktif" {{ old('status_aktif', $pasien->status_aktif ?? 'Aktif') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Nonaktif" {{ old('status_aktif', $pasien->status_aktif ?? '') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status_aktif')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Generate No. RM
    $('#btn-generate-rm').click(function() {
        $.ajax({
            url: '{{ route("pasiens.generate-no-rm") }}',
            method: 'GET',
            success: function(response) {
                $('#no_rm').val(response.no_rm);
            },
            error: function() {
                alert('Gagal generate nomor RM');
            }
        });
    });

    // Toggle pembayaran fields
    function togglePembayaranFields() {
        const jenisPembayaran = $('#jenis_pembayaran').val();
        
        $('#field_bpjs, #field_asuransi, #field_polis').hide();
        
        if (jenisPembayaran === 'BPJS') {
            $('#field_bpjs').show();
        } else if (jenisPembayaran === 'Asuransi') {
            $('#field_asuransi, #field_polis').show();
        }
    }

    $('#jenis_pembayaran').change(togglePembayaranFields);
    togglePembayaranFields(); // Initial call

    // Filter ruangan berdasarkan jenis
    function filterRuangan() {
        const jenisRuangan = $('#jenis_ruangan').val();
        const $ruanganSelect = $('#ruangan_id');
        
        // Show all options first
        $ruanganSelect.find('option').each(function() {
            if ($(this).val() !== '') {
                if (jenisRuangan === '') {
                    $(this).show();
                } else {
                    const optionJenis = $(this).data('jenis');
                    if (optionJenis === jenisRuangan) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                }
            }
        });

        // Reset selection if current selection is hidden
        const currentOption = $ruanganSelect.find('option:selected');
        if (currentOption.val() !== '' && !currentOption.is(':visible')) {
            $ruanganSelect.val('');
            $('#ruangan-info').hide();
        }
    }

    $('#jenis_ruangan').change(filterRuangan);

    // Show ruangan info
    function showRuanganInfo() {
        const $selected = $('#ruangan_id option:selected');
        
        if ($selected.val() !== '') {
            const kode = $selected.data('kode');
            const jenis = $selected.data('jenis');
            const kapasitas = $selected.data('kapasitas');
            
            // Map jenis to label
            const jenisLabels = {
                'rawat_jalan': 'Rawat Jalan',
                'rawat_inap': 'Rawat Inap',
                'igd': 'IGD',
                'penunjang': 'Penunjang'
            };
            
            $('#info-kode').text(kode);
            $('#info-jenis').text(jenisLabels[jenis] || jenis);
            
            if (jenis === 'rawat_inap') {
                $('#info-kapasitas').text(' | Kapasitas: ' + kapasitas + ' orang');
            } else {
                $('#info-kapasitas').text('');
            }
            
            $('#ruangan-info').slideDown();
        } else {
            $('#ruangan-info').slideUp();
        }
    }

    $('#ruangan_id').change(showRuanganInfo);
    
    // Initial calls
    filterRuangan();
    showRuanganInfo();
});
</script>
@endpush