<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

// Notifikasi ke tim sales saat ada pesan baru dari form kontak.
class NewContactMessage extends Mailable
{
    public function __construct(public ContactMessage $contactMessage) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            // Reply-To ke pengirim supaya tim cukup klik "Balas".
            replyTo: [new Address($this->contactMessage->email, $this->contactMessage->name)],
            subject: 'Pesan Website: '.($this->contactMessage->subject ?: $this->contactMessage->name),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.contact-message');
    }
}
