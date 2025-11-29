<?php
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    json_response(['status' => 'ok']);
}

require_method(['POST']);

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $input['action'] ?? 'login';
$pdo = get_db_connection();

switch ($action) {
    case 'login':
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';
        $stmt = $pdo->prepare('SELECT id, name, email, password_hash, role, affiliate_id FROM users WHERE email = ? AND active = 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($password, $user['password_hash'])) {
            json_response(['message' => 'Credenziali non valide'], 401);
        }
        $token = issue_token((int)$user['id']);
        unset($user['password_hash']);
        json_response(['token' => $token, 'user' => $user]);
        break;
    case 'logout':
        $token = get_bearer_token();
        if ($token) {
            $stmt = $pdo->prepare('DELETE FROM user_tokens WHERE token_hash = ?');
            $stmt->execute([hash_token($token)]);
        }
        json_response(['success' => true]);
        break;
    case 'recover':
        // Hook email provider here. For boilerplate we just return success.
        json_response(['message' => 'Se esiste un account riceverai una email.']);
        break;
    default:
        json_response(['message' => 'Azione non supportata'], 400);
}
?>
