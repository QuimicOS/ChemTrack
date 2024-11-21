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
        'status_of_chemical'
    ];

    protected static function boot()
    {
        parent::boot();

        // Listen to the `created` event
        static::created(function ($chemical) {
            // Trigger a notification (use a service or event instead of directly calling a controller)
            Notification::create([
                'send_to' => 'Administrator',
                'status_of_notification' => 0,
                'notification_type' => 5,
                'message' => 'A new chemical has been created: ' . $chemical->chemical_name . ' with CAS Number: ' .$chemical->cas_number,
                'chemical_id' => $chemical->id,
            ]);
        });
    }

    // INVERSE RELATIONSHIPS
    public function user()
    {
    return $this->hasMany(User::class, 'user_id');
    }

    public function notification()
    {
        return $this->belongsTo(Notification::class, 'chemical_id');
    }

    public function label()
    {
        return $this->belongsTo(Label::class, 'label_id');
    }

    public function index()
{
    $chemicals = Chemical::all(); // Fetch all chemicals
    return response()->json($chemicals);
}
}
