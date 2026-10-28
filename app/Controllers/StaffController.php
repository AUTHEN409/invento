<?php

namespace app\Controllers;

use core\Controller;
use core\Database;
use PDO;

class StaffController extends Controller {

    public function index() {
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];

        $stmt = $db->prepare("SELECT u.*, s.store_name FROM users u LEFT JOIN stores s ON u.store_id = s.id WHERE u.business_id = ? ORDER BY u.created_at DESC");
        $stmt->execute([$bid]);
        $staff = $stmt->fetchAll();

        $stStmt = $db->prepare("SELECT id, store_name FROM stores WHERE business_id = ?");
        $stStmt->execute([$bid]);
        $stores = $stStmt->fetchAll();

        $this->view('staff/index', ['staff' => $staff, 'stores' => $stores, 'csrf_token' => $this->generateCsrfToken()]);
    }

    public function store() {
        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];

        $name = trim($_POST['name']);
        $phone = trim($_POST['phone']);
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $role = $_POST['role'];
        $store_id = $_POST['store_id'] ?: null; // Null if owner across all stores

        if (empty($name) || empty($username) || empty($password)) {
            die("Required fields missing");
        }

        if ($role !== 'owner' && empty($store_id)) {
            die("Managers and Cashiers must be assigned to a specific store.");
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $db->prepare("INSERT INTO users (business_id, store_id, name, phone, username, password_hash, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$bid, $store_id, $name, $phone, $username, $hash, $role]);
        } catch (\PDOException $e) {
            die("Error adding staff. Username might be taken.");
        }

        $this->redirect('/staff');
    }

    public function destroy($id) {
        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];

        if ($id == $_SESSION['user_id']) {
            $_SESSION['error_msg'] = "You cannot delete your own account.";
            $this->redirect('/staff');
        }

        try {
            $stmt = $db->prepare("DELETE FROM users WHERE id = ? AND business_id = ?");
            $stmt->execute([$id, $bid]);
        } catch (\PDOException $e) {
            $_SESSION['error_msg'] = "Cannot delete this staff member because they have associated records.";
        }
        $this->redirect('/staff');
    }
}
