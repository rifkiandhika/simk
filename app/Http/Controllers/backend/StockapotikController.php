<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\DetailGudang;
use App\Models\DetailstockApotik;
use App\Models\DetailSupplier;
use App\Models\Gudang;
use App\Models\HistoryStockApotik;
use App\Models\StockApotik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StockapotikController extends Controller
{
    public function index(Request $request)
    {
        // Eager load detailSupplier (via obat relationship)
        $query = StockApotik::with(['gudang.supplier', 'details.detailSupplier']);

        // Filter by date range (for day sorting buttons)
        if ($request->has('filter') && $request->filter) {
            $filter = $request->filter;
            $query->where('tanggal_penerimaan', '>=', match ($filter) {
                'today' => now()->startOfDay(),
                '7d' => now()->subDays(7),
                '2w' => now()->subWeeks(2),
                '1m' => now()->subMonth(),
                '3m' => now()->subMonths(3),
                '6m' => now()->subMonths(6),
                '1y' => now()->subYear(),
                default => now()->startOfDay()
            });
        }

        // Filter by gudang
        if ($request->gudang_id) {
            $query->where('gudang_id', $request->gudang_id);
        }

        // Filter by date
        if ($request->date) {
            $query->whereDate('tanggal_penerimaan', $request->date);
        }

        $stocks = $query->orderBy('tanggal_penerimaan', 'desc')->paginate(25);

        // Get statistics
        $totalItems = DetailStockApotik::sum('stock_apotik');
        $totalRetur = DetailStockApotik::sum('retur');

        // Get gudangs with supplier for filter
        $gudangs = Gudang::with('supplier')
            ->where('status', 'Aktif')
            ->get();

        // Count unique suppliers
        $totalSuppliers = $gudangs->pluck('supplier_id')->unique()->count();

        return view('stockapotik.index', compact(
            'stocks',
            'totalItems',
            'totalRetur',
            'totalSuppliers',
            'gudangs'
        ));
    }

    public function create()
    {
        $gudangs = Gudang::with('supplier')
            ->where('status', 'Aktif')
            ->whereNotNull('supplier_id')
            ->get();

        return view('stockapotik.create', compact('gudangs'));
    }

    public function getGudangDetails($gudangId)
    {
        try {
            $details = DetailGudang::where('gudang_id', $gudangId)
                ->where('kondisi', 'Baik')
                ->where('stock_gudang', '>', 0)
                ->get();

            if ($details->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada barang tersedia',
                    'data' => []
                ]);
            }

            $mapped = $details->map(function ($detail) {
                // Ambil DetailSupplier berdasarkan barang_id
                $detailSupplier = DetailSupplier::find($detail->barang_id);

                if (!$detailSupplier) {
                    Log::warning("DetailSupplier not found for DetailGudang ID: {$detail->id}, barang_id: {$detail->barang_id}");
                    return null;
                }

                return [
                    'id' => $detail->id,
                    'detail_supplier_id' => $detailSupplier->id,
                    'barang_type' => strtolower($detailSupplier->jenis),
                    'barang_id' => $detail->barang_id,
                    'no_batch' => $detail->no_batch,
                    'judul' => $detailSupplier->judul,
                    'nama' => $detailSupplier->nama,
                    'jenis' => $detailSupplier->jenis,
                    'merk' => $detailSupplier->merk ?? '-',
                    'satuan' => $detailSupplier->satuan,
                    'exp_date' => $detail->tanggal_kadaluarsa ?? $detailSupplier->exp_date,
                    'stock_gudang' => $detail->stock_gudang,
                    'min_persediaan' => $detail->min_persediaan ?? $detailSupplier->min_persediaan ?? 0,
                    'lokasi_rak' => $detail->lokasi_rak ?? $detailSupplier->kode_rak ?? '-',
                ];
            })
                ->filter()
                ->values();

            return response()->json([
                'success' => true,
                'total' => $mapped->count(),
                'data' => $mapped
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getGudangDetails: ' . $e->getMessage());
            Log::error('Stack: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'gudang_id' => 'required|exists:gudangs,id',
            'tanggal_penerimaan' => 'required|date',
            'keterangan' => 'nullable|string',
            'detail_gudang_id' => 'required|array|min:1',
            'detail_gudang_id.*' => 'required|exists:detail_gudangs,id',
            'stock_apotik' => 'required|array|min:1',
            'stock_apotik.*' => 'required|integer|min:1',
            'retur' => 'nullable|array',
            'retur.*' => 'nullable|integer|min:0',
            'min_persediaan' => 'nullable|array',
            'min_persediaan.*' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Generate kode transaksi
            $lastStock = StockApotik::whereDate('created_at', today())->latest()->first();
            $counter = $lastStock ? intval(substr($lastStock->kode_transaksi, -4)) + 1 : 1;
            $kodeTransaksi = 'SA-' . date('Ymd') . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT);

            // Create stock apotik header
            $stockApotik = StockApotik::create([
                'id' => Str::uuid(),
                'gudang_id' => $request->gudang_id,
                'kode_transaksi' => $kodeTransaksi,
                'tanggal_penerimaan' => $request->tanggal_penerimaan,
                'keterangan' => $request->keterangan,
            ]);

            // Create detail stock apotik
            foreach ($request->detail_gudang_id as $index => $detailGudangId) {
                $detailGudang = DetailGudang::findOrFail($detailGudangId);

                // Ambil detail_supplier_id dari detail_gudang
                $detailSupplierId = $detailGudang->barang_id;

                // Validasi bahwa detail supplier exists
                $detailSupplier = DetailSupplier::find($detailSupplierId);
                if (!$detailSupplier) {
                    throw new \Exception("Detail supplier tidak ditemukan untuk barang batch {$detailGudang->no_batch}");
                }

                $stockApotikQty = $request->stock_apotik[$index] ?? 0;
                $returQty = $request->retur[$index] ?? 0;

                // Validate stock
                if ($stockApotikQty > $detailGudang->stock_gudang) {
                    throw new \Exception("Stock apotik untuk batch {$detailGudang->no_batch} melebihi stock gudang");
                }

                $detailStock = DetailStockApotik::create([
                    'id' => Str::uuid(),
                    'stock_apotik_id' => $stockApotik->id,
                    'obat_id' => $detailSupplierId, // Simpan detail_supplier_id di kolom obat_id
                    'no_batch' => $detailGudang->no_batch,
                    'stock_apotik' => $stockApotikQty,
                    'min_persediaan' => $request->min_persediaan[$index] ?? 0,
                    'retur' => $returQty,
                    'tanggal_kadaluarsa' => $detailGudang->tanggal_kadaluarsa ?? $detailSupplier->exp_date,
                ]);

                // Create history for penerimaan
                HistoryStockApotik::create([
                    'id' => Str::uuid(),
                    'detail_apotik_id' => $detailStock->id,
                    'jumlah' => $stockApotikQty,
                    'waktu_proses' => now(),
                    'status' => 'penerimaan',
                    'keterangan' => "Penerimaan stock dari gudang - {$kodeTransaksi}",
                ]);

                // Create history for retur if any
                if ($returQty > 0) {
                    HistoryStockApotik::create([
                        'id' => Str::uuid(),
                        'detail_apotik_id' => $detailStock->id,
                        'jumlah' => $returQty,
                        'waktu_proses' => now(),
                        'status' => 'retur',
                        'keterangan' => "Retur barang - {$kodeTransaksi}",
                    ]);
                }

                // Update stock gudang
                $detailGudang->decrement('stock_gudang', $stockApotikQty);
            }

            DB::commit();

            return redirect()->route('stock_apotiks.index')
                ->with('success', 'Stock apotik berhasil ditambahkan dengan kode: ' . $kodeTransaksi);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $stock = StockApotik::with(['gudang.supplier', 'details.detailSupplier'])->findOrFail($id);
        return view('stockapotik.show', compact('stock'));
    }

    public function edit($id)
    {
        $stock = StockApotik::with(['gudang.supplier', 'details.detailSupplier'])->findOrFail($id);

        $gudangs = Gudang::with('supplier')
            ->where('status', 'Aktif')
            ->whereNotNull('supplier_id')
            ->get();

        return view('stockapotik.edit', compact('stock', 'gudangs'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'gudang_id' => 'required|exists:gudangs,id',
            'tanggal_penerimaan' => 'required|date',
            'keterangan' => 'nullable|string',
            'detail_gudang_id' => 'nullable|array',
            'detail_gudang_id.*' => 'nullable|exists:detail_gudangs,id',
            'stock_apotik' => 'nullable|array',
            'stock_apotik.*' => 'nullable|integer|min:1',
            'retur' => 'nullable|array',
            'retur.*' => 'nullable|integer|min:0',
            'min_persediaan' => 'nullable|array',
            'min_persediaan.*' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $stock = StockApotik::findOrFail($id);

            // Update header
            $stock->update([
                'tanggal_penerimaan' => $request->tanggal_penerimaan,
                'keterangan' => $request->keterangan,
            ]);

            // Process new items if any
            if ($request->has('detail_gudang_id') && is_array($request->detail_gudang_id)) {
                foreach ($request->detail_gudang_id as $index => $detailGudangId) {
                    $detailGudang = DetailGudang::findOrFail($detailGudangId);

                    // Ambil detail_supplier_id dari detail_gudang
                    $detailSupplierId = $detailGudang->barang_id;

                    // Validasi bahwa detail supplier exists
                    $detailSupplier = DetailSupplier::find($detailSupplierId);
                    if (!$detailSupplier) {
                        throw new \Exception("Detail supplier tidak ditemukan untuk barang batch {$detailGudang->no_batch}");
                    }

                    $stockApotikQty = $request->stock_apotik[$index] ?? 0;
                    $returQty = $request->retur[$index] ?? 0;

                    // Validate stock
                    if ($stockApotikQty > $detailGudang->stock_gudang) {
                        throw new \Exception("Stock apotik untuk batch {$detailGudang->no_batch} melebihi stock gudang");
                    }

                    $detailStock = DetailStockApotik::create([
                        'id' => Str::uuid(),
                        'stock_apotik_id' => $stock->id,
                        'obat_id' => $detailSupplierId, // Simpan detail_supplier_id di kolom obat_id
                        'no_batch' => $detailGudang->no_batch,
                        'stock_apotik' => $stockApotikQty,
                        'min_persediaan' => $request->min_persediaan[$index] ?? 0,
                        'retur' => $returQty,
                        'tanggal_kadaluarsa' => $detailGudang->tanggal_kadaluarsa ?? $detailSupplier->exp_date,
                    ]);

                    // Create history for penerimaan
                    HistoryStockApotik::create([
                        'id' => Str::uuid(),
                        'detail_apotik_id' => $detailStock->id,
                        'jumlah' => $stockApotikQty,
                        'waktu_proses' => now(),
                        'status' => 'penerimaan',
                        'keterangan' => "Penambahan stock (Edit) - {$stock->kode_transaksi}",
                    ]);

                    // Create history for retur if any
                    if ($returQty > 0) {
                        HistoryStockApotik::create([
                            'id' => Str::uuid(),
                            'detail_apotik_id' => $detailStock->id,
                            'jumlah' => $returQty,
                            'waktu_proses' => now(),
                            'status' => 'retur',
                            'keterangan' => "Retur barang (Edit) - {$stock->kode_transaksi}",
                        ]);
                    }

                    // Update stock gudang
                    $detailGudang->decrement('stock_gudang', $stockApotikQty);
                }
            }

            DB::commit();

            return redirect()->route('stock_apotiks.index')
                ->with('success', 'Stock apotik berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $stock = StockApotik::with('details')->findOrFail($id);

            // Return stock to gudang
            foreach ($stock->details as $detail) {
                // Find corresponding detail_gudang
                $detailGudang = DetailGudang::where('gudang_id', $stock->gudang_id)
                    ->where('no_batch', $detail->no_batch)
                    ->first();

                if ($detailGudang) {
                    $detailGudang->increment('stock_gudang', $detail->stock_apotik);
                }
            }

            $stock->delete();

            DB::commit();

            return redirect()->route('stock_apotiks.index')
                ->with('success', 'Stock apotik berhasil dihapus dan stock gudang telah dikembalikan');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
