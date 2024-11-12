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
            // Add foreign key columns
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('label_id')->nullable();
            $table->unsignedBigInteger('laboratory_id')->nullable();
            $table->unsignedBigInteger('chemical_id')->nullable();
            $table->unsignedBigInteger('pickup_id')->nullable();

            // Add foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('label_id')->references('id')->on('label')->onDelete('set null');
            $table->foreign('laboratory_id')->references('id')->on('laboratories')->onDelete('set null');
            $table->foreign('chemical_id')->references('id')->on('chemical')->onDelete('set null');
            $table->foreign('pickup_id')->references('id')->on('pickup')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            //
        });
    }
};
