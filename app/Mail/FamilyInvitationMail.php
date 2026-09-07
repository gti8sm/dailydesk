<?php

namespace App\Mail;

use App\Models\FamilyInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FamilyInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public FamilyInvitation $invitation)
    {
    }

    public function build()
    {
        $appName = \App\Models\Setting::get('app_name', config('app.name', 'DailyDesk'));
        $url = url('/invitation/accept/' . $this->invitation->token);

        return $this->subject("Invitation - {$appName}")
            ->view('emails.family-invitation', [
                'appName' => $appName,
                'url' => $url,
                'family' => $this->invitation->family,
            ]);
    }
}
