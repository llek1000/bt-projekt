<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conference_years', function (Blueprint $table) {
            $table->id();
            $table->string('year');
            $table->enum('semester', ['Winter', 'Summer']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['year', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conference_years');
    }
};
