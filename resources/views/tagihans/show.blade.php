@extends('layouts.app')

@section('title', 'Detail Tagihan - ' . $tagihan->no_tagihan)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Detail Tagihan</h1>
            <p class="text-muted">{{ $tagihan->no_tagihan }}</p>
        </div>
        <div>
            <a href="{{ route('tagihans.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            @if(!$tagihan->locked)
                @if($tagihan->status != 'LUNAS')
                <a href="{{ route('tagihans.payment', $tagihan->id_tagihan) }}" class="btn btn-success">
                    <i class="fas fa-money-bill-wave"></i> Bayar
                </a>
                @endif
                @if($tagihan->status == 'LUNAS')
                <button type="button" class="btn btn-warning" onclick="lockTagihan({{ $tagihan->id_tagihan }})">
                    <i class="fas fa-lock"></i> Lock Tagihan
                </button>
                @endif
            @else
            <span class="badge badge-secondary p-2">
                <i class="fas fa-lock"></i> Tagihan Terkunci
            </span>
            @endif
            <a href="{{ route('tagihans.print', $tagihan->id_tagihan) }}" class="btn btn-info" target="_blank">
                <i class="fas fa-print"></i> Cetak
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Info Pasien & Tagihan -->
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Pasien</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%"><strong>No. RM</strong></td>
                            <td>: {{ $tagihan->pasien->no_rm }}</td>
                        </tr>
                        <tr>
                            <td><strong>Nama</strong></td>
                            <td>: {{ $tagihan->pasien->nama_lengkap }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Lahir</strong></td>
                            <td>: {{ $tagihan->pasien->tanggal_lahir->format('d/m/Y') ?? '-'}}</td>
                        </tr>
                        <tr>
                            <td><strong>Alamat</strong></td>
                            <td>: {{ $tagihan->pasien->alamat ?? '-'}}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Tagihan</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%"><strong>No. Tagihan</strong></td>
                            <td>: {{ $tagihan->no_tagihan }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal</strong></td>
                            <td>: {{ $tagihan->tanggal_tagihan->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Jenis</strong></td>
                            <td>: 
                                @if($tagihan->jenis_tagihan == 'IGD')
                                <span class="badge badge-danger">IGD</span>
                                @elseif($tagihan->jenis_tagihan == 'RAWAT_JALAN')
                                <span class="badge badge-info">Rawat Jalan</span>
                                @else
                                <span class="badge badge-warning">Rawat Inap</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Status</strong></td>
                            <td>: 
                                @if($tagihan->status == 'LUNAS')
                                <span class="badge badge-success">Lunas</span>
                                @elseif($tagihan->status == 'CICILAN')
                                <span class="badge badge-info">Cicilan</span>
                                @else
                                <span class="badge badge-warning">Belum Lunas</span>
                                @endif
                            </td>
                        </tr>
                        @if($tagihan->status_klaim != 'NON_KLAIM')
                        <tr>
                            <td><strong>Status Klaim</strong></td>
                            <td>: <span class="badge badge-secondary">{{ $tagihan->status_klaim }}</span></td>
                        </tr>
                        @endif
                        @if($tagihan->tanggal_lunas)
                        <tr>
                            <td><strong>Tanggal Lunas</strong></td>
                            <td>: {{ $tagihan->tanggal_lunas->format('d/m/Y') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Detail Items & Pembayaran -->
        <div class="col-md-8">
            <!-- Summary -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card border-left-primary shadow h-100">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Tagihan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-left-success shadow h-100">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Dibayar
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($tagihan->total_dibayar, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-left-{{ $tagihan->sisa_tagihan > 0 ? 'danger' : 'success' }} shadow h-100">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-{{ $tagihan->sisa_tagihan > 0 ? 'danger' : 'success' }} text-uppercase mb-1">
                                Sisa Tagihan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Items -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Tagihan</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Kategori</th>
                                    <th>Deskripsi</th>
                                    <th width="10%" class="text-center">Qty</th>
                                    <th width="15%" class="text-right">Harga</th>
                                    <th width="15%" class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tagihan->items->groupBy('kategori') as $kategori => $items)
                                <tr class="table-active">
                                    <td colspan="5"><strong>{{ $kategori }}</strong></td>
                                </tr>
                                @foreach($items as $item)
                                <tr>
                                    <td></td>
                                    <td>
                                        {{ $item->deskripsi }}
                                        @if($item->ditanggung)
                                        <span class="badge badge-info badge-sm">Ditanggung</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->qty }}</td>
                                    <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                    <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td colspan="4" class="text-right"><strong>Subtotal {{ $kategori }}:</strong></td>
                                    <td class="text-right"><strong>Rp {{ number_format($items->sum('subtotal'), 0, ',', '.') }}</strong></td>
                                </tr>
                                @endforeach
                                <tr class="table-primary">
                                    <td colspan="4" class="text-right"><strong>TOTAL TAGIHAN:</strong></td>
                                    <td class="text-right"><strong>Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Riwayat Pembayaran -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Pembayaran</h6>
                </div>
                <div class="card-body">
                    @if($tagihan->pembayarans->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Metode</th>
                                    <th>No. Referensi</th>
                                    <th width="15%" class="text-right">Jumlah</th>
                                    <th>Petugas</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tagihan->pembayarans as $pembayaran)
                                <tr>
                                    <td>{{ $pembayaran->tanggal_bayar->format('d/m/Y H:i') }}</td>
                                    @php
                                        $badgeClass = match ($pembayaran->metode) {
                                            'TUNAI'     => 'bg-success',
                                            'DEBIT'     => 'bg-primary',
                                            'CREDIT'    => 'bg-warning',
                                            'TRANSFER'  => 'bg-info',
                                            'BPJS'      => 'bg-danger',
                                            'ASURANSI'  => 'bg-secondary',
                                            default     => 'bg-light',
                                        };
                                    @endphp

                                    <td>
                                        <span class="badge {{ $badgeClass }}">
                                            {{ $pembayaran->metode }}
                                        </span>
                                    </td>
                                    <td>{{ $pembayaran->no_referensi ?? '-' }}</td>
                                    <td class="text-right">
                                        <strong>Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</strong>
                                    </td>
                                    <td>{{ $pembayaran->creator->nama_lengkap ?? '-' }}</td>
                                    <td>
                                        @if(!$tagihan->locked && $pembayaran->created_at->diffInDays(now()) <= 7)
                                        <button class="btn btn-sm btn-danger" 
                                                onclick="voidPayment({{ $pembayaran->id }})">
                                            <i class="ri ri-restart-line"></i>
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                <tr class="table-success">
                                    <td colspan="3" class="text-right"><strong>TOTAL DIBAYAR:</strong></td>
                                    <td class="text-right"><strong>Rp {{ number_format($tagihan->total_dibayar, 0, ',', '.') }}</strong></td>
                                    <td colspan="2"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada pembayaran</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Catatan -->
            @if($tagihan->catatan)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Catatan</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $tagihan->catatan }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function lockTagihan(id) {
    Swal.fire({
        title: 'Lock Tagihan?',
        text: 'Tagihan yang sudah di-lock tidak dapat diubah lagi',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Lock!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/api/tagihan/${id}/lock`,
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('token')
                },
                success: function(response) {
                    Swal.fire('Berhasil!', 'Tagihan berhasil di-lock', 'success')
                        .then(() => location.reload());
                },
                error: function(xhr) {
                    Swal.fire('Error!', xhr.responseJSON.message, 'error');
                }
            });
        }
    });
}

function voidPayment(id) {
    Swal.fire({
        title: 'Batalkan Pembayaran?',
        input: 'textarea',
        inputLabel: 'Alasan Pembatalan',
        inputPlaceholder: 'Masukkan alasan pembatalan (min. 10 karakter)',
        inputAttributes: {
            'aria-label': 'Alasan pembatalan'
        },
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Batalkan!',
        cancelButtonText: 'Batal',
        preConfirm: (alasan) => {
            if (!alasan || alasan.length < 10) {
                Swal.showValidationMessage('Alasan minimal 10 karakter');
            }
            return alasan;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/api/tagihan/pembayaran/${id}`,
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('token')
                },
                data: { alasan: result.value },
                success: function(response) {
                    Swal.fire('Berhasil!', 'Pembayaran berhasil dibatalkan', 'success')
                        .then(() => location.reload());
                },
                error: function(xhr) {
                    Swal.fire('Error!', xhr.responseJSON.message, 'error');
                }
            });
        }
    });
}
</script>
@endpush