<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        User::create([
            'username' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => 1, // Assuming role_id 1 is for Admin
        ]);


        User::create([
            'username' => 'Editor',
            'email' => 'editor@example.com',
            'password' => Hash::make('password'),
            'role_id' => 2, // Assuming role_id 2 is for Editor
        ]);


    }
}
