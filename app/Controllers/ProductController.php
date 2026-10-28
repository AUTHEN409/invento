<?php

namespace app\Controllers;

use core\Controller;
use core\Database;
use PDO;

class ProductController extends Controller {
    public function index() {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT p.*, c.name as category_name 
                              FROM products p 
                              LEFT JOIN categories c ON p.category_id = c.id 
                              WHERE p.business_id = ? AND p.status != 'archived'
                              ORDER BY p.name ASC");
        $stmt->execute([$_SESSION['business_id']]);
        $products = $stmt->fetchAll();

        // Also fetch total consolidated stock across all stores to show in the catalog
        $stockStmt = $db->prepare("SELECT product_id, SUM(remaining_quantity) as total_qty FROM inventory_batches WHERE business_id = ? GROUP BY product_id");
        $stockStmt->execute([$_SESSION['business_id']]);
        $stocks = $stockStmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $this->view('products/index', ['products' => $products, 'stocks' => $stocks]);
    }

    public function create() {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM categories WHERE business_id = ?");
        $stmt->execute([$_SESSION['business_id']]);
        $categories = $stmt->fetchAll();

        $this->view('products/create', ['categories' => $categories, 'csrf_token' => $this->generateCsrfToken()]);
    }

    public function store() {
        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
        
        $db = Database::getInstance();
        
        $name = trim($_POST['name'] ?? '');
        $sku = trim($_POST['sku'] ?? '') ?: null;
        $category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
        $new_category = trim($_POST['new_category'] ?? '');
        $purchase_price = $_POST['purchase_price'] ?? 0;
        $selling_price = $_POST['selling_price'] ?? 0;
        $gst_percent = $_POST['gst_percent'] ?? 0;
        $tax_mode = $_POST['tax_mode'] ?? 'exclusive';
        $status = $_POST['status'] ?? 'active';
        $opening_stock = (float)($_POST['opening_stock'] ?? 0);

        try {
            $db->beginTransaction();

            if ($category_id === 'new' && !empty($new_category)) {
                $ncStmt = $db->prepare("INSERT INTO categories (business_id, name) VALUES (?, ?)");
                $ncStmt->execute([$_SESSION['business_id'], $new_category]);
                $category_id = $db->lastInsertId();
            }

            $stmt = $db->prepare("INSERT INTO products 
                (business_id, category_id, sku, name, purchase_price, selling_price, gst_percent, tax_mode, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_SESSION['business_id'], $category_id, $sku, $name, $purchase_price, $selling_price, $gst_percent, $tax_mode, $status
            ]);
            
            // Log creation
            $product_id = $db->lastInsertId();
            $audit = $db->prepare("INSERT INTO audit_logs (business_id, user_id, action, entity_type, entity_id, description) VALUES (?, ?, ?, ?, ?, ?)");
            $audit->execute([$_SESSION['business_id'], $_SESSION['user_id'], 'create', 'product', $product_id, "Created product: $name"]);

            if ($opening_stock > 0 && $_SESSION['current_store_id'] !== 'all') {
                $bInsert = $db->prepare("INSERT INTO inventory_batches (business_id, store_id, product_id, batch_number, purchase_price, original_quantity, remaining_quantity, purchase_date, created_by) VALUES (?, ?, ?, 'OPENING', ?, ?, ?, CURDATE(), ?)");
                $bInsert->execute([$_SESSION['business_id'], $_SESSION['current_store_id'], $product_id, $purchase_price, $opening_stock, $opening_stock, $_SESSION['user_id']]);
                
                $batchId = $db->lastInsertId();
                $mInsert = $db->prepare("INSERT INTO inventory_movements (business_id, store_id, product_id, batch_id, movement_type, quantity, reference_type, created_by) VALUES (?, ?, ?, ?, 'stock_in', ?, 'batch', ?)");
                $mInsert->execute([$_SESSION['business_id'], $_SESSION['current_store_id'], $product_id, $batchId, $opening_stock, $_SESSION['user_id']]);
            }

            $db->commit();
            $this->redirect('/products');
        } catch (\Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            die("Error saving product: " . $e->getMessage());
        }
    }

    public function edit($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM products WHERE id = ? AND business_id = ?");
        $stmt->execute([$id, $_SESSION['business_id']]);
        $product = $stmt->fetch();

        if (!$product) {
            $this->redirect('/products');
        }

        $cStmt = $db->prepare("SELECT * FROM categories WHERE business_id = ?");
        $cStmt->execute([$_SESSION['business_id']]);
        $categories = $cStmt->fetchAll();

        $this->view('products/edit', ['product' => $product, 'categories' => $categories, 'csrf_token' => $this->generateCsrfToken()]);
    }

    public function update($id) {
        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
        $db = Database::getInstance();
        
        // Ensure product belongs to business
        $stmt = $db->prepare("SELECT id FROM products WHERE id = ? AND business_id = ?");
        $stmt->execute([$id, $_SESSION['business_id']]);
        if (!$stmt->fetch()) {
            die("Unauthorized");
        }

        $name = trim($_POST['name'] ?? '');
        $sku = trim($_POST['sku'] ?? '') ?: null;
        $category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
        $purchase_price = $_POST['purchase_price'] ?? 0;
        $selling_price = $_POST['selling_price'] ?? 0;
        $gst_percent = $_POST['gst_percent'] ?? 0;
        $tax_mode = $_POST['tax_mode'] ?? 'exclusive';
        $status = $_POST['status'] ?? 'active';

        $update = $db->prepare("UPDATE products SET category_id=?, sku=?, name=?, purchase_price=?, selling_price=?, gst_percent=?, tax_mode=?, status=? WHERE id=?");
        $update->execute([$category_id, $sku, $name, $purchase_price, $selling_price, $gst_percent, $tax_mode, $status, $id]);

        $audit = $db->prepare("INSERT INTO audit_logs (business_id, user_id, action, entity_type, entity_id, description) VALUES (?, ?, ?, ?, ?, ?)");
        $audit->execute([$_SESSION['business_id'], $_SESSION['user_id'], 'update', 'product', $id, "Updated product ID: $id"]);

        $this->redirect('/products');
    }

