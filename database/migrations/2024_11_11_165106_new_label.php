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
        Schema::create('label', function (Blueprint $table) {
            $table->id('label_id'); // Unique identifier for the label
            $table->string('created_by'); // References the user's email
            $table->string('department'); // Department from the user table

            $table->string('building'); // Building name from the laboratory table
            $table->string('room_number',255); // Room number from the laboratory table
            $table->string('lab_name',255); // Laboratory name from the laboratory table
            $table->string('principal_investigator'); // PI from the laboratory table


            $table->date('date_created'); // Date the label was created
            $table->integer('container_size'); 
            $table->string('label_size',255); 
            $table->integer('quantity'); // Quantity of the substance
            $table->string('units'); // Units of measurement (e.g., L, mL)
            $table->string('status_of_label');
            $table->text('message')->nullable(); // Message field (optional)
            $table->timestamps();
            
            // Indexes
            $table->foreign('created_by')->references('email')->on('users')->onDelete('set null'); // Foreign key to user email
            $table->foreign('department')->references('department')->on('users')->onDelete('set null'); // Foreign key to user department

            $table->foreign('room_number')->references('room_number')->on('laboratories')->onDelete('set null'); 
            $table->foreign('building')->references('building_name')->on('laboratories')->onDelete('set null'); 
            $table->foreign('lab_name')->references('lab_name')->on('laboratories')->onDelete('set null'); 
            $table->foreign('principal_investigator')->references('principal_investigator')->on('laboratories')->onDelete('set null'); 


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('labels');
    }
};
