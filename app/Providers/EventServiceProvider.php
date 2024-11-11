<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Slides\Saml2\Events\SignedIn;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(SignedIn::class, function (SignedIn $event) {
            $messageId = $event->auth->getLastMessageId();
            // Prevent reuse of $messageId to stop replay attacks if needed

            $samlUser = $event->auth->getSaml2User();
            $attributes = $samlUser->getAttributes();
            
            // Get the user's email from the SAML attributes
            $email = $attributes['email'][0] ?? null; // Adjust this based on your SAML attributes

            // Check if user exists in the database
            $user = User::where('email', $email)->first();

            // If the user does not exist, create a new record
            if (!$user && $email) {
                $user = User::create([
                    'name' => $attributes['first_name'][0] ?? 'DefaultName', // Adjust as needed
                    'last_name' => $attributes['last_name'][0] ?? 'DefaultLastName', // Adjust as needed
                    'email' => $email,
                    'role' => 'Staff', // Define a default role or set it based on SAML attributes
                ]);
            }

            // Log the user in
            if ($user) {
                Auth::login($user);
                return redirect()->route('admin.homeAdmin'); // Replace 'dashboard' with your intended route

            }
        });
    }
}
