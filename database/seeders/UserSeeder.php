<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil role
        $roles = Role::pluck('id_role', 'name');

        // Ambil department
        $prodiTI = Department::where('name', 'Teknik Informatika')->first();
        $prodiSI = Department::where('name', 'Sistem Informasi')->first();
        $kajur   = Department::where('type', 'kajur')->first();
        $wadir   = Department::where('type', 'wadir')->first();
        $upa     = Department::where('type', 'upa')->first();

        $users = [
            // Administrator
            [
                'name' => 'Administrator',
                'email' => 'administrator@gmail.com',
                'password' => 'admindigicampus123',
                'role' => 'administrator',
                'department_id' => null,
            ],
            // Assessor
            [
                'name' => 'Assessor COBIT',
                'email' => 'assessor@gmail.com',
                'password' => 'assessor123',
                'role' => 'assessor',
                'department_id' => null,
            ],
            // Direktur
            [
                'name' => 'Direktur',
                'email' => 'direktur@gmail.com',
                'password' => 'direktur123',
                'role' => 'direktur',
                'department_id' => null,
            ],
            // Wadir
            [
                'name' => 'Wakil Direktur 1',
                'email' => 'wakildirektur@gmail.com',
                'password' => 'wadir123',
                'role' => 'wadir',
                'department_id' => $wadir?->id_department,
            ],
            // Kajur
            [
                'name' => 'Ketua Jurusan Teknik',
                'email' => 'kepalajurusanteknik@gmail.com',
                'password' => 'kajurteknik123',
                'role' => 'kajur',
                'department_id' => $kajur?->id_department,
            ],
            // Keuangan
            [
                'name' => 'Keuangan',
                'email' => 'keuangan@gmail.com',
                'password' => 'keuangan123',
                'role' => 'keuangan',
                'department_id' => null,
            ],
            // Kaprodi TI
            [
                'name' => 'Kaprodi Teknologi Informasi',
                'email' => 'kaproditi@gmail.com',
                'password' => 'kaproditi123',
                'role' => 'kaprodi',
                'department_id' => $prodiTI?->id_department,
            ],
            // Admin Prodi TI
            [
                'name' => 'Admin Prodi TI',
                'email' => 'adminproditi@gmail.com',
                'password' => 'adminproditi123',
                'role' => 'admin_prodi',
                'department_id' => $prodiTI?->id_department,
            ],
            // UPA
            [
                'name' => 'Admin UPA Bahasa',
                'email' => 'adminupabahasa@gmail.com',
                'password' => 'adminupabahasa123',
                'role' => 'upa',
                'department_id' => $upa?->id_department,
            ],
            // Dosen TI
            [
                'name' => 'Tri Lestariningsih',
                'email' => 'trilestariningsih@gmail.com',
                'password' => 'dosenti123',
                'role' => 'dosen',
                'department_id' => $prodiTI?->id_department,
            ],
            [
                'name' => 'Hendrik Kusbandono',
                'email' => 'hendrikkusbandono@gmail.com',
                'password' => 'dosenti123',
                'role' => 'dosen',
                'department_id' => $prodiTI?->id_department,
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make($data['password']),
                    'role_id' => $roles[$data['role']] ?? null,
                    'department_id' => $data['department_id'],
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}