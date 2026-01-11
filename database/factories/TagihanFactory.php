<?php

namespace Database\Factories;

use App\Models\Tagihan;
use App\Models\Pasien;
use App\Models\Registrasi;
use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Factories\Factory;

class TagihanFactory extends Factory
{
    protected $model = Tagihan::class;

    public function definition()
    {
        $totalTagihan = $this->faker->numberBetween(500000, 10000000);
        $totalDibayar = $this->faker->numberBetween(0, $totalTagihan);
        $sisaTagihan = $totalTagihan - $totalDibayar;

        $status = 'BELUM_LUNAS';
        if ($sisaTagihan <= 0) {
            $status = 'LUNAS';
        } elseif ($totalDibayar > 0) {
            $status = 'CICILAN';
        }

        return [
            'no_tagihan' => 'TGH/' . date('Ymd') . '/' . $this->faker->unique()->numberBetween(1000, 9999),
            'id_registrasi' => Registrasi::factory(),
            'id_pasien' => Pasien::factory(),
            'tanggal_tagihan' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'jenis_tagihan' => $this->faker->randomElement(['IGD', 'RAWAT_JALAN', 'RAWAT_INAP']),
            'total_tagihan' => $totalTagihan,
            'total_dibayar' => $totalDibayar,
            'sisa_tagihan' => $sisaTagihan,
            'status' => $status,
            'status_klaim' => 'NON_KLAIM',
            'tanggal_lunas' => $status == 'LUNAS' ? now() : null,
            'locked' => false,
            'created_by' => Karyawan::factory(),
        ];
    }

    public function lunas()
    {
        return $this->state(function (array $attributes) {
            return [
                'total_dibayar' => $attributes['total_tagihan'],
                'sisa_tagihan' => 0,
                'status' => 'LUNAS',
                'tanggal_lunas' => now(),
            ];
        });
    }

    public function locked()
    {
        return $this->state(function (array $attributes) {
            return [
                'locked' => true,
                'locked_at' => now(),
                'locked_by' => Karyawan::factory(),
            ];
        });
    }
}
