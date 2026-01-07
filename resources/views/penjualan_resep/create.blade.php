@extends('layouts.app')

@section('title', 'Transaksi Penjualan Resep')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('penjualan_resep.index') }}">Penjualan Resep</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">Resep Baru</li>
@endsection

@section('content')
<div class="app-body">
    <div class="row">
        <div class="col-xl-8">
            {{-- Form Data Resep --}}
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="ri-file-text-line me-2"></i>Data Resep</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Resep <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="no_resep" placeholder="Masukkan no. resep" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Resep <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_resep" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. RM Pasien <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="no_rm_pasien" placeholder="Masukkan No. RM" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Pasien <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_pasien" placeholder="Masukkan nama pasien" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Dokter <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_dokter" placeholder="Masukkan nama dokter" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. HP</label>
                            <input type="text" class="form-control" id="no_hp" placeholder="Masukkan no. HP">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" rows="2" placeholder="Masukkan alamat"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Diagnosa</label>
                            <textarea class="form-control" id="diagnosa" rows="2" placeholder="Masukkan diagnosa"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Obat --}}
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="ri-medicine-bottle-line me-2"></i>Pilih Obat dari Resep</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cari Obat</label>
                            <input type="text" class="form-control" id="searchObat" placeholder="Ketik nama obat..." autocomplete="off">
                            <div id="searchResults" class="list-group position-absolute w-100" style="z-index: 1000; display: none; max-height: 300px; overflow-y: auto;"></div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Jumlah</label>
                            <input type="number" class="form-control" id="jumlah" placeholder="0" min="1">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Signa/Aturan Pakai</label>
                            <select class="form-select" id="aturan_pakai_select">
                                <option value="">Pilih...</option>
                                <option value="3 x 1 sehari">3 x 1 sehari</option>
                                <option value="2 x 1 sehari">2 x 1 sehari</option>
                                <option value="1 x 1 sehari">1 x 1 sehari</option>
                                <option value="3 x 1 sehari sesudah makan">3 x 1 sehari sesudah makan</option>
                                <option value="2 x 1 sehari sesudah makan">2 x 1 sehari sesudah makan</option>
                                <option value="1 x 1 sehari sesudah makan">1 x 1 sehari sesudah makan</option>
                                <option value="3 x 1 sehari sebelum makan">3 x 1 sehari sebelum makan</option>
                                <option value="2 x 1 sehari sebelum makan">2 x 1 sehari sebelum makan</option>
                                <option value="custom">Custom...</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3" id="customAturanContainer" style="display: none;">
                            <label class="form-label">Aturan Pakai Custom</label>
                            <input type="text" class="form-control" id="aturan_pakai_custom" placeholder="Masukkan aturan pakai...">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" id="btnTambahObat">
                        <i class="ri-add-line me-1"></i>Tambah ke Resep
                    </button>
                </div>
            </div>

            {{-- Daftar Obat Resep --}}
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="ri-file-list-3-line me-2"></i>Daftar Obat Resep</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th width="40">No</th>
                                    <th>Nama Obat</th>
                                    <th>Batch</th>
                                    <th width="100">Jumlah</th>
                                    <th width="200">Aturan Pakai</th>
                                    <th width="130">Harga</th>
                                    <th width="130">Subtotal</th>
                                    <th width="60" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="resepTable">
                                <tr id="emptyRow">
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="ri-file-list-line ri-2x mb-2"></i>
                                        <p class="mb-0">Belum ada obat dalam resep</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar Pembayaran --}}
        <div class="col-xl-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-warning text-white">
                    <h6 class="mb-0"><i class="ri-money-dollar-circle-line me-2"></i>Pembayaran</h6>
                </div>
                <div class="card-body">
                    {{-- Summary --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <strong id="displaySubtotal">Rp 0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Diskon:</span>
                            <div class="input-group input-group-sm" style="width: 150px;">
                                <input type="number" class="form-control" id="diskon" value="0" min="0">
                                <span class="input-group-text">Rp</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Pajak:</span>
                            <div class="input-group input-group-sm" style="width: 150px;">
                                <input type="number" class="form-control" id="pajak" value="0" min="0">
                                <span class="input-group-text">Rp</span>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <h4 class="text-primary mb-0" id="displayTotal">Rp 0</h4>
                        </div>
                    </div>

                    {{-- Form Pembayaran --}}
                    <div class="mb-3">
                        <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select class="form-select" id="metode_pembayaran">
                            <option value="tunai">Tunai</option>
                            <option value="transfer">Transfer</option>
                            <option value="debit">Debit</option>
                            <option value="kredit">Kredit</option>
                            <option value="asuransi">Asuransi</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Bayar <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="bayar" placeholder="0" min="0">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kembalian</label>
                        <input type="text" class="form-control" id="kembalian" readonly value="Rp 0">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" rows="2" placeholder="Keterangan tambahan..."></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-success btn-lg" id="btnSimpan">
                            <i class="ri-save-line me-2"></i>Simpan Resep
                        </button>
                        <a href="{{ route('penjualan_resep.index') }}" class="btn btn-outline-secondary">
                            <i class="ri-arrow-left-line me-2"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .list-group-item:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
    .sticky-top {
        position: sticky;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let resepItems = [];
let selectedObat = null;

$(document).ready(function() {
    // Search obat with autocomplete
    let searchTimeout;
    $('#searchObat').on('keyup', function() {
        clearTimeout(searchTimeout);
        const query = $(this).val();
        
        if (query.length < 2) {
            $('#searchResults').hide();
            return;
        }
        
        searchTimeout = setTimeout(() => {
            searchObat(query);
        }, 300);
    });

    // Aturan pakai select change
    $('#aturan_pakai_select').on('change', function() {
        if ($(this).val() === 'custom') {
            $('#customAturanContainer').show();
            $('#aturan_pakai_custom').focus();
        } else {
            $('#customAturanContainer').hide();
        }
    });

    // Tambah obat ke resep
    $('#btnTambahObat').on('click', function() {
        tambahKeResep();
    });

    // Calculate on input change
    $('#diskon, #pajak, #bayar').on('input', function() {
        hitungTotal();
    });

    // Simpan transaksi
    $('#btnSimpan').on('click', function() {
        simpanTransaksi();
    });

    // Hide search results when click outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#searchObat, #searchResults').length) {
            $('#searchResults').hide();
        }
    });
});

