<?php

namespace App\Mail\Common;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $message;

    public function __construct($subject, $message)
    {
        $this->subject = $subject;
        $this->message = $message;
    }


    public function build()
    {
        return $this->subject($this->subject)
            ->text('notification.mail.template', ['template' => $this->message]);
    }
}
