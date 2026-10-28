<?php

namespace app\Controllers;

use core\Controller;
use core\Database;
use PDO;

class AuthController extends Controller {

    public function loginForm() {
        // If already logged in
        if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'business') {
            $this->redirect('/app');
        }
        $this->view('auth/login', ['csrf_token' => $this->generateCsrfToken()]);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }

        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $this->view('auth/login', ['error' => 'Username and password are required', 'csrf_token' => $this->generateCsrfToken()]);
            return;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT u.*, b.status as business_status FROM users u JOIN businesses b ON u.business_id = b.id WHERE u.username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            
            if ($user['status'] !== 'active') {
                $this->view('auth/login', ['error' => 'Your account has been disabled.', 'csrf_token' => $this->generateCsrfToken()]);
                return;
            }

            if ($user['business_status'] === 'suspended') {
                $this->view('auth/login', ['error' => 'Your business account is suspended.', 'csrf_token' => $this->generateCsrfToken()]);
                return;
            }

            // Prevent session fixation
            session_regenerate_id(true);

            $_SESSION['user_type'] = 'business';
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['business_id'] = $user['business_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            
            // Set current store context
            if ($user['role'] === 'owner') {
                // Owner selects store later or views all
                $_SESSION['current_store_id'] = 'all';
            } else {
                $_SESSION['current_store_id'] = $user['store_id'];
            }

            // Update last login
            $update = $db->prepare("UPDATE users SET last_login_at = NOW() WHERE id = :id");
            $update->execute(['id' => $user['id']]);

            // Audit log
            \app\Helpers\Logger::log('USER_LOGIN', 'User logged in', $user['id'], $user['business_id']);

            $this->redirect('/app');
        } else {
            // Protect against brute force here (e.g. rate limiting or delay)
            sleep(1);
            $this->view('auth/login', ['error' => 'Invalid username or password', 'csrf_token' => $this->generateCsrfToken()]);
        }
    }

    public function logout() {
        // Prevent browser prefetching from accidentally logging the user out
        if (isset($_SERVER['HTTP_X_PURPOSE']) && $_SERVER['HTTP_X_PURPOSE'] === 'prefetch') return;
        if (isset($_SERVER['HTTP_SEC_FETCH_PURPOSE']) && $_SERVER['HTTP_SEC_FETCH_PURPOSE'] === 'prefetch') return;

        session_unset();
        session_destroy();
        $this->redirect('/login');
    }
}
