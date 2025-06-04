<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class RoleUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $editorRole = Role::where('name', 'editor')->first();

        $adminUsers = [
            'admin@example.com',
            'admin2@example.com'
        ];

        foreach ($adminUsers as $email) {
            $user = User::where('email', $email)->first();
            $user->roles()->syncWithoutDetaching([$adminRole->id]);
        }

        // Assign editor role to editor users
        $editorUsers = [
            'editor1@example.com',
            'editor2@example.com',
            'editor3@example.com',
            'editor4@example.com'
        ];

        foreach ($editorUsers as $email) {
            $user = User::where('email', $email)->first();
            $user->roles()->syncWithoutDetaching([$editorRole->id]);
        }
    }
}
