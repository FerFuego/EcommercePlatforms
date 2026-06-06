<?php

namespace App\Listeners;

use App\Mail\NewUserAdminNotification;
use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendWelcomeEmails implements ShouldQueue
{
    use InteractsWithQueue;

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

            // 2. Send Notification to Admins and info@cocinarte.com
            $admins = User::where('role', 'admin')->get();
            $adminEmails = $admins->pluck('email')->toArray();

            // We use 'info@cocinarte.app' as the primary TO, and BCC the admins
            Mail::to('info@cocinarte.app')
                ->bcc($adminEmails)
                ->send(new NewUserAdminNotification($user));

        } catch (\Exception $e) {
            Log::error("Error sending welcome emails: " . $e->getMessage());
        }
    }
}
