<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Asuransi;
use App\Models\DetailSupplier;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function searchAsuransi(Request $request)
    {
        $term = $request->get('term', '');

        $asuransi = Asuransi::where('status', 'Aktif')
            ->where(function ($q) use ($term) {
                $q->where('nama_asuransi', 'like', "%{$term}%")
                    ->orWhere('tipe', 'like', "%{$term}%");
            })
            ->limit(20)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->nama_asuransi . ' (' . $item->tipe . ')'
                ];
            });

        return response()->json($asuransi);
    }

    public function searchObat(Request $request)
    {
        $search = $request->input('q');
        $page = $request->input('page', 1);
        $perPage = 10;

        $query = DB::table('detail_obat_rs')
            ->join('obat_masters', 'detail_obat_rs.id_obat_master', '=', 'obat_masters.id_obat_master')
            ->where('detail_obat_rs.status_aktif', 'Aktif')
            ->where(function ($q) use ($search) {
                $q->where('detail_obat_rs.nama_obat_rs', 'LIKE', "%{$search}%")
                    ->orWhere('obat_masters.nama_obat', 'LIKE', "%{$search}%")
                    ->orWhere('obat_masters.nama_generik', 'LIKE', "%{$search}%")
                    ->orWhere('detail_obat_rs.kode_obat_rs', 'LIKE', "%{$search}%");
            });

        $total = $query->count();

        $items = $query->select(
            'detail_obat_rs.id_detail_obat_rs',
            'detail_obat_rs.nama_obat_rs',
            'detail_obat_rs.kode_obat_rs',
            'detail_obat_rs.lokasi_penyimpanan',
            'obat_masters.nama_generik',
            'obat_masters.bentuk_sediaan',
            'obat_masters.kemasan',
            'obat_masters.manufacturer'
        )
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get()
            ->map(function ($item) {
                // Buat display text yang informatif
                $displayText = $item->nama_obat_rs;
                if ($item->nama_generik) {
                    $displayText .= ' (' . $item->nama_generik . ')';
                }
                if ($item->manufacturer) {
                    $displayText .= ' - ' . $item->manufacturer;
                }

                return [
                    'id' => $item->id_detail_obat_rs,
                    'text' => $displayText,
                    'nama_generik' => $item->nama_generik ?? '-',
                    'satuan' => $item->bentuk_sediaan ?? '-',
                    'merk' => $item->manufacturer ?? '-',
                    'kode_obat_rs' => $item->kode_obat_rs,
                    'lokasi_penyimpanan' => $item->lokasi_penyimpanan,
                ];
            });

        return response()->json([
            'items' => $items,
            'pagination' => [
                'more' => ($page * $perPage) < $total
            ]
        ]);
    }


    /**
     * Search Alkes
     */
    public function searchAlkes(Request $request)
    {
        $search = $request->input('q');
        $page = $request->input('page', 1);
        $perPage = 10;

        $query = DB::table('alkes')
            ->where('status', 'Aktif')
            ->where(function ($q) use ($search) {
                $q->where('nama_alkes', 'LIKE', "%{$search}%")
                    ->orWhere('kode_alkes', 'LIKE', "%{$search}%")
                    ->orWhere('merk', 'LIKE', "%{$search}%");
            });

        $total = $query->count();

        $items = $query->select(
            'id',
            'nama_alkes as text',
            'merk',
            'satuan'
        )
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get()
            ->map(function ($item) {
                $displayText = $item->text;
                if ($item->merk) {
                    $displayText .= ' - ' . $item->merk;
                }

                return [
                    'id' => $item->id, // Gunakan nama sebagai value
                    'text' => $displayText,
                    'merk' => $item->merk,
                    'satuan' => $item->satuan
                ];
            });

        return response()->json([
            'items' => $items,
            'pagination' => [
                'more' => ($page * $perPage) < $total
            ]
        ]);
    }

    /**
     * Search Reagensia
     */
    public function searchReagensia(Request $request)
    {
        $search = $request->input('q');
        $page = $request->input('page', 1);
        $perPage = 10;

        $query = DB::table('reagensias')
            ->where('status', 'Aktif')
            ->where(function ($q) use ($search) {
                $q->where('nama_reagensia', 'LIKE', "%{$search}%")
                    ->orWhere('kode_reagensia', 'LIKE', "%{$search}%")
                    ->orWhere('merk', 'LIKE', "%{$search}%")
                    ->orWhere('no_katalog', 'LIKE', "%{$search}%");
            });

        $total = $query->count();

        $items = $query->select(
            'id',
            'nama_reagensia as text',
            'merk',
            'satuan',
            'no_katalog'
        )
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get()
            ->map(function ($item) {
                $displayText = $item->text;
                if ($item->merk) {
                    $displayText .= ' - ' . $item->merk;
                }
                if ($item->no_katalog) {
                    $displayText .= ' (Kat: ' . $item->no_katalog . ')';
                }

                return [
                    'id' => $item->id, // Gunakan nama sebagai value
                    'text' => $displayText,
                    'merk' => $item->merk,
                    'satuan' => $item->satuan
                ];
            });

        return response()->json([
            'items' => $items,
            'pagination' => [
                'more' => ($page * $perPage) < $total
            ]
        ]);
    }

    public function searchSupplierProducts($supplierId, Request $request)
    {
        $query = $request->get('q', '');

        // Ambil semua detail supplier dengan relasi obats (untuk jenis Obat)
        $detailSuppliers = DetailSupplier::with('obats')
            ->where('supplier_id', $supplierId)
            ->get();

        $results = [];

        foreach ($detailSuppliers as $detail) {
            $nama = null;
            $barangId = null;

            // Tentukan nama dan ID berdasarkan jenis
            if ($detail->jenis === 'Obat' && $detail->obats) {
                // Untuk Obat, ambil dari relasi detail_obat_rs
                $nama = $detail->obats->nama_obat_rs;
                $barangId = $detail->obats->id_detail_obat_rs;
            } else {
                // Untuk Alkes, Reagensia, Lainnya, ambil dari kolom nama
                $nama = $detail->nama;
                $barangId = $detail->id; // Gunakan ID detail_supplier sebagai referensi
            }

            // Skip jika nama kosong
            if (!$nama) continue;

            // Filter berdasarkan query pencarian
            if ($query) {
                $searchLower = strtolower($query);
                $namaMatch = stripos(strtolower($nama), $searchLower) !== false;
                $judulMatch = stripos(strtolower($detail->judul ?? ''), $searchLower) !== false;
                $jenisMatch = stripos(strtolower($detail->jenis ?? ''), $searchLower) !== false;
                $merkMatch = stripos(strtolower($detail->merk ?? ''), $searchLower) !== false;

                if (!$namaMatch && !$judulMatch && !$jenisMatch && !$merkMatch) {
                    continue;
                }
            }

            $results[] = [
                'id' => $barangId,
                'detail_supplier_id' => $detail->id,
                'nama' => $nama,
                'judul' => $detail->judul ?? '-',
                'jenis' => $detail->jenis ?? '-',
                'merk' => $detail->merk ?? '-',
                'satuan' => $detail->satuan ?? '-',
                'exp_date' => $detail->exp_date ?? '',
                'no_batch' => $detail->no_batch ?? '',
                'type' => $detail->jenis, // Penting untuk membedakan tipe barang
            ];
        }

        return response()->json($results);
    }
}
