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

function redirect(string $path): void
{
    $target = $path;
    if (isset($path[0]) && $path[0] === '/' && defined('APP_URL')) {
        $target = rtrim(APP_URL, '/') . $path;
    }
    header('Location: ' . $target);
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
    return strtok($uri, '?');
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
