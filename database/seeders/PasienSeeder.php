<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasienSeeder extends Seeder
{
    public function run(): void
    {
        $jumlah = 20; // jumlah pasien

        for ($i = 1; $i <= $jumlah; $i++) {

            DB::table('pasiens')->insert([
                'no_rm'        => 'RM' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'nik'          => '3273' . rand(1000000000, 9999999999),
                'nama_lengkap' => 'Pasien Apotik ' . $i,
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => Carbon::now()->subYears(rand(18, 70))->format('Y-m-d'),
                'jenis_kelamin' => rand(0, 1) ? 'L' : 'P',
                'golongan_darah' => collect(['A', 'B', 'AB', 'O', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->random(),
                'alamat'       => 'Jl. Contoh Alamat No. ' . $i,
                'no_telp'      => '08' . rand(1111111111, 9999999999),
                'no_telp_darurat' => '08' . rand(1111111111, 9999999999),
                'nama_kontak_darurat' => 'Kontak Darurat ' . $i,
                'hubungan_kontak_darurat' => 'Keluarga',
                'status_perkawinan' => collect(['Belum Kawin', 'Kawin'])->random(),
                'pekerjaan'    => 'Karyawan',
                'jenis_pembayaran' => collect(['Umum', 'BPJS'])->random(),
                'no_bpjs'      => rand(0, 1) ? '000' . rand(1000000000, 9999999999) : null,
                'asuransi_id'  => null,
                'no_polis_asuransi' => null,
                'foto'         => null,
                'status_aktif' => 'Aktif',
                'tanggal'      => Carbon::now()->format('Y-m-d'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }
}
