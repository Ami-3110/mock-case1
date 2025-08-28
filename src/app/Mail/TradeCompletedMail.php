<?php

// app/Mail/TradeCompletedMail.php
namespace App\Mail;

use App\Models\Trade;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TradeCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Trade $trade) {}

    public function build()
    {
        return $this->subject('【通知】取引が完了されました')
            ->markdown('emails.trade_completed');
    }
}
