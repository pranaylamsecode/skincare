<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use App\Mail\TemplateMail;

$target = $argv[1] ?? 'prashant@veerit.com';

echo "Sending branded TemplateMail with logo to: {$target}...\n";

try {
    $subject = 'FMZ Glow - Logo Display Test (' . date('H:i:s') . ')';
    $content = "Hello,\n\nThis is a verification test to ensure the FMZ Glow logo in the header renders crisp and clean in Gmail without breaking.\n\nAll systems operational!";
    
    Mail::to($target)->send(new TemplateMail($subject, $content));
    echo ">>> SUCCESS: Branded email with logo sent successfully to {$target}!\n";
} catch (\Throwable $e) {
    echo ">>> FAILED: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
}
