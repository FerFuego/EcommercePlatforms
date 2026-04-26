<?php

namespace App\Services;

use App\Models\Feedback;
use App\Models\User;
use App\Mail\FeedbackMail;
use Illuminate\Support\Facades\Mail;

class FeedbackService
{
    public function createFeedback(User $user, array $data)
    {
        $feedback = Feedback::create([
            'user_id' => $user->id,
            'type' => $data['type'],
            'message' => $data['message'],
            'status' => 'new',
        ]);

        // Send email to admin
        $adminEmail = $this->getAdminEmail();
        if ($adminEmail) {
            Mail::to($adminEmail)->send(new FeedbackMail($feedback));
        }

        return $feedback;
    }

    protected function getAdminEmail()
    {
        // Get first admin user email
        return User::where('role', 'admin')->first()?->email;
    }

    public function getFeedbacks()
    {
        return Feedback::with('user')->latest()->paginate(10);
    }

    public function markAsRead(Feedback $feedback)
    {
        $feedback->update(['status' => 'read']);
        return $feedback;
    }

    public function archive(Feedback $feedback)
    {
        $feedback->update(['status' => 'archived']);
        return $feedback;
    }
}
