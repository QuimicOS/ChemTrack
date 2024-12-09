<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Label;
use App\Models\PickupRequest;
use App\Models\Notification;
use Carbon\Carbon;

class Almost3YearNotice extends Command
{
    // Command signature
    protected $signature = 'notifications:admin-35months';

    // Command description
    protected $description = 'Create notifications for admin when labels or pickup that are close to 3 years and will be deleted';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Call the methods you need
        $this->Month35();
    }

    public function Month35()
    {
        // Calculate the threshold date for 35 months ago
        $thresholdDate = Carbon::now()->subMonths(35)->toDateString();

        // Fetch Labels exactly or beyond 35 months
        $labels = Label::whereDate('date_created', '<=', $thresholdDate)
            ->whereNotIn('status_of_label', [0]) // Exclude invalid
            ->whereNotExists(function ($query) {
                $query->selectRaw(1)
                    ->from('notification')
                    ->whereColumn('notification.label_id', 'label.label_id') // Match label ID
                    ->where('notification.notification_type', 10);
            })
            ->get();

        // Fetch Pickups exactly or beyond 35 months
        $pickups = PickupRequest::whereDate('created_at', '<=', $thresholdDate)
            ->whereNotIn('status_of_pickup', [0]) // Exclude invalid
            ->whereNotExists(function ($query) {
                $query->selectRaw(1)
                    ->from('notification')
                    ->whereColumn('notification.pickup_id', 'pickup.id') // Match pickup ID
                    ->where('notification.notification_type', 10);
            })
            ->get();

        // Process Labels
        foreach ($labels as $label) {
            Notification::create([
                'send_to' => 'Administrator',
                'notification_type' => 10,
                'status_of_notification' => 0,
                'message' => "Label ID {$label->label_id} is nearing deletion! Review and take any action if needed!",
                'label_id' => $label->id,
            ]);
        }

        // Process Pickups
        foreach ($pickups as $pickup) {
            Notification::create([
                'send_to' => 'Administrator',
                'notification_type' => 10,
                'status_of_notification' => 0,
                'message' => "Pickup ID {$pickup->id} is nearing deletion! Review and take any action  if needed!",
                'pickup_id' => $pickup->id,
            ]);
        }

        $this->info('Notifications created for labels and pickups that are 35 months old.');
    }

}

