<?php

namespace app\Controllers;

use core\Controller;
use core\Database;
use PDO;

class SupplierController extends Controller {
    public function index() {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM suppliers WHERE business_id = ? ORDER BY name ASC");
        $stmt->execute([$_SESSION['business_id']]);
        $suppliers = $stmt->fetchAll();

        $this->view('suppliers/index', ['suppliers' => $suppliers, 'csrf_token' => $this->generateCsrfToken()]);
    }

    public function store() {
        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
        
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $gst = trim($_POST['gst_number'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if (!empty($name)) {
            $db = Database::getInstance();
            $stmt = $db->prepare("INSERT INTO suppliers (business_id, name, phone, gst_number, address) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['business_id'], $name, $phone, $gst, $address]);
            
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                $this->json(['success' => true, 'id' => $db->lastInsertId(), 'name' => $name]);
            }
        }
        $this->redirect('/suppliers');
    }

    public function destroy($id) {
        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
        $db = Database::getInstance();
        try {
            $stmt = $db->prepare("DELETE FROM suppliers WHERE id = ? AND business_id = ?");
            $stmt->execute([$id, $_SESSION['business_id']]);
        } catch (\PDOException $e) {
            $_SESSION['error_msg'] = "Cannot delete this supplier because they are linked to existing inventory purchases.";
        }
        $this->redirect('/suppliers');
    }
}
