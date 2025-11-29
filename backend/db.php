<?php
$DB_HOST = $_ENV['DB_HOST'] ?? 'localhost';
$DB_NAME = $_ENV['DB_NAME'] ?? 'dealerhub';
$DB_USER = $_ENV['DB_USER'] ?? 'dealerhub_user';
$DB_PASS = $_ENV['DB_PASS'] ?? 'change-me';
$DB_PORT = $_ENV['DB_PORT'] ?? 5432;
$DB_DRIVER = $_ENV['DB_DRIVER'] ?? 'pgsql';

function get_db_connection(): PDO
{
    static $connection = null;
    if ($connection instanceof PDO) {
        return $connection;
    }

    $databaseUrl = $_ENV['DATABASE_URL'] ?? null;

    $config = [
        'driver' => $_ENV['DB_DRIVER'] ?? 'pgsql',
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'port' => $_ENV['DB_PORT'] ?? 5432,
        'database' => $_ENV['DB_NAME'] ?? 'dealerhub',
        'username' => $_ENV['DB_USER'] ?? 'dealerhub_user',
        'password' => $_ENV['DB_PASS'] ?? 'change-me',
        'params' => '',
    ];

    if ($databaseUrl) {
        $parts = parse_url($databaseUrl);
        if ($parts !== false) {
            $config['driver'] = str_starts_with($parts['scheme'], 'postgre') ? 'pgsql' : ($parts['scheme'] ?? 'pgsql');
            $config['host'] = $parts['host'] ?? $config['host'];
            $config['port'] = $parts['port'] ?? $config['port'];
            $config['database'] = isset($parts['path']) ? ltrim($parts['path'], '/') : $config['database'];
            $config['username'] = $parts['user'] ?? $config['username'];
            $config['password'] = $parts['pass'] ?? $config['password'];
            if (isset($parts['query'])) {
                $config['params'] = ';' . str_replace('&', ';', $parts['query']);
            }
        }
    }

    $dsn = sprintf(
        '%s:host=%s;port=%s;dbname=%s%s',
        $config['driver'],
        $config['host'],
        $config['port'],
        $config['database'],
        $config['params']
    );

    // Force UTF8 when using MySQL, Postgres handles Unicode by default.
    if ($config['driver'] === 'mysql' && !str_contains($dsn, 'charset=')) {
        $dsn .= ';charset=utf8mb4';
    }

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    $connection = new PDO($dsn, $config['username'], $config['password'], $options);
    return $connection;
}
?>
