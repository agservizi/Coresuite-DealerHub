<?php
// models/User.php - user repository

declare(strict_types=1);

class User
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByUsername(string $username): ?array
    {
        return $this->db->fetch('SELECT * FROM users WHERE username = :username LIMIT 1', ['username' => $username]);
    }

    public function findByEmail(string $email): ?array
    {
        return $this->db->fetch('SELECT * FROM users WHERE email = :email LIMIT 1', ['email' => $email]);
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetch('SELECT * FROM users WHERE id = :id LIMIT 1', ['id' => $id]);
    }

    public function getAll(): array
    {
        return $this->db->fetchAll('SELECT * FROM users ORDER BY created_at DESC');
    }

    public function getAffiliates(bool $onlyActive = true): array
    {
        $sql = 'SELECT id, username, email, status FROM users WHERE role = :role';
        $params = ['role' => 'affiliato'];
        if ($onlyActive) {
            $sql .= " AND status = 'active'";
        }
        $sql .= ' ORDER BY username ASC';
        return $this->db->fetchAll($sql, $params);
    }

    public function create(array $data): int
    {
        $payload = [
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'],
            'status' => $data['status'] ?? 'active',
            'created_at' => date('Y-m-d H:i:s')
        ];
        return $this->db->insert('users', $payload);
    }

    public function updateStatus(int $id, string $status): bool
    {
        return $this->db->update('users', ['status' => $status], 'id = :id', ['id' => $id]);
    }

    public function update(int $id, array $data): bool
    {
        $payload = $data;
        if (isset($data['password']) && $data['password'] !== '') {
            $payload['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($payload['password']);
        }
        return $this->db->update('users', $payload, 'id = :id', ['id' => $id]);
    }
}

?>
