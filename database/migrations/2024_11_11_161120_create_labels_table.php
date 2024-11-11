<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('labels', function (Blueprint $table) {
            $table->id('label_id'); // Unique identifier for the label
            $table->string('created_by'); // References the user's email
            $table->string('department'); // Department from the laboratory table
            $table->string('building'); // Building name from the laboratory table
            $table->string('room_number'); // Room number from the laboratory table
            $table->string('lab_name'); // Laboratory name from the laboratory table
            $table->date('date_created'); // Date the label was created
            $table->string('principal_investigator'); // PI from the laboratory table
            $table->integer('quantity'); // Quantity of the substance
            $table->string('units'); // Units of measurement (e.g., L, mL)
            $table->string('status')->default('Pending'); // Status of the label (default 'Pending')
            $table->text('message')->nullable(); // Message field (optional)
            $table->timestamps();
            
            // Indexes
            $table->foreign('created_by')->references('email')->on('users')->onDelete('set null'); // Foreign key to user email
            $table->index(['room_number', 'building', 'lab_name']); // Composite index for faster querying by lab details
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
}
