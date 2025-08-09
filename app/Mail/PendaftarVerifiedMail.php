<?php

namespace App\Mail;

use App\Models\PendaftarEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PendaftarVerifiedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PendaftarEvent $pendaftar, public string $subject, public string $message)
    {
        $this->subject = $subject;
    }

    public function build()
    {
        return $this
            ->subject($this->subject)
            ->view('emails.pendaftar_verified')
            ->with([
                'pendaftar' => $this->pendaftar,
                'messageBody' => $this->message,
            ]);
    }
}
