<?php
namespace Models;

use Config\Database;
use PDO;

class ActivityLog {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function log($user_id, $username, $action, $description, $ip_address = null) {
        $query = "INSERT INTO user_activity_logs (user_id, username, action, description, ip_address) 
                  VALUES (:user_id, :username, :action, :description, :ip_address)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':ip_address', $ip_address);
        $stmt->execute();
    }

    public function getRecent($limit = 100) {
        $query = "SELECT * FROM user_activity_logs ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentByUser($userId, $limit = 100) {
        $query = "SELECT * FROM user_activity_logs WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user_id', (int)$userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentByDepartment($department, $limit = 100) {
        $query = "SELECT ual.* FROM user_activity_logs ual 
                  JOIN users u ON ual.user_id = u.id 
                  WHERE u.department = :department 
                  ORDER BY ual.created_at DESC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':department', $department);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
