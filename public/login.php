<?php
require_once __DIR__ . '/../config/config.php';
if (isAuthenticated()) {
    redirect('/dashboard');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    (new AuthController())->processLogin();
}
$flash = getFlash('auth');
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME; ?> | Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600&display=swap" rel="stylesheet">
    <link href="<?= asset('css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/app.css'); ?>">
</head>
<body class="login-ghost">
    <div class="ghost-pattern"></div>
    <div class="auth-card">
        <div class="text-center mb-4">
            <div class="avatar mx-auto mb-3" style="width:52px;height:52px;">DH</div>
            <h1 class="mb-1"><?= APP_NAME; ?></h1>
            <p class="text-muted">Portale professionale contratti</p>
        </div>
        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type']; ?>"><?= $flash['message']; ?></div>
        <?php endif; ?>
        <form class="ghost-form" method="post" novalidate>
            <?= csrfTokenField(); ?>
            <div>
                <label class="form-label">Username</label>
                <input type="text" class="ghost-input" name="username" required>
            </div>
            <div>
                <label class="form-label">Password</label>
                <input type="password" class="ghost-input" name="password" required>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <label class="ghost-toggle mb-0">
                    <input type="checkbox" id="remember">
                    <span></span>
                </label>
                <a href="#" class="text-decoration-none">Password dimenticata?</a>
            </div>
            <button class="ghost-button w-100 justify-content-center" type="submit">Accedi</button>
        </form>
    </div>
    <script src="<?= asset('js/bootstrap.bundle.min.js'); ?>"></script>
</body>
</html>
