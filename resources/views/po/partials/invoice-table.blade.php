<div class="table-responsive">
    <table class="table table-hover table-striped align-middle" id="{{ $tableId }}">
        <thead class="table-light">
            <tr>
                <th width="50">No</th>
                <th>No Invoice</th>
                <th>No GR / PO</th>
                <th>Tanggal</th>
                <th>Tanggal Jatuh Tempo</th>
                <th>Supplier</th>
                <th width="150">Total</th>
                <th width="150">Status</th>
                <th width="100" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($purchaseOrders as $po)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        <strong class="text-success">{{ $po->no_invoice }}</strong>
                    </td>
                    <td>
                        <small class="text-muted">
                            {{ $po->no_gr ?? $po->no_po }}
                        </small>
                    </td>
                    <td>
                        <small class="text-muted">
                            <i class="ri-calendar-line"></i> 
                            {{ $po->tanggal_permintaan->format('d/m/Y') }}
                        </small>
                    </td>
                    <td>
                        @if($po->tanggal_jatuh_tempo)
                            @php
                                $dueDate = \Carbon\Carbon::parse($po->tanggal_jatuh_tempo);
                                $today = \Carbon\Carbon::today();
                                $daysLeft = $today->diffInDays($dueDate, false);
                            @endphp
                            <strong class="{{ $daysLeft < 0 ? 'text-danger' : ($daysLeft <= 3 ? 'text-warning' : 'text-success') }}">
                                {{ $dueDate->format('d/m/Y') }}
                                @if($daysLeft < 0)
                                    <br><small>(Terlambat {{ abs($daysLeft) }} hari)</small>
                                @elseif($daysLeft == 0)
                                    <br><small>(Jatuh tempo hari ini!)</small>
                                @else
                                    <br><small>({{ $daysLeft }} hari lagi)</small>
                                @endif
                            </strong>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($po->supplier)
                            <span class="badge bg-secondary">{{ $po->supplier->nama_supplier }}</span>
                        @endif
                    </td>
                    <td>
                        <strong class="text-success">
                            Rp {{ number_format($po->grand_total_diterima ?? $po->grand_total, 0, ',', '.') }}
                        </strong>
                    </td>
                    <td>
                        <span class="badge bg-success">
                            <i class="ri-file-text-line"></i> Selesai
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ri-more-2-fill"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li>
                                    <a class="dropdown-item" href="{{ route('po.show', $po->id_po) }}">
                                        <i class="ri-eye-fill me-2"></i>Detail
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('po.print-invoice', $po->id_po) }}" target="_blank">
                                        <i class="ri-printer-line me-2"></i>Print Invoice
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <i class="ri-file-text-line ri-3x text-muted d-block mb-3"></i>
                        <p class="text-muted mb-0">Belum ada Invoice</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>