<?php
// helpers/utils.php - common utility helpers

declare(strict_types=1);

if (!class_exists('User')) {
    require_once __DIR__ . '/../models/User.php';
}

function sanitize(?string $value): string
{
    return htmlspecialchars(trim((string) $value), ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    if ($path === '') {
        return APP_URL;
    }

    if (preg_match('#^(https?:)?//#', $path)) {
        return $path;
    }

    return APP_URL . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function isAuthenticated(): bool
{
    return isset($_SESSION['user_id']);
}

function requireAuth(): void
{
    if (!isAuthenticated()) {
        redirect('/login.php');
    }
}

function getCurrentUser(): ?array
{
    if (!isAuthenticated()) {
        return null;
    }
    static $cachedUser = null;
    if ($cachedUser !== null) {
        return $cachedUser;
    }
    $class = 'User';
    if (!class_exists($class)) {
        require_once __DIR__ . '/../models/User.php';
    }
    $userModel = new $class();
    $cachedUser = $userModel->findById((int) $_SESSION['user_id']);
    return $cachedUser;
}

function hasRole(string $role): bool
{
    $user = getCurrentUser();
    return $user && $user['role'] === $role;
}

function hasAnyRole(array $roles): bool
{
    $user = getCurrentUser();
    return $user && in_array($user['role'], $roles, true);
}

function setFlash(string $key, string $message, string $type = 'success'): void
{
    $_SESSION['flash'][$key] = ['message' => $message, 'type' => $type];
}

function getFlash(string $key): ?array
{
    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }
    $flash = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return $flash;
}

function currentPath(): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = strtok($uri, '?') ?: '/';
    $base = APP_BASE_PATH;

    if ($base && str_starts_with($path, $base)) {
        $path = substr($path, strlen($base)) ?: '/';
    }

    return $path;
}

function formatDateTime(string $datetime): string
{
    return (new DateTime($datetime))->format('d/m/Y H:i');
}

function formatStatusBadge(string $status): string
{
    $label = ucwords(str_replace('_', ' ', $status));
    $class = 'ghost-badge status-' . $status;
    return '<span class="' . $class . '"><span class="dot"></span>' . $label . '</span>';
}

?>
