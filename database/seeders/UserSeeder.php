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
        ]);

        User::create([
            'username' => 'Admin2',
            'email' => 'admin2@example.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'username' => 'Editor1',
            'email' => 'editor1@example.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'username' => 'Editor2',
            'email' => 'editor2@example.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'username' => 'Editor3',
            'email' => 'editor3@example.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'username' => 'Editor4',
            'email' => 'editor4@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}
