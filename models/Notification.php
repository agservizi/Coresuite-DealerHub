<?php
// models/Notification.php - notification handling

declare(strict_types=1);

class Notification
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->ensureIsReadColumn();
    }

    private function ensureIsReadColumn(): void
    {
        $sql = "SELECT COUNT(*) AS total FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notifications' AND COLUMN_NAME = 'is_read'";
        $column = $this->db->fetch($sql);
        if ((int) ($column['total'] ?? 0) === 0) {
            $pdo = $this->db->getConnection();
            $pdo->exec("ALTER TABLE notifications ADD COLUMN is_read TINYINT(1) DEFAULT 0 AFTER type");
            $pdo->exec("UPDATE notifications SET is_read = 0 WHERE is_read IS NULL");
        }
    }

    public function create(array $data): int
    {
        $payload = [
            'user_id' => $data['user_id'],
            'message' => $data['message'],
            'type' => $data['type'] ?? 'info',
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        return $this->db->insert('notifications', $payload);
    }

    public function getUnreadCount(int $userId): int
    {
        $row = $this->db->fetch('SELECT COUNT(*) AS total FROM notifications WHERE user_id = :uid AND is_read = 0', ['uid' => $userId]);
        return (int) ($row['total'] ?? 0);
    }

    public function getByUser(int $userId, int $limit = 10): array
    {
        return $this->db->fetchAll('SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT :limit', [
            'uid' => $userId,
            'limit' => $limit
        ]);
    }

    public function markAllRead(int $userId): void
    {
        $this->db->update('notifications', ['is_read' => 1], 'user_id = :uid', ['uid' => $userId]);
    }
}

?>
