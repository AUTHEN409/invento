<?php

namespace app\Controllers;

use core\Controller;
use core\Database;
use PDO;

class InventoryController extends Controller {
    
    public function index() {
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];
        $sid = $_SESSION['current_store_id'];

        $query = "SELECT p.name, p.sku, c.name as category_name, SUM(b.remaining_quantity) as total_qty, s.store_name 
                  FROM products p 
                  JOIN inventory_batches b ON p.id = b.product_id
                  LEFT JOIN categories c ON p.category_id = c.id
                  JOIN stores s ON b.store_id = s.id
                  WHERE b.business_id = :bid AND b.remaining_quantity > 0";
        
        $params = [':bid' => $bid];
        
        if ($sid !== 'all') {
            $query .= " AND b.store_id = :sid";
            $params[':sid'] = $sid;
        }
        
        $query .= " GROUP BY p.id, b.store_id ORDER BY p.name ASC";
        
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $inventory = $stmt->fetchAll();

        $this->view('inventory/index', ['inventory' => $inventory]);
    }

    public function stockInForm() {
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];

        $pStmt = $db->prepare("SELECT id, name, sku, purchase_price FROM products WHERE business_id = ? AND status = 'active' ORDER BY name ASC");
        $pStmt->execute([$bid]);
        $products = $pStmt->fetchAll();

        $sStmt = $db->prepare("SELECT id, name FROM suppliers WHERE business_id = ? ORDER BY name ASC");
        $sStmt->execute([$bid]);
        $suppliers = $sStmt->fetchAll();

        $stStmt = $db->prepare("SELECT id, store_name FROM stores WHERE business_id = ?");
        $stStmt->execute([$bid]);
        $stores = $stStmt->fetchAll();

        $this->view('inventory/stock_in', [
            'products' => $products, 
            'suppliers' => $suppliers, 
            'stores' => $stores,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    public function stockIn() {
        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];

        $product_id = $_POST['product_id'];
        $store_id = $_POST['store_id'];
        $supplier_id = !empty($_POST['supplier_id']) ? $_POST['supplier_id'] : null;
        
        if ($supplier_id === 'new' && !empty($_POST['new_supplier'])) {
            $ns = trim($_POST['new_supplier']);
            $supStmt = $db->prepare("INSERT INTO suppliers (business_id, name) VALUES (?, ?)");
            $supStmt->execute([$bid, $ns]);
            $supplier_id = $db->lastInsertId();
        }

        $quantity = (float)$_POST['quantity'];
        $purchase_price = (float)$_POST['purchase_price'];
        $batch_number = $_POST['batch_number'] ?? null;
        $purchase_date = $_POST['purchase_date'] ?: date('Y-m-d');

        // Validation
        if ($quantity <= 0) die("Invalid quantity");

        // Validate store access (Manager/Cashier can only stock in their store)
        if ($_SESSION['role'] !== 'owner' && $store_id != $_SESSION['current_store_id']) {
            die("Unauthorized store access.");
        }

        try {
            $db->beginTransaction();

            // 1. Create Batch
            $bStmt = $db->prepare("INSERT INTO inventory_batches 
                (business_id, store_id, product_id, supplier_id, batch_number, purchase_price, original_quantity, remaining_quantity, purchase_date, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $bStmt->execute([
                $bid, $store_id, $product_id, $supplier_id, $batch_number, $purchase_price, $quantity, $quantity, $purchase_date, $_SESSION['user_id']
            ]);
            
            $batch_id = $db->lastInsertId();

            // 2. Create Movement Record
            $mStmt = $db->prepare("INSERT INTO inventory_movements 
                (business_id, store_id, product_id, batch_id, movement_type, quantity, reference_type, reference_id, created_by)
                VALUES (?, ?, ?, ?, 'stock_in', ?, 'batch', ?, ?)");
            $mStmt->execute([
                $bid, $store_id, $product_id, $batch_id, $quantity, $batch_id, $_SESSION['user_id']
            ]);

            // 3. Audit Log
            $audit = $db->prepare("INSERT INTO audit_logs (business_id, store_id, user_id, action, entity_type, entity_id, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $audit->execute([$bid, $store_id, $_SESSION['user_id'], 'stock_in', 'inventory_batch', $batch_id, "Stock In: $quantity units of Product ID $product_id"]);

            $db->commit();
            $this->redirect('/inventory');
        } catch (\Exception $e) {
            $db->rollBack();
            die("Transaction failed: " . $e->getMessage());
        }
    }

    public function deadStock() {
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];
        $sid = $_SESSION['current_store_id'];

        // Find products in batches that haven't been sold (moved) in 90+ days, or never sold and bought 90+ days ago
        // For MVP, simplified: batches older than 90 days with remaining quantity > 0
        
        $query = "SELECT p.name, c.name as category_name, s.store_name, b.remaining_quantity, b.purchase_price, b.purchase_date,
                         DATEDIFF(CURDATE(), b.purchase_date) as days_old,
                         (b.remaining_quantity * b.purchase_price) as stock_value
                  FROM inventory_batches b
                  JOIN products p ON b.product_id = p.id
                  LEFT JOIN categories c ON p.category_id = c.id
                  JOIN stores s ON b.store_id = s.id
                  WHERE b.business_id = :bid AND b.remaining_quantity > 0 AND DATEDIFF(CURDATE(), b.purchase_date) >= 90";
                  
        $params = [':bid' => $bid];
        if ($sid !== 'all') {
            $query .= " AND b.store_id = :sid";
            $params[':sid'] = $sid;
        }
        $query .= " ORDER BY days_old DESC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $dead_stock = $stmt->fetchAll();

        $this->view('inventory/dead_stock', ['dead_stock' => $dead_stock]);
    }
}
