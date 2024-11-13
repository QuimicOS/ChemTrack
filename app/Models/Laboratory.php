<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laboratory extends Model
{
    // Specify which fields are mass assignable
    protected $fillable = [
        'department',
        'building_name',
        'room_number',
        'lab_name',
        'professor_investigator',
        'department_director',
        'lab_status',
        'created_by',
    ];
    protected $primaryKey = 'id';

    protected $table = 'laboratory';

    public function users()
    {
        return $this->belongsto(User::class, 'user_id');
    }

    protected static function boot() // will assign Active to a Laboratory if left null during creation
    {
        parent::boot();

        static::creating(function ($laboratory) {
            if (is_null($laboratory->lab_status)) {
                $laboratory->lab_status = 'Active';
            }
        });
    }

    public static $AdminPostRulesForLab = [
        'department' => 'required|string|max:255',
        'building_name' => 'required|string|max:255',
        'room_number' => 'required|string|max:255',
        'lab_name' => 'required|string|max:255',
        'professor_investigator' => 'required|string|max:255',
        'department_director' => 'required|string|max:255',
        'lab_status' => 'nullable|string|max:255', 
        'created_by' => 'nullable|string|max:255', 
    ];

     
    public static $AdminPutRulesForLab= [
        'department' => 'required|string|max:255',
        'building_name' => 'required|string|max:255',
        'room_number' => 'required|string|max:255',
        'lab_name' => 'required|string|max:255',
        'professor_investigator' => 'required|string|max:255',
        'department_director' => 'required|string|max:255',
        'lab_status' => 'nullable|string|max:255', 
        'created_by' => 'nullable|string|max:255', 
     ];
}
