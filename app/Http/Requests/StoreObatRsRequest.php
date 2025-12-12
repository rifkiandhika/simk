<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreObatRsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_obat' => 'required|string|max:200',
            'nama_obat_internasional' => 'required|string|max:200',

            // Detail array
            'id_obat_master' => 'required|array|min:1',
            'id_obat_master.*' => 'required|exists:obat_masters,id_obat_master',

            'kode_obat_rs' => 'required|array|min:1',
            'kode_obat_rs.*' => 'required|string|max:50|distinct',

            'nama_obat_rs' => 'nullable|array',
            'nama_obat_rs.*' => 'nullable|string|max:100',

            'stok_minimal' => 'required|array|min:1',
            'stok_minimal.*' => 'nullable|integer|min:0',

            'stok_maksimal' => 'required|array|min:1',
            'stok_maksimal.*' => 'nullable|integer|min:0',

            'lokasi_penyimpanan' => 'nullable|array',
            'lokasi_penyimpanan.*' => 'nullable|string|max:100',

            'catatan_khusus' => 'nullable|array',
            'catatan_khusus.*' => 'nullable|string|max:500',

            'status_aktif' => 'required|array|min:1',
            'status_aktif.*' => 'required|in:Aktif,Nonaktif,Diskontinyu',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_obat.required' => 'Nama obat wajib diisi.',
            'nama_obat_internasional.required' => 'Nama internasional wajib diisi.',
            'id_obat_master.*.exists' => 'Data obat master tidak ditemukan.',
            'kode_obat_rs.*.distinct' => 'Kode obat RS tidak boleh duplikat.',
            'status_aktif.*.in' => 'Status hanya boleh Aktif, Nonaktif, atau Diskontinyu.',
        ];
    }
}
