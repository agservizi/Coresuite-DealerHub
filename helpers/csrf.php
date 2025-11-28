<?php
// helpers/csrf.php - CSRF token utilities

declare(strict_types=1);

function generateCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken(?string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string) $token);
}

function csrfTokenField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . generateCsrfToken() . '">';
}

?>
