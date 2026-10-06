<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $roles = [
            'administrator',
            'admin_prodi',
            'kaprodi',
            'dosen',
            'kajur',
            'wadir',
            'direktur',
            'keuangan',
            'upa',
            'assessor',
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert([
                'name' => $role,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('roles')->truncate();
    }
};