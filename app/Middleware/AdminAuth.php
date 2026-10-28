<?php

namespace app\Middleware;

class AdminAuth {
    public function handle() {
        if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
            header("Location: " . APP_URL . "/admin/login");
            return false;
        }
        return true;
    }
}
