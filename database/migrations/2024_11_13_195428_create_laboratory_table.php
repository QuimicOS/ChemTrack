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
        Schema::create('laboratory', function (Blueprint $table) {
            $table->id();
            $table->string('department',255);
            $table->string('building_name',255);
            $table->string('room_number',255);
            $table->string('lab_name',255);
            $table->string('professor_investigator',255);
            $table->string('department_director',255);
            $table->string('lab_status',255);

            $table->string('created_by', 255)->nullable();

            $table->foreign('created_by')->references('email')->on('user')->onDelete('set null');

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
