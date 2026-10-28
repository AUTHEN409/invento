<?php
namespace app\Helpers;

use core\Database;

class Logger {
    public static function log($action, $description, $user_id = null, $business_id = null) {
        $db = Database::getInstance();
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        
        $stmt = $db->prepare(
            "INSERT INTO audit_logs (business_id, user_id, action, description, ip_address) 
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $business_id,
            $user_id,
            $action,
            $description,
            $ip_address
        ]);
    }
}
