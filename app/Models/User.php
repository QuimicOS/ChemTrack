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
        'password',
        'role',
        'department',
        'room_number',
        'user_status',
        'certification_date',
        'certification_status',
    ];

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
        'email' => 'required|string|max:255',
        'role' => 'required|string|max:255',
        'department' => 'required|string|max:255',
        'room_number' => 'required|string|max:255',
        'user_status' => 'required|string|max:255',
        'certification_status' => 'required|boolean',
        'certification_date' => 'required|date',
        'lab_id' => 'nullable|integer'
        
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
