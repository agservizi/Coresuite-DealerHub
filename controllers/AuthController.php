<?php
// controllers/AuthController.php - authentication flow

declare(strict_types=1);

class AuthController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function processLogin(): void
    {
        $username = sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $csrf = $_POST['csrf_token'] ?? '';

        if (!validateCsrfToken($csrf)) {
            setFlash('auth', 'Token CSRF non valido', 'danger');
            redirect('/login.php');
        }

        $user = $this->userModel->findByUsername($username);
        if (!$user || !password_verify($password, $user['password'])) {
            setFlash('auth', 'Credenziali non valide', 'danger');
            redirect('/login.php');
        }

        if ($user['status'] !== 'active') {
            setFlash('auth', 'Utente sospeso. Contatta il supporto.', 'warning');
            redirect('/login.php');
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        redirect('/dashboard');
    }

    public function logout(): void
    {
        session_destroy();
        redirect('/login.php');
    }

    public function checkAuth(): void
    {
        if (!isAuthenticated()) {
            redirect('/login.php');
        }
    }
}

?>
