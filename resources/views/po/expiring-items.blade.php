{{-- resources/views/po/expiring-items.blade.php --}}
@extends('layouts.app')

@section('title', 'Item Mendekati Kadaluarsa')
@section('page-title', 'Item Kadaluarsa')

@section('content')
<div class="space-y-6">
    
    {{-- Header & Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-red-50 border border-red-200 rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-600 text-sm font-medium">Kritis (< 30 hari)</p>
                    <p class="text-3xl font-bold text-red-700 mt-2">{{ $stats['critical'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-600 text-sm font-medium">Warning (< 90 hari)</p>
                    <p class="text-3xl font-bold text-yellow-700 mt-2">{{ $stats['warning'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Expired</p>
                    <p class="text-3xl font-bold text-gray-700 mt-2">{{ $stats['expired'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-times-circle text-gray-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Filter --}}
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter</label>
                <select id="filterLevel" onchange="filterItems()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="all">Semua</option>
                    <option value="critical">Kritis (< 30 hari)</option>
                    <option value="warning">Warning (< 90 hari)</option>
                    <option value="expired">Expired</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Produk</label>
                <input type="text" id="searchProduct" onkeyup="filterItems()"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Nama produk atau batch...">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                <select id="sortBy" onchange="filterItems()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="date">Tanggal Kadaluarsa</option>
                    <option value="name">Nama Produk</option>
                    <option value="qty">Quantity</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button onclick="exportToExcel()" 
                        class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-file-excel mr-2"></i> Export
                </button>
            </div>
        </div>
    </div>
    
    {{-- Items Table --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="expiringItemsTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Produk</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Batch</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Qty</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No PO</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal Kadaluarsa</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($items as $item)
                    <tr class="hover:bg-gray-50 transition item-row" 
                        data-level="{{ $item->expiry_level }}"
                        data-name="{{ strtolower($item->nama_produk) }}"
                        data-date="{{ $item->tanggal_kadaluarsa->format('Y-m-d') }}"
                        data-qty="{{ $item->qty_diterima }}">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-800">{{ $item->nama_produk }}</p>
                            <p class="text-xs text-gray-500 mt-1">ID: {{ $item->id_produk }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-mono bg-gray-100 px-2 py-1 rounded">
                                {{ $item->batch_number ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm font-bold text-gray-800">{{ $item->qty_diterima }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('po.show', $item->purchaseOrder->id_po) }}" 
                               class="text-blue-600 hover:underline text-sm font-medium">
                                {{ $item->purchaseOrder->no_po }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-semibold {{ $item->expiry_level === 'critical' ? 'text-red-600' : ($item->expiry_level === 'warning' ? 'text-yellow-600' : 'text-gray-800') }}">
                                {{ $item->tanggal_kadaluarsa->format('d/m/Y') }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">{{ $item->days_until_expiry }} hari lagi</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($item->expiry_level === 'expired')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                Expired
                            </span>
                            @elseif($item->expiry_level === 'critical')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-exclamation-triangle mr-1"></i> Kritis
                            </span>
                            @elseif($item->expiry_level === 'warning')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                <i class="fas fa-exclamation-circle mr-1"></i> Warning
                            </span>
                            @else
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Aman
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button onclick="showItemDetail('{{ $item->id_po_item }}')" 
                                    class="text-blue-600 hover:text-blue-800" title="Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <i class="fas fa-check-circle text-green-400 text-5xl mb-4"></i>
                            <p class="text-gray-500 text-lg">Tidak ada item yang akan kadaluarsa</p>
                            <p class="text-gray-400 text-sm mt-2">Semua item dalam kondisi aman</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
function filterItems() {
    const filterLevel = document.getElementById('filterLevel').value;
    const searchQuery = document.getElementById('searchProduct').value.toLowerCase();
    const sortBy = document.getElementById('sortBy').value;
    const rows = document.querySelectorAll('.item-row');
    
    let visibleRows = Array.from(rows).filter(row => {
        const level = row.dataset.level;
        const name = row.dataset.name;
        
        const levelMatch = filterLevel === 'all' || level === filterLevel;
        const searchMatch = name.includes(searchQuery);
        
        if (levelMatch && searchMatch) {
            row.style.display = '';
            return true;
        } else {
            row.style.display = 'none';
            return false;
        }
    });
    
    // Sort
    if (sortBy === 'date') {
        visibleRows.sort((a, b) => a.dataset.date.localeCompare(b.dataset.date));
    } else if (sortBy === 'name') {
        visibleRows.sort((a, b) => a.dataset.name.localeCompare(b.dataset.name));
    } else if (sortBy === 'qty') {
        visibleRows.sort((a, b) => parseInt(b.dataset.qty) - parseInt(a.dataset.qty));
    }
    
    const tbody = document.querySelector('#expiringItemsTable tbody');
    visibleRows.forEach(row => tbody.appendChild(row));
}

function exportToExcel() {
    window.location.href = '{{ route("po.expiring-items.export") }}';
}

function showItemDetail(itemId) {
    // Implement detail modal if needed
    console.log('Show detail for item:', itemId);
}
</script>
@endpush
@endsection