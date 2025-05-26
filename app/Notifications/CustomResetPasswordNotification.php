<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
                    ->subject('Reset Your Password')
                    ->view('emails.reset-password', [
                        'url' => $url,
                        'user' => $notifiable,
                    ])
                    ->attach(public_path('images/logo2.png'), [
                        'as' => 'logo2.png',
                        'mime' => 'image/png',
                        'cid' => 'logo2.png',
                    ]);
    }
}
