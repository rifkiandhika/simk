<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Services\TagihanPembayaranService;
use Exception;
use Illuminate\Http\Request;

class TagihanPembayaranController extends Controller
{
    protected $pembayaranService;

    public function __construct(TagihanPembayaranService $pembayaranService)
    {
        $this->pembayaranService = $pembayaranService;
    }

    /**
     * Proses pembayaran baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_tagihan' => 'required|exists:tagihans,id_tagihan',
            'tanggal_bayar' => 'nullable|date',
            'jumlah_bayar' => 'required|numeric|min:0.01',
            'metode' => 'required|in:TUNAI,DEBIT,CREDIT,TRANSFER,BPJS,ASURANSI',
            'no_referensi' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string|max:500',
        ]);

        try {
            $result = $this->pembayaranService->prosesPembayaran($validated);

            return response()->json($result, 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Batalkan pembayaran
     */
    public function destroy(Request $request, $id)
    {
        $validated = $request->validate([
            'alasan' => 'required|string|min:10|max:500'
        ]);

        try {
            $result = $this->pembayaranService->batalkanPembayaran($id, $validated['alasan']);

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Lock tagihan
     */
    public function lock($id)
    {
        try {
            $result = $this->pembayaranService->lockTagihan($id);

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Unlock tagihan
     */
    public function unlock(Request $request, $id)
    {
        $validated = $request->validate([
            'alasan' => 'required|string|min:10|max:500'
        ]);

        try {
            $result = $this->pembayaranService->unlockTagihan($id, $validated['alasan']);

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get ringkasan pembayaran
     */
    public function show($id)
    {
        try {
            $result = $this->pembayaranService->getRingkasanPembayaran($id);

            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }
}
