<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>FMZ Server Diagnostic</title>
    <style>
        body { font-family: monospace; background: #0f172a; color: #f8fafc; padding: 30px; line-height: 1.6; }
        .box { background: #1e293b; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #334155; }
        .ok { color: #4ade80; font-weight: bold; }
        .err { color: #f87171; font-weight: bold; background: #450a0a; padding: 4px 8px; border-radius: 4px; display: inline-block; }
        h1 { color: #38bdf8; margin-top: 0; }
        h2 { color: #94a3b8; border-bottom: 1px solid #334155; padding-bottom: 8px; }
        pre { background: #020617; padding: 12px; border-radius: 6px; overflow-x: auto; color: #f87171; }
    </style>
</head>
<body>
    <h1>⚙️ FMZ Glow Diagnostic (Folder: <?= htmlspecialchars(basename(__DIR__)) ?>)</h1>

    <div class="box">
        <h2>1. PHP Environment</h2>
        <p>PHP Version: <strong><?= PHP_VERSION ?></strong> <?= (version_compare(PHP_VERSION, '8.2.0') >= 0) ? '<span class="ok">✓ OK (>= 8.2)</span>' : '<span class="err">✗ Upgrade PHP to >= 8.2 or 8.3 in cPanel!</span>' ?></p>
        <p>PHP SAPI: <strong><?= php_sapi_name() ?></strong></p>
        <p>Current Directory: <strong><?= __DIR__ ?></strong></p>
    </div>

    <div class="box">
        <h2>2. Environment File (.env)</h2>
        <?php
        $envPath = file_exists(__DIR__ . '/.env') ? __DIR__ . '/.env' : __DIR__ . '/../.env';
        if (!file_exists($envPath)) {
            echo '<p><span class="err">✗ .env file NOT FOUND in ' . htmlspecialchars(__DIR__) . '! Copy your .env into this folder!</span></p>';
        } else {
            echo '<p><span class="ok">✓ .env file found at: ' . htmlspecialchars($envPath) . '</span></p>';
        }
        ?>
    </div>

    <div class="box">
        <h2>3. Autoloader & Laravel Bootstrap</h2>
        <?php
        $autoloaderPath = file_exists(__DIR__ . '/vendor/autoload.php') ? __DIR__ . '/vendor/autoload.php' : __DIR__ . '/../vendor/autoload.php';
        $bootstrapPath = file_exists(__DIR__ . '/bootstrap/app.php') ? __DIR__ . '/bootstrap/app.php' : __DIR__ . '/../bootstrap/app.php';

        $app = null;
        if (!file_exists($autoloaderPath)) {
            echo '<p><span class="err">✗ vendor/autoload.php NOT FOUND! Run composer install</span></p>';
        } else {
            try {
                require_once $autoloaderPath;
                echo '<p><span class="ok">✓ Autoloader loaded successfully</span></p>';
            } catch (\Throwable $e) {
                echo '<p><span class="err">✗ Autoloader Error: ' . htmlspecialchars($e->getMessage()) . '</span></p>';
                echo '<pre>' . htmlspecialchars($e->getFile() . ':' . $e->getLine() . "\n" . $e->getTraceAsString()) . '</pre>';
            }
        }

        if (!file_exists($bootstrapPath)) {
            echo '<p><span class="err">✗ bootstrap/app.php NOT FOUND!</span></p>';
        } else {
            try {
                $app = require_once $bootstrapPath;
                echo '<p><span class="ok">✓ bootstrap/app.php loaded successfully</span></p>';

                // Fully bootstrap Laravel Console Kernel to load configuration and database service providers
                $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
                $kernel->bootstrap();
                echo '<p><span class="ok">✓ Laravel Application Kernel Bootstrapped successfully</span></p>';
            } catch (\Throwable $e) {
                echo '<p><span class="err">✗ Kernel Bootstrap Error: ' . htmlspecialchars($e->getMessage()) . '</span></p>';
                echo '<pre>' . htmlspecialchars($e->getFile() . ':' . $e->getLine() . "\n" . $e->getTraceAsString()) . '</pre>';
            }
        }
        ?>
    </div>

    <div class="box">
        <h2>4. Key & Database Check</h2>
        <?php
        if (isset($app)) {
            try {
                $key = config('app.key');
                if (empty($key)) {
                    echo '<p><span class="err">✗ APP_KEY is empty! Run php artisan key:generate</span></p>';
                } else {
                    echo '<p><span class="ok">✓ APP_KEY is set</span></p>';
                }

                $dbName = config('database.connections.mysql.database');
                echo '<p>Configured Database Name: <strong>' . htmlspecialchars($dbName ?? 'N/A') . '</strong></p>';

                try {
                    \Illuminate\Support\Facades\DB::connection()->getPdo();
                    echo '<p><span class="ok">✓ MySQL Database connected successfully!</span></p>';
                } catch (\Throwable $dbe) {
                    echo '<p><span class="err">✗ Database Connection Failed: ' . htmlspecialchars($dbe->getMessage()) . '</span></p>';
                    echo '<pre>' . htmlspecialchars($dbe->getFile() . ':' . $dbe->getLine()) . '</pre>';
                }
            } catch (\Throwable $e) {
                echo '<p><span class="err">✗ Config/DB Error: ' . htmlspecialchars($e->getMessage()) . '</span></p>';
                echo '<pre>' . htmlspecialchars($e->getFile() . ':' . $e->getLine()) . '</pre>';
            }
        }
        ?>
    </div>
</body>
</html>
