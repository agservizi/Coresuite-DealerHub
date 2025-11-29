<?php
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    json_response(['status' => 'ok']);
}

$user = authenticate();
if ($user['role'] !== 'SUPERADMIN') {
    json_response(['message' => 'Permessi insufficenti'], 403);
}

$pdo = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query('SELECT id, name, email, role, active, affiliate_id FROM users ORDER BY created_at DESC');
    $users = $stmt->fetchAll();
    json_response($users);
}

require_method(['POST']);
$input = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $input['action'] ?? '';

switch ($action) {
    case 'create':
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, role, affiliate_id, active) VALUES (?, ?, ?, ?, ?, 1)');
        $password = password_hash($input['password'] ?? 'Dealer123', PASSWORD_BCRYPT);
        $stmt->execute([
            $input['name'],
            $input['email'],
            $password,
            $input['role'] ?? 'AFFILIATO',
            $input['affiliate_id'] ?? null,
        ]);
        json_response(['message' => 'Affiliato creato']);
        break;
    case 'enable':
    case 'disable':
        $stmt = $pdo->prepare('UPDATE users SET active = ? WHERE id = ?');
        $stmt->execute([$action === 'enable' ? 1 : 0, $input['userId']]);
        json_response(['message' => 'Stato aggiornato']);
        break;
    case 'reset-password':
        $newPass = password_hash($input['newPassword'] ?? 'Dealer123', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $stmt->execute([$newPass, $input['userId']]);
        json_response(['message' => 'Password aggiornata']);
        break;
    default:
        json_response(['message' => 'Azione non valida'], 400);
}
?>
