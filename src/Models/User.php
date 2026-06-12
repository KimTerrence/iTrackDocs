<?php
namespace Models;

use Config\Database;
use PDO;

class User {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function authenticate($username, $password) {
        $query = "SELECT id, username, password, name, role FROM users WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            // Verify the hashed password
            if (password_verify($password, $row['password'])) {
                return $row;
            }
        }
        return false;
    }

    public function getUnreadNotifications($userId) {
        // We will build a real notification table later. Returning empty array for now.
        return [];
    }

    // Add this inside src/Models/User.php
    public function create($name, $username, $password, $role) {
        // Encrypt the password securely
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        $query = "INSERT INTO users (name, username, password, role) 
                  VALUES (:name, :username, :password, :role)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $role);
        
        return $stmt->execute();
    }

    // Find a single user by their ID
    public function findById($id) {
        $db = (new \Config\Database())->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Update the user's settings profile
    public function updateProfile($id, $name, $email, $department) {
        $db = (new \Config\Database())->getConnection();
        $stmt = $db->prepare("
            UPDATE users 
            SET name = :name, email = :email, department = :department 
            WHERE id = :id
        ");
        return $stmt->execute([
            'name' => $name,
            'email' => $email,
            'department' => $department,
            'id' => $id
        ]);
    }
    // Update user details from the Admin panel
    public function updateAsAdmin($id, $name, $username, $role, $password = '') {
        $db = (new \Config\Database())->getConnection();
        
        // If the admin typed a new password, hash it and update everything
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("
                UPDATE users 
                SET name = :name, username = :username, role = :role, password = :password 
                WHERE id = :id
            ");
            return $stmt->execute([
                'name' => $name,
                'username' => $username,
                'role' => $role,
                'password' => $hashed_password,
                'id' => $id
            ]);
        } 
        // If the password field was left blank, update everything EXCEPT the password
        else {
            $stmt = $db->prepare("
                UPDATE users 
                SET name = :name, username = :username, role = :role 
                WHERE id = :id
            ");
            return $stmt->execute([
                'name' => $name,
                'username' => $username,
                'role' => $role,
                'id' => $id
            ]);
        }
    }
}