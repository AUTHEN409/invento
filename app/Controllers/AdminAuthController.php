<?php

namespace app\Controllers;

use core\Controller;
use core\Database;
use PDO;

class AdminAuthController extends Controller {

    public function loginForm() {
        if (isset($_SESSION['admin_id']) && $_SESSION['user_type'] === 'admin') {
            $this->redirect('/admin');
        }
        $this->view('admin/login', ['csrf_token' => $this->generateCsrfToken()]);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/login');
        }

        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $this->view('admin/login', ['error' => 'Username and password are required', 'csrf_token' => $this->generateCsrfToken()]);
            return;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM super_admins WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password_hash'])) {
            
            if ($admin['status'] !== 'active') {
                $this->view('admin/login', ['error' => 'Account inactive.', 'csrf_token' => $this->generateCsrfToken()]);
                return;
            }

            session_regenerate_id(true);

            $_SESSION['user_type'] = 'admin';
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            
            $update = $db->prepare("UPDATE super_admins SET last_login_at = NOW() WHERE id = :id");
            $update->execute(['id' => $admin['id']]);

            $this->redirect('/admin');
        } else {
            sleep(1);
            $this->view('admin/login', ['error' => 'Invalid credentials', 'csrf_token' => $this->generateCsrfToken()]);
        }
    }

    public function logout() {
        // Prevent browser prefetching from accidentally logging the user out
        if (isset($_SERVER['HTTP_X_PURPOSE']) && $_SERVER['HTTP_X_PURPOSE'] === 'prefetch') return;
        if (isset($_SERVER['HTTP_SEC_FETCH_PURPOSE']) && $_SERVER['HTTP_SEC_FETCH_PURPOSE'] === 'prefetch') return;

        session_unset();
        session_destroy();
        $this->redirect('/admin/login');
    }
}
