<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\NotificationController;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\CreateAdminLabelNotification::class,
        \App\Console\Commands\ExpiredUsers::class,
        \App\Console\Commands\DeleteOldRecords::class,

    ];
    

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
    $schedule->command('notifications:admin-labels')->daily(); 
    //php artisan notifications:admin-labels
    
    $schedule->command('notifications:cleanup')->daily();
    //php artisan notifications:cleanup
   
    $schedule->command('users:expire-certifications')->daily();
    //php artisan users:expire-certifications

    $schedule->command('cleanup:delete-old-records')->daily();
    //php artisan cleanup:delete-old-records

    $schedule->command('notifications:admin-35months')->daily();
    //php artisan notifications:admin-35months
    }


    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
