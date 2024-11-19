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
        Schema::table('pickup', function (Blueprint $table) {
            // Add foreign key columns
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('label_id')->nullable(); //capaz de problema algun momento

            // Add foreign key constraints
            $table->foreign('user_id')->references('id')->on('user')->onDelete('set null');
            $table->foreign('label_id')->references('label_id')->on('label')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pickup', function (Blueprint $table) {
            //
        });
    }
};