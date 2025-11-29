<?php
require_once __DIR__ . '/../helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    json_response(['status' => 'ok']);
}

$user = authenticate();
json_response(['user' => $user]);
?>
