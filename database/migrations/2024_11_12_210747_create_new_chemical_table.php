<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('chemical', function (Blueprint $table) {
            $table->id();
            $table->string('chemical_name', 255);
            $table->string('cas_number', 255);
            $table->tinyInteger('status_of_chemical')->default(1); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_chemical');
    }
};
