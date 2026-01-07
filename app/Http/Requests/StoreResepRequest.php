<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResepRequest extends FormRequest
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
            'pasien_id' => 'required|exists:pasiens,id_pasien',
            'ruangan_id' => 'required|exists:ruangans,id',
            'status_obat' => 'required|in:Racik,Non Racik',
            'jenis_racikan' => 'nullable|string|max:50',
            'dosis_signa' => 'nullable|string|max:50',
            'hasil_racikan' => 'nullable|in:Kapsul,Tablet,Sirup,Puyer',
            'aturan_pakai' => 'nullable|in:Sebelum Makan,Sesudah Makan,Saat Makan',
            'embalase' => 'nullable|numeric|min:0',
            'jasa_racik' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'obat' => 'required|array|min:1',
            'obat.*.detail_supplier_id' => 'required|exists:detail_suppliers,id',
            'obat.*.jumlah' => 'required|numeric|min:1',
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
            'pasien_id.required' => 'Data pasien tidak ditemukan',
            'pasien_id.exists' => 'Data pasien tidak valid',
            'ruangan_id.required' => 'Ruangan harus dipilih',
            'ruangan_id.exists' => 'Data ruangan tidak valid',
            'status_obat.required' => 'Status obat harus dipilih',
            'status_obat.in' => 'Status obat tidak valid',
            'obat.required' => 'Minimal harus ada 1 obat',
            'obat.array' => 'Format data obat tidak valid',
            'obat.min' => 'Minimal harus ada 1 obat',
            'obat.*.detail_supplier_id.required' => 'Obat harus dipilih',
            'obat.*.detail_supplier_id.exists' => 'Data obat tidak valid',
            'obat.*.jumlah.required' => 'Jumlah obat harus diisi',
            'obat.*.jumlah.numeric' => 'Jumlah obat harus berupa angka',
            'obat.*.jumlah.min' => 'Jumlah obat minimal 1',
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
            'pasien_id' => 'Pasien',
            'ruangan_id' => 'Ruangan',
            'status_obat' => 'Status Obat',
            'jenis_racikan' => 'Jenis Racikan',
            'dosis_signa' => 'Dosis/Signa',
            'hasil_racikan' => 'Hasil Racikan',
            'aturan_pakai' => 'Aturan Pakai',
            'embalase' => 'Embalase',
            'jasa_racik' => 'Jasa Racik',
            'keterangan' => 'Keterangan',
        ];
    }
}
