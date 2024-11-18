<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Label;

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
            $table->string('created_by')->nullable(); // References the user's email
            $table->string('department'); // Department from the user table
            $table->string('building'); // Building name from the laboratory table
            $table->string('room_number'); // Room number from the laboratory table
            $table->string('lab_name'); // Laboratory name from the laboratory table
            $table->string('principal_investigator'); // PI from the laboratory table
            $table->date('date_created'); // Date the label was created
            $table->string('container_size'); // Container size as a string
            $table->string('label_size'); // Label size
            $table->integer('quantity'); // Quantity of the substance
            $table->string('units'); // Units of measurement (e.g., L, mL)
            $table->tinyInteger('status_of_label')->default(1); // Default status is 'Pending'
            $table->text('message')->nullable(); // Message field (optional)
            $table->timestamps();
        
            // Foreign key constraints
            $table->foreign('created_by')->references('email')->on('users')->onDelete('set null');
            $table->foreign('department')->references('department')->on('users')->onDelete('set null');
            $table->foreign('room_number')->references('room_number')->on('laboratory')->onDelete('set null');
            $table->foreign('building')->references('building_name')->on('laboratory')->onDelete('set null');
            $table->foreign('lab_name')->references('lab_name')->on('laboratory')->onDelete('set null');
            $table->foreign('principal_investigator')->references('professor_investigator')->on('laboratory')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('label');
    }
};
