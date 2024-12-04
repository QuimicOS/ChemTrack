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
        Schema::create('pickup', function (Blueprint $table) {
            $table->id();
            $table->string('timeframe', 255);
            $table->string('invalidated_by', 255)->nullable();
            $table->string('completion_method', 255)->nullable();
            $table->date('completion_date')->nullable();
            $table->tinyInteger('status_of_pickup')->default(2); 
            $table->string('message', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickup');
    }
};
