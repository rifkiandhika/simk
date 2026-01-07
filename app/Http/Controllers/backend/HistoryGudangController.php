<?php

namespace App\Http\Controllers\backend;

use App\Exports\HistoryGudangExport;
use App\Http\Controllers\Controller;
use App\Models\Gudang;
use App\Models\HistoryGudang;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class HistoryGudangController extends Controller
{
    public function index($gudangId)
    {
        // Get gudang data
        $gudang = Gudang::findOrFail($gudangId);

        // Get histories dengan pagination (default: bulan ini)
        $histories = HistoryGudang::with(['supplier', 'barang', 'purchaseOrder'])
            ->where('gudang_id', $gudangId)
            ->whereBetween('waktu_proses', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->orderBy('waktu_proses', 'desc')
            ->paginate(50);

        // Calculate summary untuk bulan ini
        $totalPenerimaan = HistoryGudang::where('gudang_id', $gudangId)
            ->where('status', 'penerimaan')
            ->whereBetween('waktu_proses', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->sum('jumlah');

        $totalPengiriman = HistoryGudang::where('gudang_id', $gudangId)
            ->where('status', 'pengiriman')
            ->whereBetween('waktu_proses', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->sum('jumlah');

        $totalTransaksi = HistoryGudang::where('gudang_id', $gudangId)
            ->whereBetween('waktu_proses', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->count();

        return view('gudang.history', compact(
            'gudang',
            'histories',
            'totalPenerimaan',
            'totalPengiriman',
            'totalTransaksi'
        ));
    }

    /**
     * Filter history gudang (AJAX)
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filter(Request $request)
    {
        // Validate request
        $request->validate([
            'gudang_id' => 'required|string',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
            'status' => 'nullable|in:penerimaan,pengiriman'
        ]);

        $query = HistoryGudang::with(['supplier', 'barang', 'gudang', 'purchaseOrder'])
            ->where('gudang_id', $request->gudang_id);

        // Filter tanggal
        if ($request->tanggal_mulai && $request->tanggal_akhir) {
            $query->whereBetween('waktu_proses', [
                Carbon::parse($request->tanggal_mulai)->startOfDay(),
                Carbon::parse($request->tanggal_akhir)->endOfDay()
            ]);
        }

        // Filter status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $histories = $query->orderBy('waktu_proses', 'desc')->get();

        // Format data untuk response
        $formattedHistories = $histories->map(function ($history) {
            $waktuProses = Carbon::parse($history->waktu_proses);

            return [
                'id' => $history->id,
                'waktu_proses' => $history->waktu_proses,
                'waktu_proses_formatted' => $waktuProses->format('d/m/Y') . '<br><small class="text-muted"><i class="ri-time-line"></i> ' . $waktuProses->format('H:i:s') . '</small>',
                'status' => $history->status,
                'referensi_type' => $history->referensi_type,
                'no_referensi' => $history->no_referensi,
                'no_batch' => $history->no_batch,
                'barang_nama' => $history->barang->nama_barang ?? '-',
                'jumlah' => $history->jumlah,
                'jumlah_formatted' => number_format($history->jumlah, 0, ',', '.'),
                'supplier_nama' => $history->supplier->nama ?? '-',
                'keterangan' => $history->keterangan ? (strlen($history->keterangan) > 50 ? substr($history->keterangan, 0, 50) . '...' : $history->keterangan) : '-'
            ];
        });

        // Hitung summary
        $totalPenerimaan = $histories->where('status', 'penerimaan')->sum('jumlah');
        $totalPengiriman = $histories->where('status', 'pengiriman')->sum('jumlah');

        $summary = [
            'total_penerimaan' => number_format($totalPenerimaan, 0, ',', '.'),
            'total_pengiriman' => number_format($totalPengiriman, 0, ',', '.'),
            'total_transaksi' => $histories->count(),
            'saldo' => number_format($totalPenerimaan - $totalPengiriman, 0, ',', '.')
        ];

        return response()->json([
            'success' => true,
            'histories' => $formattedHistories,
            'summary' => $summary,
            'message' => 'Data berhasil difilter'
        ]);
    }

    /**
     * Export to Excel
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel(Request $request)
    {
        $gudangId = $request->gudang_id;
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalAkhir = $request->tanggal_akhir;
        $status = $request->status;

        // Get gudang info
        $gudang = Gudang::find($gudangId);

        // Generate filename
        $gudangNama = $gudang ? str_replace(' ', '_', $gudang->nama) : 'Export';
        $filename = 'History_Gudang_' . $gudangNama . '_' . date('YmdHis') . '.xlsx';

        try {
            return Excel::download(
                new HistoryGudangExport($gudangId, $tanggalMulai, $tanggalAkhir, $status),
                $filename
            );
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal export Excel: ' . $e->getMessage());
        }
    }

    /**
     * Export to PDF
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Request $request)
    {
        $query = HistoryGudang::with(['supplier', 'barang', 'gudang', 'purchaseOrder'])
            ->where('gudang_id', $request->gudang_id);

        // Filter tanggal
        if ($request->tanggal_mulai && $request->tanggal_akhir) {
            $query->whereBetween('waktu_proses', [
                Carbon::parse($request->tanggal_mulai)->startOfDay(),
                Carbon::parse($request->tanggal_akhir)->endOfDay()
            ]);
        }

        // Filter status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $histories = $query->orderBy('waktu_proses', 'desc')->get();
        $gudang = Gudang::find($request->gudang_id);

        // Calculate summary data
        $totalPenerimaan = $histories->where('status', 'penerimaan')->sum('jumlah');
        $totalPengiriman = $histories->where('status', 'pengiriman')->sum('jumlah');

        $summary = [
            'total_penerimaan' => $totalPenerimaan,
            'total_pengiriman' => $totalPengiriman,
            'total_transaksi' => $histories->count(),
            'saldo' => $totalPenerimaan - $totalPengiriman
        ];

        // Prepare data for PDF
        $data = [
            'histories' => $histories,
            'gudang' => $gudang,
            'tanggal_mulai' => $request->tanggal_mulai ? Carbon::parse($request->tanggal_mulai)->format('d/m/Y') : '-',
            'tanggal_akhir' => $request->tanggal_akhir ? Carbon::parse($request->tanggal_akhir)->format('d/m/Y') : '-',
            'summary' => $summary,
            'tanggal_cetak' => Carbon::now()->format('d/m/Y H:i:s')
        ];

        try {
            $pdf = Pdf::loadView('gudang.pdf.history-gudang', $data)
                ->setPaper('a4', 'landscape')
                ->setOption('margin-top', 10)
                ->setOption('margin-right', 10)
                ->setOption('margin-bottom', 10)
                ->setOption('margin-left', 10);

            $gudangNama = $gudang ? str_replace(' ', '_', $gudang->nama) : 'Export';
            $filename = 'History_Gudang_' . $gudangNama . '_' . date('YmdHis') . '.pdf';

            return $pdf->stream($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal export PDF: ' . $e->getMessage());
        }
    }

    /**
     * Get history detail
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $history = HistoryGudang::with(['supplier', 'barang', 'gudang', 'purchaseOrder'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $history->id,
                    'gudang_id' => $history->gudang_id,
                    'gudang_nama' => $history->gudang->nama ?? '-',
                    'barang_id' => $history->barang_id,
                    'barang_nama' => $history->barang->nama_barang ?? '-',
                    'status' => $history->status,
                    'jumlah' => $history->jumlah,
                    'jumlah_formatted' => number_format($history->jumlah, 0, ',', '.'),
                    'waktu_proses' => $history->waktu_proses->format('d/m/Y H:i:s'),
                    'referensi_type' => $history->referensi_type,
                    'referensi_id' => $history->referensi_id,
                    'no_referensi' => $history->no_referensi,
                    'no_batch' => $history->no_batch,
                    'supplier_id' => $history->supplier_id,
                    'supplier_nama' => $history->supplier->nama ?? '-',
                    'keterangan' => $history->keterangan,
                    'created_at' => $history->created_at->format('d/m/Y H:i:s'),
                    'updated_at' => $history->updated_at->format('d/m/Y H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Get summary statistik
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function summary(Request $request)
    {
        $gudangId = $request->gudang_id;
        $startDate = $request->tanggal_mulai ? Carbon::parse($request->tanggal_mulai)->startOfDay() : null;
        $endDate = $request->tanggal_akhir ? Carbon::parse($request->tanggal_akhir)->endOfDay() : null;

        $query = HistoryGudang::where('gudang_id', $gudangId);

        if ($startDate && $endDate) {
            $query->whereBetween('waktu_proses', [$startDate, $endDate]);
        }

        // Total Penerimaan
        $totalPenerimaan = (clone $query)->where('status', 'penerimaan')->sum('jumlah');

        // Total Pengiriman
        $totalPengiriman = (clone $query)->where('status', 'pengiriman')->sum('jumlah');

        // Total Transaksi
        $totalTransaksi = (clone $query)->count();

        // Transaksi Hari Ini
        $transaksiHariIni = HistoryGudang::where('gudang_id', $gudangId)
            ->whereDate('waktu_proses', today())
            ->count();

        // Transaksi Bulan Ini
        $transaksiBulanIni = HistoryGudang::where('gudang_id', $gudangId)
            ->whereMonth('waktu_proses', date('m'))
            ->whereYear('waktu_proses', date('Y'))
            ->count();

        // Saldo Stok
        $saldo = $totalPenerimaan - $totalPengiriman;

        return response()->json([
            'success' => true,
            'summary' => [
                'total_penerimaan' => number_format($totalPenerimaan, 0, ',', '.'),
                'total_pengiriman' => number_format($totalPengiriman, 0, ',', '.'),
                'total_transaksi' => $totalTransaksi,
                'transaksi_hari_ini' => $transaksiHariIni,
                'transaksi_bulan_ini' => $transaksiBulanIni,
                'saldo' => number_format($saldo, 0, ',', '.'),
                'saldo_raw' => $saldo
            ]
        ]);
    }

    /**
     * Get monthly chart data (optional - untuk chart)
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function chartData(Request $request)
    {
        $gudangId = $request->gudang_id;
        $year = $request->year ?? date('Y');

        $data = [];

        for ($month = 1; $month <= 12; $month++) {
            $penerimaan = HistoryGudang::where('gudang_id', $gudangId)
                ->where('status', 'penerimaan')
                ->whereYear('waktu_proses', $year)
                ->whereMonth('waktu_proses', $month)
                ->sum('jumlah');

            $pengiriman = HistoryGudang::where('gudang_id', $gudangId)
                ->where('status', 'pengiriman')
                ->whereYear('waktu_proses', $year)
                ->whereMonth('waktu_proses', $month)
                ->sum('jumlah');

            $data[] = [
                'month' => Carbon::create($year, $month, 1)->format('M Y'),
                'penerimaan' => $penerimaan,
                'pengiriman' => $pengiriman,
                'saldo' => $penerimaan - $pengiriman
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
