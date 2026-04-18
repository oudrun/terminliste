<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$basePath = dirname(__DIR__);

define('APP_BASE_PATH', $basePath);

$autoloadPath = APP_BASE_PATH . '/vendor/autoload.php';

if (file_exists($autoloadPath)) {
    require $autoloadPath;
} else {
    spl_autoload_register(static function (string $class): void {
        $prefix = 'App\\';
        $baseDir = APP_BASE_PATH . '/app/';

        if (!str_starts_with($class, $prefix)) {
            return;
        }

        $relativeClass = substr($class, strlen($prefix));
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    });

    require APP_BASE_PATH . '/app/Support/helpers.php';
}

App\Support\Database::getPdo();
