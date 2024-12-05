<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notification';

    protected $fillable = [
        'send_to',
        'message',
        'status_of_notification',
        'notification_type',
        'user_id',
        'label_id',
        'laboratory_id',
        'chemical_id',
        'pickup_id',
    ];

    /**
     * NOTIFICATION STATUS:
     * 0 = Unread
     * 1 = Read
     */
    
    /**
     * NOTIFICATION TYPES:
     * 0 = New Pickup Request
     * 1 = Pickup Request Invalidated
     * 2 = Label Due For Pickup (5 Month Warning)
     * 3 = Label Without Pickup Request (5 1/2 Month Warning)
     * 4 = Label Overdue (6 Month Warning)
     * 5 = New Chemical
     * 6 = User Role Requested
     * 7 = Maximum Capacity Reached (55 Gallons)
     * 8 = Maximum P Material Capacity Reached (1 Quart)
     * 9 = Label Invalidated
     */
    
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
        return $this->hasMany(Label::class, 'label_id');
    }
}
