<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Retur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturReportController extends Controller
{
    /**
     * Get retur summary/statistics
     */
    public function summary(Request $request)
    {
        $query = Retur::query();

        // Filter by date range
        if ($request->has('tanggal_dari') && $request->has('tanggal_sampai')) {
            $query->whereBetween('tanggal_retur', [
                $request->tanggal_dari,
                $request->tanggal_sampai
            ]);
        }

        // Filter by type
        if ($request->has('tipe_retur')) {
            $query->where('tipe_retur', $request->tipe_retur);
        }

        $summary = [
            'total_retur' => $query->count(),
            'total_nilai' => $query->sum('total_nilai_retur'),
            'by_status' => $query->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get(),
            'by_alasan' => $query->select('alasan_retur', DB::raw('count(*) as total'))
                ->groupBy('alasan_retur')
                ->get(),
            'by_tipe' => $query->select('tipe_retur', DB::raw('count(*) as total'))
                ->groupBy('tipe_retur')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }

    /**
     * Get detailed retur report
     */
    public function detailReport(Request $request)
    {
        $query = Retur::with([
            'returItems',
            'karyawanPelapor',
            'supplier'
        ]);

        // Apply filters
        if ($request->has('tanggal_dari') && $request->has('tanggal_sampai')) {
            $query->whereBetween('tanggal_retur', [
                $request->tanggal_dari,
                $request->tanggal_sampai
            ]);
        }

        if ($request->has('tipe_retur')) {
            $query->where('tipe_retur', $request->tipe_retur);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $returs = $query->orderBy('tanggal_retur', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $returs
        ]);
    }
}
