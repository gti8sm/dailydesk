<?php

namespace App\Mail;

use App\Models\User;
use App\Modules\Stock\Models\StockItem;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StockAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public StockItem $item,
        public User $admin
    ) {
    }

    public function build()
    {
        $appName = \App\Models\Setting::get('app_name', config('app.name', 'DailyDesk'));

        return $this->subject("[{$appName}] Alerte stock — {$this->item->name}")
            ->view('emails.stock-alert', [
                'appName' => $appName,
                'item' => $this->item,
                'admin' => $this->admin,
            ]);
    }
}
