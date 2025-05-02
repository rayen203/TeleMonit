<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class WorkHoursNotification extends Notification
{
    use Queueable;

    protected $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Alerte sur les heures de travail')
                    ->line($this->message)
                    ->line('Merci de vérifier votre planning.');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
        ];
    }
}
