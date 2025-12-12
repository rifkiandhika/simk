<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\ObatMaster;
use Illuminate\Http\Request;

class ObatMasterController extends Controller
{
    public function search(Request $request)
    {
        $term = $request->get('term', '');

        $data = ObatMaster::where('nama_obat', 'like', "%{$term}%")
            ->orWhere('kfa_code', 'like', "%{$term}%")
            ->limit(20)
            ->get(['id_obat_master', 'nama_obat', 'kfa_code']);

        return response()->json($data->map(function ($item) {
            return [
                'id' => $item->id_obat_master,
                'text' => "{$item->nama_obat} ({$item->kfa_code})",
            ];
        }));
    }
}
