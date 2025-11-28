<?php
// models/ActivityLog.php - audit trail

declare(strict_types=1);

class ActivityLog
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->ensureContextColumn();
    }

    public function log(int $userId, string $action, string $context = ''): void
    {
        $this->db->insert('activity_logs', [
            'user_id' => $userId,
            'action' => $action,
            'context' => $context,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function latest(int $limit = 20): array
    {
        return $this->db->fetchAll('SELECT al.*, u.username FROM activity_logs al LEFT JOIN users u ON u.id = al.user_id ORDER BY al.created_at DESC LIMIT :limit', [
            'limit' => $limit
        ]);
    }

    private function ensureContextColumn(): void
    {
        $sql = "SELECT COUNT(*) AS total FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'activity_logs' AND COLUMN_NAME = 'context'";
        $column = $this->db->fetch($sql);
        if ((int) ($column['total'] ?? 0) === 0) {
            $pdo = $this->db->getConnection();
            $pdo->exec("ALTER TABLE activity_logs ADD COLUMN context TEXT NULL AFTER action");
        }
    }
}

?>
