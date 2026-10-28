<?php

namespace app\Controllers;

use core\Controller;
use core\Database;
use PDO;

class CategoryController extends Controller {
    public function index() {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM categories WHERE business_id = ? ORDER BY name ASC");
        $stmt->execute([$_SESSION['business_id']]);
        $categories = $stmt->fetchAll();

        $this->view('categories/index', ['categories' => $categories, 'csrf_token' => $this->generateCsrfToken()]);
    }

    public function store() {
        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
        $name = trim($_POST['name'] ?? '');

        if (!empty($name)) {
            $db = Database::getInstance();
            try {
                $stmt = $db->prepare("INSERT INTO categories (business_id, name) VALUES (?, ?)");
                $stmt->execute([$_SESSION['business_id'], $name]);
            } catch (\Exception $e) {
                // Ignore duplicate entries
            }
        }
        $this->redirect('/categories');
    }

    public function destroy($id) {
        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
        $db = Database::getInstance();
        try {
            $stmt = $db->prepare("DELETE FROM categories WHERE id = ? AND business_id = ?");
            $stmt->execute([$id, $_SESSION['business_id']]);
        } catch (\PDOException $e) {
            $_SESSION['error_msg'] = "Cannot delete this category because it is linked to existing products.";
        }
        $this->redirect('/categories');
    }
}
