<?php

namespace App\Models;
use Carbon\Carbon; 

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Label extends Model
{

    use HasFactory;
    protected $table = 'label';






    const STATUS_INVALID = 0;
    const STATUS_PENDING = 1;
    const STATUS_COMPLETED = 2;

    public static $statuses = [
        self::STATUS_INVALID => 'INVALID',
        self::STATUS_PENDING => 'Pending',
        self::STATUS_COMPLETED => 'Completed'
    ];

    // Fillable fields for mass assignment (adjusted to match your table columns)
    protected $fillable = [
        'label_id', 
        'created_by', 
        'department', 
        'building', 
        'room_number', 
        'lab_name', 
        'date_created', 
        'container_size',
        'principal_investigator', 
        'quantity', 
        'units', 
        'status_of_label', 
        'message'
    ];




     // Define the user relationship
     public function user()
     {
         return $this->belongsTo(User::class, 'created_by', 'email');
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




    

    
  


    protected $nullable = [
        'message'
    ];

    public static $LabelPostRules = [
        'created_by' => 'required|email',
        'department' => 'required|string|max:255',
        'building' => 'required|string|max:255',
        'room_number' => 'required|string|max:255',
        'lab_name' => 'required|string|max:255',
        'date_created' => 'required|date',
        'principal_investigator' => 'required|string|max:255',
        'container_size' => 'required|integer',
        'label_size' => 'required|string|max:255',
        'quantity' => 'required|integer',
        'units' => 'required|string|max:10',
        'status_of_label' => 'required|integer|max:50',
        'message' => 'nullable|string|max:255'
    ];

    public static $LabelPutRules = [

        'created_by' => 'required|email',
        'department' => 'required|string|max:255',
        'building' => 'required|string|max:255',
        'room_number' => 'required|string|max:255',
        'lab_name' => 'required|string|max:255',
        'date_created' => 'required|date',
        'principal_investigator' => 'required|string|max:255',
        'quantity' => 'required|integer',
        'units' => 'required|string|max:10',
        'status_of_label' => 'required|string|max:50',
        'message' => 'nullable|string|max:255'

    ];


  
    

}
