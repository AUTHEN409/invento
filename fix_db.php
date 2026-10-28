<?php
require 'config/config.php';
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $db = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    // Check if entity_type exists
    $stmt = $db->query("SHOW COLUMNS FROM audit_logs LIKE 'entity_type'");
    $exists = $stmt->fetch();
    if (!$exists) {
        $db->exec("ALTER TABLE audit_logs ADD COLUMN `entity_type` VARCHAR(100) NULL AFTER `action`");
        echo "Added entity_type column to audit_logs.<br>";
    } else {
        echo "entity_type column already exists in audit_logs.<br>";
    }

    // Check if entity_id exists
    $stmt = $db->query("SHOW COLUMNS FROM audit_logs LIKE 'entity_id'");
    $exists = $stmt->fetch();
    if (!$exists) {
        $db->exec("ALTER TABLE audit_logs ADD COLUMN `entity_id` BIGINT UNSIGNED NULL AFTER `entity_type`");
        echo "Added entity_id column to audit_logs.<br>";
    } else {
        echo "entity_id column already exists in audit_logs.<br>";
    }
    
    echo "Database schema successfully updated.<br>";
    echo "<a href='index.php'>Go back</a>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
