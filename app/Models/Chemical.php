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
