<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\DetailSupplier;
use App\Models\Karyawan;
use App\Models\PoAuditTrail;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReceivingController extends Controller
{
    public function receiveGoods(Request $request, $id_po)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|size:6',
            'id_karyawan' => 'required|uuid',
            'items' => 'required|array|min:1',
            'items.*.id_po_item' => 'required|uuid',
            'items.*.qty_diterima' => 'required|integer|min:0',
            'items.*.tanggal_kadaluarsa' => 'nullable|date',
            'items.*.batch_number' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Verifikasi PIN
        $karyawan = Karyawan::where('id_karyawan', $request->id_karyawan)
            ->where('pin', $request->pin)
            ->first();

        if (!$karyawan) {
            return response()->json(['error' => 'PIN tidak valid'], 403);
        }

        DB::beginTransaction();
        try {
            $po = PurchaseOrder::findOrFail($id_po);
            $dataBefore = $po->toArray();

            // Update setiap item
            foreach ($request->items as $itemData) {
                $item = PurchaseOrderItem::findOrFail($itemData['id_po_item']);

                $item->update([
                    'qty_diterima' => $itemData['qty_diterima'],
                    'tanggal_kadaluarsa' => $itemData['tanggal_kadaluarsa'] ?? null,
                    'batch_number' => $itemData['batch_number'] ?? null,
                ]);

                // Update stock produk
                $produk = DetailSupplier::find($item->id_produk);

                if ($po->tipe_po === 'eksternal') {
                    // Untuk PO eksternal: tambah stock_live, kurangi stock_po
                    $produk->increment('stock_live', $itemData['qty_diterima']);
                    $produk->decrement('stock_po', $item->qty_diminta);
                } else {
                    // Untuk PO internal: kurangi stock_live saja
                    $produk->decrement('stock_live', $itemData['qty_diterima']);
                }
            }

            // Update status PO
            $po->update(['status' => 'diterima']);

            // Audit Trail
            PoAuditTrail::create([
                'id_po' => $po->id_po,
                'id_karyawan' => $request->id_karyawan,
                'pin_karyawan' => $request->pin,
                'aksi' => 'terima_barang',
                'deskripsi_aksi' => 'Penerimaan barang untuk PO ' . $po->no_po,
                'data_sebelum' => $dataBefore,
                'data_sesudah' => $po->toArray(),
            ]);

            DB::commit();
            return response()->json(['message' => 'Barang berhasil diterima', 'data' => $po->load('items')], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal menerima barang: ' . $e->getMessage()], 500);
        }
    }
}
