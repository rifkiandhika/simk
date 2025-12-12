@if($errors->any())
    @foreach($errors->all() as $error)
        <div class="alert alert-danger">{{ $error }}</div>
    @endforeach
@endif

<div class="row mb-3">
    <div class="col-md-6">
        <label>Satuan <span class="text-danger">*</span></label>
        <input type="text" name="nama_satuan" class="form-control"
               value="{{ old('nama_satuan', $satuan->nama_satuan ?? '') }}"
               placeholder="e.g. ml" required>
    </div>

    <div class="col-md-6">
        <label>Deskripsi (Opsional)</label>
        <textarea name="deskripsi" class="form-control"
               placeholder="e.g. Miligram" cols="2" rows="1">{{ old('deskripsi', $satuan->deskripsi ?? '') }}</textarea>
    </div>
    <div class="col-md-12">
        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" id="status" class="form-select">
            <option value="Aktif" {{ old('status', $satuan->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="Nonaktif" {{ old('status', $satuan->status) == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
        </select>
    </div>
</div>
