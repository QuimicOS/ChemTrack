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
        Schema::create('user', function (Blueprint $table) {
        $table->id();
        $table->string('name',255);
        $table->string('last_name',255);
        $table->string('email',255)->unique();
        $table->string('role',255);
        $table->string('department',255);
        $table->string('user_status',255);
        $table->boolean('certification_status');
        $table->date('certification_date')->nullable();
        $table->string('room_number',255)->nullable();
        $table->timestamps();



        // $table->foreign('room_number')->references('room_number')->on('laboratory')->onDelete('set null'); 

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
