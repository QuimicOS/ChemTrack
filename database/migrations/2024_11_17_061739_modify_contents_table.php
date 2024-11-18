<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id(); // Primary key for the table
            $table->unsignedBigInteger('label_id'); // Foreign key to labels table
            
            // Fields provided by the user
            $table->string('chemical_name'); 
            $table->string('cas_number'); 
            $table->float('percentage'); 

            // Timestamps
            $table->timestamps();

            // Add foreign key constraint for label_id
            $table->foreign('label_id')->references('label_id')->on('label')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contents');
    }
};
