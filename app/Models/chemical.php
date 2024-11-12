<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chemical extends Model
{
    use HasFactory;

    protected $table = 'chemical';

    protected $fillable = [
        'chemical_name',
        'cas_number',
        'status'
    ];

    // INVERSE RELATIONSHIPS
    public function pickupRequests()
    {
    return $this->hasMany(PickupRequest::class, 'chemical_id');
    }

    public function notification()
    {
        return $this->belongsTo(Notification::class, 'chemical_id');
    }


    public function labels(){
        return $this->belongsToMany(Label::class, 'label_chemical', 'chemical_id', 'label_id')
        ->withPivot('chemical_name', 'cas_number', 'percentage')
        ->withTimestamps();
    }

    
}
