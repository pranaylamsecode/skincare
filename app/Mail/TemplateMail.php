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
        
        $logoSetting = Setting::get('site_logo', 'images/fmz-glow-logo.jpg');
        if ($logoUrl) {
            $this->logoUrl = $logoUrl;
        } elseif (filter_var($logoSetting, FILTER_VALIDATE_URL)) {
            $this->logoUrl = $logoSetting;
        } else {
            $cleanPath = ltrim($logoSetting, '/');
            // Prefer filename without spaces if available to prevent Gmail proxy converting spaces to '+'
            if ($cleanPath === 'images/FMZ Glow logo.jpg' && file_exists(public_path('images/fmz-glow-logo.jpg'))) {
                $cleanPath = 'images/fmz-glow-logo.jpg';
            }
            $this->logoUrl = url('/' . $cleanPath);
        }

        // Clean spaces from URL: Gmail image proxy converts spaces to '+' which can 404 on webservers
        if (str_contains($this->logoUrl, ' ')) {
            if (str_contains($this->logoUrl, 'FMZ Glow logo.jpg')) {
                $this->logoUrl = str_replace('FMZ Glow logo.jpg', 'fmz-glow-logo.jpg', $this->logoUrl);
            } else {
                $this->logoUrl = str_replace(' ', '%20', $this->logoUrl);
            }
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
