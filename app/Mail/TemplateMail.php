<?php

namespace App\Mail;

use App\Models\Setting;
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
    public string $logoUrl;

    public function __construct(string $emailSubject, string $emailContent, ?string $logoUrl = null)
    {
        $this->emailSubject = $emailSubject;
        $this->emailContent = $emailContent;
        
        $logoSetting = Setting::get('site_logo', 'images/FMZ Glow logo.jpg');
        if ($logoUrl) {
            $this->logoUrl = $logoUrl;
        } elseif (filter_var($logoSetting, FILTER_VALIDATE_URL)) {
            $this->logoUrl = $logoSetting;
        } else {
            $this->logoUrl = url('/' . ltrim($logoSetting, '/'));
        }
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
                'logoUrl' => $this->logoUrl,
                'siteName' => Setting::get('site_name', 'FMZ Glow'),
            ]
        );
    }
}
