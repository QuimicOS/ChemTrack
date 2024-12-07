<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupRequest extends Model
{
    use HasFactory;

    protected $table = 'pickup';

    protected $fillable = [
        'completion_date',
        'status_of_pickup',
        'timeframe',
        'completion_method',
        'message',
        'label_id',
        'user_id',
        'invalidated_by'
    ];

    /**
     * PICKUP REQUEST STATUS:
     * 0 = Invalid
     * 1 = Completed
     * 2 = Pending
     * 3 = Overdue
     */

    protected static function boot()
    {
        parent::boot();

        // Listen to the `created` event
        static::created(function ($pickupRequest) {
            // Trigger a notification (use a service or event instead of directly calling a controller)
            Notification::create([
                'send_to' => 'Administrator',
                'status_of_notification' => 0,
                'notification_type' => 0,
                'message' => "A new Pickup Request has been made by {$pickupRequest->label->created_by} for Label {$pickupRequest->label_id} in {$pickupRequest->label->room_number}, {$pickupRequest->label->building} for a container of {$pickupRequest->label->container_size}.",
                'label_id' => $pickupRequest->label->label_id,
                'pickup_id' => $pickupRequest->id,
            ]);
        });

        static::updated(function ($pickupInvalid) {
            // Trigger a notification (use a service or event instead of directly calling a controller)
            if($pickupInvalid->status_of_pickup===0){

            Notification::create([
                'send_to' => 'Administrator',
                'status_of_notification' => 0,
                'notification_type' => 1,
                'message' => "Pickup Request {$pickupInvalid->id} has been invalidated by {$pickupInvalid->invalidated_by}. Reason for invalidation: {$pickupInvalid->message}.",
                'label_id' => $pickupInvalid->label->label_id,
                'pickup_id' => $pickupInvalid->id,
            ]);
        }
        });
        
        static::updated(function ($pickupInvalid) {
            // Trigger a notification (use a service or event instead of directly calling a controller)
            if($pickupInvalid->status_of_pickup===0){

            Notification::create([
                'send_to' => $pickupInvalid->label->room_number,
                'status_of_notification' => 0,
                'notification_type' => 1,
                'message' => "Pickup Request {$pickupInvalid->id} has been invalidated by {$pickupInvalid->invalidated_by}. Reason for invalidation: {$pickupInvalid->message}.",
                'label_id' => $pickupInvalid->label->label_id,
                'pickup_id' => $pickupInvalid->id,
            ]);
        }
        });
    }

    // DIRECT RELATIONSHIPS
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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
