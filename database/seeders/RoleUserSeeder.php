<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class RoleUserSeeder extends Seeder
{
    public function run(): void
    {
        // Get users and roles
        $adminUser = User::where('email', 'admin@example.com')->first();
        $editorUser = User::where('email', 'editor@example.com')->first();

        $adminRole = Role::where('name', 'admin')->first();
        $editorRole = Role::where('name', 'editor')->first();

        // Assign roles using the model methods (better than direct DB insert)
        if ($adminUser && $adminRole) {
            $adminUser->assignRole('admin');
        }

        if ($editorUser && $editorRole) {
            $editorUser->assignRole('editor');
        }

        // Optional: Output confirmation
        $this->command->info('Roles assigned successfully!');
        $this->command->info('Admin user roles: ' . implode(', ', $adminUser->getRoleNames()));
        $this->command->info('Editor user roles: ' . implode(', ', $editorUser->getRoleNames()));
    }
}
