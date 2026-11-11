<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$settings = App\Models\Setting::where('key', 'like', '%payment%')
    ->orWhere('key', 'like', '%razor%')
    ->orWhere('key', 'like', '%zoho%')
    ->pluck('value', 'key');

echo json_encode($settings, JSON_PRETTY_PRINT);

