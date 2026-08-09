<?php
/**
 * iTrackDocs Database Setup Script
 * Run this once to initialize the database tables.
 * php database/setup.php
 */

$host = 'localhost';
$dbname = 'itrackdocs';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");

    echo "✅ Database '$dbname' ready.\n";

    // --- Users Table ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        username VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(255) DEFAULT NULL,
        role VARCHAR(50) NOT NULL DEFAULT 'staff',
        department VARCHAR(100) NOT NULL DEFAULT '',
        status VARCHAR(20) NOT NULL DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✅ Table 'users' ready.\n";

    // --- Documents Table ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS documents (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tracking_id VARCHAR(20) NOT NULL UNIQUE,
        creator_id INT DEFAULT NULL,
        title VARCHAR(255) NOT NULL,
        category VARCHAR(100) DEFAULT NULL,
        origin VARCHAR(100) NOT NULL,
        target_department VARCHAR(100) NOT NULL DEFAULT '',
        current_location VARCHAR(100) NOT NULL,
        status VARCHAR(50) NOT NULL DEFAULT 'Routing',
        file_path VARCHAR(500) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✅ Table 'documents' ready.\n";

    // --- Notifications Table ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS notifications (
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
    echo "✅ Table 'notifications' ready.\n";

    // --- Document History Table ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS document_history (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tracking_id VARCHAR(20) NOT NULL,
        location VARCHAR(255) NOT NULL,
        action TEXT NOT NULL,
        recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (tracking_id) REFERENCES documents(tracking_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✅ Table 'document_history' ready.\n";

    // --- Seed Default Admin User ---
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE username = 'admin'");
    if ($stmt->fetchColumn() == 0) {
        $hashed = password_hash('admin123', PASSWORD_BCRYPT);
        $pdo->exec("INSERT INTO users (name, username, password, email, role, department, status) VALUES 
            ('System Administrator', 'admin', '$hashed', 'admin@itrackdocs.com', 'admin', 'CICS', 'active'),
            ('CICS Department Head', 'cicshead', '$hashed', 'cics@itrackdocs.com', 'head', 'CICS', 'active'),
            ('CICS Staff User', 'staff1', '$hashed', 'staff@itrackdocs.com', 'staff', 'CICS', 'active')
        ");
        echo "✅ Default users seeded (admin/admin123, cicshead/admin123, staff1/admin123).\n";
    } else {
        echo "ℹ️  Default users already exist, skipping seed.\n";
    }

    // 🔥 CICS ONLY: Update any existing non-CICS users to CICS
    $pdo->exec("UPDATE users SET department = 'CICS' WHERE department != 'CICS'");
    echo "✅ All users updated to CICS department.\n";

    // 🔥 CICS ONLY: Update existing documents to CICS departments
    $pdo->exec("UPDATE documents SET origin = 'CICS', target_department = 'CICS', current_location = 'CICS' WHERE origin != 'CICS' OR target_department != 'CICS' OR current_location != 'CICS'");
    echo "✅ All documents updated to CICS department.\n";

    // 🔥 CICS ONLY: Convert old categories to IC/EC
    // Any existing "Internal Letter" or "Letter" category becomes "IC" (Internal Communication)
    $pdo->exec("UPDATE documents SET category = 'IC' WHERE category IN ('Internal Letter', 'Letter')");
    // Any other category (External Letter, Memorandum, Report, etc.) becomes "EC" (External Communication)
    $pdo->exec("UPDATE documents SET category = 'EC' WHERE category IS NULL OR category NOT IN ('IC', 'EC')");
    echo "✅ All document categories converted to IC/EC.\n";

    echo "\n🎉 Setup complete!\n";
    echo "   Login with: admin / admin123\n";

} catch (PDOException $e) {
    die("❌ Database Error: " . $e->getMessage() . "\n");
}