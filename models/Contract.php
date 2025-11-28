<?php
// models/Contract.php - contract repository

declare(strict_types=1);

class Contract
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(?int $userId = null, ?string $role = null): array
    {
        if ($role === 'affiliato' && $userId !== null) {
            return $this->db->fetchAll('SELECT * FROM contracts WHERE user_id = :user_id ORDER BY created_at DESC', ['user_id' => $userId]);
        }
        return $this->db->fetchAll('SELECT * FROM contracts ORDER BY created_at DESC');
    }

    public function getById(int $id, ?int $userId = null, ?string $role = null): ?array
    {
        $sql = 'SELECT * FROM contracts WHERE id = :id';
        $params = ['id' => $id];
        if ($role === 'affiliato' && $userId !== null) {
            $sql .= ' AND user_id = :user_id';
            $params['user_id'] = $userId;
        }
        return $this->db->fetch($sql . ' LIMIT 1', $params);
    }

    public function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('contracts', $data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->db->update('contracts', $data, 'id = :id', ['id' => $id]);
    }

    public function delete(int $id): bool
    {
        return $this->db->update('contracts', ['deleted_at' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $id]);
    }

    public function codeExists(string $code): bool
    {
        $result = $this->db->fetch('SELECT id FROM contracts WHERE contract_code = :code LIMIT 1', ['code' => $code]);
        return $result !== null;
    }

    public function stats(): array
    {
        $sql = 'SELECT type, status, COUNT(*) as total FROM contracts GROUP BY type, status';
        return $this->db->fetchAll($sql);
    }
}

?>
