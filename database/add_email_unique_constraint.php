<?php
$host = 'localhost';
$dbname = 'itrackdocs';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Add unique constraint on email column
    $sql = "ALTER TABLE users ADD UNIQUE INDEX unique_email (email)";
    $pdo->exec($sql);
    
    echo "Success: Unique constraint added on email column.\n";
} catch(PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate') !== false || strpos($e->getMessage(), 'exists') !== false) {
        echo "Info: Unique constraint already exists.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}