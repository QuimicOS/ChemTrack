<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use Carbon\Carbon;

class CleanupOldNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes read notifications older than 1 month from the system.';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle()
    {
        $oneMonthAgo = Carbon::now()->subMonth();

        $oldNotifications = Notification::where('created_at', '<', $oneMonthAgo)
        ->where('status_of_notification', 1)
        ->get();

        if ($oldNotifications->isEmpty()) {
            $this->info('No old notifications to delete.');
            return 0;
        }

        // Delete notifications
        $count = $oldNotifications->count();
        Notification::where('created_at', '<', $oneMonthAgo)->delete();

        $this->info("Deleted {$count} notifications.");
        return 0;
    }

    // FOR TESTING (Warning: Deletes ALL Notifications)

    // public function handle()
    // {
    //     // Get today's date
    //     $today = Carbon::now();

    //     // Find notifications created today or later
    //     $notificationsToDelete = Notification::where('created_at', '<=', $today)->get();

    //     if ($notificationsToDelete->isEmpty()) {
    //         $this->info('No notifications to delete.');
    //         return 0;
    //     }

    //     // Delete the notifications
    //     $count = $notificationsToDelete->count();
    //     Notification::where('created_at', '<=', $today)->delete();

    //     $this->info("Deleted {$count} notifications created on or after today.");
    //     return 0;
    // }

}
