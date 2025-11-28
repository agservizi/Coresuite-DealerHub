<?php
// config/config.php - global configuration for Coresuite DealerHub portal

declare(strict_types=1);

// Display errors in development (set to 0 in production)
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Base path detection
$baseDir = dirname(__DIR__);

// Load environment configuration if available
$envFile = $baseDir . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        $value = trim($value, "\"'");
        $_ENV[$key] = $value;
        putenv("{$key}={$value}");
    }
}

if (!function_exists('env')) {
    function env(string $key, ?string $default = null): ?string
    {
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }
}

// Database credentials
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_NAME', env('DB_NAME', 'dealerhub'));
define('DB_USER', env('DB_USER', 'dealerhub_user'));
define('DB_PASS', env('DB_PASS', 'change_me_please'));
define('DB_CHARSET', 'utf8mb4');

// Session & security configuration
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    session_start();
}

// Autoload helpers
require_once $baseDir . '/helpers/utils.php';
require_once $baseDir . '/helpers/csrf.php';
require_once $baseDir . '/helpers/cache.php';
require_once $baseDir . '/helpers/rate_limit.php';

// Autoload models on demand
spl_autoload_register(function (string $class): void {
    $paths = [
        __DIR__ . '/../models/' . $class . '.php',
        __DIR__ . '/../controllers/' . $class . '.php'
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Global constants
const APP_NAME = 'Coresuite DealerHub';
const APP_URL = 'https://dealerhub.coresuite.it';

?>
