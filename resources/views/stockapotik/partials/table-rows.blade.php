@forelse($stocks as $x => $stock)
    @foreach($stock->details as $detail)
    <tr>
        <td class="text-center">{{ $stocks->firstItem() + $x }}</td>
        <td>
            <small class="text-muted">
                {{ $stock->created_at->format('d/m/Y H:i') }}
            </small>
        </td>
        <td>
            <strong>{{ $stock->supplier->supplier ?? '-' }}</strong>
        </td>
        <td>
            <span class="badge bg-info text-dark">{{ $detail->no_batch }}</span>
        </td>
        <td>
            <div>
                <strong>{{ $detail->nama }}</strong>
                @if($detail->merk && $detail->merk != '-')
                    <br><small class="text-muted">{{ $detail->merk }}</small>
                @endif
            </div>
        </td>
        <td>
            <span class="badge bg-secondary">{{ $detail->jenis }}</span>
        </td>
        <td class="text-center">{{ $detail->satuan }}</td>
        <td>
            @php
                $expDate = \Carbon\Carbon::parse($detail->exp_date ?? now());
                $daysUntilExpiry = now()->diffInDays($expDate, false);
                $badgeClass = 'bg-success';
                if ($daysUntilExpiry < 0) {
                    $badgeClass = 'bg-danger';
                } elseif ($daysUntilExpiry <= 30) {
                    $badgeClass = 'bg-warning';
                }
            @endphp
            <span class="badge {{ $badgeClass }}">
                {{ $expDate->format('d/m/Y') }}
            </span>
        </td>
        <td>
            <div class="text-center">
                <strong class="text-primary">{{ number_format($detail->stock_gudang ?? 0) }}</strong>
                @if($detail->min_persediaan > 0)
                    <br><small class="text-muted">Min: {{ $detail->min_persediaan }}</small>
                @endif
            </div>
        </td>
        <td>
            <div class="text-center">
                @php
                    $stockApotik = $detail->stock_apotik ?? 0;
                    $minPersediaan = $detail->min_persediaan ?? 0;
                    $stockClass = 'text-success';
                    if ($stockApotik <= 0) {
                        $stockClass = 'text-danger';
                    } elseif ($stockApotik <= $minPersediaan) {
                        $stockClass = 'text-warning';
                    }
                @endphp
                <strong class="{{ $stockClass }}">{{ number_format($stockApotik) }}</strong>
                @if($minPersediaan > 0)
                    <br><small class="text-muted">Min: {{ $minPersediaan }}</small>
                @endif
            </div>
        </td>
        <td class="text-center">
            @if($detail->retur > 0)
                <span class="badge bg-danger">{{ number_format($detail->retur) }}</span>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td class="text-center">
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ri-more-2-fill"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li>
                        <a href="{{ route('stock_apotiks.edit', $stock->id) }}" class="dropdown-item">
                            <i class="ri-pencil-fill me-2"></i>Edit
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('stock_apotiks.destroy', $stock->id) }}" method="POST" class="delete-confirm">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="ri-delete-bin-6-line me-2"></i>Hapus
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </td>
    </tr>
    @endforeach
@empty
    <tr>
        <td colspan="12" class="text-center py-5">
            <i class="ri-inbox-line ri-3x text-muted d-block mb-3"></i>
            <p class="text-muted mb-0">Tidak ada data yang sesuai dengan filter</p>
            <button type="button" class="btn btn-outline-secondary btn-sm mt-3" id="btnClearFilterFromEmpty">
                <i class="ri-refresh-line me-1"></i>Reset Filter
            </button>
        </td>
    </tr>
@endforelse