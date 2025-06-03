<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConferenceYearsTable extends Migration
{
    public function up()
    {
        Schema::create('conference_years', function (Blueprint $table) {
            $table->id();
            $table->integer('year')->unique();
            $table->string('description');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('conference_years');
    }
}
