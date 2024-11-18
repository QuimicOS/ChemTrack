<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $fillable = ['label_id', 'chemical_name', 'cas_number', 'percentage'];

    // Define relationship to Label
    public function label()
    {
        return $this->belongsTo(Label::class, 'label_id');
    }
}
