<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;
// use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;
    // use HasApiTokens;

    protected $primaryKey = 'id';
    protected $table = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'role',
        'department',
        'user_status',
        'certification_status',        
        'certification_date',
        'room_number',
        
    ];

    protected static function boot()
    {
        parent::boot();
    
        static::created(function ($user) {
            if ($user->user_status === 'Requested') {
                Notification::create([
                    'send_to' => 'Administrator', 
                    'status_of_notification' => 0, 
                    'notification_type' => 6, 
                    'message' => "A Professor has requested {$user->role} access for {$user->email}",
                    'user_id' => $user->id,
                ]);
            }
        });
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'certification_date' => 'date',
        'certification_status' => 'boolean',
    ];


     public static $PostRules= [
        
        'name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255',
        'role' => 'nullable|string|max:255',
        'department' => 'nullable|string|max:255',
        'user_status' => 'nullable|string|max:255',
        'room_number' => 'nullable|string|max:255|exists:laboratory,room_number'
        
     ];

     public static $PutRules= [
        
        'name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|string|max:255',
        'role' => 'required|string|max:255',
        'department' => 'required|string|max:255',
        'user_status' => 'required|string|max:255',
   
        
     ];
}
