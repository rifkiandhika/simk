<h5 class="mb-3">Informasi Signa (Aturan Pakai)</h5>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="kode_signa" class="form-label">
                Kode Signa <span class="text-danger">*</span>
            </label>
            <input type="text" 
                   class="form-control text-uppercase @error('kode_signa') is-invalid @enderror" 
                   id="kode_signa" 
                   name="kode_signa" 
                   value="{{ old('kode_signa', $signa->kode_signa ?? '') }}" 
                   placeholder="e.g. 3DD1, 2DD2, AC, PC" 
                   maxlength="50"
                   required>
            @error('kode_signa')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Kode akan otomatis diubah menjadi huruf kapital</small>
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="kepanjangan" class="form-label">Kepanjangan</label>
            <input type="text" 
                   class="form-control @error('kepanjangan') is-invalid @enderror" 
                   id="kepanjangan" 
                   name="kepanjangan" 
                   value="{{ old('kepanjangan', $signa->kepanjangan ?? '') }}" 
                   placeholder="e.g. Ter in die, Ante Cibum" 
                   maxlength="200">
            @error('kepanjangan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Kepanjangan bahasa Latin (opsional)</small>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="mb-3">
            <label for="deskripsi" class="form-label">
                Deskripsi <span class="text-danger">*</span>
            </label>
            <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                      id="deskripsi" 
                      name="deskripsi" 
                      rows="4" 
                      placeholder="Masukkan penjelasan aturan pakai obat..."
                      maxlength="500"
                      required>{{ old('deskripsi', $signa->deskripsi ?? '') }}</textarea>
            @error('deskripsi')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="d-flex justify-content-between mt-1">
                <small class="text-muted">Penjelasan cara pakai obat dalam Bahasa Indonesia</small>
                <small class="text-muted"><span id="charCount">0</span>/500 karakter</small>
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
                <li>Field yang ditandai <span class="text-danger">*</span> wajib diisi</li>
                <li><strong>Kode Signa:</strong> Singkatan/kode unik untuk aturan pakai (akan otomatis kapital)</li>
                <li><strong>Kepanjangan:</strong> Kepanjangan kode dalam bahasa Latin (opsional)</li>
                <li><strong>Deskripsi:</strong> Penjelasan lengkap aturan pakai dalam Bahasa Indonesia</li>
                <li>Kode signa harus unik dan tidak boleh sama dengan yang sudah ada</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card bg-light border-0">
            <div class="card-body">
                <h6 class="card-title"><i class="ri-lightbulb-line text-warning"></i> Contoh Signa yang Umum</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead>
                            <tr>
                                <th width="120">Kode</th>
                                <th width="200">Kepanjangan</th>
                                <th>Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <tr>
                                <td><code>3DD1</code></td>
                                <td>Ter in die</td>
                                <td>3 kali sehari 1 tablet/kapsul</td>
                            </tr>
                            <tr>
                                <td><code>2DD2</code></td>
                                <td>Bis die</td>
                                <td>2 kali sehari 2 tablet/kapsul</td>
                            </tr>
                            <tr>
                                <td><code>AC</code></td>
                                <td>Ante Cibum</td>
                                <td>Sebelum makan</td>
                            </tr>
                            <tr>
                                <td><code>PC</code></td>
                                <td>Post Cibum</td>
                                <td>Sesudah makan</td>
                            </tr>
                            <tr>
                                <td><code>DC</code></td>
                                <td>Durante Cibum</td>
                                <td>Saat makan</td>
                            </tr>
                            <tr>
                                <td><code>PRN</code></td>
                                <td>Pro Re Nata</td>
                                <td>Bila perlu/bila diperlukan</td>
                            </tr>
                            <tr>
                                <td><code>HS</code></td>
                                <td>Hora Somni</td>
                                <td>Malam sebelum tidur</td>
                            </tr>
                            <tr>
                                <td><code>OM</code></td>
                                <td>Omni Mane</td>
                                <td>Setiap pagi</td>
                            </tr>
                            <tr>
                                <td><code>ON</code></td>
                                <td>Omni Nocte</td>
                                <td>Setiap malam</td>
                            </tr>
                            <tr>
                                <td><code>Q4H</code></td>
                                <td>Quaque 4 Hora</td>
                                <td>Setiap 4 jam</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto uppercase kode signa
    document.getElementById('kode_signa').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });

    // Character counter for deskripsi
    const deskripsiTextarea = document.getElementById('deskripsi');
    const charCountSpan = document.getElementById('charCount');
    
    function updateCharCount() {
        const count = deskripsiTextarea.value.length;
        charCountSpan.textContent = count;
        
        if (count > 450) {
            charCountSpan.classList.add('text-warning');
        } else {
            charCountSpan.classList.remove('text-warning');
        }
        
        if (count >= 500) {
            charCountSpan.classList.remove('text-warning');
            charCountSpan.classList.add('text-danger');
        } else {
            charCountSpan.classList.remove('text-danger');
        }
    }
    
    deskripsiTextarea.addEventListener('input', updateCharCount);
    
    // Initialize on page load
    updateCharCount();

    // Quick fill buttons (optional enhancement)
    function quickFill(kode, kepanjangan, deskripsi) {
        document.getElementById('kode_signa').value = kode;
        document.getElementById('kepanjangan').value = kepanjangan;
        document.getElementById('deskripsi').value = deskripsi;
        updateCharCount();
    }

    // Add click handlers to example table rows
    document.querySelectorAll('.table-responsive tbody tr').forEach(row => {
        row.style.cursor = 'pointer';
        row.title = 'Klik untuk mengisi form dengan contoh ini';
        
        row.addEventListener('click', function() {
            const cells = this.querySelectorAll('td');
            const kode = cells[0].textContent.trim();
            const kepanjangan = cells[1].textContent.trim();
            const deskripsi = cells[2].textContent.trim();
            
            if (confirm('Isi form dengan contoh ini?')) {
                quickFill(kode, kepanjangan, deskripsi);
            }
        });
    });
</script>
@endpush