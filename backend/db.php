<?php
$DB_HOST = $_ENV['DB_HOST'] ?? 'localhost';
$DB_NAME = $_ENV['DB_NAME'] ?? 'dealerhub';
$DB_USER = $_ENV['DB_USER'] ?? 'dealerhub_user';
$DB_PASS = $_ENV['DB_PASS'] ?? 'change-me';
$DB_PORT = $_ENV['DB_PORT'] ?? 3306;

function get_db_connection(): PDO
{
    global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS, $DB_PORT;

    $dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    return new PDO($dsn, $DB_USER, $DB_PASS, $options);
}
?>
