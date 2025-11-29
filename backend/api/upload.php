<?php
require_once __DIR__ . '/../helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    json_response(['status' => 'ok']);
}

require_method(['POST']);
$user = authenticate();

if (!isset($_FILES['file'])) {
    json_response(['message' => 'File mancante'], 400);
}

$file = $_FILES['file'];
if ($file['size'] > 10 * 1024 * 1024) {
    json_response(['message' => 'File troppo grande'], 400);
}

$allowed = ['application/pdf', 'image/jpeg', 'image/png'];
if (!in_array($file['type'], $allowed, true)) {
    json_response(['message' => 'Formato non supportato'], 400);
}

$contractId = (int)($_POST['contractId'] ?? 0);
$targetDir = __DIR__ . '/../../uploads/contratti/' . $user['id'];
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0775, true);
}

$filename = sprintf('%s_%s', $contractId ?: 'doc', basename($file['name']));
$destination = $targetDir . '/' . $filename;

if (!move_uploaded_file($file['tmp_name'], $destination)) {
    json_response(['message' => 'Errore salvataggio file'], 500);
}

$publicPath = '/uploads/contratti/' . $user['id'] . '/' . $filename;
json_response(['path' => $publicPath]);
?>
