<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeleteOldRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:delete-old-records';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete records from labels and pickup_requests older than 3 years';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Call the methods you need
        $this->threeYearsOld();
    }

    public function threeYearsOld()
    {
        $threeYearsAgo = Carbon::now()->subYears(3);

        // Delete old records from the labels table
        $deletedLabels = DB::table('label')
            ->where('date_created', '<', $threeYearsAgo)
            ->delete();

        // Delete old records from the pickup_requests table
        $deletedPickups = DB::table('pickup')
            ->where('created_at', '<', $threeYearsAgo)
            ->delete();

        $deletedContents = DB::table('contents')
        ->where('created_at', '<', $threeYearsAgo)
        ->delete();

        // Log the results
        $this->info("$deletedLabels labels deleted.");
        $this->info("$deletedPickups pickup requests deleted.");
        $this->info("$deletedContents contents deleted.");

        $this->info('All label or pickup older than 3 years had been deleted.');;
    }
}
