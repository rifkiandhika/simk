<div class="table-responsive">
    <table class="table table-hover table-striped align-middle" id="{{ $tableId }}">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th width="180">No PO</th>
                <th>Tanggal</th>
                <th>Pemohon</th>
                <th>Tujuan</th>
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
                        <strong class="text-primary">{{ $po->no_po }}</strong>
                        <br>
                        @if($po->tipe_po == 'internal')
                            <span class="badge badge-sm bg-info">Internal</span>
                        @else
                            <span class="badge badge-sm bg-warning text-dark">Eksternal</span>
                        @endif
                    </td>
                    <td>
                        <small class="text-muted">
                            <i class="ri-calendar-line"></i> 
                            {{ $po->tanggal_permintaan->format('d/m/Y') }}
                        </small>
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
                        @if($po->supplier)
                            <span class="badge bg-secondary">{{ $po->supplier->nama_supplier }}</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($po->unit_tujuan) }}</span>
                        @endif
                    </td>
                    <td>
                        <strong class="text-success">
                            Rp {{ number_format($po->grand_total, 0, ',', '.') }}
                        </strong>
                    </td>
                    <td>
                        @php
                            $statusColors = [
                                'draft' => 'bg-secondary',
                                'menunggu_persetujuan_kepala_gudang' => 'bg-warning text-dark',
                                'menunggu_persetujuan_kasir' => 'bg-warning text-dark',
                                'disetujui' => 'bg-success',
                                'dikirim_ke_supplier' => 'bg-info',
                                'dalam_pengiriman' => 'bg-primary',
                                'diterima' => 'bg-success',
                                'ditolak' => 'bg-danger',
                            ];
                            
                            // Label status untuk PO internal
                            $statusLabelsInternal = [
                                'draft' => 'Draft',
                                'menunggu_persetujuan_kepala_gudang' => 'Menunggu Gudang',
                                'menunggu_persetujuan_kasir' => 'Menunggu Kasir',
                                'disetujui' => 'Disetujui',
                                'dikirim_ke_supplier' => 'Dikirim ke Supplier',
                                'dalam_pengiriman' => 'Dalam Pengiriman',
                                'diterima' => 'Diterima',
                                'ditolak' => 'Ditolak',
                            ];
                            
                            // Label status untuk PO eksternal
                            $statusLabelsExternal = [
                                'draft' => 'Draft',
                                'menunggu_persetujuan_kepala_gudang' => 'Menunggu Kepala Gudang',
                                'menunggu_persetujuan_kasir' => 'Menunggu Kasir',
                                'disetujui' => 'Disetujui',
                                'dikirim_ke_supplier' => 'Dikirim ke Supplier',
                                'dalam_pengiriman' => 'Dalam Pengiriman',
                                'diterima' => 'Diterima',
                                'ditolak' => 'Ditolak',
                            ];
                            
                            // Pilih label berdasarkan tipe PO
                            $statusLabels = $po->tipe_po == 'internal' ? $statusLabelsInternal : $statusLabelsExternal;
                        @endphp
                        <span class="badge {{ $statusColors[$po->status] ?? 'bg-secondary' }}">
                            <i class="ri-record-circle-line"></i> {{ $statusLabels[$po->status] ?? $po->status }}
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
                                @if($po->status === 'draft')
                                <li>
                                    <a class="dropdown-item" href="{{ route('po.edit', $po->id_po) }}">
                                        <i class="ri-pencil-fill me-2"></i>Edit
                                    </a>
                                </li>
                                <li>
                                    <button class="dropdown-item text-success" onclick="submitPO('{{ $po->id_po }}')">
                                        <i class="ri-send-plane-fill me-2"></i>Submit
                                    </button>
                                </li>
                                @endif
                                @if(in_array($po->status, ['draft', 'ditolak']))
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <button class="dropdown-item text-danger" onclick="deletePO('{{ $po->id_po }}')">
                                        <i class="ri-delete-bin-6-line me-2"></i>Hapus
                                    </button>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <i class="ri-file-list-3-line ri-3x text-muted d-block mb-3"></i>
                        <p class="text-muted mb-0">Belum ada data Purchase Order</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>