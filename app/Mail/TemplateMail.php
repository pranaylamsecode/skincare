<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TemplateMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailSubject;
    public string $emailContent;

    public function __construct(string $emailSubject, string $emailContent)
    {
        $this->emailSubject = $emailSubject;
        $this->emailContent = $emailContent;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.template',
            with: [
                'bodyContent' => $this->emailContent,
                'emailSubject' => $this->emailSubject,
            ]
        );
    }
}
