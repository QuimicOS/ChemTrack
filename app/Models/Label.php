<?php

namespace App\Models;
use Carbon\Carbon; 

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Label extends Model
{

    use HasFactory;
    protected $table = 'label';

    // Fillable fields for mass assignment (adjusted to match your table columns)
    protected $fillable = [
        'label_id', 
        'created_by', 
        'department', 
        'building', 
        'room_number', 
        'lab_name', 
        'date_created', 
        'principal_investigator', 
        'quantity', 
        'units', 
        'status', 
        'message'
    ];


    // Custom method to fetch User by created_by (email)
    public function getUser()
    {
        return User::where('email', $this->created_by)
        ->where('department',$this->department)
        ->first();
    }





    // Custom method to fetch Laboratory details by multiple fields
    public function getLaboratory()
    {
        return Laboratory::where('room_number', $this->room_number)
            ->where('building_name', $this->building)
            ->where('lab_name', $this->lab_name)
            ->where('principal_investigator', $this->principal_investigator)
            ->first();
    }




    // Relationship with Chemicals (Many-to-Many)
    public function chemicals()
    {
        return $this->belongsToMany(Chemical::class, 'label_chemical', 'label_id', 'chemical_id')
                    ->withPivot('percentage'); // For storing the percentage in the pivot table
    }

  


    protected $nullable = [
        'message'
    ];

    public static $LabelPostRules = [
        'chemical_name' => 'required|string|max:255',
        'accumulation_start_date' => 'required|date',
        'container_size' => 'required|numeric',
        'quantity' => 'required|numeric',
        'solution_percentage' => 'required|numeric',
        'label_size' => 'required|string',
        'units' => 'required|string',
        'status_of_label' => 'required|string',
        'message' => 'required|string',
    ];

    public static $LabelPutRules = [

        'quantity' => 'required|numeric',
        'units' => 'required|string',

    ];


  
    

}
