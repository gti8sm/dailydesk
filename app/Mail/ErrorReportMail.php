<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ErrorReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $exceptionClass,
        public readonly string $message,
        public readonly string $file,
        public readonly int $line,
        public readonly string $trace,
        public readonly string $url,
        public readonly ?string $user,
        public readonly ?string $tenant,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[DailyDesk] Erreur en production — ' . class_basename($this->exceptionClass),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.error-report',
        );
    }
}
