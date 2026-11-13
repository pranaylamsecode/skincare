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

        // Enhance SMTP transport with stream options to bypass hosting/proxy SSL certificate mismatches and force IPv4
        Mail::extend('smtp', function (array $config = []) {
            $rawHost = $config['host'] ?? 'smtp.gmail.com';
            // Force IPv4 resolution for smtp.gmail.com to prevent IPv6 "Network is unreachable" on VPS
            $host = $rawHost;
            if ($rawHost === 'smtp.gmail.com') {
                $resolved = gethostbyname('smtp.gmail.com');
                if ($resolved && filter_var($resolved, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                    $host = $resolved;
                }
            }

            $port = (int) ($config['port'] ?? 465);
            $isTls = ($port === 465) || (($config['encryption'] ?? '') === 'ssl');

            $transport = new EsmtpTransport(
                $host,
                $port,
                $isTls
            );

            if (!empty($config['username'])) {
                $transport->setUsername($config['username']);
            }
            if (!empty($config['password'])) {
                $transport->setPassword($config['password']);
            }

            if (!empty($config['timeout'])) {
                $transport->setTimeout($config['timeout']);
            }

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
