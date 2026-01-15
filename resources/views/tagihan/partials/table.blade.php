<div class="row">
    <div class="col-xl-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="ri-file-list-line me-2"></i>
                        @if($tabType === 'draft')
                            Daftar Tagihan Draft
                        @else
                            Daftar Tagihan Purchase Order
                        @endif
                    </h5>
                </div>
            </div>

            <!-- Filter & Action Bar -->
            <div class="card-body border-bottom bg-light">
                <form method="GET" action="{{ route('tagihan.index') }}">
                    <input type="hidden" name="tab" value="{{ $tabType }}">
                    <div class="row g-3">
                        <!-- Filter Supplier -->
                        <div class="col-md-{{ $tabType === 'aktif' ? '3' : '4' }}">
                            <label class="form-label small fw-bold">Supplier</label>
                            <select name="supplier_id" class="form-select form-select-sm">
                                <option value="">Semua Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->nama_supplier }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if($tabType === 'aktif')
                        <!-- Filter Status -->
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">Semua Status</option>
                                <option value="menunggu_pembayaran" {{ request('status') == 'menunggu_pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                                <option value="dibayar_sebagian" {{ request('status') == 'dibayar_sebagian' ? 'selected' : '' }}>Dibayar Sebagian</option>
                                <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                                <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>

                        <!-- Filter Jatuh Tempo -->
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Jatuh Tempo</label>
                            <select name="jatuh_tempo" class="form-select form-select-sm">
                                <option value="">Semua</option>
                                <option value="lewat" {{ request('jatuh_tempo') == 'lewat' ? 'selected' : '' }}>Sudah Lewat</option>
                                <option value="minggu_ini" {{ request('jatuh_tempo') == 'minggu_ini' ? 'selected' : '' }}>Minggu Ini</option>
                            </select>
                        </div>
                        @endif

                        <!-- Filter Tanggal Dari -->
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Tanggal Dari</label>
                            <input type="date" name="tanggal_dari" class="form-control form-control-sm" value="{{ request('tanggal_dari') }}">
                        </div>

                        <!-- Filter Tanggal Sampai -->
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Tanggal Sampai</label>
                            <input type="date" name="tanggal_sampai" class="form-control form-control-sm" value="{{ request('tanggal_sampai') }}">
                        </div>

                        <!-- Search Input -->
                        <div class="col-md-{{ $tabType === 'aktif' ? '3' : '4' }}">
                            <label class="form-label small fw-bold">Cari</label>
                            <input type="text" class="form-control form-control-sm" name="search" 
                                   placeholder="Cari No Tagihan, No PO, atau Supplier..." value="{{ request('search') }}">
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-md-{{ $tabType === 'aktif' ? '12' : '12' }} mt-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="btn-group" role="group">
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="ri-filter-line me-1"></i>Filter
                                    </button>
                                    <a href="{{ route('tagihan.index') }}?tab={{ $tabType }}" class="btn btn-sm btn-secondary">
                                        <i class="ri-refresh-line me-1"></i>Reset
                                    </a>
                                </div>

                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-success" onclick="exportExcel('{{ $tabType }}')">
                                        <i class="ri-file-excel-line me-1"></i>Excel
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="exportPDF('{{ $tabType }}')">
                                        <i class="ri-file-pdf-line me-1"></i>PDF
                                    </button>
                                    <button type="button" class="btn btn-sm btn-info" onclick="printTagihan('{{ $tabType }}')">
                                        <i class="ri-printer-line me-1"></i>Print
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="tagihanTable">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>No Tagihan</th>
                                <th>No PO/GR</th>
                                <th>No Invoice</th>
                                <th>Supplier</th>
                                <th>Tanggal</th>
                                @if($tabType === 'aktif')
                                <th>Jatuh Tempo</th>
                                @endif
                                <th width="150">Total</th>
                                @if($tabType === 'aktif')
                                <th width="150">Dibayar</th>
                                <th width="150">Sisa</th>
                                @endif
                                <th width="130">Status</th>
                                <th width="100" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tagihan as $x => $t)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>
                                        <strong class="text-primary">{{ $t->no_tagihan }}</strong>
                                    </td>
                                    <td>
                                        <a href="{{ route('po.show', $t->id_po) }}" class="text-decoration-none">
                                            {{ $t->purchaseOrder->no_gr }}
                                        </a>
                                    </td>
                                    <td>
                                        <strong class="text-primary">{{ $t->purchaseOrder->no_invoice }}</strong>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-2">
                                                <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                    <i class="ri-store-2-line"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <strong>{{ $t->supplier->nama_supplier ?? '-' }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="ri-calendar-line"></i>
                                            {{ $t->tanggal_tagihan ? $t->tanggal_tagihan->format('d/m/Y') : '-' }}
                                        </small>
                                    </td>
                                    @if($tabType === 'aktif')
                                    <td>
                                        @if($t->tanggal_jatuh_tempo)
                                            @php
                                                $dueDate = \Carbon\Carbon::parse($t->tanggal_jatuh_tempo);
                                                $today = \Carbon\Carbon::today();
                                                $daysLeft = $today->diffInDays($dueDate, false);
                                            @endphp
                                            <strong class="{{ $daysLeft < 0 ? 'text-danger' : ($daysLeft <= 3 ? 'text-warning' : 'text-success') }}">
                                                {{ $dueDate->format('d/m/Y') }}
                                                @if($daysLeft < 0)
                                                    (Terlambat {{ abs($daysLeft) }} hari)
                                                @elseif($daysLeft == 0)
                                                    (Jatuh tempo hari ini!)
                                                @else
                                                    ({{ $daysLeft }} hari lagi)
                                                @endif
                                            </strong>
                                        @else
                                            <small class="text-muted">-</small>
                                        @endif
                                    </td>
                                    @endif
                                    <td>
                                        <strong>Rp {{ number_format($t->grand_total, 0, ',', '.') }}</strong>
                                    </td>
                                    @if($tabType === 'aktif')
                                    <td>
                                        <span class="text-success">
                                            Rp {{ number_format($t->total_dibayar, 0, ',', '.') }}
                                        </span>
                                        @if($t->total_dibayar > 0 && $t->grand_total > 0)
                                            <br>
                                            <div class="progress mt-1" style="height: 5px;">
                                                <div class="progress-bar bg-success" 
                                                     style="width: {{ ($t->total_dibayar / $t->grand_total) * 100 }}%"></div>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong class="{{ $t->sisa_tagihan > 0 ? 'text-danger' : 'text-success' }}">
                                            Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }}
                                        </strong>
                                    </td>
                                    @endif
                                    <td>
                                        @php
                                            $statusColors = [
                                                'draft' => 'bg-secondary',
                                                'menunggu_pembayaran' => 'bg-warning text-dark',
                                                'dibayar_sebagian' => 'bg-info',
                                                'lunas' => 'bg-success',
                                                'dibatalkan' => 'bg-danger',
                                            ];
                                            $statusLabels = [
                                                'draft' => 'Draft',
                                                'menunggu_pembayaran' => 'Menunggu Bayar',
                                                'dibayar_sebagian' => 'Dibayar Sebagian',
                                                'lunas' => 'Lunas',
                                                'dibatalkan' => 'Dibatalkan',
                                            ];
                                        @endphp
                                        <span class="badge {{ $statusColors[$t->status] ?? 'bg-secondary' }}">
                                            <i class="ri-record-circle-line"></i> {{ $statusLabels[$t->status] ?? $t->status }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                                    data-bs-toggle="dropdown">
                                                <i class="ri-more-2-fill"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('tagihan.show', $t->id_tagihan) }}">
                                                        <i class="ri-eye-fill me-2"></i>Detail
                                                    </a>
                                                </li>
                                                @if($t->canBePaid())
                                                <li>
                                                    <a class="dropdown-item text-success" href="{{ route('tagihan.payment.form', $t->id_tagihan) }}">
                                                        <i class="ri-money-dollar-circle-line me-2"></i>Bayar
                                                    </a>
                                                </li>
                                                @endif
                                                @if($tabType === 'aktif')
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('tagihan.payment.history', $t->id_tagihan) }}">
                                                        <i class="ri-history-line me-2"></i>History Bayar
                                                    </a>
                                                </li>
                                                @endif
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('tagihan.print', $t->id_tagihan) }}" target="_blank">
                                                        <i class="ri-printer-line me-2"></i>Print
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $tabType === 'aktif' ? 12 : 9 }}" class="text-center py-5">
                                        <i class="ri-file-list-line ri-3x text-muted d-block mb-3"></i>
                                        <p class="text-muted mb-0">
                                            @if($tabType === 'draft')
                                                Belum ada data tagihan draft
                                            @else
                                                Belum ada data tagihan
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Info Total Data --}}
                @if($tagihan->count() > 0)
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted small">
                        Total {{ $tagihan->count() }} data
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>