// Search obat
function searchObat(query) {
    $.ajax({
        url: '{{ route("penjualan_resep.search.obat") }}',
        method: 'GET',
        data: { q: query },
        success: function(response) {
            if (response.success && response.data.length > 0) {
                let html = '';
                response.data.forEach(obat => {
                    html += `
                        <a href="#" class="list-group-item list-group-item-action" onclick="pilihObat(${JSON.stringify(obat).replace(/"/g, '&quot;')}); return false;">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>${obat.nama}</strong>
                                    <br><small class="text-muted">${obat.merk} | Batch: ${obat.no_batch}</small>
                                    <br><small class="text-info">Stock: ${obat.stock} ${obat.satuan}</small>
                                </div>
                                <div class="text-end">
                                    <strong class="text-primary">Rp ${Number(obat.harga).toLocaleString('id-ID')}</strong>
                                    <br><small class="text-muted">Exp: ${obat.exp_date}</small>
                                </div>
                            </div>
                        </a>
                    `;
                });
                $('#searchResults').html(html).show();
            } else {
                $('#searchResults').html('<div class="list-group-item text-muted">Obat tidak ditemukan</div>').show();
            }
        },
        error: function() {
            $('#searchResults').hide();
        }
    });
}

// Pilih obat dari hasil search
function pilihObat(obat) {
    selectedObat = obat;
    $('#searchObat').val(obat.nama);
    $('#searchResults').hide();
    $('#jumlah').focus();
}

