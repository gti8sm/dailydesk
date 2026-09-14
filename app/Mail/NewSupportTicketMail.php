<?php

namespace App\Mail;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewSupportTicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SupportTicket $ticket,
    ) {
    }

    public function build()
    {
        return $this->subject("Nouveau ticket de support #{$this->ticket->id} - {$this->ticket->subject}")
            ->view('emails.support-new-ticket', [
                'ticket' => $this->ticket,
            ]);
    }
}
