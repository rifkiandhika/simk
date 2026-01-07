<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResepLuarRequest extends FormRequest
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
            'nama_pasien_luar' => 'nullable|string|max:100',
            'umur' => 'nullable|integer|min:0|max:150',
            'jenis_kelamin' => 'nullable|in:L,P',
            'alamat' => 'nullable|string',
            'dokter_resep' => 'nullable|string|max:100',
            'status_obat_luar' => 'required|in:Racik,Non Racik',
            'jenis_racikan_luar' => 'nullable|string|max:50',
            'dosis_signa_luar' => 'nullable|string|max:50',
            'hasil_racikan_luar' => 'nullable|in:Kapsul,Tablet,Sirup,Puyer',
            'aturan_pakai_luar' => 'nullable|in:Sebelum Makan,Sesudah Makan,Saat Makan',
            'embalase_luar' => 'nullable|numeric|min:0',
            'jasa_racik_luar' => 'nullable|numeric|min:0',
            'keterangan_luar' => 'nullable|string',
            'obat_luar' => 'required|array|min:1',
            'obat_luar.*.detail_supplier_id' => 'required|exists:detail_suppliers,id',
            'obat_luar.*.jumlah' => 'required|numeric|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'status_obat_luar.required' => 'Status obat harus dipilih',
            'status_obat_luar.in' => 'Status obat tidak valid',
            'umur.integer' => 'Umur harus berupa angka',
            'umur.min' => 'Umur tidak valid',
            'umur.max' => 'Umur tidak valid',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid',
            'obat_luar.required' => 'Minimal harus ada 1 obat',
            'obat_luar.array' => 'Format data obat tidak valid',
            'obat_luar.min' => 'Minimal harus ada 1 obat',
            'obat_luar.*.detail_supplier_id.required' => 'Obat harus dipilih',
            'obat_luar.*.detail_supplier_id.exists' => 'Data obat tidak valid',
            'obat_luar.*.jumlah.required' => 'Jumlah obat harus diisi',
            'obat_luar.*.jumlah.numeric' => 'Jumlah obat harus berupa angka',
            'obat_luar.*.jumlah.min' => 'Jumlah obat minimal 1',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'nama_pasien_luar' => 'Nama Pasien',
            'umur' => 'Umur',
            'jenis_kelamin' => 'Jenis Kelamin',
            'alamat' => 'Alamat',
            'dokter_resep' => 'Dokter/Sumber Resep',
            'status_obat_luar' => 'Status Obat',
            'jenis_racikan_luar' => 'Jenis Racikan',
            'dosis_signa_luar' => 'Dosis/Signa',
            'hasil_racikan_luar' => 'Hasil Racikan',
            'aturan_pakai_luar' => 'Aturan Pakai',
            'embalase_luar' => 'Embalase',
            'jasa_racik_luar' => 'Jasa Racik',
            'keterangan_luar' => 'Keterangan',
        ];
    }
}
