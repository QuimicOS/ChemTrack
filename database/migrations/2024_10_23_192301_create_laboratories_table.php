<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('laboratories', function (Blueprint $table) {
            $table->id();
            $table->integer('building_number');
            $table->string('building_name',255);
            $table->string('room_number',255);
            $table->string('room_department',255);
            $table->string('lab_name',255);
            $table->string('lab_department',255);
            $table->string('lab_status',255);
            $table->string('professor_investigator',255);
            $table->string('lab_supervisor',255);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laboratories');
    }
};
