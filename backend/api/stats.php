<?php
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    json_response(['status' => 'ok']);
}

$user = authenticate();
$pdo = get_db_connection();

if ($user['role'] === 'SUPERADMIN') {
    $total = $pdo->query('SELECT COUNT(*) as total FROM contracts')->fetch()['total'] ?? 0;
    $pending = $pdo->query("SELECT COUNT(*) as total FROM contracts WHERE status IN ('NUOVO','IN_ELABORAZIONE')")->fetch()['total'] ?? 0;
    $affiliates = $pdo->query('SELECT COUNT(*) as total FROM users WHERE role = "AFFILIATO" AND active = 1')->fetch()['total'] ?? 0;
    json_response([
        'totalContracts' => (int) $total,
        'pendingContracts' => (int) $pending,
        'affiliatesEnabled' => (int) $affiliates,
        'coverageChecks' => 0,
    ]);
}

$stmt = $pdo->prepare('SELECT COUNT(*) as total FROM contracts WHERE affiliate_id = ?');
$stmt->execute([$user['id']]);
$total = $stmt->fetch()['total'] ?? 0;
json_response([
    'totalContracts' => (int) $total,
    'pendingContracts' => (int) $total,
    'affiliatesEnabled' => 1,
    'coverageChecks' => 0,
]);
?>
