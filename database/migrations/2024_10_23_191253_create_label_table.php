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
        Schema::create('label', function (Blueprint $table) {
            $table->id();
            $table->date('accumulation_start_date');
            $table->float('container_size');
            $table->string('label_size',255);
            $table->float('quantity');
            $table->string('units');
            $table->string('status_of_label',255);
            $table->float('solution_percentage');
            $table->string('message',255);












            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('label');
    }
};
