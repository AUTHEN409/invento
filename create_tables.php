<?php
define('BASE_PATH', __DIR__ . '/');

// Autoloader function
spl_autoload_register(function ($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = BASE_PATH . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Load Config
require_once BASE_PATH . 'config/config.php';

try {
    $db = \core\Database::getInstance();

    // Drop it if it was created wrong previously
    $db->query("DROP TABLE IF EXISTS audit_logs");
    
    // Create Audit Logs Table
    $db->query("CREATE TABLE audit_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        business_id INT NULL,
        user_id INT NULL,
        action VARCHAR(100) NOT NULL,
        description TEXT,
        ip_address VARCHAR(45),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Audit Logs table created successfully.<br>";

    // Create Platform Settings Table
    $db->query("CREATE TABLE IF NOT EXISTS platform_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) UNIQUE NOT NULL,
        setting_value TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    echo "Platform Settings table created successfully.<br>";

    // Create Plans Table
    $db->query("CREATE TABLE IF NOT EXISTS plans (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        billing_cycle ENUM('monthly', 'yearly', 'lifetime') DEFAULT 'monthly',
        max_stores INT DEFAULT 1,
        max_users INT DEFAULT 1,
        features JSON,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Plans table verified/created successfully.<br>";

    // Add permissions column to businesses if it doesn't exist
    try {
        $db->query("ALTER TABLE businesses ADD COLUMN permissions JSON NULL AFTER plan_id");
        echo "Permissions column added to businesses table.<br>";
    } catch (Exception $e) {
        // Column likely already exists
    }

    // Create CMS Content Table
    $db->query("CREATE TABLE IF NOT EXISTS cms_content (
        id INT AUTO_INCREMENT PRIMARY KEY,
        section VARCHAR(50) NOT NULL,
        content_key VARCHAR(100) NOT NULL,
        content_value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY section_key (section, content_key)
    )");
    echo "CMS Content table created successfully.<br>";

    // Create CMS Reviews Table
    $db->query("CREATE TABLE IF NOT EXISTS cms_reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        business_id INT NULL,
        reviewer_name VARCHAR(100) NOT NULL,
        business_name VARCHAR(100) NOT NULL,
        review_text TEXT NOT NULL,
        rating INT DEFAULT 5,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "CMS Reviews table created successfully.<br>";

    // Create CMS Screenshots Table
    $db->query("CREATE TABLE IF NOT EXISTS cms_screenshots (
        id INT AUTO_INCREMENT PRIMARY KEY,
        business_id INT NULL,
        title VARCHAR(100),
        image_path VARCHAR(255) NOT NULL,
        display_order INT DEFAULT 0,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "CMS Screenshots table created successfully.<br>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
