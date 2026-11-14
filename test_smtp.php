<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

echo "Current config in Laravel:\n";
echo "mail.default: " . Config::get('mail.default') . "\n";
echo "mail.mailers.smtp.host: " . Config::get('mail.mailers.smtp.host') . "\n";
echo "mail.mailers.smtp.port: " . Config::get('mail.mailers.smtp.port') . "\n";
echo "mail.mailers.smtp.username: " . Config::get('mail.mailers.smtp.username') . "\n";
echo "mail.mailers.smtp.encryption: " . Config::get('mail.mailers.smtp.encryption') . "\n";
echo "mail.from.address: " . Config::get('mail.from.address') . "\n";

echo "\nAttempting to send a test email via Laravel Mail facade...\n";

try {
    Mail::raw('This is a test email from Laravel test_smtp.php', function ($message) {
        $to = Config::get('mail.from.address') ?: 'fmz@myfmz.com';
        $message->to($to)
                ->subject('Laravel SMTP Test');
    });
    echo ">>> SUCCESS: Email sent successfully via Laravel Mail!\n";
} catch (\Throwable $e) {
    echo ">>> FAILED: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

