@extends('layouts.app')

@section('title', 'Data Detail Obat')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('obatrs.index') }}">Obat</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('obatrs.edit', $obat->id_obat_rs) }}">Edit</a>
    </li>
    <li class="breadcrumb-item active text-primary" aria-current="page">Detail Obat</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-1">Detail Obat RS</h5>
                        <p class="text-muted mb-0">
                            <strong>Obat:</strong> <span id="nama-obat">{{ $obat->nama_obat }}</span>
                        </p>
                    </div>
                    <a href="{{ route('obatrs.edit', $obat->id_obat_rs) }}" class="btn btn-secondary btn-sm">
                        <i class="ri-arrow-left-line"></i> Kembali
                    </a>
                </div>

                {{-- Tab Navigation --}}
                <div class="card-body">
                    <ul class="nav nav-tabs mb-3" id="detailTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="data-obat-tab" data-bs-toggle="tab" 
                                    data-bs-target="#data-obat" type="button" role="tab">
                                <i class="ri-capsule-line"></i> Data Obat
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="kfa-tab" data-bs-toggle="tab" 
                                    data-bs-target="#kfa" type="button" role="tab">
                                <i class="ri-hospital-line"></i> Pemetaan KFA
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="medscape-tab" data-bs-toggle="tab" 
                                    data-bs-target="#medscape" type="button" role="tab">
                                <i class="ri-medicine-bottle-line"></i> Pemetaan Medscape
                            </button>
                        </li>
                    </ul>

                    {{-- Tab Content --}}
                    <div class="tab-content" id="detailTabsContent">
                        {{-- TAB 1: Data Obat --}}
                        <div class="tab-pane fade show active" id="data-obat" role="tabpanel">
                            <form id="form-data-obat">
                                @csrf
                                <input type="hidden" name="id_detail_obat_rs" value="{{ $detail->id_detail_obat_rs }}">
                                
                                <h6 class="mb-3">Informasi Detail Obat RS</h6>
                                
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Obat Master (Satu Sehat) <span class="text-danger">*</span></label>
                                        <select name="id_obat_master" id="id_obat_master" class="form-select" required disabled>
                                            <option value="{{ $detail->id_obat_master }}">
                                                {{ $detail->obatMaster->nama_obat }}
                                            </option>
                                        </select>
                                        <small class="text-muted">Data dari Obat Master</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Kode Obat RS <span class="text-danger">*</span></label>
                                        <input type="text" name="kode_obat_rs" class="form-control" 
                                               value="{{ $detail->kode_obat_rs }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Nama Obat RS <span class="text-danger">*</span></label>
                                        <input type="text" name="nama_obat_rs" class="form-control" 
                                               value="{{ $detail->nama_obat_rs }}" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Stok Minimal</label>
                                        <input type="number" name="stok_minimal" class="form-control" 
                                               value="{{ $detail->stok_minimal }}" min="0">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Stok Maksimal</label>
                                        <input type="number" name="stok_maksimal" class="form-control" 
                                               value="{{ $detail->stok_maksimal }}" min="0">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Status <span class="text-danger">*</span></label>
                                        <select name="status_aktif" class="form-select" required>
                                            @foreach(['Aktif', 'Nonaktif', 'Diskontinyu'] as $opt)
                                                <option value="{{ $opt }}" {{ $detail->status_aktif == $opt ? 'selected' : '' }}>
                                                    {{ $opt }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Lokasi Penyimpanan</label>
                                        <input type="text" name="lokasi_penyimpanan" class="form-control" 
                                               value="{{ $detail->lokasi_penyimpanan }}" 
                                               placeholder="Contoh: Lemari A1, Rak B2">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Catatan Khusus</label>
                                        <textarea name="catatan_khusus" class="form-control" rows="3" 
                                                  placeholder="Catatan internal RS...">{{ $detail->catatan_khusus }}</textarea>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <h6 class="mb-3">Informasi dari Obat Master</h6>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Nama Generik</label>
                                        <input type="text" class="form-control" value="{{ $detail->obatMaster->nama_generik ?? '-' }}" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Bentuk Sediaan</label>
                                        <input type="text" class="form-control" value="{{ $detail->obatMaster->bentuk_sediaan ?? '-' }}" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Kekuatan</label>
                                        <input type="text" class="form-control" value="{{ ($detail->obatMaster->kekuatan ?? '-') . ' ' . ($detail->obatMaster->satuan_kekuatan ?? '') }}" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Kemasan</label>
                                        <input type="text" class="form-control" value="{{ ($detail->obatMaster->kemasan ?? '-') . ' (' . ($detail->obatMaster->isi_kemasan ?? '-') . ')' }}" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Manufacturer</label>
                                        <input type="text" class="form-control" value="{{ $detail->obatMaster->manufacturer ?? '-' }}" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">NIE</label>
                                        <input type="text" class="form-control" value="{{ $detail->obatMaster->nie ?? '-' }}" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Kategori</label>
                                        <input type="text" class="form-control text-bg-info text-white" value="{{ $detail->obatMaster->kategori ?? '-' }}" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Golongan</label>
                                        <input type="text" class="form-control bg-warning text-dark" value="{{ $detail->obatMaster->golongan ?? '-' }}" readonly>
                                    </div>

                                    @if($detail->obatMaster->komposisi)
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">Komposisi</label>
                                            <textarea class="form-control" rows="2" readonly>{{ $detail->obatMaster->komposisi }}</textarea>
                                        </div>
                                    @endif

                                    @if($detail->obatMaster->indikasi)
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">Indikasi</label>
                                            <textarea class="form-control" rows="2" readonly>{{ $detail->obatMaster->indikasi }}</textarea>
                                        </div>
                                    @endif
                                </div>

                                <hr class="my-4">

                                <h6 class="mb-3">Harga Obat</h6>
                                
                                {{-- Harga Umum --}}
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Harga Umum (IDR)</label>
                                        <input type="text" name="harga_obat" class="form-control format-rupiah" 
                                            value="{{ number_format($hargaObat->harga_obat ?? 0, 0, ',', '.') }}">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Harga Khusus/Promo (IDR)</label>
                                        <input type="text" name="harga_khusus" class="form-control format-rupiah" 
                                            value="{{ number_format($hargaObat->harga_khusus ?? 0, 0, ',', '.') }}">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Harga BPJS (IDR)</label>
                                        <input type="text" name="harga_bpjs" class="form-control format-rupiah" 
                                            value="{{ number_format($hargaObat->harga_bpjs ?? 0, 0, ',', '.') }}">
                                    </div>
                                </div>

                                {{-- Harga Asuransi --}}
                                <div class="card border">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Harga Asuransi</h6>
                                        <button type="button" class="btn btn-sm btn-primary" id="btn-tambah-asuransi">
                                            <i class="ri-add-line"></i> Tambah Asuransi
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div id="harga-asuransi-container">
                                            @forelse($hargaAsuransi as $index => $ha)
                                            <div class="card mb-3 harga-asuransi-item" data-index="{{ $index }}">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <h6 class="mb-0">Asuransi #{{ $index + 1 }}</h6>
                                                        <button type="button" class="btn btn-sm btn-danger btn-hapus-asuransi" data-id="{{ $ha->id }}">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </div>
                                                    <input type="hidden" name="harga_asuransi[{{ $index }}][id]" value="{{ $ha->id }}">
                                                    
                                                    <div class="row mb-2">
                                                        <div class="col-md-6">
                                                            <label class="form-label">Pilih Asuransi <span class="text-danger">*</span></label>
                                                            <select name="harga_asuransi[{{ $index }}][asuransi_id]" class="form-select select-asuransi" required>
                                                                <option value="{{ $ha->asuransi_id }}" selected>
                                                                    {{ $ha->asuransi->nama_asuransi ?? '-' }}
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Harga (IDR) <span class="text-danger">*</span></label>
                                                            <input type="number" name="harga_asuransi[{{ $index }}][harga]" 
                                                                   class="form-control" value="{{ $ha->harga }}" min="0" step="1" required>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row mb-2">
                                                        <div class="col-md-4">
                                                            <label class="form-label">Tanggal Mulai</label>
                                                            <input type="date" name="harga_asuransi[{{ $index }}][tanggal_mulai]" 
                                                                   class="form-control" value="{{ $ha->tanggal_mulai }}">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label">Tanggal Selesai</label>
                                                            <input type="date" name="harga_asuransi[{{ $index }}][tanggal_selesai]" 
                                                                   class="form-control" value="{{ $ha->tanggal_selesai }}">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label">Status</label>
                                                            <select name="harga_asuransi[{{ $index }}][aktif]" class="form-select">
                                                                <option value="1" {{ $ha->aktif ? 'selected' : '' }}>Aktif</option>
                                                                <option value="0" {{ !$ha->aktif ? 'selected' : '' }}>Nonaktif</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label class="form-label">Keterangan</label>
                                                            <textarea name="harga_asuransi[{{ $index }}][keterangan]" 
                                                                      class="form-control" rows="2">{{ $ha->keterangan }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @empty
                                            <p class="text-muted text-center py-3">Belum ada harga asuransi. Klik "Tambah Asuransi" untuk menambah.</p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('obatrs.edit', $obat->id_obat_rs) }}'">
                                        <i class="ri-close-line"></i> Batal
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-save-line"></i> Simpan Semua
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- TAB 2: Data KFA --}}
                        <div class="tab-pane fade" id="kfa" role="tabpanel">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Pemetaan KFA (Katalog Farmasi Nasional)</h6>
                                        <button type="button" class="btn btn-sm btn-primary" id="btn-ubah-mapping">
                                            <i class="ri-refresh-line"></i> Ubah Pemetaan
                                        </button>
                                    </div>
                                    
                                    {{-- Informasi KFA Saat Ini --}}
                                    <div class="alert alert-info">
                                        <div class="d-flex align-items-start">
                                            <i class="ri-information-line fs-4 me-2"></i>
                                            <div>
                                                <strong>Obat Master yang Dipetakan Saat Ini:</strong>
                                                <div class="mt-2">
                                                    <span class="badge bg-primary me-2">{{ $detail->obatMaster->kfa_code }}</span>
                                                    <span>{{ $detail->obatMaster->nama_obat }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Detail Informasi KFA --}}
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <table class="table table-bordered table-sm">
                                                <tbody>
                                                    <tr>
                                                        <th width="30%" class="bg-light">Kode KFA</th>
                                                        <td>{{ $detail->obatMaster->kfa_code }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="bg-light">Nama Obat</th>
                                                        <td>{{ $detail->obatMaster->nama_obat }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="bg-light">Nama Generik</th>
                                                        <td>{{ $detail->obatMaster->nama_generik ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="bg-light">Bentuk Sediaan</th>
                                                        <td>{{ $detail->obatMaster->bentuk_sediaan ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="bg-light">Kekuatan</th>
                                                        <td>{{ $detail->obatMaster->kekuatan ?? '-' }} {{ $detail->obatMaster->satuan_kekuatan ?? '' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="bg-light">Kemasan</th>
                                                        <td>{{ $detail->obatMaster->kemasan ?? '-' }} ({{ $detail->obatMaster->isi_kemasan ?? '-' }})</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="bg-light">Manufacturer</th>
                                                        <td>{{ $detail->obatMaster->manufacturer ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="bg-light">NIE</th>
                                                        <td>{{ $detail->obatMaster->nie ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="bg-light">Kategori</th>
                                                        <td>
                                                            <span class="badge bg-info">{{ $detail->obatMaster->kategori ?? '-' }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="bg-light">Golongan</th>
                                                        <td>
                                                            <span class="badge bg-warning text-dark">{{ $detail->obatMaster->golongan ?? '-' }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="bg-light">Status</th>
                                                        <td>
                                                            <span class="badge {{ $detail->obatMaster->status == 'Aktif' ? 'bg-success' : 'bg-secondary' }}">
                                                                {{ $detail->obatMaster->status }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="bg-light">Last Sync</th>
                                                        <td>
                                                            @if($detail->obatMaster->last_sync)
                                                                {{ \Carbon\Carbon::parse($detail->obatMaster->last_sync)->format('d M Y H:i:s') }}
                                                                <span class="text-muted">({{ \Carbon\Carbon::parse($detail->obatMaster->last_sync)->diffForHumans() }})</span>
                                                            @else
                                                                <span class="text-muted">Belum pernah sync</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    {{-- Detail Komposisi & Indikasi --}}
                                    @if($detail->obatMaster->komposisi || $detail->obatMaster->indikasi || $detail->obatMaster->kontraindikasi || $detail->obatMaster->efek_samping)
                                    <hr>
                                    <h6 class="mb-3">Informasi Klinis</h6>

                                    <div class="row">
                                        {{-- Komposisi --}}
                                        @if($detail->obatMaster->komposisi)
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-primary fw-semibold">Komposisi</label>
                                            <textarea class="form-control" rows="2" readonly>{{ $detail->obatMaster->komposisi }}</textarea>
                                        </div>
                                        @endif

                                        {{-- Indikasi --}}
                                        @if($detail->obatMaster->indikasi)
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-success fw-semibold">Indikasi</label>
                                            <textarea class="form-control" rows="2" readonly>{{ $detail->obatMaster->indikasi }}</textarea>
                                        </div>
                                        @endif

                                        {{-- Kontraindikasi --}}
                                        @if($detail->obatMaster->kontraindikasi)
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-danger fw-semibold">Kontraindikasi</label>
                                            <textarea class="form-control" rows="2" readonly>{{ $detail->obatMaster->kontraindikasi }}</textarea>
                                        </div>
                                        @endif

                                        {{-- Efek Samping --}}
                                        @if($detail->obatMaster->efek_samping)
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-warning fw-semibold">Efek Samping</label>
                                            <textarea class="form-control" rows="2" readonly>{{ $detail->obatMaster->efek_samping }}</textarea>
                                        </div>
                                        @endif

                                        {{-- Peringatan --}}
                                        @if($detail->obatMaster->peringatan)
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-info fw-semibold">Peringatan</label>
                                            <textarea class="form-control" rows="2" readonly>{{ $detail->obatMaster->peringatan }}</textarea>
                                        </div>
                                        @endif

                                        {{-- Dosis --}}
                                        @if($detail->obatMaster->dosis)
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-secondary fw-semibold">Dosis</label>
                                            <textarea class="form-control" rows="2" readonly>{{ $detail->obatMaster->dosis }}</textarea>
                                        </div>
                                        @endif
                                    </div>
                                    @endif

                                    {{-- Raw Data API (Collapsible) --}}
                                    @if($detail->obatMaster->data_api)
                                    <hr>
                                    <div class="accordion" id="accordionKFA">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAPI">
                                                    <i class="ri-code-box-line me-2"></i> Raw Data API Satu Sehat
                                                </button>
                                            </h2>
                                            <div id="collapseAPI" class="accordion-collapse collapse" data-bs-parent="#accordionKFA">
                                                <div class="accordion-body">
                                                    <pre class="bg-dark text-light p-3 rounded" style="max-height: 400px; overflow-y: auto;">{{ json_encode(json_decode($detail->obatMaster->data_api), JSON_PRETTY_PRINT) }}</pre>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    {{-- Action Buttons --}}
                                    <div class="mt-4 d-flex gap-2">
                                        <button type="button" class="btn btn-primary" id="btn-sync-kfa">
                                            <i class="ri-refresh-line"></i> Sync Ulang dari API
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" id="btn-lihat-history">
                                            <i class="ri-history-line"></i> Riwayat Pemetaan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 3: Pemetaan Medscape --}}
                        <div class="tab-pane fade" id="medscape" role="tabpanel">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="mb-3">Pemetaan dengan Medscape</h6>
                                    <div class="alert alert-info">
                                        <i class="ri-information-line"></i> Fitur pemetaan Medscape akan segera tersedia
                                    </div>
                                    
                                    <form id="form-medscape">
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label class="form-label">Medscape Drug ID</label>
                                                <input type="text" class="form-control" placeholder="Masukkan Medscape Drug ID">
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ri-link"></i> Hubungkan dengan Medscape
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Loading Overlay --}}
<div id="loading-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
    <div class="d-flex justify-content-center align-items-center h-100">
        <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
        padding: 6px 12px;
        border: 1px solid #ced4da;
        border-radius: 6px;
    }
    .select2-selection__rendered {
        line-height: 26px !important;
    }
    .harga-asuransi-item {
        border: 1px solid #dee2e6;
    }
    .nav-tabs .nav-link {
        color: #6c757d;
    }
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        font-weight: 500;
    }
</style>
@endpush
@push('scripts')
{{-- Format Rupiah (5.000) --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rupiahInputs = document.querySelectorAll('.format-rupiah');

        rupiahInputs.forEach(input => {
            input.addEventListener('input', function (e) {
                // Hapus semua karakter non-digit
                let value = this.value.replace(/\D/g, '');

                // Format angka jadi ribuan
                this.value = new Intl.NumberFormat('id-ID').format(value);
            });

            // Saat form disubmit, ubah ke angka murni agar backend tidak bingung
            input.form?.addEventListener('submit', function () {
                rupiahInputs.forEach(inp => {
                    inp.value = inp.value.replace(/\./g, '');
                });
            });
        });
    });
</script>

{{-- Script Update Data --}}
<script>
   $(document).ready(function () {
    // ✅ DEKLARASI VARIABEL GLOBAL
    const obatId = '{{ $obat->id_obat_rs }}';
    const detailId = '{{ $detail->id_detail_obat_rs }}';
    const obatMasterId = '{{ $detail->id_obat_master }}';
    
    let asuransiIndex = {{ count($hargaAsuransi) }};

    // ========================================
    // HELPER FUNCTIONS
    // ========================================
    function showLoading() {
        $('#loading-overlay').fadeIn(200);
    }

    function hideLoading() {
        $('#loading-overlay').fadeOut(200);
    }

    function initSelect2Asuransi(element) {
        element.select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih Asuransi...',
            allowClear: true,
            ajax: {
                url: '{{ route("api.asuransi.search") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { term: params.term };
                },
                processResults: function(data) {
                    return { results: data };
                },
                cache: true
            }
        });
    }

    // ========================================
    // FORM SUBMIT - UPDATE ALL DATA
    // ========================================
    $('#form-data-obat').on('submit', function (e) {
        e.preventDefault();
        
        console.log('Form submitted');

        // ✅ Konversi format rupiah ke angka murni
        $(this).find('.format-rupiah').each(function () {
            let originalValue = this.value;
            this.value = this.value.replace(/\./g, '').replace(/[^\d]/g, '');
            console.log('Converting:', originalValue, '->', this.value);
        });

        // ✅ Validasi data asuransi
        let isValid = true;
        let errorMessages = [];

        $('.harga-asuransi-item').each(function(index) {
            let asuransiId = $(this).find('select[name*="[asuransi_id]"]').val();
            let harga = $(this).find('input[name*="[harga]"]').val();
            
            if (!asuransiId) {
                isValid = false;
                errorMessages.push(`Asuransi #${index + 1}: Pilih asuransi terlebih dahulu`);
            }
            
            if (!harga || parseFloat(harga) < 0) {
                isValid = false;
                errorMessages.push(`Asuransi #${index + 1}: Harga harus >= 0`);
            }
        });

        if (!isValid) {
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: errorMessages.join('<br>'),
                confirmButtonText: 'OK'
            });
            return false;
        }

        const formData = $(this).serialize();
        const updateUrl = '{{ route("obat.detail.update-all", [$obat->id_obat_rs, $detail->id_detail_obat_rs]) }}';

        console.log('Sending data to:', updateUrl);
        showLoading();

        $.ajax({
            url: updateUrl,
            type: 'POST',
            data: formData,
            success: function (response) {
                hideLoading();
                console.log('Success:', response);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Data obat berhasil disimpan!',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function (xhr) {
                hideLoading();
                console.error('Error:', xhr);
                
                let errorMessage = 'Terjadi kesalahan saat menyimpan data.';
                
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    if (xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        let errorList = '<ul class="text-start">';
                        for (let field in errors) {
                            errors[field].forEach(err => {
                                errorList += `<li>${err}</li>`;
                            });
                        }
                        errorList += '</ul>';
                        errorMessage = errorList;
                    }
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan',
                    html: errorMessage,
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    // ========================================
    // ASURANSI - INIT EXISTING SELECT2
    // ========================================
    $('.select-asuransi').each(function() {
        initSelect2Asuransi($(this));
    });

    // ========================================
    // ASURANSI - TAMBAH BARU
    // ========================================
    $('#btn-tambah-asuransi').on('click', function() {
        console.log('Adding asuransi, index:', asuransiIndex);
        
        let newCard = `
            <div class="card mb-3 harga-asuransi-item" data-index="${asuransiIndex}">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="mb-0">Asuransi #${asuransiIndex + 1}</h6>
                        <button type="button" class="btn btn-sm btn-danger btn-hapus-asuransi-new">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                    <input type="hidden" name="harga_asuransi[${asuransiIndex}][id]" value="">
                    
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label">Pilih Asuransi <span class="text-danger">*</span></label>
                            <select name="harga_asuransi[${asuransiIndex}][asuransi_id]" class="form-select select-asuransi" required>
                                <option value=""></option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Harga (IDR) <span class="text-danger">*</span></label>
                            <input type="number" name="harga_asuransi[${asuransiIndex}][harga]" 
                                class="form-control" value="0" min="0" step="1" required>
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="harga_asuransi[${asuransiIndex}][tanggal_mulai]" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="harga_asuransi[${asuransiIndex}][tanggal_selesai]" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="harga_asuransi[${asuransiIndex}][aktif]" class="form-select">
                                <option value="1" selected>Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label">Keterangan</label>
                            <textarea name="harga_asuransi[${asuransiIndex}][keterangan]" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#harga-asuransi-container').append(newCard);
        initSelect2Asuransi($(`[name="harga_asuransi[${asuransiIndex}][asuransi_id]"]`));
        asuransiIndex++;
    });

    // ========================================
    // ASURANSI - HAPUS BARU
    // ========================================
    $(document).on('click', '.btn-hapus-asuransi-new', function() {
        console.log('Removing new asuransi');
        $(this).closest('.harga-asuransi-item').fadeOut(300, function() {
            $(this).remove();
        });
    });

    // ========================================
    // ASURANSI - HAPUS EXISTING
    // ========================================
    $(document).on('click', '.btn-hapus-asuransi', function() {
        let id = $(this).data('id');
        let $card = $(this).closest('.harga-asuransi-item');
        
        console.log('Deleting asuransi, id:', id);
        
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Yakin ingin menghapus harga asuransi ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                
                $.ajax({
                    url: `/obatrs/${obatId}/detail/${detailId}/harga-asuransi/${id}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        hideLoading();
                        console.log('Delete success:', response);
                        
                        $card.fadeOut(300, function() {
                            $(this).remove();
                        });
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Harga asuransi berhasil dihapus',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        hideLoading();
                        console.error('Delete error:', xhr);
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal menghapus: ' + (xhr.responseJSON?.message || 'Unknown error')
                        });
                    }
                });
            }
        });
    });

    // ========================================
    // KFA - UBAH PEMETAAN
    // ========================================
    $('#btn-ubah-mapping').on('click', function() {
        console.log('Ubah mapping clicked');
        
        Swal.fire({
            title: 'Ubah Pemetaan KFA',
            html: `
                <div class="text-start">
                    <label class="form-label fw-bold">Cari Obat Master (KFA) Baru:</label>
                    <select id="swal-select-kfa" class="form-select" style="width: 100%">
                        <option value="">-- Ketik untuk mencari --</option>
                    </select>
                    <div class="alert alert-warning mt-3 mb-0" role="alert">
                        <i class="ri-information-line"></i> 
                        <small>Pemetaan ini akan mengubah referensi Detail Obat RS ke Obat Master KFA yang baru.</small>
                    </div>
                </div>
            `,
            width: '600px',
            showCancelButton: true,
            confirmButtonText: '<i class="ri-save-line"></i> Simpan Pemetaan',
            cancelButtonText: '<i class="ri-close-line"></i> Batal',
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            didOpen: () => {
                $('#swal-select-kfa').select2({
                    dropdownParent: $('.swal2-container'),
                    placeholder: 'Ketik nama obat atau kode KFA...',
                    allowClear: true,
                    minimumInputLength: 2,
                    ajax: {
                        url: '{{ route("api.obat-master.search") }}',
                        dataType: 'json',
                        delay: 300,
                        data: function(params) {
                            return { 
                                term: params.term,
                                page: params.page || 1
                            };
                        },
                        processResults: function(data) {
                            console.log('Search results:', data);
                            return { 
                                results: data.map(item => ({
                                    id: item.id,
                                    text: `[${item.kfa_code}] ${item.text}`
                                }))
                            };
                        },
                        cache: true
                    }
                });
            },
            preConfirm: () => {
                const kfaId = $('#swal-select-kfa').val();
                if (!kfaId) {
                    Swal.showValidationMessage('Silakan pilih obat master');
                    return false;
                }
                return kfaId;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                updateKFAMapping(result.value);
            }
        });
    });

    function updateKFAMapping(newKfaId) {
        console.log('Updating KFA mapping to:', newKfaId);
        showLoading();
        
        $.ajax({
            url: `/obatrs/${obatId}/detail/${detailId}/update-kfa-mapping`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id_obat_master: newKfaId
            },
            success: function(response) {
                console.log('Update success:', response);
                hideLoading();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Pemetaan KFA berhasil diubah',
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                console.error('Update error:', xhr);
                hideLoading();
                
                let errorMsg = 'Gagal mengubah pemetaan KFA';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: errorMsg,
                    confirmButtonText: 'OK'
                });
            }
        });
    }

    // ========================================
    // KFA - SYNC ULANG
    // ========================================
    $('#btn-sync-kfa').on('click', function() {
        console.log('Sync KFA clicked');
        
        Swal.fire({
            title: 'Konfirmasi Sync',
            text: 'Apakah Anda yakin ingin melakukan sinkronisasi ulang data KFA dari API Satu Sehat?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="ri-refresh-line"></i> Ya, Sync Sekarang',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                syncKFAData();
            }
        });
    });

    function syncKFAData() {
        console.log('Syncing KFA:', obatMasterId);
        showLoading();
        
        $.ajax({
            url: `/obatrs/${obatId}/detail/${detailId}/sync-kfa/${obatMasterId}`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('Sync success:', response);
                hideLoading();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Sync Berhasil!',
                    html: `
                        <p>Data KFA berhasil disinkronkan dari API Satu Sehat</p>
                        <small class="text-muted">Last sync: ${new Date().toLocaleString('id-ID')}</small>
                    `,
                    showConfirmButton: false,
                    timer: 2500
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                console.error('Sync error:', xhr);
                hideLoading();
                
                let errorMsg = 'Gagal sinkronisasi data KFA';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Sync Gagal!',
                    text: errorMsg,
                    confirmButtonText: 'OK'
                });
            }
        });
    }

    // ========================================
    // KFA - LIHAT HISTORY
    // ========================================
    $('#btn-lihat-history').on('click', function() {
        console.log('History clicked');
        showLoading();
        
        $.ajax({
            url: `/obatrs/${obatId}/detail/${detailId}/kfa-history`,
            type: 'GET',
            success: function(response) {
                console.log('History data:', response);
                hideLoading();
                
                let historyHtml = '<div class="table-responsive">';
                historyHtml += '<table class="table table-sm table-bordered table-hover">';
                historyHtml += '<thead class="table-light">';
                historyHtml += '<tr>';
                historyHtml += '<th width="25%">Tanggal</th>';
                historyHtml += '<th width="20%">Kode KFA</th>';
                historyHtml += '<th width="35%">Nama Obat</th>';
                historyHtml += '<th width="20%">Diubah Oleh</th>';
                historyHtml += '</tr>';
                historyHtml += '</thead>';
                historyHtml += '<tbody>';
                
                if (response.data && response.data.length > 0) {
                    response.data.forEach((item, index) => {
                        historyHtml += `<tr>
                            <td><small>${item.created_at}</small></td>
                            <td><span class="badge bg-primary">${item.kfa_code}</span></td>
                            <td><small>${item.nama_obat}</small></td>
                            <td><small>${item.user_name}</small></td>
                        </tr>`;
                    });
                } else {
                    historyHtml += '<tr><td colspan="4" class="text-center text-muted py-3">';
                    historyHtml += '<i class="ri-inbox-line fs-3 d-block mb-2"></i>';
                    historyHtml += 'Belum ada riwayat perubahan pemetaan';
                    historyHtml += '</td></tr>';
                }
                
                historyHtml += '</tbody></table></div>';
                
                Swal.fire({
                    title: '<i class="ri-history-line"></i> Riwayat Pemetaan KFA',
                    html: historyHtml,
                    width: '800px',
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#6c757d'
                });
            },
            error: function(xhr) {
                console.error('History error:', xhr);
                hideLoading();
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Gagal memuat riwayat pemetaan',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    // ========================================
    // MEDSCAPE - COMING SOON
    // ========================================
    $('#form-medscape').on('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            icon: 'info',
            title: 'Coming Soon',
            text: 'Fitur pemetaan Medscape akan segera tersedia'
        });
    });

    console.log('All scripts initialized successfully');
});
</script>
@endpush