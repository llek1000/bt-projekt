<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('articles','conference_year_id')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->unsignedBigInteger('conference_year_id')->after('content');
                $table->foreign('conference_year_id')
                      ->references('id')->on('conference_years')
                      ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('articles','conference_year_id')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->dropForeign(['conference_year_id']);
                $table->dropColumn('conference_year_id');
            });
        }
    }
};