<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WorkHoursMail extends Mailable
{
    use Queueable, SerializesModels;

    public $message;
    public $attachmentPath;

    public function __construct($message)
    {
        $this->message = $message;
        $this->attachmentPath = public_path('images/logo2.png');
    }

    public function build()
    {
        \Log::info('Construction de l\'email avec Mailable.', ['message' => $this->message]);

        $mail = $this->subject('Work Hours Alert')
                     ->html("<html>
                         <head>
                             <meta charset='UTF-8'>
                             <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                             <title>Work Hours Alert</title>
                            <style>
                                body {
                                    font-family: 'Poppins', sans-serif;
                                    margin: 0;
                                    padding: 0;
                                    text-align: center;
                                    position: relative;
                                }
                                .email-wrapper {
                                    width: 100%;

                                    background-color: #EDF2F7;
                                }
                                .logo-container {
                                    width: 1022px;
                                    height: auto;
                                    margin: 0 auto;
                                    padding: 20px;
                                    background-color: #FFFFFF;
                                    border-radius: 10px;
                                }
                                .logo {
                                    display: block;
                                    width: 409.5px;
                                    height: 91px;
                                    margin: 20px auto 0;
                                }
                                .content {
                                    max-width: 600px;
                                    margin: 0 auto;
                                    padding: 20px;
                                    text-align: center;
                                    color: #696669;
                                }
                                .content h2 {
                                    font-size: 20px;
                                    margin-bottom: 10px;
                                    color: #000;
                                }
                                .content p {
                                    font-size: 16px;
                                    line-height: 1.5;
                                    margin: 10px 0;
                                    color: #626161;
                                }
                                .credentials {
                                    margin: 20px 0;
                                    text-align: left;
                                    display: inline-block;
                                }
                                .credentials p {
                                    margin: 5px 0;
                                }
                                .button {
                                    display: inline-block;
                                    padding: 10px 30px;
                                    background-color: #000A44;
                                    color: #fff !important;
                                    text-decoration: none;
                                    border-radius: 16px;
                                    font-weight: bold;
                                    margin: 20px 0;
                                }
                                .external-footer {
                                    margin-top: 20px;
                                    font-size: 12px;
                                    color: #4A5568;
                                    text-align: center;
                                }
                            </style>
                         </head>
                         <body>
                             <div class='email-wrapper'>
                                 " . (file_exists($this->attachmentPath) ? '<img src="cid:logo2.png" alt="TeleMonit Logo" class="logo">' : '') . "
                                 <div class='logo-container'>
                                     <div class='content'>
                                         <h2><strong>Work Hours Alert<strong></h2>
                                         <p>" . htmlspecialchars($this->message) . "</p>
                                         <p>Please check your schedule.</p>
                                         <p>Best regards,<br>TELEMONIT</p>
                                     </div>
                                 </div>
                                 <div class='external-footer'>
                                     <p>© 2025 TELEMONIT. All rights reserved.</p>
                                 </div>
                             </div>
                         </body>
                     </html>");

        if (file_exists($this->attachmentPath)) {
            \Log::info('Attachement de l\'image trouvé, ajout au mail.', ['path' => $this->attachmentPath]);
            $mail->attach($this->attachmentPath, [
                'as' => 'logo2.png',
                'mime' => 'image/png',
                'cid' => 'logo2.png',
            ]);
        } else {
            \Log::warning('Image logo2.png non trouvée, envoi sans attachement.', ['path' => $this->attachmentPath]);
        }

        return $mail;
    }
}
