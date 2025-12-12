<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\PoAuditTrail;
use App\Models\PurchaseOrder;
use App\Models\ShippingActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ShippingController extends Controller
{
    public function index()
    {
        // Ambil semua PO yang sedang dalam pengiriman
        $purchaseOrders = PurchaseOrder::with([
            'supplier',
            'shippingActivities' => function ($q) {
                $q->orderBy('tanggal_aktivitas', 'desc');
            }
        ])
            ->whereIn('status', ['dikirim_ke_supplier', 'dalam_pengiriman'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Hitung jumlah PO yang masih aktif shipping
        $activeShipments = PurchaseOrder::whereIn('status', [
            'dikirim_ke_supplier',
            'dalam_pengiriman'
        ])->count();

        return view('po.shipping', compact('purchaseOrders', 'activeShipments'));
    }

    public function store(Request $request)
    {
        try {
            // Log request untuk debugging
            Log::info('Shipping Store Request', $request->all());

            $validator = Validator::make($request->all(), [
                'id_po' => 'required|string',
                'status_shipping' => 'required|in:persiapan,dikemas,dalam_perjalanan,tiba_di_tujuan,diterima,selesai',
                'deskripsi_aktivitas' => 'required|string',
                'id_karyawan_input' => 'required|string',
                'pin' => 'required|string|size:6',
                'catatan' => 'nullable|string',
                'foto_bukti' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            ], [
                'id_po.required' => 'ID Purchase Order harus diisi',
                'status_shipping.required' => 'Status pengiriman harus dipilih',
                'status_shipping.in' => 'Status pengiriman tidak valid',
                'deskripsi_aktivitas.required' => 'Deskripsi aktivitas harus diisi',
                'id_karyawan_input.required' => 'ID Karyawan tidak ditemukan',
                'pin.required' => 'PIN harus diisi',
                'pin.size' => 'PIN harus 6 digit',
                'foto_bukti.image' => 'File harus berupa gambar',
                'foto_bukti.mimes' => 'Format gambar harus jpeg, jpg, atau png',
                'foto_bukti.max' => 'Ukuran gambar maksimal 2MB',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verifikasi PIN
            $karyawan = Karyawan::where('id_karyawan', $request->id_karyawan_input)
                ->where('pin', $request->pin)
                ->first();

            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'PIN tidak valid atau tidak sesuai'
                ], 403);
            }

            // Cek apakah PO ada
            $po = PurchaseOrder::find($request->id_po);
            if (!$po) {
                return response()->json([
                    'success' => false,
                    'message' => 'Purchase Order tidak ditemukan'
                ], 404);
            }

            DB::beginTransaction();

            $fotoBukti = null;
            if ($request->hasFile('foto_bukti')) {
                $file = $request->file('foto_bukti');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $fotoBukti = $file->storeAs('shipping_photos', $filename, 'public');
            }

            // Gunakan tanggal dari form atau sekarang
            $tanggalAktivitas = $request->tanggal_aktivitas
                ? \Carbon\Carbon::parse($request->tanggal_aktivitas)
                : now();

            $shipping = ShippingActivity::create([
                'id_po' => $request->id_po,
                'status_shipping' => $request->status_shipping,
                'deskripsi_aktivitas' => $request->deskripsi_aktivitas,
                'id_karyawan_input' => $request->id_karyawan_input,
                'tanggal_aktivitas' => $tanggalAktivitas,
                'catatan' => $request->catatan,
                'foto_bukti' => $fotoBukti,
            ]);

            // Update status PO berdasarkan status shipping
            if ($request->status_shipping === 'diterima') {
                $po->update(['status' => 'diterima']);
            } elseif (in_array($request->status_shipping, ['dikemas', 'dalam_perjalanan', 'tiba_di_tujuan'])) {
                $po->update(['status' => 'dalam_pengiriman']);
            }

            // Audit Trail
            PoAuditTrail::create([
                'id_po' => $request->id_po,
                'id_karyawan' => $request->id_karyawan_input,
                'pin_karyawan' => $request->pin,
                'aksi' => 'update_shipping',
                'deskripsi_aksi' => 'Update shipping: ' . $request->status_shipping . ' - ' . $request->deskripsi_aktivitas,
                'data_sesudah' => json_encode($shipping->toArray()),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status pengiriman berhasil diupdate',
                'data' => $shipping
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Shipping Store Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan shipping activity',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get Shipping Activities by PO
    public function getByPO($id_po)
    {
        try {
            $activities = ShippingActivity::with('karyawan')
                ->where('id_po', $id_po)
                ->orderBy('tanggal_aktivitas', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $activities
            ], 200);
        } catch (\Exception $e) {
            Log::error('Get Shipping Activities Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data shipping',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
