<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        DB::beginTransaction();

        try {
            // ===== DEFINE MODULES =====
            $modules = [
                // System
                'role_permission' => 'Role & Permission',
                'user' => 'User Management',

                // Master Data
                'karyawan' => 'Karyawan',
                'department' => 'Department',
                'poli' => 'Poli',
                'unit' => 'Unit',
                'dokter' => 'Dokter',
                'perawat' => 'Perawat',
                'jadwal_dokter' => 'Jadwal Dokter',
                'asuransi' => 'Asuransi',
                'pasien' => 'Pasien',
                'icd10' => 'ICD10',
                'tindakan_medis' => 'Tindakan Medis',
                'jenis_pemeriksaan_lab' => 'Jenis Pemeriksaan Lab',
                'jenis_pemeriksaan_radiologi' => 'Jenis Pemeriksaan Radiologi',

                // Operasional
                'registrasi' => 'Registrasi',
                'antrian' => 'Antrian',
                'rekam_medis' => 'Rekam Medis',
                'soap' => 'SOAP',
                'cppt' => 'CPPT',
                'vital_sign' => 'Vital Sign',
                'diagnosis' => 'Diagnosis',
                'tindakan_medis_detail' => 'Detail Tindakan Medis',
                'igd' => 'IGD',
                'triase' => 'Triase',

                // Farmasi & Lab
                'obat_master' => 'Obat Master',
                'obat_rs' => 'Obat RS',
                'resep' => 'Resep',
                'reagensia' => 'Reagensia',
                'alkes' => 'Alkes',

                // Inventori & Gudang
                'supplier' => 'Supplier',
                'gudang' => 'Gudang',
                'history_gudang' => 'History Gudang',
                'jenis' => 'Jenis',
                'satuan' => 'Satuan',

                // Keuangan
                'tagihan' => 'Tagihan',
                'pembayaran' => 'Pembayaran',
            ];

            // ===== CREATE PERMISSIONS =====
            $permissions = [];
            $actions = ['view', 'create', 'update', 'delete', 'show'];

            foreach ($modules as $module => $label) {
                foreach ($actions as $action) {
                    $permissionName = "{$action}_{$module}";
                    $permission = Permission::create([
                        'name' => $permissionName,
                        'guard_name' => 'web'
                    ]);
                    $permissions[$permissionName] = $permission;
                }
            }

            // ===== CREATE SPECIAL PERMISSIONS =====
            $specialPermissions = [
                'approve_tagihan' => 'Approve Tagihan',
                'approve_resep' => 'Approve Resep',
                'void_pembayaran' => 'Void Pembayaran',
                'export_laporan' => 'Export Laporan',
                'print_resep' => 'Print Resep',
                'print_tagihan' => 'Print Tagihan',
                'transfer_pasien' => 'Transfer Pasien',
                'manage_all' => 'Manage All (Superadmin)',
            ];

            foreach ($specialPermissions as $permName => $label) {
                $permission = Permission::create([
                    'name' => $permName,
                    'guard_name' => 'web'
                ]);
                $permissions[$permName] = $permission;
            }

            // ===== CREATE ROLES =====

            // 1. SUPERADMIN - Full Access
            $superadmin = Role::create(['name' => 'Superadmin']);
            $superadmin->givePermissionTo(Permission::all());

            // 2. ADMIN - Manage Data Master & Users
            $admin = Role::create(['name' => 'Admin']);
            $adminPermissions = [
                // System
                'view_role_permission',
                'create_role_permission',
                'update_role_permission',
                'delete_role_permission',
                'show_role_permission',
                'view_user',
                'create_user',
                'update_user',
                'delete_user',
                'show_user',

                // Master Data
                'view_karyawan',
                'create_karyawan',
                'update_karyawan',
                'delete_karyawan',
                'show_karyawan',
                'view_department',
                'create_department',
                'update_department',
                'delete_department',
                'show_department',
                'view_poli',
                'create_poli',
                'update_poli',
                'delete_poli',
                'show_poli',
                'view_unit',
                'create_unit',
                'update_unit',
                'delete_unit',
                'show_unit',
                'view_dokter',
                'create_dokter',
                'update_dokter',
                'delete_dokter',
                'show_dokter',
                'view_perawat',
                'create_perawat',
                'update_perawat',
                'delete_perawat',
                'show_perawat',
                'view_jadwal_dokter',
                'create_jadwal_dokter',
                'update_jadwal_dokter',
                'delete_jadwal_dokter',
                'show_jadwal_dokter',
                'view_asuransi',
                'create_asuransi',
                'update_asuransi',
                'delete_asuransi',
                'show_asuransi',
                'view_pasien',
                'create_pasien',
                'update_pasien',
                'delete_pasien',
                'show_pasien',
                'view_icd10',
                'create_icd10',
                'update_icd10',
                'delete_icd10',
                'show_icd10',
                'view_tindakan_medis',
                'create_tindakan_medis',
                'update_tindakan_medis',
                'delete_tindakan_medis',
                'show_tindakan_medis',
                'view_jenis',
                'create_jenis',
                'update_jenis',
                'delete_jenis',
                'show_jenis',
                'view_satuan',
                'create_satuan',
                'update_satuan',
                'delete_satuan',
                'show_satuan',

                // Special
                'export_laporan',
            ];
            $admin->givePermissionTo($adminPermissions);

            // 3. DIREKTUR/MANAGER - View All & Approve
            $direktur = Role::create(['name' => 'Direktur']);
            $direkturPermissions = [];
            foreach ($modules as $module => $label) {
                $direkturPermissions[] = "view_{$module}";
                $direkturPermissions[] = "show_{$module}";
            }
            $direkturPermissions = array_merge($direkturPermissions, [
                'approve_tagihan',
                'approve_resep',
                'export_laporan',
                'print_tagihan',
            ]);
            $direktur->givePermissionTo($direkturPermissions);

            // 4. DOKTER - Rekam Medis, Diagnosis, Resep
            $dokter = Role::create(['name' => 'Dokter']);
            $dokterPermissions = [
                // Operasional - CRUD
                'view_rekam_medis',
                'create_rekam_medis',
                'update_rekam_medis',
                'show_rekam_medis',
                'view_soap',
                'create_soap',
                'update_soap',
                'show_soap',
                'view_diagnosis',
                'create_diagnosis',
                'update_diagnosis',
                'delete_diagnosis',
                'show_diagnosis',
                'view_tindakan_medis_detail',
                'create_tindakan_medis_detail',
                'update_tindakan_medis_detail',
                'delete_tindakan_medis_detail',
                'show_tindakan_medis_detail',
                'view_resep',
                'create_resep',
                'update_resep',
                'show_resep',

                // View Only
                'view_pasien',
                'show_pasien',
                'view_registrasi',
                'show_registrasi',
                'view_antrian',
                'show_antrian',
                'view_vital_sign',
                'show_vital_sign',
                'view_cppt',
                'show_cppt',
                'view_icd10',
                'show_icd10',
                'view_tindakan_medis',
                'show_tindakan_medis',
                'view_obat_master',
                'show_obat_master',
                'view_obat_rs',
                'show_obat_rs',

                // Special
                'print_resep',
                'transfer_pasien',
            ];
            $dokter->givePermissionTo($dokterPermissions);

            // 5. PERAWAT - Vital Signs, CPPT, Triase
            $perawat = Role::create(['name' => 'Perawat']);
            $perawatPermissions = [
                // Operasional - CRUD
                'view_vital_sign',
                'create_vital_sign',
                'update_vital_sign',
                'show_vital_sign',
                'view_cppt',
                'create_cppt',
                'update_cppt',
                'show_cppt',
                'view_triase',
                'create_triase',
                'update_triase',
                'show_triase',
                'view_igd',
                'create_igd',
                'update_igd',
                'show_igd',

                // View Only
                'view_pasien',
                'show_pasien',
                'view_rekam_medis',
                'show_rekam_medis',
                'view_soap',
                'show_soap',
                'view_diagnosis',
                'show_diagnosis',
                'view_registrasi',
                'show_registrasi',
                'view_antrian',
                'show_antrian',
                'view_resep',
                'show_resep',

                // Special
                'transfer_pasien',
            ];
            $perawat->givePermissionTo($perawatPermissions);

            // 6. APOTEKER - Farmasi
            $apoteker = Role::create(['name' => 'Apoteker']);
            $apotekerPermissions = [
                // Farmasi - CRUD
                'view_obat_master',
                'create_obat_master',
                'update_obat_master',
                'delete_obat_master',
                'show_obat_master',
                'view_obat_rs',
                'create_obat_rs',
                'update_obat_rs',
                'delete_obat_rs',
                'show_obat_rs',
                'view_resep',
                'create_resep',
                'update_resep',
                'show_resep',

                // View Only
                'view_pasien',
                'show_pasien',
                'view_registrasi',
                'show_registrasi',
                'view_supplier',
                'show_supplier',

                // Special
                'approve_resep',
                'print_resep',
            ];
            $apoteker->givePermissionTo($apotekerPermissions);

            // 7. ANALIS LAB - Lab & Reagensia
            $analisLab = Role::create(['name' => 'Analis Lab']);
            $analisLabPermissions = [
                // Lab - CRUD
                'view_jenis_pemeriksaan_lab',
                'create_jenis_pemeriksaan_lab',
                'update_jenis_pemeriksaan_lab',
                'delete_jenis_pemeriksaan_lab',
                'show_jenis_pemeriksaan_lab',
                'view_reagensia',
                'create_reagensia',
                'update_reagensia',
                'delete_reagensia',
                'show_reagensia',

                // View Only
                'view_pasien',
                'show_pasien',
                'view_registrasi',
                'show_registrasi',
                'view_supplier',
                'show_supplier',
            ];
            $analisLab->givePermissionTo($analisLabPermissions);

            // 8. RADIOGRAFER - Radiologi
            $radiografer = Role::create(['name' => 'Radiografer']);
            $radiograferPermissions = [
                // Radiologi - CRUD
                'view_jenis_pemeriksaan_radiologi',
                'create_jenis_pemeriksaan_radiologi',
                'update_jenis_pemeriksaan_radiologi',
                'delete_jenis_pemeriksaan_radiologi',
                'show_jenis_pemeriksaan_radiologi',

                // View Only
                'view_pasien',
                'show_pasien',
                'view_registrasi',
                'show_registrasi',
            ];
            $radiografer->givePermissionTo($radiograferPermissions);

            // 9. KASIR - Keuangan
            $kasir = Role::create(['name' => 'Kasir']);
            $kasirPermissions = [
                // Keuangan - CRUD
                'view_tagihan',
                'create_tagihan',
                'update_tagihan',
                'show_tagihan',
                'view_pembayaran',
                'create_pembayaran',
                'update_pembayaran',
                'show_pembayaran',

                // View Only
                'view_pasien',
                'show_pasien',
                'view_registrasi',
                'show_registrasi',
                'view_asuransi',
                'show_asuransi',

                // Special
                'print_tagihan',
                'void_pembayaran',
            ];
            $kasir->givePermissionTo($kasirPermissions);

            // 10. GUDANG - Inventori & Supplier
            $gudang = Role::create(['name' => 'Gudang']);
            $gudangPermissions = [
                // Gudang - CRUD
                'view_gudang',
                'create_gudang',
                'update_gudang',
                'delete_gudang',
                'show_gudang',
                'view_history_gudang',
                'create_history_gudang',
                'update_history_gudang',
                'show_history_gudang',
                'view_supplier',
                'create_supplier',
                'update_supplier',
                'delete_supplier',
                'show_supplier',
                'view_alkes',
                'create_alkes',
                'update_alkes',
                'delete_alkes',
                'show_alkes',
                'view_reagensia',
                'create_reagensia',
                'update_reagensia',
                'delete_reagensia',
                'show_reagensia',
                'view_obat_rs',
                'create_obat_rs',
                'update_obat_rs',
                'show_obat_rs',
                'view_jenis',
                'show_jenis',
                'view_satuan',
                'show_satuan',
            ];
            $gudang->givePermissionTo($gudangPermissions);

            // 11. REGISTRASI - Pendaftaran & Antrian
            $registrasi = Role::create(['name' => 'Registrasi']);
            $registrasiPermissions = [
                // Registrasi - CRUD
                'view_registrasi',
                'create_registrasi',
                'update_registrasi',
                'show_registrasi',
                'view_antrian',
                'create_antrian',
                'update_antrian',
                'show_antrian',
                'view_pasien',
                'create_pasien',
                'update_pasien',
                'show_pasien',

                // View Only
                'view_dokter',
                'show_dokter',
                'view_jadwal_dokter',
                'show_jadwal_dokter',
                'view_poli',
                'show_poli',
                'view_asuransi',
                'show_asuransi',
            ];
            $registrasi->givePermissionTo($registrasiPermissions);

            // 12. STAFF - View Only
            $staff = Role::create(['name' => 'Staff']);
            $staffPermissions = [];
            foreach ($modules as $module => $label) {
                if (!in_array($module, ['role_permission', 'user'])) {
                    $staffPermissions[] = "view_{$module}";
                    $staffPermissions[] = "show_{$module}";
                }
            }
            $staff->givePermissionTo($staffPermissions);

            DB::commit();

            $this->command->info('✅ Roles and Permissions seeded successfully!');
            $this->command->info('');
            $this->command->info('📋 Created Roles:');
            $this->command->info('   1. Superadmin (Full Access)');
            $this->command->info('   2. Admin (Data Master & Users)');
            $this->command->info('   3. Direktur (View All & Approve)');
            $this->command->info('   4. Dokter (Rekam Medis & Diagnosis)');
            $this->command->info('   5. Perawat (Vital Signs & CPPT)');
            $this->command->info('   6. Apoteker (Farmasi)');
            $this->command->info('   7. Analis Lab (Lab & Reagensia)');
            $this->command->info('   8. Radiografer (Radiologi)');
            $this->command->info('   9. Kasir (Keuangan)');
            $this->command->info('  10. Gudang (Inventori)');
            $this->command->info('  11. Registrasi (Pendaftaran)');
            $this->command->info('  12. Staff (View Only)');
            $this->command->info('');
            $this->command->info('📦 Total Permissions: ' . Permission::count());
            $this->command->info('👥 Total Roles: ' . Role::count());
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
