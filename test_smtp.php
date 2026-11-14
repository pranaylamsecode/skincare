<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

$target = $argv[1] ?? 'pranay.l@veerit.com';

echo "Sending Laravel test email to: {$target}...\n";

try {
    Mail::raw("Hello Pranay,\n\nThis is a test email sent through the Laravel framework mail system using smtp.gmail.com:587 (TLS).\n\nSMTP settings are fully operational!", function ($message) use ($target) {
        $message->to($target)
                ->subject('Laravel SMTP Verification - ' . date('Y-m-d H:i:s'));
    });
    echo ">>> SUCCESS: Email sent successfully via Laravel Mail to {$target}!\n";
} catch (\Throwable $e) {
    echo ">>> FAILED: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
}
