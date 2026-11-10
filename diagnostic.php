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
        pre { background: #020617; padding: 12px; border-radius: 6px; overflow-x: auto; color: #f87171; white-space: pre-wrap; word-break: break-all; }
    </style>
</head>
<body>
    <h1>⚙️ FMZ Diagnostic Tool</h1>

    <div class="box">
        <h2>1. PHP Environment & Permissions</h2>
        <p>PHP Version: <strong><?= PHP_VERSION ?></strong> <?= (version_compare(PHP_VERSION, '8.2.0') >= 0) ? '<span class="ok">✓ OK (>= 8.2)</span>' : '<span class="err">✗ Upgrade PHP to >= 8.2</span>' ?></p>
        <p>Current Directory: <strong><?= __DIR__ ?></strong></p>
        <?php
        $storagePath = __DIR__ . '/storage';
        $bootstrapCachePath = __DIR__ . '/bootstrap/cache';
        
        $storageOk = is_dir($storagePath) && is_writable($storagePath);
        $cacheOk = is_dir($bootstrapCachePath) && is_writable($bootstrapCachePath);
        
        echo '<p>Storage Writable: ' . ($storageOk ? '<span class="ok">✓ Writable</span>' : '<span class="err">✗ Not Writable! Run chmod -R 775 storage</span>') . '</p>';
        echo '<p>Bootstrap Cache Writable: ' . ($cacheOk ? '<span class="ok">✓ Writable</span>' : '<span class="err">✗ Not Writable! Run chmod -R 775 bootstrap/cache</span>') . '</p>';
        ?>
    </div>

    <div class="box">
        <h2>2. Environment File (.env)</h2>
        <?php
        $envPath = file_exists(__DIR__ . '/.env') ? __DIR__ . '/.env' : __DIR__ . '/../.env';
        if (!file_exists($envPath)) {
            echo '<p><span class="err">✗ .env file NOT FOUND!</span></p>';
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
                echo '<pre>' . htmlspecialchars($e->getFile() . ':' . $e->getLine()) . '</pre>';
            }
        }

        if (!file_exists($bootstrapPath)) {
            echo '<p><span class="err">✗ bootstrap/app.php NOT FOUND!</span></p>';
        } else {
            try {
                $app = require_once $bootstrapPath;
                echo '<p><span class="ok">✓ bootstrap/app.php loaded successfully</span></p>';

                $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
                $kernel->bootstrap();
                echo '<p><span class="ok">✓ Laravel Application Kernel Bootstrapped successfully</span></p>';
            } catch (\Throwable $e) {
                echo '<p><span class="err">✗ Kernel Bootstrap Error: ' . htmlspecialchars($e->getMessage()) . '</span></p>';
                echo '<pre>' . htmlspecialchars($e->getFile() . ':' . $e->getLine()) . '</pre>';
            }
        }
        ?>
    </div>

    <div class="box">
        <h2>4. Key, Sessions & Database Check</h2>
        <?php
        if (isset($app)) {
            try {
                $key = config('app.key');
                if (empty($key)) {
                    echo '<p><span class="err">✗ APP_KEY is empty! Run php artisan key:generate in terminal!</span></p>';
                } else {
                    echo '<p><span class="ok">✓ APP_KEY is set</span></p>';
                }

                $dbName = config('database.connections.mysql.database');
                echo '<p>Configured Database Name: <strong>' . htmlspecialchars($dbName ?? 'N/A') . '</strong></p>';
                echo '<p>Session Driver: <strong>' . htmlspecialchars(config('session.driver') ?? 'N/A') . '</strong></p>';

                try {
                    $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
                    echo '<p><span class="ok">✓ MySQL Database connected successfully!</span></p>';

                    // Check tables
                    $tables = ['users', 'settings', 'sessions', 'services', 'specialists'];
                    foreach ($tables as $t) {
                        $hasTable = \Illuminate\Support\Facades\Schema::hasTable($t);
                        echo '<p>Table <code>' . htmlspecialchars($t) . '</code>: ' . ($hasTable ? '<span class="ok">✓ Exists</span>' : '<span class="err">✗ Missing! Run php artisan migrate</span>') . '</p>';
                    }
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

    <div class="box">
        <h2>5. Vite Assets Check</h2>
        <?php
        $manifestPublic = public_path('build/manifest.json');
        $manifestBase = base_path('build/manifest.json');
        
        if (file_exists($manifestPublic)) {
            echo '<p><span class="ok">✓ Vite build manifest found at: ' . htmlspecialchars($manifestPublic) . '</span></p>';
        } elseif (file_exists($manifestBase)) {
            echo '<p><span class="ok">✓ Vite build manifest found at: ' . htmlspecialchars($manifestBase) . '</span></p>';
        } else {
            echo '<p><span class="err">✗ Vite manifest.json NOT FOUND! Run npm run build</span></p>';
        }
        ?>
    </div>

    <div class="box">
        <h2>6. Test Admin Route Simulation (/admin/login)</h2>
        <?php
        if (isset($app)) {
            try {
                $httpKernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
                $testReq = \Illuminate\Http\Request::create('/admin/login', 'GET');
                $testRes = $httpKernel->handle($testReq);
                
                $statusCode = $testRes->getStatusCode();
                if ($statusCode === 200 || $statusCode === 302) {
                    echo '<p><span class="ok">✓ /admin/login responded with HTTP ' . $statusCode . ' (Working!)</span></p>';
                } else {
                    echo '<p><span class="err">✗ /admin/login returned HTTP ' . $statusCode . '</span></p>';
                    if (isset($testRes->exception)) {
                        echo '<pre>' . htmlspecialchars(get_class($testRes->exception) . ': ' . $testRes->exception->getMessage() . "\n" . $testRes->exception->getFile() . ':' . $testRes->exception->getLine()) . '</pre>';
                    }
                }
            } catch (\Throwable $routeEx) {
                echo '<p><span class="err">✗ /admin/login Execution Error: ' . htmlspecialchars($routeEx->getMessage()) . '</span></p>';
                echo '<pre>' . htmlspecialchars($routeEx->getFile() . ':' . $routeEx->getLine() . "\n" . $routeEx->getTraceAsString()) . '</pre>';
            }
        }
        ?>
    </div>
</body>
</html>
