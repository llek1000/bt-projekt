<?php

namespace Database\Seeders;

use App\Models\File;
use App\Models\User;
use Illuminate\Database\Seeder;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $user = User::first();


        File::create([

            'id' => 2,
            'name' => '1749072752_tfb-ruleset-6-3.pdf',
            'original_name' => 'TFB Ruleset 6-3.pdf',
            'file_path' => 'editor_files/1749072752_tfb-ruleset-6-3.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 174754,
            'category' => null,
            'uploaded_by' => $user?->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