    public function destroy($id) {
        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE products SET status = 'archived' WHERE id = ? AND business_id = ?");
        $stmt->execute([$id, $_SESSION['business_id']]);
        
        $audit = $db->prepare("INSERT INTO audit_logs (business_id, user_id, action, entity_type, entity_id, description) VALUES (?, ?, ?, ?, ?, ?)");
        $audit->execute([$_SESSION['business_id'], $_SESSION['user_id'], 'delete', 'product', $id, "Archived product ID: $id"]);

        $this->redirect('/products');
    }

    public function importForm() {
        $this->view('products/import', ['csrf_token' => $this->generateCsrfToken()]);
    }

    public function importData() {
        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];
        $sid = $_SESSION['current_store_id'];

        if ($sid === 'all') {
            die("Please select a specific store to assign the imported opening stock.");
        }

        if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
            die("File upload error.");
        }

        $file = $_FILES['import_file']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION));

        if ($ext !== 'xlsx') {
            die("Only .xlsx files are supported.");
        }

        $rows = \core\XLSXReader::parse($file);
        if (!$rows || count($rows) < 2) {
            die("Failed to parse XLSX or file is empty.");
        }

        // Expected Headers: Product Name, SKU, Category, Purchase Price, Selling Price, GST, Tax Mode, Opening Stock
        // For simplicity, we assume strict column index: 
        // 0: Name, 1: SKU, 2: Category, 3: PP, 4: SP, 5: GST, 6: Tax Mode, 7: Stock

        $successCount = 0;
        $errors = [];

        // Pre-fetch categories to map names to IDs
        $cStmt = $db->prepare("SELECT id, name FROM categories WHERE business_id = ?");
        $cStmt->execute([$bid]);
        $catMap = [];
        foreach($cStmt->fetchAll() as $c) {
            $catMap[strtolower($c['name'])] = $c['id'];
        }

        try {
            $db->beginTransaction();

            $pInsert = $db->prepare("INSERT INTO products (business_id, category_id, sku, name, purchase_price, selling_price, gst_percent, tax_mode, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')");
            
            $bInsert = $db->prepare("INSERT INTO inventory_batches (business_id, store_id, product_id, batch_number, purchase_price, original_quantity, remaining_quantity, purchase_date, created_by) VALUES (?, ?, ?, 'OPENING', ?, ?, ?, CURDATE(), ?)");

            $mInsert = $db->prepare("INSERT INTO inventory_movements (business_id, store_id, product_id, batch_id, movement_type, quantity, reference_type, created_by) VALUES (?, ?, ?, ?, 'stock_in', ?, 'batch', ?)");

            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (empty($row[0])) continue; // Skip empty rows
                
                $name = trim($row[0] ?? '');
                $sku = trim($row[1] ?? '') ?: null;
                $catName = trim($row[2] ?? '');
                $pp = (float)($row[3] ?? 0);
                $sp = (float)($row[4] ?? 0);
                $gst = (float)($row[5] ?? 0);
                $mode = strtolower(trim($row[6] ?? 'exclusive'));
                $stock = (float)($row[7] ?? 0);

                if (!in_array($mode, ['inclusive', 'exclusive'])) $mode = 'exclusive';

                // Category ID handling
                $catId = null;
                if ($catName) {
                    $key = strtolower($catName);
                    if (isset($catMap[$key])) {
                        $catId = $catMap[$key];
                    } else {
                        // Create category
                        $nc = $db->prepare("INSERT INTO categories (business_id, name) VALUES (?, ?)");
                        $nc->execute([$bid, $catName]);
                        $catId = $db->lastInsertId();
                        $catMap[$key] = $catId;
                    }
                }

                // Check duplicate SKU
                if ($sku) {
                    $s = $db->prepare("SELECT id FROM products WHERE business_id = ? AND sku = ?");
                    $s->execute([$bid, $sku]);
                    if ($s->fetchColumn()) {
                        $errors[] = "Row $i: SKU '$sku' already exists.";
                        continue;
                    }
                }

                $pInsert->execute([$bid, $catId, $sku, $name, $pp, $sp, $gst, $mode]);
                $productId = $db->lastInsertId();

                if ($stock > 0) {
                    $bInsert->execute([$bid, $sid, $productId, $pp, $stock, $stock, $_SESSION['user_id']]);
                    $batchId = $db->lastInsertId();
                    $mInsert->execute([$bid, $sid, $productId, $batchId, $stock, $_SESSION['user_id']]);
                }

                $successCount++;
            }

            $db->commit();
            $this->redirect('/products?imported=' . $successCount);
        } catch (\Exception $e) {
            $db->rollBack();
            die("Import failed: " . $e->getMessage());
        }
    }
}
