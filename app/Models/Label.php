<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Label extends Model
{
    use HasFactory;

    protected $table = 'label';
    protected $primaryKey = 'label_id';
    public $timestamps = false; // Disable if no created_at/updated_at columns

    // Status constants for readability
    const STATUS_INVALID = 0;
    const STATUS_PENDING = 1;
    const STATUS_ACCEPTED = 2;

    public static $statuses = [
        self::STATUS_INVALID => 'INVALID',
        self::STATUS_PENDING => 'Pending',
        self::STATUS_ACCEPTED => 'Accepted'
    ];

    // Fillable fields for mass assignment
    protected $fillable = [
        'created_by', 
        'department', 
        'building', 
        'room_number', 
        'lab_name', 
        'date_created', 
        'principal_investigator', 
        'container_size',
        'quantity', 
        'units', 
        'status_of_label', 
        'message'
    ];

    // Relationships

    // Relationship with the user who created the label
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'email');
    }

    // Many-to-Many relationship with Chemical through the 'contents' pivot table
    public function chemicals()
    {
        return $this->belongsToMany(Chemical::class, 'contents', 'label_id', 'chemical_id');
    }

    // Relationship with Laboratory (assuming you have a lab_id or similar foreign key in your labels table)
    public function laboratory()
{   
    return $this->belongsTo(Laboratory::class, 'id');
}


    // Custom method to fetch Laboratory details based on multiple fields
    public function getLaboratory()
    {
        return Laboratory::where('room_number', $this->room_number)
            ->where('building_name', $this->building)
            ->where('lab_name', $this->lab_name)
            ->where('principal_investigator', $this->principal_investigator)
            ->first();
    }

    // Validation rules for creating a label
    public static $LabelPostRules = [
        'chemical_name' => 'required|string|max:255',
        'accumulation_start_date' => 'required|date',
        'container_size' => 'required|string',
        'quantity' => 'required|numeric',
        'solution_percentage' => 'required|numeric',
        'label_size' => 'required|string',
        'units' => 'required|string',
        'status_of_label' => 'required|integer|min:0|max:2',
        'message' => 'nullable|string|max:255',
    ];

    // Validation rules for updating a label
    public static $LabelPutRules = [
        'created_by' => 'required|email',
        'department' => 'required|string|max:255',
        'building' => 'required|string|max:255',
        'room_number' => 'required|string|max:255',
        'lab_name' => 'required|string|max:255',
        'date_created' => 'required|date',
        'principal_investigator' => 'required|string|max:255',
        'quantity' => 'required|numeric',
        'units' => 'required|string|max:10',
        'status_of_label' => 'required|integer|min:0|max:2',
        'message' => 'nullable|string|max:255'
    ];
}