// Tambah ke resep
function tambahKeResep() {
    if (!selectedObat) {
        Swal.fire('Perhatian!', 'Silakan pilih obat terlebih dahulu', 'warning');
        return;
    }

    const jumlah = parseInt($('#jumlah').val());
    
    if (!jumlah || jumlah <= 0) {
        Swal.fire('Perhatian!', 'Masukkan jumlah yang valid', 'warning');
        return;
    }

    if (jumlah > selectedObat.stock) {
        Swal.fire('Perhatian!', `Stock tidak mencukupi! Stock tersedia: ${selectedObat.stock}`, 'warning');
        return;
    }

    // Get aturan pakai
    let aturanPakai = $('#aturan_pakai_select').val();
    if (aturanPakai === 'custom') {
        aturanPakai = $('#aturan_pakai_custom').val();
    }

    if (!aturanPakai) {
        Swal.fire('Perhatian!', 'Masukkan aturan pakai obat', 'warning');
        return;
    }

    // Check if already in resep
    const existingIndex = resepItems.findIndex(item => item.detail_stock_apotik_id === selectedObat.id);
    
    if (existingIndex > -1) {
        // Update existing item
        const newJumlah = resepItems[existingIndex].jumlah + jumlah;
        if (newJumlah > selectedObat.stock) {
            Swal.fire('Perhatian!', `Total jumlah melebihi stock! Stock tersedia: ${selectedObat.stock}`, 'warning');
            return;
        }
        resepItems[existingIndex].jumlah = newJumlah;
        resepItems[existingIndex].subtotal = newJumlah * resepItems[existingIndex].harga_satuan;
        resepItems[existingIndex].aturan_pakai = aturanPakai;
    } else {
        // Add new item
        resepItems.push({
            detail_stock_apotik_id: selectedObat.id,
            nama_obat: selectedObat.nama,
            no_batch: selectedObat.no_batch,
            jumlah: jumlah,
            satuan: selectedObat.satuan,
            aturan_pakai: aturanPakai,
            harga_satuan: selectedObat.harga,
            diskon_item: 0,
            subtotal: jumlah * selectedObat.harga,
            stock_available: selectedObat.stock
        });
    }

    // Reset form
    selectedObat = null;
    $('#searchObat').val('');
    $('#jumlah').val('');
    $('#aturan_pakai_select').val('');
    $('#aturan_pakai_custom').val('');
    $('#customAturanContainer').hide();
    
    updateResepTable();
    hitungTotal();
}

