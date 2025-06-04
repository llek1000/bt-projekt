<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('departments')->insert([
            ['name' => 'Informatika',           'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Matematika',            'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Fyzika',                'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Biotechnológia',        'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
