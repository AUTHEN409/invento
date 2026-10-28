<?php

namespace app\Controllers;

use core\Controller;
use core\Database;
use PDO;

class StoreController extends Controller {

    public function index() {
        $db = Database::getInstance();
        $business_id = $_SESSION['business_id'];

        $stmt = $db->prepare("SELECT * FROM stores WHERE business_id = ? ORDER BY created_at DESC");
        $stmt->execute([$business_id]);
        $stores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('stores/index', ['stores' => $stores]);
    }

    public function store() {
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];
        
        $store_name = trim($_POST['store_name'] ?? '');
        $store_code = trim($_POST['store_code'] ?? '') ?: null;
        $phone = trim($_POST['phone'] ?? '') ?: null;
        
        if (empty($store_name)) die("Store name is required.");

        $stmt = $db->prepare("INSERT INTO stores (business_id, store_name, store_code, phone, status) VALUES (?, ?, ?, ?, 'active')");
        $stmt->execute([$bid, $store_name, $store_code, $phone]);

        $this->redirect('/stores');
    }

    public function update($id) {
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];
        
        $store_name = trim($_POST['store_name'] ?? '');
        $store_code = trim($_POST['store_code'] ?? '') ?: null;
        $phone = trim($_POST['phone'] ?? '') ?: null;
        $status = $_POST['status'] ?? 'active';
        
        if (empty($store_name)) die("Store name is required.");

        $stmt = $db->prepare("UPDATE stores SET store_name = ?, store_code = ?, phone = ?, status = ? WHERE id = ? AND business_id = ?");
        $stmt->execute([$store_name, $store_code, $phone, $status, $id, $bid]);

        $this->redirect('/stores');
    }

    public function destroy($id) {
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];
        
        // Prevent deleting if it's the only store
        $cStmt = $db->prepare("SELECT COUNT(*) FROM stores WHERE business_id = ?");
        $cStmt->execute([$bid]);
        if ($cStmt->fetchColumn() <= 1) {
            $_SESSION['error_msg'] = "You cannot delete your only store.";
            $this->redirect('/stores');
        }

        try {
            $stmt = $db->prepare("DELETE FROM stores WHERE id = ? AND business_id = ?");
            $stmt->execute([$id, $bid]);
        } catch (\PDOException $e) {
            $_SESSION['error_msg'] = "Cannot delete this store because it has associated inventory or staff.";
        }
        $this->redirect('/stores');
    }
}
