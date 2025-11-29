<?php
require_once __DIR__ . '/../db.php';

$email = 'ag.servizi16@gmail.com';
$passwordPlain = 'Giogiu2123@';
$name = 'Superadmin Coresuite';
$role = 'SUPERADMIN';

try {
    $pdo = get_db_connection();

    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $existing = $stmt->fetch();

    $passwordHash = password_hash($passwordPlain, PASSWORD_BCRYPT);

    if ($existing) {
        $update = $pdo->prepare('UPDATE users SET name = ?, password_hash = ?, role = ?, active = 1 WHERE id = ?');
        $update->execute([$name, $passwordHash, $role, $existing['id']]);
        echo "Updated existing superadmin user #{$existing['id']}" . PHP_EOL;
    } else {
        $insert = $pdo->prepare('INSERT INTO users (name, email, password_hash, role, active) VALUES (?, ?, ?, ?, 1)');
        $insert->execute([$name, $email, $passwordHash, $role]);
        echo "Created superadmin user with email {$email}" . PHP_EOL;
    }
} catch (Throwable $exception) {
    fwrite(STDERR, 'Migration failed: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}
?>
