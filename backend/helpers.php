<?php
require_once __DIR__ . '/db.php';

function json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');

    echo json_encode($payload);
    exit;
}

function require_method(array $allowed): void
{
    if (!in_array($_SERVER['REQUEST_METHOD'], $allowed, true)) {
        json_response(['message' => 'Metodo non consentito'], 405);
    }
}

function get_bearer_token(): ?string
{
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (stripos($header, 'Bearer ') === 0) {
        return substr($header, 7);
    }
    return null;
}

function hash_token(string $input): string
{
    $secret = $_ENV['APP_KEY'] ?? 'dealerhub-secret';
    return hash_hmac('sha256', $input, $secret);
}

function issue_token(int $userId): string
{
    $seed = $userId . '|' . microtime(true) . '|' . random_bytes(16);
    $token = base64_encode($seed);
    $hashed = hash_token($token);

    $pdo = get_db_connection();
    $stmt = $pdo->prepare('INSERT INTO user_tokens (user_id, token_hash, created_at) VALUES (?, ?, NOW())');
    $stmt->execute([$userId, $hashed]);

    return $token;
}

function authenticate(): array
{
    $token = get_bearer_token();
    if (!$token) {
        json_response(['message' => 'Token assente'], 401);
    }

    $pdo = get_db_connection();
    $stmt = $pdo->prepare('SELECT user_id FROM user_tokens WHERE token_hash = ?');
    $stmt->execute([hash_token($token)]);
    $row = $stmt->fetch();
    if (!$row) {
        json_response(['message' => 'Token non valido'], 401);
    }

    $userStmt = $pdo->prepare('SELECT id, name, email, role, affiliate_id FROM users WHERE id = ?');
    $userStmt->execute([$row['user_id']]);
    $user = $userStmt->fetch();
    if (!$user) {
        json_response(['message' => 'Utente non trovato'], 401);
    }

    return $user;
}
?>
