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
        //
        Schema::create('rooms', function (Blueprint $table) { 
            $table->id();
           $table->unsignedBigInteger('user_id')->nullable();
            $table->string('room_number',255)->nullable();
            $table->string('lab_status',255)->nullable();
            $table->timestamps();
    
           $table->foreign('room_number')->references('room_number')->on('laboratory')->onDelete('set null');  
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
