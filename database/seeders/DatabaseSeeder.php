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

        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            RoleUserSeeder::class,
        ]);


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


        Article::factory()->count(20)->create();


        EditorAssignment::factory()->count(10)->create();
    }
}
