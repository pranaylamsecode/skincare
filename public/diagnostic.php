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
        pre { background: #020617; padding: 12px; border-radius: 6px; overflow-x: auto; color: #f1f5f9; }
    </style>
</head>
<body>
    <h1>⚙️ FMZ Glow Server Diagnostic Tool</h1>

    <div class="box">
        <h2>1. PHP Environment</h2>
        <p>PHP Version: <strong><?= PHP_VERSION ?></strong> <?= (version_compare(PHP_VERSION, '8.2.0') >= 0) ? '<span class="ok">✓ OK (>= 8.2)</span>' : '<span class="err">✗ Upgrade PHP to >= 8.2 or 8.3 in cPanel!</span>' ?></p>
        <p>PHP SAPI: <strong><?= php_sapi_name() ?></strong></p>
        <p>Server Software: <strong><?= $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' ?></strong></p>
    </div>

    <div class="box">
        <h2>2. Critical PHP Extensions</h2>
        <?php
        $exts = ['pdo', 'pdo_mysql', 'openssl', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'curl', 'fileinfo'];
        foreach ($exts as $ext) {
            $loaded = extension_loaded($ext);
            echo "<p>Extension <code>$ext</code>: " . ($loaded ? '<span class="ok">✓ Loaded</span>' : '<span class="err">✗ MISSING (Enable in cPanel -> Select PHP Version -> Extensions)</span>') . "</p>";
        }
        ?>
    </div>

    <div class="box">
        <h2>3. File Permissions</h2>
        <?php
        $paths = [
            '../storage' => 'Storage Directory',
            '../storage/framework' => 'Storage Framework',
            '../storage/framework/views' => 'Storage Views',
            '../storage/framework/sessions' => 'Storage Sessions',
            '../storage/logs' => 'Storage Logs',
            '../bootstrap/cache' => 'Bootstrap Cache',
        ];
        foreach ($paths as $rel => $name) {
            $real = realpath(__DIR__ . '/' . $rel);
            if (!$real) {
                echo "<p>$name ($rel): <span class=" . '"err"' . ">✗ Path does not exist!</span></p>";
                continue;
            }
            $writable = is_writable($real);
            echo "<p>$name: " . ($writable ? '<span class="ok">✓ Writable</span>' : '<span class="err">✗ NOT WRITABLE (Run chmod -R 775 storage bootstrap/cache)</span>') . " <code>($real)</code></p>";
        }
        ?>
    </div>

    <div class="box">
        <h2>4. Laravel Autoloader & Bootstrap</h2>
        <?php
        $autoloaderPath = __DIR__ . '/../vendor/autoload.php';
        if (!file_exists($autoloaderPath)) {
            echo '<p><span class="err">✗ vendor/autoload.php NOT FOUND! Run composer install</span></p>';
        } else {
            try {
                require $autoloaderPath;
                echo '<p><span class="ok">✓ Autoloader loaded successfully</span></p>';
            } catch (\Throwable $e) {
                echo '<p><span class="err">✗ Autoloader Exception: ' . htmlspecialchars($e->getMessage()) . '</span></p>';
                echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            }
        }

        $bootstrapPath = __DIR__ . '/../bootstrap/app.php';
        if (!file_exists($bootstrapPath)) {
            echo '<p><span class="err">✗ bootstrap/app.php NOT FOUND!</span></p>';
        } else {
            try {
                $app = require_once $bootstrapPath;
                echo '<p><span class="ok">✓ bootstrap/app.php loaded successfully</span></p>';
            } catch (\Throwable $e) {
                echo '<p><span class="err">✗ Bootstrap Exception: ' . htmlspecialchars($e->getMessage()) . '</span></p>';
                echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            }
        }
        ?>
    </div>

    <div class="box">
        <h2>5. Environment & Database Connection</h2>
        <?php
        $envPath = __DIR__ . '/../.env';
        if (!file_exists($envPath)) {
            echo '<p><span class="err">✗ .env file NOT FOUND in project root! Copy .env.example to .env</span></p>';
        } else {
            echo '<p><span class="ok">✓ .env file exists</span></p>';
            try {
                if (isset($app)) {
                    $key = config('app.key');
                    if (empty($key)) {
                        echo '<p><span class="err">✗ APP_KEY is empty! Run php artisan key:generate</span></p>';
                    } else {
                        echo '<p><span class="ok">✓ APP_KEY is set</span></p>';
                    }

                    try {
                        \Illuminate\Support\Facades\DB::connection()->getPdo();
                        echo '<p><span class="ok">✓ MySQL Database connected successfully!</span></p>';
                    } catch (\Throwable $dbe) {
                        echo '<p><span class="err">✗ Database Connection Failed: ' . htmlspecialchars($dbe->getMessage()) . '</span></p>';
                    }
                }
            } catch (\Throwable $e) {
                echo '<p><span class="err">✗ Config/DB Error: ' . htmlspecialchars($e->getMessage()) . '</span></p>';
            }
        }
        ?>
    </div>
</body>
</html>
