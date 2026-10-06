<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Teknologi Informasi', 'type' => 'prodi'],
            ['name' => 'Teknik Komputer Kontrol', 'type' => 'prodi'],
            ['name' => 'Teknologi Rekayasa Perangkat Lunak', 'type' => 'prodi'],
            ['name' => 'Jurusan Teknik', 'type' => 'kajur'],
            ['name' => 'Jurusan Akuntansi', 'type' => 'kajur'],
            ['name' => 'Jurusan Administrasi Bisnis', 'type' => 'kajur'],
            ['name' => 'Wakil Direktur 1', 'type' => 'wadir'],
            ['name' => 'Unit Pelayanan Akademik Bahasa', 'type' => 'upa'],
            ['name' => 'Unit Pelayanan Akademik Perpustakan', 'type' => 'upa'],
            ['name' => 'Unit Pelayanan Akademik TIK', 'type' => 'upa'],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(
                ['name' => $dept['name']],
                ['type' => $dept['type']]
            );
        }
    }
}