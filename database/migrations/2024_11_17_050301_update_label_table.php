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
            $table->string('created_by')->nullable(); // User's email (not referenced)
            $table->string('department')->nullable(); // Department (user input)
            $table->string('building')->nullable(); // Building (user input)
            $table->string('room_number', 255)->nullable(); // Room number (user input)
            $table->string('lab_name', 255)->nullable(); // Laboratory name (user input)
            $table->string('principal_investigator')->nullable(); // Principal investigator (user input)

            $table->date('date_created'); // Date the label was created
            $table->string('container_size'); // Container size as a string
            $table->string('label_size')->nullable();// Label size
            $table->integer('quantity'); // Quantity of the substance
            $table->string('units')->nullable();; // Units of measurement (e.g., L, mL)
            $table->tinyInteger('status_of_label')->default(Label::STATUS_PENDING); // Status with default value
            $table->text('message')->nullable(); // Optional message field
            $table->timestamps(); // created_at and updated_at columns
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
