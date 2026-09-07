<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Mailable;
use Illuminate\Notifications\Notification;
use Illuminate\Mail\Message;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url(route('password.reset', ['token' => $this->token, 'email' => $notifiable->email]));

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Réinitialisation de votre mot de passe - DailyDesk')
            ->greeting('Bonjour ' . $notifiable->name ?? '')
            ->line('Vous recevez cet email car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.')
            ->action('Réinitialiser mon mot de passe', $url)
            ->line('Si vous n\'avez pas demandé cette réinitialisation, aucune action n\'est requise.')
            ->salutation('Cordialement, l\'équipe DailyDesk');
    }
}
