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
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to labels table
            $table->unsignedBigInteger('label_id');
            $table->foreign('label_id')->references('label_id')->on('label')->onDelete('set null');

            // Foreign keys to chemical table for chemical_name and cas_number
            $table->string('chemical_name');
            $table->string('cas_number');
            
            // Foreign key constraint for chemical_name and cas_number
            $table->foreign(['chemical_name', 'cas_number'])->references(['chemical_name', 'cas_number'])->on('chemical')->onDelete('set null');

            // Additional attribute for the pivot table
            $table->float('percentage');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
