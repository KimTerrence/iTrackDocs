<?php
namespace Models;

use Config\Database;
use PDO;

class Notification {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Try to create the table if it doesn't exist (auto-setup)
    private function ensureTable() {
        try {
            $this->conn->exec("CREATE TABLE IF NOT EXISTS notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                type VARCHAR(50) NOT NULL DEFAULT 'info',
                title VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                tracking_id VARCHAR(20) DEFAULT NULL,
                is_read TINYINT(1) NOT NULL DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        } catch (\Exception $e) {
            // Silently continue - table might already exist
        }
    }

    // Create a notification for a specific user
    public function create($userId, $type, $title, $message, $trackingId = null) {
        $this->ensureTable();
        $query = "INSERT INTO notifications (user_id, type, title, message, tracking_id, is_read) 
                  VALUES (:user_id, :type, :title, :message, :tracking_id, 0)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':tracking_id', $trackingId);
        return $stmt->execute();
    }

    // Create notifications for all users in a department
    public function createForDepartment($department, $type, $title, $message, $trackingId = null, $excludeUserId = null) {
        $query = "SELECT id FROM users WHERE department = :department";
        if ($excludeUserId) {
            $query .= " AND id != :exclude_id";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':department', $department);
        if ($excludeUserId) {
            $stmt->bindParam(':exclude_id', $excludeUserId, PDO::PARAM_INT);
        }
        $stmt->execute();
        
        $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $success = true;
        
        foreach ($userIds as $userId) {
            if (!$this->create($userId, $type, $title, $message, $trackingId)) {
                $success = false;
            }
        }
        return $success;
    }

    // Get unread notifications for a user
    public function getUnread($userId, $limit = 20) {
        $this->ensureTable();
        $query = "SELECT * FROM notifications 
                  WHERE user_id = :user_id AND is_read = 0 
                  ORDER BY created_at DESC 
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get recent notifications (read + unread)
    public function getRecent($userId, $limit = 20) {
        $this->ensureTable();
        $query = "SELECT * FROM notifications 
                  WHERE user_id = :user_id 
                  ORDER BY created_at DESC 
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get unread count for a user
    public function getUnreadCount($userId) {
        $this->ensureTable();
        $query = "SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    // Mark a single notification as read
    public function markAsRead($notificationId, $userId) {
        $this->ensureTable();
        $query = "UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $notificationId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Mark all notifications as read for a user
    public function markAllAsRead($userId) {
        $this->ensureTable();
        $query = "UPDATE notifications SET is_read = 1 WHERE user_id = :user_id AND is_read = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}