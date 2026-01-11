<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">{{ isset($tagihan) ? 'Edit' : 'Buat' }} Tagihan</h5>
    </div>
    <div class="card-body">
        <!-- Informasi Pasien -->
        <div class="row mb-4">
            <div class="col-12">
                <h6 class="border-bottom pb-2 mb-3">
                    <i class="ri-user-line"></i> Informasi Pasien
                </h6>
            </div>

            @if(isset($tagihan) || isset($registrasi))
                <!-- Mode Edit atau dari Registrasi -->
                <input type="hidden" name="id_pasien" value="{{ $tagihan->id_pasien ?? $registrasi->id_pasien }}">
                <input type="hidden" name="id_registrasi" value="{{ $tagihan->id_registrasi ?? $registrasi->id_registrasi }}">

                <div class="col-md-4 mb-3">
                    <label class="form-label">No. Registrasi</label>
                    <input type="text" class="form-control" 
                           value="{{ $tagihan->registrasi->no_registrasi ?? $registrasi->no_registrasi }}" readonly>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">No. Rekam Medis</label>
                    <input type="text" class="form-control" 
                           value="{{ $tagihan->pasien->no_rm ?? $registrasi->pasien->no_rm }}" readonly>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Nama Pasien</label>
                    <input type="text" class="form-control" 
                           value="{{ $tagihan->pasien->nama ?? $registrasi->pasien->nama }}" readonly>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="text" class="form-control" 
                           value="{{ isset($tagihan) ? $tagihan->pasien->tanggal_lahir->format('d/m/Y') : $registrasi->pasien->tanggal_lahir->format('d/m/Y') }}" readonly>
                </div>
            @else
                <!-- Mode Create Manual -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Pilih Pasien <span class="text-danger">*</span></label>
                    <select name="id_pasien" class="form-select select2 @error('id_pasien') is-invalid @enderror" required>
                        <option value="">-- Pilih Pasien --</option>
                        @foreach($pasiens as $pasien)
                            <option value="{{ $pasien->id_pasien }}" {{ old('id_pasien') == $pasien->id_pasien ? 'selected' : '' }}>
                                {{ $pasien->no_rm }} - {{ $pasien->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_pasien')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">ID Registrasi <span class="text-danger">*</span></label>
                    <input type="text" name="id_registrasi" 
                           class="form-control @error('id_registrasi') is-invalid @enderror"
                           value="{{ old('id_registrasi') }}" 
                           placeholder="Masukkan ID registrasi pasien" required>
                    @error('id_registrasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endif
        </div>

        <!-- Detail Tagihan -->
        <div class="row mb-4">
            <div class="col-12">
                <h6 class="border-bottom pb-2 mb-3">
                    <i class="ri-file-list-line"></i> Detail Tagihan
                </h6>
            </div>

            @if(isset($tagihan))
                <div class="col-md-4 mb-3">
                    <label class="form-label">No. Tagihan</label>
                    <input type="text" class="form-control" value="{{ $tagihan->no_tagihan }}" readonly>
                </div>
            @endif

            <div class="col-md-4 mb-3">
                <label class="form-label">Tanggal Tagihan <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_tagihan" 
                       class="form-control @error('tanggal_tagihan') is-invalid @enderror"
                       value="{{ old('tanggal_tagihan', isset($tagihan) ? $tagihan->tanggal_tagihan->format('Y-m-d') : date('Y-m-d')) }}" required>
                @error('tanggal_tagihan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Jenis Tagihan <span class="text-danger">*</span></label>
                <select name="jenis_tagihan" class="form-select @error('jenis_tagihan') is-invalid @enderror" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="IGD" {{ old('jenis_tagihan', $tagihan->jenis_tagihan ?? '') == 'IGD' ? 'selected' : '' }}>IGD</option>
                    <option value="RAWAT_JALAN" {{ old('jenis_tagihan', $tagihan->jenis_tagihan ?? '') == 'RAWAT_JALAN' ? 'selected' : '' }}>Rawat Jalan</option>
                    <option value="RAWAT_INAP" {{ old('jenis_tagihan', $tagihan->jenis_tagihan ?? '') == 'RAWAT_INAP' ? 'selected' : '' }}>Rawat Inap</option>
                </select>
                @error('jenis_tagihan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Status Klaim</label>
                <select name="status_klaim" class="form-select @error('status_klaim') is-invalid @enderror">
                    <option value="NON_KLAIM" {{ old('status_klaim', $tagihan->status_klaim ?? 'NON_KLAIM') == 'NON_KLAIM' ? 'selected' : '' }}>Non Klaim</option>
                    <option value="PENDING" {{ old('status_klaim', $tagihan->status_klaim ?? '') == 'PENDING' ? 'selected' : '' }}>Pending</option>
                    <option value="DISETUJUI" {{ old('status_klaim', $tagihan->status_klaim ?? '') == 'DISETUJUI' ? 'selected' : '' }}>Disetujui</option>
                    <option value="DITOLAK" {{ old('status_klaim', $tagihan->status_klaim ?? '') == 'DITOLAK' ? 'selected' : '' }}>Ditolak</option>
                </select>
                @error('status_klaim')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Catatan</label>
                <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" 
                          rows="3" 
                          placeholder="Catatan tambahan (opsional)">{{ old('catatan', $tagihan->catatan ?? '') }}</textarea>
                @error('catatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Item Tagihan -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <h6 class="mb-0">
                        <i class="ri-shopping-cart-line"></i> Item Tagihan
                    </h6>
                    <button type="button" class="btn btn-sm btn-success" onclick="addItem()">
                        <i class="ri-add-line"></i> Tambah Item
                    </button>
                </div>
            </div>

            <div class="col-12">
                <div id="itemsContainer">
                    @if(isset($tagihan))
                        @foreach($tagihan->items as $index => $item)
                            <div class="item-row card mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                            <select name="items[{{ $index }}][kategori]" class="form-select" required>
                                                <option value="">-- Pilih --</option>
                                                <option value="APOTIK" {{ $item->kategori == 'APOTIK' ? 'selected' : '' }}>Apotik</option>
                                                <option value="TINDAKAN" {{ $item->kategori == 'TINDAKAN' ? 'selected' : '' }}>Tindakan</option>
                                                <option value="LAB" {{ $item->kategori == 'LAB' ? 'selected' : '' }}>Laboratorium</option>
                                                <option value="RADIOLOGI" {{ $item->kategori == 'RADIOLOGI' ? 'selected' : '' }}>Radiologi</option>
                                                <option value="KAMAR" {{ $item->kategori == 'KAMAR' ? 'selected' : '' }}>Kamar</option>
                                                <option value="ADMIN" {{ $item->kategori == 'ADMIN' ? 'selected' : '' }}>Administrasi</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                                            <input type="text" name="items[{{ $index }}][deskripsi]" 
                                                   class="form-control" value="{{ $item->deskripsi }}" 
                                                   placeholder="Nama item/layanan" required>
                                        </div>
                                        <div class="col-md-1 mb-3">
                                            <label class="form-label">Qty <span class="text-danger">*</span></label>
                                            <input type="number" name="items[{{ $index }}][qty]" 
                                                   class="form-control item-qty" min="1" 
                                                   value="{{ $item->qty }}" required>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label class="form-label">Harga <span class="text-danger">*</span></label>
                                            <input type="text" name="items[{{ $index }}][harga]" 
                                                   class="form-control item-harga" 
                                                   value="{{ number_format($item->harga, 0, ',', '.') }}" 
                                                   placeholder="0" required>
                                        </div>
                                        <div class="col-md-1 mb-3">
                                            <label class="form-label">Subtotal</label>
                                            <input type="text" class="form-control item-subtotal" 
                                                   value="{{ number_format($item->subtotal, 0, ',', '.') }}" readonly>
                                        </div>
                                        <div class="col-md-1 mb-3">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="button" class="btn btn-danger d-block w-100" onclick="removeItem(this)">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-check">
                                                <input type="checkbox" name="items[{{ $index }}][ditanggung]" 
                                                       class="form-check-input" id="ditanggung_{{ $index }}" 
                                                       value="1" {{ $item->ditanggung ? 'checked' : '' }}>
                                                <label class="form-check-label" for="ditanggung_{{ $index }}">
                                                    Ditanggung (BPJS/Asuransi)
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                @error('items')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Summary -->
        <div class="row">
            <div class="col-12">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Total Tagihan</h6>
                                <small id="itemCount">{{ isset($tagihan) ? $tagihan->items->count() : 0 }} item</small>
                            </div>
                            <h3 class="mb-0" id="totalTagihan">
                                Rp {{ isset($tagihan) ? number_format($tagihan->total_tagihan, 0, ',', '.') : '0' }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Item Template -->
<template id="itemTemplate">
    <div class="item-row card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select name="items[INDEX][kategori]" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <option value="APOTIK">Apotik</option>
                        <option value="TINDAKAN">Tindakan</option>
                        <option value="LAB">Laboratorium</option>
                        <option value="RADIOLOGI">Radiologi</option>
                        <option value="KAMAR">Kamar</option>
                        <option value="ADMIN">Administrasi</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                    <input type="text" name="items[INDEX][deskripsi]" class="form-control" 
                           placeholder="Nama item/layanan" required>
                </div>
                <div class="col-md-1 mb-3">
                    <label class="form-label">Qty <span class="text-danger">*</span></label>
                    <input type="number" name="items[INDEX][qty]" class="form-control item-qty" 
                           min="1" value="1" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Harga <span class="text-danger">*</span></label>
                    <input type="text" name="items[INDEX][harga]" class="form-control item-harga" 
                           placeholder="0" required>
                </div>
                <div class="col-md-1 mb-3">
                    <label class="form-label">Subtotal</label>
                    <input type="text" class="form-control item-subtotal" readonly>
                </div>
                <div class="col-md-1 mb-3">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-danger d-block w-100" onclick="removeItem(this)">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" name="items[INDEX][ditanggung]" 
                               class="form-check-input" id="ditanggung_INDEX" value="1">
                        <label class="form-check-label" for="ditanggung_INDEX">
                            Ditanggung (BPJS/Asuransi)
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>