<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEditorConferenceYearTable extends Migration
{
    public function up()
    {
        Schema::create('editor_conference_year', function (Blueprint $table) {
            $table->id();
            $table->foreignId('editor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('conference_year_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('editor_conference_year');
    }
}
