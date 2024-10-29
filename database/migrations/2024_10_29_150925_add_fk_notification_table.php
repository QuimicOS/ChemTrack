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
        Schema::table('notification', function (Blueprint $table) {
            // Foreign key to 'users' table
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('set null'); // Set to null if user is deleted

            // Foreign key to 'chemicals' table
            $table->unsignedBigInteger('chemical_id')->nullable();
            $table->foreign('chemical_id')
                  ->references('id')->on('chemical')
                  ->onDelete('set null'); // Set to null if chemical is deleted

            // Foreign key to 'laboratories' table
            $table->unsignedBigInteger('lab_id')->nullable();
            $table->foreign('lab_id')
                  ->references('id')->on('laboratories')
                  ->onDelete('set null'); // Set to null if lab is deleted

            $table->unsignedBigInteger('pickup_id')->nullable();
            $table->foreign('pickup_id')
                  ->references('id')->on('pickup')
                  ->onDelete('set null'); // Set to null if user is deleted

            // Foreign key to 'labels' table
            $table->unsignedBigInteger('label_id')->nullable();
            $table->foreign('label_id')
                  ->references('id')->on('label')
                  ->onDelete('set null'); // Set to null if label is deleted
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
