<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Utilisateur;

class WelcomeTeletravailleur extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $password;
    public $completionLink;


    public function __construct(Utilisateur $user, string $password, string $completionLink)
    {
        $this->user = $user;
        $this->password = $password;
        $this->completionLink = $completionLink;
    }


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenue chez TeleMonit - Complétez votre profil',
        );
    }


    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome_teletravailleur',
            with: [
                'user' => $this->user,
                'password' => $this->password,
                'completionLink' => $this->completionLink,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [\Illuminate\Mail\Mailables\Attachment::fromPath(public_path('images/logo2.png'))
        ->as('logo2.png')
        ->withMime('image/png'),];
    }
}
