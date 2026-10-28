<?php

namespace app\Middleware;

class OwnerAuth {
    public function handle() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
            http_response_code(403);
            die("403 Forbidden - Owner access required.");
            return false;
        }
        return true;
    }
}
