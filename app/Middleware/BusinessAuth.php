<?php

namespace app\Middleware;

class BusinessAuth {
    public function handle() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'business') {
            header("Location: " . APP_URL . "/login");
            return false; // Block request
        }
        
        // Tenant Isolation Check is inherently enforced by using $_SESSION['business_id']
        // in all queries. The middleware ensures this session variable exists.
        if (empty($_SESSION['business_id'])) {
            die("Tenant context missing. Security violation.");
        }

        // Check subscription status
        $db = \core\Database::getInstance();
        $stmt = $db->prepare("SELECT status FROM businesses WHERE id = ?");
        $stmt->execute([$_SESSION['business_id']]);
        $businessStatus = $stmt->fetchColumn();

        if ($businessStatus === 'suspended') {
            session_destroy();
            die("Your business account has been suspended by the administrator.");
        }

        // Read-Only Enforcement for Expired Subscriptions
        if ($businessStatus === 'expired') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Allow logout POST if we change logout to post, but logout is currently GET
                http_response_code(403);
                die("Subscription Expired. Your account is in READ-ONLY mode. Please contact the administrator to renew your subscription.");
            }
        }

        return true;
    }
}
