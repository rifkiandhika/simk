<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // ===== CREATE DEFAULT KARYAWAN =====
            $karyawans = [
                [
                    'id_karyawan' => Str::uuid(),
                    'nip' => '001',
                    'nama_lengkap' => 'Super Administrator',
                    'tempat_lahir' => 'Jakarta',
                    'tanggal_lahir' => '1990-01-01',
                    'jenis_kelamin' => 'L',
                    'alamat' => 'Jakarta',
                    'no_telp' => '081234567890',
                    'email' => 'superadmin@hospital.com',
                    'pin' => '000001',
                    'status_aktif' => 'Aktif',
                    'tanggal_bergabung' => now(),
                ],
                [
                    'id_karyawan' => Str::uuid(),
                    'nip' => '002',
                    'nama_lengkap' => 'Administrator',
                    'tempat_lahir' => 'Jakarta',
                    'tanggal_lahir' => '1991-01-01',
                    'jenis_kelamin' => 'L',
                    'alamat' => 'Jakarta',
                    'no_telp' => '081234567891',
                    'email' => 'admin@hospital.com',
                    'pin' => '000002',
                    'status_aktif' => 'Aktif',
                    'tanggal_bergabung' => now(),
                ],
                [
                    'id_karyawan' => Str::uuid(),
                    'nip' => '003',
                    'nama_lengkap' => 'Dr. Ahmad Sudirman',
                    'tempat_lahir' => 'Bandung',
                    'tanggal_lahir' => '1985-05-15',
                    'jenis_kelamin' => 'L',
                    'alamat' => 'Bandung',
                    'no_telp' => '081234567892',
                    'email' => 'dokter@hospital.com',
                    'pin' => '000003',
                    'status_aktif' => 'Aktif',
                    'tanggal_bergabung' => now(),
                ],
                [
                    'id_karyawan' => Str::uuid(),
                    'nip' => '004',
                    'nama_lengkap' => 'Siti Nurhaliza',
                    'tempat_lahir' => 'Surabaya',
                    'tanggal_lahir' => '1992-08-20',
                    'jenis_kelamin' => 'P',
                    'alamat' => 'Surabaya',
                    'no_telp' => '081234567893',
                    'email' => 'perawat@hospital.com',
                    'pin' => '000004',
                    'status_aktif' => 'Aktif',
                    'tanggal_bergabung' => now(),
                ],
                [
                    'id_karyawan' => Str::uuid(),
                    'nip' => '005',
                    'nama_lengkap' => 'Apt. Budi Santoso',
                    'tempat_lahir' => 'Yogyakarta',
                    'tanggal_lahir' => '1988-03-10',
                    'jenis_kelamin' => 'L',
                    'alamat' => 'Yogyakarta',
                    'no_telp' => '081234567894',
                    'email' => 'apoteker@hospital.com',
                    'pin' => '000005',
                    'status_aktif' => 'Aktif',
                    'tanggal_bergabung' => now(),
                ],
                [
                    'id_karyawan' => Str::uuid(),
                    'nip' => '006',
                    'nama_lengkap' => 'Dewi Analis',
                    'tempat_lahir' => 'Semarang',
                    'tanggal_lahir' => '1993-11-25',
                    'jenis_kelamin' => 'P',
                    'alamat' => 'Semarang',
                    'no_telp' => '081234567895',
                    'email' => 'analislab@hospital.com',
                    'pin' => '000006',
                    'status_aktif' => 'Aktif',
                    'tanggal_bergabung' => now(),
                ],
                [
                    'id_karyawan' => Str::uuid(),
                    'nip' => '007',
                    'nama_lengkap' => 'Rina Kasir',
                    'tempat_lahir' => 'Jakarta',
                    'tanggal_lahir' => '1994-06-30',
                    'jenis_kelamin' => 'P',
                    'alamat' => 'Jakarta',
                    'no_telp' => '081234567896',
                    'email' => 'kasir@hospital.com',
                    'pin' => '000007',
                    'status_aktif' => 'Aktif',
                    'tanggal_bergabung' => now(),
                ],
                [
                    'id_karyawan' => Str::uuid(),
                    'nip' => '008',
                    'nama_lengkap' => 'Andi Registrasi',
                    'tempat_lahir' => 'Medan',
                    'tanggal_lahir' => '1995-09-12',
                    'jenis_kelamin' => 'L',
                    'alamat' => 'Medan',
                    'no_telp' => '081234567897',
                    'email' => 'registrasi@hospital.com',
                    'pin' => '000008',
                    'status_aktif' => 'Aktif',
                    'tanggal_bergabung' => now(),
                ],
            ];

            foreach ($karyawans as $karyawan) {
                Karyawan::create($karyawan);
            }

            // ===== CREATE DEFAULT USERS =====
            $users = [
                [
                    'karyawan_nip' => '001',
                    'username' => 'superadmin',
                    'role' => 'Superadmin',
                ],
                [
                    'karyawan_nip' => '002',
                    'username' => 'admin',
                    'role' => 'Admin',
                ],
                [
                    'karyawan_nip' => '003',
                    'username' => 'dokter',
                    'role' => 'Dokter',
                ],
                [
                    'karyawan_nip' => '004',
                    'username' => 'perawat',
                    'role' => 'Perawat',
                ],
                [
                    'karyawan_nip' => '005',
                    'username' => 'apoteker',
                    'role' => 'Apoteker',
                ],
                [
                    'karyawan_nip' => '006',
                    'username' => 'analislab',
                    'role' => 'Analis Lab',
                ],
                [
                    'karyawan_nip' => '007',
                    'username' => 'kasir',
                    'role' => 'Kasir',
                ],
                [
                    'karyawan_nip' => '008',
                    'username' => 'registrasi',
                    'role' => 'Registrasi',
                ],
            ];

            foreach ($users as $userData) {
                $karyawan = Karyawan::where('nip', $userData['karyawan_nip'])->first();

                if ($karyawan) {
                    $user = User::create([
                        'id_karyawan' => $karyawan->id_karyawan,
                        'name' => $karyawan->nama_lengkap,
                        'username' => $userData['username'],
                        'email' => $karyawan->email,
                        'password' => Hash::make('password'), // Default password: password
                        'status' => 'Aktif',
                        'email_verified_at' => now(),
                    ]);

                    // Assign role
                    $user->assignRole($userData['role']);
                }
            }

            DB::commit();

            $this->command->info('✅ Default users created successfully!');
            $this->command->info('');
            $this->command->info('🔐 Default Login Credentials:');
            $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->command->info('');
            $this->command->info('1. Superadmin');
            $this->command->info('   Username: superadmin');
            $this->command->info('   Password: password');
            $this->command->info('');
            $this->command->info('2. Admin');
            $this->command->info('   Username: admin');
            $this->command->info('   Password: password');
            $this->command->info('');
            $this->command->info('3. Dokter');
            $this->command->info('   Username: dokter');
            $this->command->info('   Password: password');
            $this->command->info('');
            $this->command->info('4. Perawat');
            $this->command->info('   Username: perawat');
            $this->command->info('   Password: password');
            $this->command->info('');
            $this->command->info('5. Apoteker');
            $this->command->info('   Username: apoteker');
            $this->command->info('   Password: password');
            $this->command->info('');
            $this->command->info('6. Analis Lab');
            $this->command->info('   Username: analislab');
            $this->command->info('   Password: password');
            $this->command->info('');
            $this->command->info('7. Kasir');
            $this->command->info('   Username: kasir');
            $this->command->info('   Password: password');
            $this->command->info('');
            $this->command->info('8. Registrasi');
            $this->command->info('   Username: registrasi');
            $this->command->info('   Password: password');
            $this->command->info('');
            $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->command->warn('⚠️  IMPORTANT: Change default passwords immediately!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
