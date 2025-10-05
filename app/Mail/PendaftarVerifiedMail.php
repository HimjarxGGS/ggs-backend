<?php

namespace App\Mail;

use App\Models\PendaftarEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\HtmlString;

class PendaftarVerifiedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PendaftarEvent $pendaftar,
        public string $emailSubject,
        public string $emailMessage
    ) {
        //
    }

    public function build()
    {
        return $this
            ->subject($this->emailSubject)
            ->view('emails.pendaftar_verified')
            ->with([
                'pendaftar' => $this->pendaftar,
                'messageBody' => new HtmlString($this->emailMessage),
            ]);
    }
}
