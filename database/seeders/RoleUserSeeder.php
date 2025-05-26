<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user (user_id: 1) gets admin role (role_id: 1)
        DB::table('role_user')->insert([
            'user_id' => 1,
            'role_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Editor user (user_id: 2) gets editor role (role_id: 2)
        DB::table('role_user')->insert([
            'user_id' => 2,
            'role_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
