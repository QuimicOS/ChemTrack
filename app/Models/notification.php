<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notification';

    protected $fillable = [
        'pending_date',
        'send_to',
        'message'
    ];
    
    // DIRECT RELATIONSHIPS
    public function pickupRequests()
    {
        return $this->hasMany(PickupRequest::class, 'pickup_id');
    }

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
}
