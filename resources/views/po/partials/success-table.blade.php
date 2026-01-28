<div class="table-responsive">
    <table class="table table-hover table-striped align-middle" id="{{ $tableId }}">
        <thead class="table-light">
            <tr>
                <th width="50">No</th>
                <th>No GR</th>
                <th>No PO</th>
                <th>Tanggal</th>
                <th>Tanggal Selesai</th>
                <th>Pemohon</th>
                <th>Unit Tujuan</th>
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
                        <strong class="text-success">{{ $po->no_gr }}</strong>
                    </td>
                    <td>
                        <small class="text-muted">{{ $po->no_po }}</small>
                    </td>
                    <td>
                        <small class="text-muted">
                            <i class="ri-calendar-line"></i> 
                            {{ $po->tanggal_permintaan->format('d/m/Y') }}
                        </small>
                    </td>
                    <td>
                        @if($po->updated_at)
                            <small class="text-muted">
                                <i class="ri-checkbox-circle-line"></i> 
                                {{ $po->updated_at->format('d/m/Y H:i') }}
                            </small>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-xs me-2">
                                <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                    <i class="ri-user-line"></i>
                                </span>
                            </div>
                            <div>
                                <strong>{{ $po->karyawanPemohon->nama_lengkap }}</strong>
                                <br><small class="text-muted">{{ ucfirst($po->unit_pemohon) }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-secondary">{{ ucfirst($po->unit_tujuan) }}</span>
                    </td>
                    <td>
                        <strong class="text-success">
                            Rp {{ number_format($po->grand_total_diterima ?? $po->grand_total, 0, ',', '.') }}
                        </strong>
                    </td>
                    <td>
                        <span class="badge bg-success">
                            <i class="ri-checkbox-circle-line"></i> Selesai
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
                                    <a class="dropdown-item" href="{{ route('po.print', $po->id_po) }}" target="_blank">
                                        <i class="ri-printer-line me-2"></i>Print PO
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center py-5">
                        <i class="ri-checkbox-circle-line ri-3x text-muted d-block mb-3"></i>
                        <p class="text-muted mb-0">Belum ada transaksi yang selesai</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>