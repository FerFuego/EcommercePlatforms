<?php

namespace App\Notifications;

use App\Models\Cook;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNewCookProfileNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $cook;

    /**
     * Create a new notification instance.
     */
    public function __construct(Cook $cook)
    {
        $this->cook = $cook;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Nueva Solicitud de Cocinero: ' . $this->cook->user->name)
                    ->greeting('¡Hola Administrador!')
                    ->line('Un usuario ha completado su perfil de cocinero y está esperando aprobación.')
                    ->line('**Cocinero:** ' . $this->cook->user->name)
                    ->line('**Email:** ' . $this->cook->user->email)
                    ->action('Revisar Solicitud', url('/admin/cooks/' . $this->cook->id));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
