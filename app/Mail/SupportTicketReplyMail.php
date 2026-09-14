<?php

namespace App\Mail;

use App\Models\SupportTicket;
use App\Models\SupportTicketComment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportTicketReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SupportTicket $ticket,
        public SupportTicketComment $comment,
    ) {
    }

    public function build()
    {
        $appName = \App\Models\Setting::get('app_name', config('app.name', 'DailyDesk'));

        return $this->subject("Réponse à votre ticket #{$this->ticket->id} - {$this->ticket->subject}")
            ->view('emails.support-ticket-reply', [
                'appName' => $appName,
                'ticket' => $this->ticket,
                'comment' => $this->comment,
            ]);
    }
}
