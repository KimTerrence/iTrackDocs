<?php
namespace Config;

use PDO;
use PDOException;

class Database {
    private $host = 'localhost';
    private $db_name = 'itrackdocs';
    private $username = 'root';
    private $password = ''; 
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            // Set PDO error mode to exception for better debugging
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Fetch data as associative arrays by default
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            echo "Database Connection Error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}