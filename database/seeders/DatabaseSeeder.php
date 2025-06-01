<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConferenceYear;
use App\Models\Article;
use App\Models\EditorAssignment;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles and users first
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            RoleUserSeeder::class,
            DepartmentSeeder::class,
        ]);

        // Create conference years with unique combinations
        ConferenceYear::factory()
            ->count(6)
            ->sequence(
                ['year' => '2020', 'semester' => 'Winter'],
                ['year' => '2020', 'semester' => 'Summer'],
                ['year' => '2021', 'semester' => 'Winter'],
                ['year' => '2021', 'semester' => 'Summer'],
                ['year' => '2022', 'semester' => 'Winter'],
                ['year' => '2022', 'semester' => 'Summer'],
            )
            ->create();

        // Create articles - remove the user_id assignment
        Article::factory()->count(20)->create();

        // Create editor assignments using existing users and conference years
        EditorAssignment::factory()->count(10)->create();
    }
}
