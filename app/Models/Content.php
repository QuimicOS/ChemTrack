<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Content extends Model
{
    use HasFactory;

    protected $table = 'contents';

    protected $fillable = ['label_id', 'chemical_name', 'cas_number', 'percentage'];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($content) {
            try {
                // List of restricted chemicals
                $pMaterials = ['aluminum phosphide', 'ammonium picrate', 'mercury fulminate', 'nitroglycerine', 'tetranitromethane', 'zinc phosphide'];

                // Check if the chemical is in the restricted list
                if (in_array(strtolower($content->chemical_name), $pMaterials)) {
                    // Retrieve the associated label and its room number
                    $label = $content->label()->first();

                    if ($label) {
                        $roomNumber = $label->room_number;

                        // Use DB to calculate total weight and volume in the room
                        $totals = DB::table('contents')
                            ->join('label', 'contents.label_id', '=', 'label.label_id')
                            ->where('label.room_number', $roomNumber)
                            ->whereIn('contents.chemical_name', $pMaterials)
                            ->selectRaw("
                                SUM(CASE 
                                    WHEN label.units = 'Kilograms' THEN label.quantity
                                    WHEN label.units = 'Grams' THEN label.quantity / 1000
                                    WHEN label.units = 'Pounds' THEN label.quantity * 0.453592
                                    ELSE 0 
                                END) as total_weight,
                                SUM(CASE 
                                    WHEN label.units = 'Liters' THEN label.quantity
                                    WHEN label.units = 'Milliliters' THEN label.quantity / 1000
                                    WHEN label.units = 'Gallons' THEN label.quantity * 3.78541
                                    ELSE 0 
                                END) as total_volume
                            ")
                            ->first();

                        $totalWeight = $totals->total_weight ?? 0;
                        $totalVolume = $totals->total_volume ?? 0;

                        // Log the calculated totals for debugging
                        Log::info("Total weight in room {$roomNumber}: {$totalWeight} kg");
                        Log::info("Total volume in room {$roomNumber}: {$totalVolume} liters");

                        // Check thresholds and create notification if exceeded
                        if ($totalWeight >= 1 || $totalVolume >= 1) {
                            \App\Models\Notification::create([
                                'notification_type' => 8,
                                'message' => "The maximum quantity of P materials in room {$roomNumber} has exceeded 1 Kilogram or 1 Liter. Total weight: {$totalWeight} kg, Total volume: {$totalVolume} liters.",
                                'send_to' => 'Administrator',
                                'status_of_notification' => 0,
                                'label_id' => $label->label_id,
                            ]);

                            Log::info("Notification created for Room {$roomNumber} due to exceeding capacity.");
                        }
                    } else {
                        Log::warning("No label found for Content ID {$content->id}.");
                    }
                } else {
                    Log::info("Content added with chemical '{$content->chemical_name}' is not in the restricted list.");
                }
            } catch (\Exception $e) {
                Log::error("Error in Content created hook: " . $e->getMessage());
            }
        });
    }

    // Define relationship to Label
    public function label()
    {
        return $this->belongsTo(Label::class, 'label_id', 'label_id');
    }
}
