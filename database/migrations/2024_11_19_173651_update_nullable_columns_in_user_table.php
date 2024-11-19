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
        Schema::table('user', function (Blueprint $table) {
            $table->string('role', 255)->nullable()->change();
            $table->string('department', 255)->nullable()->change();
            $table->string('user_status', 255)->nullable()->change();
            $table->boolean('certification_status')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->string('role', 255)->nullable(false)->change();
            $table->string('department', 255)->nullable(false)->change();
            $table->string('user_status', 255)->nullable(false)->change();
            $table->boolean('certification_status')->nullable(false)->change();
        });
    }
};
