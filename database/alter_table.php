<?php
$pdo = new PDO('mysql:host=localhost;dbname=itrackdocs', 'root', '');
$stmt = $pdo->query("DESCRIBE users");
$columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('status', $columns)) {
    $pdo->exec("ALTER TABLE users ADD COLUMN status VARCHAR(20) DEFAULT 'active' NOT NULL");
    echo "Column 'status' added successfully.\n";
} else {
    echo "Column 'status' already exists.\n";
}