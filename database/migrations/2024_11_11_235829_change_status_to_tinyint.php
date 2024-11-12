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
        Schema::table('label', function (Blueprint $table) {
            $table->tinyInteger('status_of_label')->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('user_status')->change();
        });

        Schema::table('laboratories', function (Blueprint $table) {
            $table->tinyInteger('lab_status')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('label', function (Blueprint $table) {
            $table->string('status_of_label')->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('user_status')->change();
        });

        Schema::table('laboratories', function (Blueprint $table) {
            $table->string('lab_status')->change();
        });
    }
};
