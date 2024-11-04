<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laboratory extends Model
{
    // Specify which fields are mass assignable
    protected $fillable = [
        'building_number',
        'building_name',
        'room_number',
        'room_department',
        'lab_name',
        'lab_department',
        'lab_status',
        'professor_investigator',
        'lab_supervisor',
    ];
    protected $primaryKey = 'id';

    protected $table = 'laboratories';

    public function users()
    {
        return $this->belongsto(User::class, 'user_id');
    }




    public static $AdminPostRulesForLab= [
        'building_number' => 'required|string|max:255',
        'building_name' => 'required|string|max:255',
        'room_number' => 'required|string|max:255',
        'room_department' => 'required|string|max:255',
        'lab_name' => 'required|string|max:255',
        'lab_department' => 'required|string|max:255',
        'lab_status' => 'required|string|max:255',
        'professor_investigator' => 'required|string|max:255',
        'lab_supervisor' => 'required|string|max:255',
     ];

     
    public static $AdminPutRulesForLab= [
        'building_number' => 'required|string|max:255',
        'building_name' => 'required|string|max:255',
        'room_number' => 'required|string|max:255',
        'room_department' => 'required|string|max:255',
        'lab_name' => 'required|string|max:255',
        'lab_department' => 'required|string|max:255',
        'lab_status' => 'required|string|max:255',
        'professor_investigator' => 'required|string|max:255',
        'lab_supervisor' => 'required|string|max:255',
     ];
}
