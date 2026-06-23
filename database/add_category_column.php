<?php
$pdo = new PDO('mysql:host=localhost;dbname=itrackdocs', 'root', '');

// Check if category column already exists
$stmt = $pdo->query("DESCRIBE documents");
$columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (!in_array('category', $columns)) {
    $pdo->exec("ALTER TABLE documents ADD COLUMN category VARCHAR(100) DEFAULT NULL AFTER title");
    echo "Column 'category' added successfully to documents table.\n";
} else {
    echo "Column 'category' already exists.\n";
}