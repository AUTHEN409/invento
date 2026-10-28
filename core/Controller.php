<?php

namespace core;

class Controller {
    
    protected function view($view, $data = []) {
        extract($data);
        $viewFile = VIEWS_PATH . $view . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View $view does not exist.");
        }
    }

    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect($url) {
        header("Location: " . APP_URL . $url);
        exit;
    }

    // CSRF Protection Helpers
    protected function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function verifyCsrfToken($token) {
        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            die("CSRF Token Validation Failed. Please refresh the page and try again.");
        }
    }
}