// Update resep table
function updateResepTable() {
    if (resepItems.length === 0) {
        $('#resepTable').html(`
            <tr id="emptyRow">
                <td colspan="8" class="text-center text-muted py-4">
                    <i class="ri-file-list-line ri-2x mb-2"></i>
                    <p class="mb-0">Belum ada obat dalam resep</p>
                </td>
            </tr>
        `);
        return;
    }

    let html = '';
    resepItems.forEach((item, index) => {
        html += `
            <tr>
                <td class="text-center">${index + 1}</td>
                <td>
                    <strong>${item.nama_obat}</strong>
                    <br><small class="text-muted">Stock: ${item.stock_available}</small>
                </td>
                <td><small>${item.no_batch}</small></td>
                <td>
                    <input type="number" class="form-control form-control-sm" value="${item.jumlah}" 
                           min="1" max="${item.stock_available}" onchange="updateJumlah(${index}, this.value)">
                </td>
                <td><small>${item.aturan_pakai}</small></td>
                <td>Rp ${Number(item.harga_satuan).toLocaleString('id-ID')}</td>
                <td><strong>Rp ${Number(item.subtotal).toLocaleString('id-ID')}</strong></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger" onclick="hapusItem(${index})">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    $('#resepTable').html(html);
}

// Update jumlah item
function updateJumlah(index, newJumlah) {
    newJumlah = parseInt(newJumlah);
    
    if (newJumlah <= 0 || newJumlah > resepItems[index].stock_available) {
        Swal.fire('Perhatian!', 'Jumlah tidak valid', 'warning');
        updateResepTable();
        return;
    }

    resepItems[index].jumlah = newJumlah;
    resepItems[index].subtotal = newJumlah * resepItems[index].harga_satuan;
    
    updateResepTable();
    hitungTotal();
}

// Hapus item dari resep
function hapusItem(index) {
    resepItems.splice(index, 1);
    updateResepTable();
    hitungTotal();
}

// Hitung total
function hitungTotal() {
    const subtotal = resepItems.reduce((sum, item) => sum + item.subtotal, 0);
    const diskon = parseFloat($('#diskon').val()) || 0;
    const pajak = parseFloat($('#pajak').val()) || 0;
    const total = subtotal - diskon + pajak;
    const bayar = parseFloat($('#bayar').val()) || 0;
    const kembalian = bayar - total;

    $('#displaySubtotal').text('Rp ' + subtotal.toLocaleString('id-ID'));
    $('#displayTotal').text('Rp ' + total.toLocaleString('id-ID'));
    $('#kembalian').val('Rp ' + (kembalian >= 0 ? kembalian : 0).toLocaleString('id-ID'));
}

// Simpan transaksi
function simpanTransaksi() {
    // Validasi
    const requiredFields = [
        { id: 'no_resep', label: 'No. Resep' },
        { id: 'tanggal_resep', label: 'Tanggal Resep' },
        { id: 'no_rm_pasien', label: 'No. RM Pasien' },
        { id: 'nama_pasien', label: 'Nama Pasien' },
        { id: 'nama_dokter', label: 'Nama Dokter' }
    ];

    for (let field of requiredFields) {
        if (!$(`#${field.id}`).val()) {
            Swal.fire('Perhatian!', `${field.label} harus diisi`, 'warning');
            $(`#${field.id}`).focus();
            return;
        }
    }

    if (resepItems.length === 0) {
        Swal.fire('Perhatian!', 'Belum ada obat dalam resep', 'warning');
        return;
    }

    const bayar = parseFloat($('#bayar').val()) || 0;
    const subtotal = resepItems.reduce((sum, item) => sum + item.subtotal, 0);
    const diskon = parseFloat($('#diskon').val()) || 0;
    const pajak = parseFloat($('#pajak').val()) || 0;
    const total = subtotal - diskon + pajak;

    if (bayar < total) {
        Swal.fire('Perhatian!', 'Jumlah bayar kurang dari total', 'warning');
        return;
    }

    const data = {
        no_resep: $('#no_resep').val(),
        tanggal_resep: $('#tanggal_resep').val(),
        no_rm_pasien: $('#no_rm_pasien').val(),
        nama_pasien: $('#nama_pasien').val(),
        nama_dokter: $('#nama_dokter').val(),
        diagnosa: $('#diagnosa').val(),
        alamat: $('#alamat').val(),
        no_hp: $('#no_hp').val(),
        subtotal: subtotal,
        diskon: diskon,
        pajak: pajak,
        total: total,
        bayar: bayar,
        kembalian: bayar - total,
        metode_pembayaran: $('#metode_pembayaran').val(),
        keterangan: $('#keterangan').val(),
        items: resepItems,
        _token: '{{ csrf_token() }}'
    };

    // Konfirmasi
    Swal.fire({
        title: 'Konfirmasi Resep',
        html: `
            <div class="text-start">
                <p><strong>No. Resep:</strong> ${data.no_resep}</p>
                <p><strong>Pasien:</strong> ${data.nama_pasien}</p>
                <p><strong>Dokter:</strong> ${data.nama_dokter}</p>
                <hr>
                <p><strong>Total:</strong> Rp ${total.toLocaleString('id-ID')}</p>
                <p><strong>Bayar:</strong> Rp ${bayar.toLocaleString('id-ID')}</p>
                <p><strong>Kembalian:</strong> Rp ${(bayar - total).toLocaleString('id-ID')}</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            prosesTransaksi(data);
        }
    });
}

// Proses transaksi
function prosesTransaksi(data) {
    $.ajax({
        url: '{{ route("penjualan_resep.store") }}',
        method: 'POST',
        data: data,
        beforeSend: function() {
            Swal.fire({
                title: 'Menyimpan...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: response.message,
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: 'Print Resep',
                    cancelButtonText: 'Kembali ke Daftar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.open(`/penjualan-resep/${response.data.id}/print`, '_blank');
                    }
                    window.location.href = '{{ route("penjualan_resep.index") }}';
                });
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            Swal.fire('Error!', response.message, 'error');
        }
    });
}
</script>
@endpush