<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('label', function (Blueprint $table) {
            // Make created_by nullable
            $table->string('created_by')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('label', function (Blueprint $table) {
            // Revert created_by to NOT NULL if necessary
            $table->string('created_by')->nullable(false)->change();
        });
    }
};

