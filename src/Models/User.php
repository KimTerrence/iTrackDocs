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

    public function authenticate($email, $password) {
        $query = "SELECT id, username, password, name, role, department, status FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            // Check if user is blocked
            if (strtolower($row['status'] ?? 'active') === 'blocked') {
                return 'blocked';
            }
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
    public function create($name, $email, $password, $role, $department = '') {
        // Encrypt the password securely
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        $query = "INSERT INTO users (name, username, email, password, role, department) 
                  VALUES (:name, :username, :email, :password, :role, :department)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':username', $email);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':department', $department);
        
        return $stmt->execute();
    }

    // Find a single user by their ID
    public function findById($id) {
        $db = (new \Config\Database())->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Block or Unblock a user
    public function updateStatus($id, $status) {
        $db = (new \Config\Database())->getConnection();
        $stmt = $db->prepare("UPDATE users SET status = :status WHERE id = :id");
        return $stmt->execute([
            'status' => $status,
            'id' => $id
        ]);
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
    public function updateAsAdmin($id, $name, $email, $role, $password = '') {
        $db = (new \Config\Database())->getConnection();
        
        // If the admin typed a new password, hash it and update everything
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("
                UPDATE users 
                SET name = :name, username = :username, email = :email, role = :role, password = :password 
                WHERE id = :id
            ");
            return $stmt->execute([
                'name' => $name,
                'username' => $email,
                'email' => $email,
                'role' => $role,
                'password' => $hashed_password,
                'id' => $id
            ]);
        } 
        // If the password field was left blank, update everything EXCEPT the password
        else {
            $stmt = $db->prepare("
                UPDATE users 
                SET name = :name, username = :username, email = :email, role = :role 
                WHERE id = :id
            ");
            return $stmt->execute([
                'name' => $name,
                'username' => $email,
                'email' => $email,
                'role' => $role,
                'id' => $id
            ]);
        }
    }

    //Fetch all users (used by add_user_modal)
    public function getUsers() {
        $query = "SELECT id, name, email, department, role FROM users ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    //Fetch all users belonging to a specific department
    public function getUsersByDepartment($department) {
        // Notice we DO NOT select the password column here for security!
        $query = "SELECT id, name, email, department, role 
                  FROM users 
                  WHERE department = :dept 
                  ORDER BY name ASC";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':dept', $department);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}