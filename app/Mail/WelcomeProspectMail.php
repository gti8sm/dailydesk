<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeProspectMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $email,
        public string $token,
        public string $domain,
    ) {
    }

    public function build()
    {
        $url = url('/password/reset/' . $this->token . '?email=' . urlencode($this->email));
        $tenantUrl = url('/' . $this->domain . '/login');

        return $this->subject('Bienvenue sur DailyDesk — Configurez votre accès')
            ->view('emails.welcome-prospect', [
                'name' => $this->name,
                'url' => $url,
                'domain' => $tenantUrl,
            ]);
    }
}
