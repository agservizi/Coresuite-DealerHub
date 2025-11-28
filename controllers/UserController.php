<?php
// controllers/UserController.php - superadmin user management

declare(strict_types=1);

class UserController
{
    private User $userModel;
    private ActivityLog $log;

    public function __construct()
    {
        $this->userModel = new User();
        $this->log = new ActivityLog();
    }

    public function index(): void
    {
        requireAuth();
        if (!hasRole('superadmin')) {
            redirect('/dashboard');
        }
        $users = $this->userModel->getAll();
        include __DIR__ . '/../views/users/list.php';
    }

    public function create(): void
    {
        requireAuth();
        if (!hasRole('superadmin')) {
            redirect('/dashboard');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('users', 'Token CSRF non valido', 'danger');
                redirect('/users/create');
            }
            $data = [
                'username' => sanitize($_POST['username'] ?? ''),
                'email' => sanitize($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'role' => sanitize($_POST['role'] ?? 'affiliato'),
                'status' => sanitize($_POST['status'] ?? 'active')
            ];
            $errors = $this->validateUserPayload($data);
            if (!empty($errors)) {
                setFlash('users', implode('<br>', $errors), 'danger');
                redirect('/users/create');
            }

            try {
                $userId = $this->userModel->create($data);
            } catch (PDOException $exception) {
                setFlash('users', 'Impossibile creare l\'affiliato: ' . $exception->getMessage(), 'danger');
                redirect('/users/create');
            }
            $this->log->log($_SESSION['user_id'], 'create_user', json_encode(['user_id' => $userId]));
            setFlash('users', 'Affiliato creato con successo', 'success');
            redirect('/users');
        }
        include __DIR__ . '/../views/users/create.php';
    }

    public function edit(int $id): void
    {
        requireAuth();
        if (!hasRole('superadmin')) {
            redirect('/dashboard');
        }
        $user = $this->userModel->findById($id);
        if (!$user) {
            redirect('/users');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('users', 'Token CSRF non valido', 'danger');
                redirect('/users/edit/' . $id);
            }
            $data = [
                'email' => sanitize($_POST['email'] ?? ''),
                'role' => sanitize($_POST['role'] ?? 'affiliato'),
                'status' => sanitize($_POST['status'] ?? 'active'),
            ];
            if (!empty($_POST['password'])) {
                $data['password'] = $_POST['password'];
            }
            $this->userModel->update($id, $data);
            $this->log->log($_SESSION['user_id'], 'update_user', json_encode(['user_id' => $id]));
            setFlash('users', 'Affiliato aggiornato', 'success');
            redirect('/users');
        }
        include __DIR__ . '/../views/users/edit.php';
    }

    public function toggleStatus(int $id): void
    {
        requireAuth();
        if (!hasRole('superadmin')) {
            redirect('/dashboard');
        }
        $user = $this->userModel->findById($id);
        if (!$user) {
            redirect('/users');
        }
        $newStatus = $user['status'] === 'active' ? 'suspended' : 'active';
        $this->userModel->updateStatus($id, $newStatus);
        $this->log->log($_SESSION['user_id'], 'toggle_user_status', json_encode(['user_id' => $id, 'status' => $newStatus]));
        redirect('/users');
    }

    private function validateUserPayload(array $data): array
    {
        $errors = [];
        if ($data['username'] === '') {
            $errors[] = 'Lo username è obbligatorio.';
        }
        if ($data['email'] === '' || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Inserisci un indirizzo email valido.';
        }
        if (strlen($data['password']) < 8) {
            $errors[] = 'La password deve contenere almeno 8 caratteri.';
        }
        if ($this->userModel->findByUsername($data['username'])) {
            $errors[] = 'Lo username selezionato è già in uso.';
        }
        if ($this->userModel->findByEmail($data['email'])) {
            $errors[] = 'L\'email indicata risulta già registrata.';
        }
        return $errors;
    }
}

?>
