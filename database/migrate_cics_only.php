<?php
/**
 * iTrackDocs Migration Script: CICS Only
 * Run this to convert existing data to CICS-only + IC/EC categories
 * php database/migrate_cics_only.php
 */

$host = 'localhost';
$dbname = 'itrackdocs';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "🔄 Starting CICS-only migration...\n\n";

    // 1. Update all users to CICS department
    $count = $pdo->exec("UPDATE users SET department = 'CICS' WHERE department != 'CICS'");
    echo "✅ Updated $count user(s) to CICS department.\n";

    // 2. Update all documents to CICS departments
    $count = $pdo->exec("UPDATE documents SET origin = 'CICS', target_department = 'CICS', current_location = 'CICS' WHERE origin != 'CICS' OR target_department != 'CICS' OR current_location != 'CICS'");
    echo "✅ Updated $count document(s) to CICS department.\n";

    // 3. Convert old categories to IC/EC
    // Any existing "Internal Letter" or "Letter" category becomes "IC" (Internal Communication)
    $count = $pdo->exec("UPDATE documents SET category = 'IC' WHERE category IN ('Internal Letter', 'Letter')");
    echo "✅ Converted $count document(s) to 'IC' (Internal Communication).\n";

    // Any other category (External Letter, Memorandum, Report, etc.) becomes "EC" (External Communication)
    $count = $pdo->exec("UPDATE documents SET category = 'EC' WHERE category IS NULL OR category NOT IN ('IC', 'EC')");
    echo "✅ Converted $count document(s) to 'EC' (External Communication).\n";

    // 4. Update document_history locations to CICS
    $count = $pdo->exec("UPDATE document_history SET location = 'CICS' WHERE location != 'CICS' AND location NOT LIKE '%@%' AND location NOT LIKE '%,%'");
    echo "✅ Updated $count history record(s) location to CICS.\n";

    echo "\n🎉 Migration complete! All data is now CICS-only with IC/EC categories.\n";

} catch (PDOException $e) {
    die("❌ Database Error: " . $e->getMessage() . "\n");
}
