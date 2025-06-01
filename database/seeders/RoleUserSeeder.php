<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the users we created in UserSeeder
        $adminUser = User::where('email', 'admin@example.com')->first();
        $editorUser = User::where('email', 'editor@example.com')->first();

        // Get the roles
        $adminRole = Role::where('name', 'admin')->first();
        $editorRole = Role::where('name', 'editor')->first();

        // Only assign roles if both user and role exist
        if ($adminUser && $adminRole) {
            DB::table('role_user')->insert([
                'user_id' => $adminUser->id,
                'role_id' => $adminRole->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($editorUser && $editorRole) {
            DB::table('role_user')->insert([
                'user_id' => $editorUser->id,
                'role_id' => $editorRole->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
