<?php
require_once __DIR__ . '/../helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    json_response(['status' => 'ok']);
}

require_method(['POST']);
$input = json_decode(file_get_contents('php://input'), true) ?? [];
authenticate();

$technologies = [
    'FASTWEB' => ['FTTH', '5G'],
    'WINDTRE' => ['FTTC', '4G+'],
    'ILIAD' => ['5G', 'VoIP'],
    'FIBRA' => ['Open Fiber', 'FiberCop'],
];
$operator = strtoupper($input['operator'] ?? 'FASTWEB');

json_response([
    'operator' => $operator,
    'available' => (bool) random_int(0, 1),
    'technologies' => $technologies[$operator] ?? ['ADSL'],
    'notes' => 'Risposta simulata. Integrare API reali del gestore.',
]);
?>
