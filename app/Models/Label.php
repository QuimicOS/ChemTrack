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
        'chemical_name', 'accumulation_start_date', 'container_size', 'quantity', 
        'solution_percentage', 'label_size', 'units', 'status_of_label','created_at','message',
    ];



  


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
