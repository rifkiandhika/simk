<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\DetailPenjualanBebas;
use App\Models\DetailstockApotik;
use App\Models\HistoryStockApotik;
use App\Models\PenjualanBebas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenjualanBebasController extends Controller
{
    public function index(Request $request)
    {
        $query = PenjualanBebas::with(['details', 'user'])
            ->orderBy('tanggal_transaksi', 'desc');

        // Filter tanggal
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereBetween('tanggal_transaksi', [
                $request->tanggal_dari . ' 00:00:00',
                $request->tanggal_sampai . ' 23:59:59'
            ]);
        }

        // Filter search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_transaksi', 'like', "%{$search}%")
                    ->orWhere('nama_pasien', 'like', "%{$search}%")
                    ->orWhere('no_rm_pasien', 'like', "%{$search}%");
            });
        }

        $penjualan = $query->paginate(20);

        // Statistik
        $today = Carbon::today();
        $stats = [
            'total_transaksi' => PenjualanBebas::count(),
            'transaksi_hari_ini' => PenjualanBebas::whereDate('tanggal_transaksi', $today)->count(),
            'pendapatan_hari_ini' => PenjualanBebas::whereDate('tanggal_transaksi', $today)->sum('total'),
            'pendapatan_bulan_ini' => PenjualanBebas::whereMonth('tanggal_transaksi', $today->month)
                ->whereYear('tanggal_transaksi', $today->year)->sum('total')
        ];

        // Untuk request AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $penjualan,
                'stats' => $stats
            ]);
        }

        return view('penjualan_bebas.index', compact('penjualan', 'stats'));
    }

    /**
     * Halaman form tambah transaksi
     */
    public function create()
    {
        return view('penjualan_bebas.create');
    }

    /**
     * API: Search obat untuk autocomplete
     */
    public function searchObat(Request $request)
    {
        $search = $request->get('q', '');

        $obat = DetailStockApotik::with(['detailSupplier', 'stockApotik.gudang'])
            ->where('stock_apotik', '>', 0)
            ->whereHas('detailSupplier', function ($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('merk', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama' => $item->detailSupplier->nama ?? '-',
                    'merk' => $item->detailSupplier->merk ?? '-',
                    'satuan' => $item->detailSupplier->satuan ?? '-',
                    'no_batch' => $item->no_batch,
                    'stock' => $item->stock_apotik,
                    'harga' => $item->detailSupplier->harga_jual ?? 0,
                    'exp_date' => $item->tanggal_kadaluarsa ? Carbon::parse($item->tanggal_kadaluarsa)->format('d/m/Y') : '-',
                    'gudang' => $item->stockApotik->gudang->nama_gudang ?? '-'
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $obat
        ]);
    }

    /**
     * API: Simpan transaksi penjualan bebas
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_pasien' => 'required|string|max:255',
            'metode_pembayaran' => 'required|in:tunai,transfer,debit,kredit',
            'bayar' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.detail_stock_apotik_id' => 'required|exists:detail_stock_apotiks,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Validasi stock untuk semua item
            foreach ($request->items as $item) {
                $detailStock = DetailStockApotik::findOrFail($item['detail_stock_apotik_id']);

                if ($detailStock->stock_apotik < $item['jumlah']) {
                    throw new \Exception("Stock tidak mencukupi untuk {$detailStock->detailSupplier->nama}. Stock tersedia: {$detailStock->stock_apotik}");
                }
            }

            // Create penjualan
            $penjualan = PenjualanBebas::create([
                'no_rm_pasien' => $request->no_rm_pasien,
                'nama_pasien' => $request->nama_pasien,
                'alamat' => $request->alamat,
                'no_hp' => $request->no_hp,
                // 'user_id' => Auth::id(),
                'subtotal' => $request->subtotal ?? 0,
                'diskon' => $request->diskon ?? 0,
                'pajak' => $request->pajak ?? 0,
                'total' => $request->total ?? 0,
                'bayar' => $request->bayar ?? 0,
                'kembalian' => $request->kembalian ?? 0,
                'metode_pembayaran' => $request->metode_pembayaran,
                'keterangan' => $request->keterangan,
                'tanggal_transaksi' => now(),
            ]);

            // Process items
            foreach ($request->items as $item) {
                $detailStock = DetailStockApotik::findOrFail($item['detail_stock_apotik_id']);

                // Create detail penjualan
                DetailPenjualanBebas::create([
                    'penjualan_bebas_id' => $penjualan->id,
                    'detail_stock_apotik_id' => $detailStock->id,
                    'nama_obat' => $item['nama_obat'],
                    'no_batch' => $detailStock->no_batch,
                    'jumlah' => $item['jumlah'],
                    'satuan' => $item['satuan'],
                    'harga_satuan' => $item['harga_satuan'],
                    'diskon_item' => $item['diskon_item'] ?? 0,
                    'subtotal' => $item['subtotal'],
                ]);

                // Update stock
                $stockAwal = $detailStock->stock_apotik;
                $detailStock->stock_apotik -= $item['jumlah'];
                $detailStock->save();

                // Create history
                HistoryStockApotik::create([
                    'detail_stock_apotik_id' => $detailStock->id,
                    'kode_referensi' => $penjualan->kode_transaksi,
                    'jenis_transaksi' => 'penjualan_bebas',
                    'jumlah_keluar' => $item['jumlah'],
                    'jumlah_masuk' => 0,
                    'stock_awal' => $stockAwal,
                    'stock_akhir' => $detailStock->stock_apotik,
                    'keterangan' => "Penjualan Bebas - {$request->nama_pasien}",
                    // 'user_id' => Auth::id(),
                    'tanggal_transaksi' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan',
                'data' => $penjualan->load('details')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Detail transaksi
     */
    public function show($id)
    {
        $penjualan = PenjualanBebas::with(['details.detailStockApotik.detailSupplier', 'user'])
            ->findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $penjualan
            ]);
        }

        return view('penjualan_bebas.show', compact('penjualan'));
    }

    /**
     * Halaman print struk
     */
    public function print($id)
    {
        $penjualan = PenjualanBebas::with(['details.detailStockApotik.detailSupplier', 'user'])
            ->findOrFail($id);

        return view('penjualan_bebas.print', compact('penjualan'));
    }

    /**
     * API: Hapus transaksi (batalkan)
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $penjualan = PenjualanBebas::with('details')->findOrFail($id);

            // Kembalikan stock
            foreach ($penjualan->details as $detail) {
                $detailStock = DetailstockApotik::find($detail->detail_stock_apotik_id);
                if ($detailStock) {
                    $stockAwal = $detailStock->stock_apotik;
                    $detailStock->stock_apotik += $detail->jumlah;
                    $detailStock->save();

                    // Create history
                    HistoryStockApotik::create([
                        'detail_stock_apotik_id' => $detailStock->id,
                        'kode_referensi' => $penjualan->kode_transaksi . ' (BATAL)',
                        'jenis_transaksi' => 'penyesuaian',
                        'jumlah_keluar' => 0,
                        'jumlah_masuk' => $detail->jumlah,
                        'stock_awal' => $stockAwal,
                        'stock_akhir' => $detailStock->stock_apotik,
                        'keterangan' => 'Pembatalan Transaksi Penjualan Bebas',
                        // 'user_id' => Auth::id(),
                        'tanggal_transaksi' => now(),
                    ]);
                }
            }

            $penjualan->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibatalkan dan stock dikembalikan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get history transaksi
     */
    public function history(Request $request)
    {
        $query = HistoryStockApotik::with(['detailStockApotik.detailSupplier', 'user'])
            ->where('jenis_transaksi', 'penjualan_bebas')
            ->orderBy('tanggal_transaksi', 'desc');

        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereBetween('tanggal_transaksi', [
                $request->tanggal_dari . ' 00:00:00',
                $request->tanggal_sampai . ' 23:59:59'
            ]);
        }

        $history = $query->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }
}
