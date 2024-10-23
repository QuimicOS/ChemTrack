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
        Schema::table('users',function(Blueprint $table){
            $table->removeColumn('email_verified_at');
            $table->removeColumn('password');
            $table->string('last_name');
            $table->string('role');
            $table->string('department',255);
            $table->string('user_status');
            $table->boolean('certification_status');
            $table->date('certification_date');







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
