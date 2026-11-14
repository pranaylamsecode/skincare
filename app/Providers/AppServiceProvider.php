<?php

namespace App\Providers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Directly force working Gmail SMTP credentials and SSL stream options
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.transport' => 'smtp',
            'mail.mailers.smtp.host' => 'smtp.gmail.com',
            'mail.mailers.smtp.port' => 587,
            'mail.mailers.smtp.encryption' => 'tls',
            'mail.mailers.smtp.username' => 'fmz@myfmz.com',
            'mail.mailers.smtp.password' => 'pykfhxccfyfmycoz',
            'mail.from.address' => 'fmz@myfmz.com',
            'mail.from.name' => 'MYFMZ',
        ]);

        Mail::extend('smtp', function (array $config = []) {
            $host = 'smtp.gmail.com';
            $port = 587;
            $isTls = false; // Port 587 uses STARTTLS, not direct SSL socket

            $transport = new EsmtpTransport(
                $host,
                $port,
                $isTls
            );

            $transport->setUsername('fmz@myfmz.com');
            $transport->setPassword('pykfhxccfyfmycoz');

            $stream = $transport->getStream();
            if ($stream instanceof SocketStream) {
                $stream->setStreamOptions([
                    'ssl' => [
                        'allow_self_signed' => true,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'peer_name' => 'smtp.gmail.com',
                        'SNI_enabled' => true,
                    ],
                ]);
            }

            return $transport;
        });
    }
}
