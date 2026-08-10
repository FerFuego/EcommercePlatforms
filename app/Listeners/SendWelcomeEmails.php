<?php

namespace App\Listeners;

use App\Mail\NewUserAdminNotification;
use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendWelcomeEmails
{


    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $user = $event->user;

        try {
            // 1. Send Welcome Email to the registered user
            Mail::to($user->email)->send(new WelcomeEmail($user));

            // 2. Send Notification to Admins and info@cocinarte.app
            $adminEmails = User::where('role', 'admin')->pluck('email')->filter()->toArray();

            $mail = Mail::to('info@cocinarte.app');
            if (!empty($adminEmails)) {
                $mail->bcc($adminEmails);
            }
            $mail->send(new NewUserAdminNotification($user));

        } catch (\Exception $e) {
            Log::error("Error sending welcome emails: " . $e->getMessage());
        }
    }
}
