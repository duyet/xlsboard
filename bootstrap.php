<?php

declare(strict_types=1);

/**
 * xlsboard Bootstrap.
 *
 * Initialize application dependencies and autoloading.
 */

// Check if running from vendor (composer install was successful).
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';

    // Load .env file if vlucas/phpdotenv is available.
    if (class_exists('Dotenv\Dotenv')) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->safeLoad();
    }
} else {
    // Fallback: Manual autoloading if composer install wasn't run.
    spl_autoload_register(function ($class) {
        $prefix = 'Xlsboard\\';
        $baseDir = __DIR__ . '/src/';

        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }

        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    });

    // Load helpers.
    require_once __DIR__ . '/src/helpers.php';

    // Load .env manually.
    if (file_exists(__DIR__ . '/.env')) {
        $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            if (strpos($line, '=') !== false) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                if (!getenv($key)) {
                    putenv("$key=$value");
                }
            }
        }
    }
}

// Error handling.
if (env('APP_DEBUG', 'false') === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}
