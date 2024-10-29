<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupRequest extends Model
{
    use HasFactory;

    protected $table = 'pickup';

    protected $fillable = [
        'request_date',
        'pickup_date',
        'status_of_pickup',
        'timeframe',
        'completion_method'
    ];

    // DIRECT RELATIONSHIPS
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function chemical()
    {
        return $this->belongsTo(Chemical::class, 'chemical_id');
    }

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class, 'lab_id');
    }

    public function label()
    {
        return $this->belongsTo(Label::class, 'label_id');
    }

    // INVERSE RELATIONSHIPS
    public function notification()
    {
        return $this->belongsTo(Notification::class, 'pickup_id');
    }
}
