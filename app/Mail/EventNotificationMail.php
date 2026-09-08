<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $childName,
        public string $module,
        public string $title,
        public string $description,
        public ?string $severity,
        public string $eventDate,
        public ?string $eventTime,
    ) {
    }

    public function build()
    {
        $appName = \App\Models\Setting::get('app_name', config('app.name', 'DailyDesk'));

        return $this->subject("Signalement - {$appName} - {$this->childName}")
            ->view('emails.event-notification', [
                'appName' => $appName,
                'childName' => $this->childName,
                'module' => $this->module,
                'title' => $this->title,
                'description' => $this->description,
                'severity' => $this->severity,
                'eventDate' => $this->eventDate,
                'eventTime' => $this->eventTime,
            ]);
    }
}
