<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Label;
use App\Models\Notification;
use Carbon\Carbon;

class CreateAdminLabelNotification extends Command
{
    // Command signature
    protected $signature = 'notifications:admin-labels';

    // Command description
    protected $description = 'Create notifications for admin when labels are pending for over 5.5 months';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Call the methods you need
        $this->Month5HalfToMonth6();
        $this->Month5ToMonth5Half();
        $this->Month6();
    }

    public function Month5HalfToMonth6()
    {
        // Your existing logic
        $labels = Label::where('status_of_label', 1) 
            ->whereDoesntHave('pickupRequests')
            ->whereDoesntHave('notifications', function ($query) {
                $query->where('notification_type', 3); 
            })
            ->get();

        if ($labels->isNotEmpty()) {
            foreach ($labels as $label) {
                $accumulationStartDate = Carbon::parse($label->date_created);
                $fiveAndHalfMonths = $accumulationStartDate->copy()->addMonths(5)->addWeeks(2);
                $sixMonths = $accumulationStartDate->copy()->addMonths(6);

                $today = Carbon::now();
                if ($today->between($fiveAndHalfMonths, $sixMonths)) {
                    Notification::create([
                        'send_to' => 'Administrator', 
                        'notification_type' => 3,
                        'status_of_notification' => 0, 
                        'message' => "Label {$label->label_id} in Room {$label->room_number}, {$label->building} has been pending for over 5.5 months.",
                        'label_id' => $label->label_id,
                    ]);
                }
            }
            $this->info('Notifications created for applicable labels.');
        } else {
            $this->info('No labels meet the conditions.');
        }
    }
    public function Month5ToMonth5Half()
    {
        // Your existing logic
        $labels = Label::where('status_of_label', 1) 
            ->whereDoesntHave('pickupRequests')
            ->whereDoesntHave('notifications', function ($query) {
                $query->where('notification_type', 2); 
            })
            ->get();

        if ($labels->isNotEmpty()) {
            foreach ($labels as $label) {
                $accumulationStartDate = Carbon::parse($label->date_created);
                $fiveMonths = $accumulationStartDate->copy()->addMonths(5);
                $fiveAndHalfMonths = $accumulationStartDate->copy()->addMonths(5)->addWeeks(2);

                $today = Carbon::now();
                if ($today->between($fiveMonths, $fiveAndHalfMonths)) {
                    Notification::create([
                        'send_to' => $label->room_number, 
                        'notification_type' => 2,
                        'status_of_notification' => 0, 
                        'message' => "Label {$label->label_id} in Room {$label->room_number}, {$label->building} requires pickup request.",
                        'label_id' => $label->label_id,
                    ]);
                }
            }
            $this->info('Notifications created for applicable labels.');
        } else {
            $this->info('No labels meet the conditions.');
        }
    }
    public function Month6()
    {
        // Your existing logic
        $labels = Label::whereNotIn('status_of_label', [0, 2])
            ->whereDoesntHave('notifications', function ($query) {
                $query->where('notification_type', 4); 
            })
            ->get();

        if ($labels->isNotEmpty()) {
            foreach ($labels as $label) {
                $accumulationStartDate = Carbon::parse($label->date_created);
                $sixMonths = $accumulationStartDate->copy()->addMonths(6)->toDateString();
                $today = Carbon::now()->toDateString();
                if ($today >= $sixMonths) {
                    Notification::create([
                        'send_to' => 'Administrator', 
                        'notification_type' => 4,
                        'status_of_notification' => 0, 
                        'message' => "Label {$label->label_id} in Room {$label->room_number}, {$label->building} is overdue!",
                        'label_id' => $label->label_id,
                    ]);
                }
            }
            $this->info('Notifications created for applicable labels.');
        } else {
            $this->info('No labels meet the conditions.');
        }
    }
}

