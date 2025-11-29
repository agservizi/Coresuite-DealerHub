<?php
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    json_response(['status' => 'ok']);
}

$user = authenticate();
$pdo = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($user['role'] === 'SUPERADMIN') {
        $stmt = $pdo->query('SELECT c.*, u.name as affiliate_name FROM contracts c LEFT JOIN users u ON u.id = c.affiliate_id ORDER BY c.created_at DESC');
    } else {
        $stmt = $pdo->prepare('SELECT c.*, u.name as affiliate_name FROM contracts c LEFT JOIN users u ON u.id = c.affiliate_id WHERE c.affiliate_id = ? ORDER BY c.created_at DESC');
        $stmt->execute([$user['affiliate_id'] ?? $user['id']]);
    }
    $contracts = $stmt->fetchAll();
    json_response($contracts);
}

require_method(['POST', 'PUT']);

function persistUpload(string $field, int $userId): ?string
{
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $file = $_FILES[$field];
    $allowed = ['application/pdf', 'image/jpeg', 'image/png'];
    if (!in_array($file['type'], $allowed, true)) {
        return null;
    }
    $targetDir = __DIR__ . '/../../uploads/contratti/' . $userId;
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0775, true);
    }
    $filename = $field . '_' . time() . '_' . basename($file['name']);
    $destination = $targetDir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return null;
    }
    return '/uploads/contratti/' . $userId . '/' . $filename;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = $_POST;
    $affiliateId = $user['role'] === 'SUPERADMIN' ? ($payload['affiliate_id'] ?? $user['id']) : $user['id'];

    $docFront = persistUpload('documentFront', $affiliateId);
    $docBack = persistUpload('documentBack', $affiliateId);
    $signedForm = persistUpload('signedForm', $affiliateId);

    $stmt = $pdo->prepare('INSERT INTO contracts (affiliate_id, customer_name, customer_email, customer_phone, provider, service_type, status, notes, document_front, document_back, signed_form, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
    $stmt->execute([
        $affiliateId,
        $payload['customerName'] ?? '',
        $payload['customerEmail'] ?? '',
        $payload['customerPhone'] ?? '',
        $payload['provider'] ?? '',
        $payload['serviceType'] ?? '',
        $payload['status'] ?? 'NUOVO',
        $payload['notes'] ?? null,
        $docFront,
        $docBack,
        $signedForm,
    ]);
    json_response(['message' => 'Contratto creato']);
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $stmt = $pdo->prepare('UPDATE contracts SET status = ?, notes = ?, updated_at = NOW() WHERE id = ?');
    $stmt->execute([$input['status'], $input['notes'], $input['contractId']]);
    json_response(['message' => 'Contratto aggiornato']);
}
?>
