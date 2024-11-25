<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class ExpiredUsers extends Command
{
    // Command signature
    protected $signature = 'users:expire-certifications';

    // Command description
    protected $description = 'Check certification dates and update certification status for expired users.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->expireUsers();
    }

    public function expireUsers()
{
    // Get the current date
    $currentDate = Carbon::now();

    // Fetch all users whose certifications are older than one year
    $usersToExpire = User::whereNotNull('certification_date')
        ->where('certification_date', '<', $currentDate->subYear())
        ->get();

    // Check if there are users to expire
    if ($usersToExpire->isEmpty()) {
        return response()->json(['message' => 'No users need their certifications expired.']);
    }

    // Expire certification for each user
    foreach ($usersToExpire as $user) {
        $user->update([
            'certification_status' => 0, // Expired
            'certification_date' => null, // Clear certification date
        ]);
    }

    return response()->json([
        'message' => 'Certification status updated for expired users.',
        'expired_users' => $usersToExpire->count(),
    ]);
}

}
