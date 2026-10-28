<?php

namespace app\Controllers;

use core\Controller;
use core\Database;
use PDO;
use Exception;

class BillingController extends Controller {

    public function index() {
        // Prevent owners from billing if they have "All Stores" selected. Billing requires a specific store.
        if ($_SESSION['current_store_id'] === 'all') {
            die("Please select a specific store to generate a bill.");
        }

        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];

        $cStmt = $db->prepare("SELECT * FROM customers WHERE business_id = ? ORDER BY name ASC");
        $cStmt->execute([$bid]);
        $customers = $cStmt->fetchAll();

        $this->view('billing/index', [
            'customers' => $customers,
            'store_id' => $_SESSION['current_store_id'],
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    // AJAX Endpoint for Product Search
    public function searchProduct() {
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
            die("Direct access not allowed");
        }

        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];
        $sid = $_SESSION['current_store_id'];
        
        $query = trim($_GET['q'] ?? '');
        if (empty($query)) $this->json([]);

        try {
            $sql = "SELECT p.id, p.name, p.sku, p.selling_price, p.gst_percent, p.tax_mode, 
                           c.name as category_name, SUM(b.remaining_quantity) as available_qty
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    LEFT JOIN inventory_batches b ON p.id = b.product_id AND b.store_id = :sid
                    WHERE p.business_id = :bid AND p.status = 'active'
                    AND (p.name LIKE :q1 OR p.sku LIKE :q2)
                    GROUP BY p.id, p.name, p.sku, p.selling_price, p.gst_percent, p.tax_mode, c.name
                    LIMIT 20";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':sid' => $sid,
                ':bid' => $bid,
                ':q1' => "%$query%",
                ':q2' => "%$query%"
            ]);

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->json($results);
        } catch (\Exception $e) {
            file_put_contents(__DIR__ . '/../../search_error.log', $e->getMessage() . "\n" . $e->getTraceAsString());
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }

    // Process the final invoice
    public function process() {
        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
        $db = Database::getInstance();
        
        $bid = $_SESSION['business_id'];
        $sid = $_SESSION['current_store_id'];
        $uid = $_SESSION['user_id'];

        if ($sid === 'all') die("Store context required.");

        $customer_id = !empty($_POST['customer_id']) ? $_POST['customer_id'] : null;
        $c_name = trim($_POST['customer_name'] ?? '');
        $c_phone = trim($_POST['customer_phone'] ?? '');

        if (!$customer_id && !empty($c_phone)) {
            $cfind = $db->prepare("SELECT id FROM customers WHERE business_id = ? AND phone = ?");
            $cfind->execute([$bid, $c_phone]);
            $cid = $cfind->fetchColumn();
            if ($cid) {
                $customer_id = $cid;
            } else {
                if(empty($c_name)) $c_name = 'Customer ' . $c_phone;
                $cins = $db->prepare("INSERT INTO customers (business_id, name, phone) VALUES (?, ?, ?)");
                $cins->execute([$bid, $c_name, $c_phone]);
                $customer_id = $db->lastInsertId();
            }
        }

        $items_json = $_POST['items'] ?? '[]';
        $items = json_decode($items_json, true);
        
        $payment_method = $_POST['payment_method'] ?? 'cash';
        $paid_amount = (float)($_POST['paid_amount'] ?? 0);
        $invoice_discount = (float)($_POST['invoice_discount'] ?? 0);

        if (empty($items)) die("Invoice cannot be empty");

        try {
            $db->beginTransaction();

            // 1. Generate unique invoice number safely (atomic sequence not built-in MySQL, so we count/lock)
            // Using a simple unique generator for MVP
            $countStmt = $db->prepare("SELECT COUNT(*) FROM invoices WHERE store_id = ?");
            $countStmt->execute([$sid]);
            $count = $countStmt->fetchColumn() + 1;
            $invoice_number = "INV-" . date('Y') . "-" . str_pad($count, 6, "0", STR_PAD_LEFT);

            // Calculation Accumulators
            $subtotal = 0;
            $taxable_amount_total = 0;
            $gst_amount_total = 0;

            // Prepare Invoice Items data
            $processed_items = [];

            foreach ($items as $item) {
                // Fetch product details strictly from DB to prevent tampering
                $pStmt = $db->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ? AND p.business_id = ?");
                $pStmt->execute([$item['product_id'], $bid]);
                $product = $pStmt->fetch(PDO::FETCH_ASSOC);

                if (!$product) throw new Exception("Invalid product: " . $item['product_id']);

                $qty = (float)$item['quantity'];
                if ($qty <= 0) throw new Exception("Invalid quantity for " . $product['name']);

                $rate = isset($item['price']) ? (float)$item['price'] : (float)$product['selling_price'];
                $gst_percent = (float)$product['gst_percent'];
                $tax_mode = $product['tax_mode'];

                $item_total = $rate * $qty;
                $subtotal += $item_total;

                // Calculate Taxes correctly
                $item_taxable = 0;
                $item_gst = 0;

                if ($tax_mode === 'inclusive') {
                    // Taxable = Total / (1 + GST/100)
                    $item_taxable = $item_total / (1 + ($gst_percent / 100));
                    $item_gst = $item_total - $item_taxable;
                } else {
                    $item_taxable = $item_total;
                    $item_gst = $item_taxable * ($gst_percent / 100);
                    $item_total = $item_taxable + $item_gst; // Exclusive pushes total up
                }

                $taxable_amount_total += $item_taxable;
                $gst_amount_total += $item_gst;

                $processed_items[] = [
                    'product_id' => $product['id'],
                    'category_id' => $product['category_id'],
                    'product_name' => $product['name'],
                    'category_name' => $product['category_name'],
                    'quantity' => $qty,
                    'rate' => $rate,
                    'gst_percent' => $gst_percent,
                    'taxable_amount' => $item_taxable,
                    'cgst' => $item_gst / 2, // Assuming Intrastate for MVP
                    'sgst' => $item_gst / 2,
                    'igst' => 0,
                    'line_total' => $item_total
                ];

                // --- FIFO INVENTORY DEDUCTION ---
                // Fetch batches with remaining stock for this product in this store
                $batchStmt = $db->prepare("SELECT id, remaining_quantity FROM inventory_batches WHERE store_id = ? AND product_id = ? AND remaining_quantity > 0 ORDER BY purchase_date ASC, id ASC FOR UPDATE");
                $batchStmt->execute([$sid, $product['id']]);
                $batches = $batchStmt->fetchAll(PDO::FETCH_ASSOC);

                $qty_to_deduct = $qty;

                foreach ($batches as $batch) {
                    if ($qty_to_deduct <= 0) break;

                    $available = (float)$batch['remaining_quantity'];
                    $deduct = min($available, $qty_to_deduct);

                    // Update batch
                    $uBatch = $db->prepare("UPDATE inventory_batches SET remaining_quantity = remaining_quantity - ? WHERE id = ?");
                    $uBatch->execute([$deduct, $batch['id']]);

                    // Log movement
                    $mStmt = $db->prepare("INSERT INTO inventory_movements (business_id, store_id, product_id, batch_id, movement_type, quantity, reference_type, created_by) VALUES (?, ?, ?, ?, 'sale', ?, 'invoice', ?)");
                    $mStmt->execute([$bid, $sid, $product['id'], $batch['id'], $deduct, $uid]);

                    $qty_to_deduct -= $deduct;
                }

                if ($qty_to_deduct > 0.001) { // Floating point safety
                    throw new Exception("Insufficient stock for " . $product['name']);
                }
            }

            // Final Invoice Totals
            $grand_total_raw = $taxable_amount_total + $gst_amount_total - $invoice_discount;
            $grand_total = round($grand_total_raw);
            $round_off = $grand_total - $grand_total_raw;
            $due_amount = $grand_total - $paid_amount;

            if ($paid_amount > $grand_total) throw new Exception("Paid amount cannot exceed grand total.");
            if ($due_amount < 0) $due_amount = 0;

            $payment_status = 'paid';
            if ($due_amount > 0 && $due_amount < $grand_total) $payment_status = 'partial';
            if ($due_amount == $grand_total) $payment_status = 'credit';

            // Requires customer if credit
            if ($due_amount > 0 && !$customer_id) {
                throw new Exception("Customer is required for credit billing.");
            }

            // 2. Insert Invoice
            $invStmt = $db->prepare("INSERT INTO invoices 
                (business_id, store_id, customer_id, invoice_number, invoice_date, invoice_time, subtotal, discount_amount, taxable_amount, cgst_amount, sgst_amount, igst_amount, gst_amount, round_off, grand_total, paid_amount, due_amount, payment_status, created_by)
                VALUES (?, ?, ?, ?, CURDATE(), CURTIME(), ?, ?, ?, ?, ?, 0, ?, ?, ?, ?, ?, ?, ?)");
            $invStmt->execute([
                $bid, $sid, $customer_id, $invoice_number, $subtotal, $invoice_discount, $taxable_amount_total, 
                $gst_amount_total/2, $gst_amount_total/2, $gst_amount_total, $round_off, $grand_total, 
                $paid_amount, $due_amount, $payment_status, $uid
            ]);
            $invoice_id = $db->lastInsertId();

            // 3. Insert Invoice Items
            $itemInsert = $db->prepare("INSERT INTO invoice_items 
                (invoice_id, product_id, category_id, product_name_snapshot, category_name_snapshot, quantity, rate, gst_percent, taxable_amount, cgst_amount, sgst_amount, igst_amount, line_total)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?)");
            
            foreach ($processed_items as $pi) {
                $itemInsert->execute([
                    $invoice_id, $pi['product_id'], $pi['category_id'], $pi['product_name'], $pi['category_name'],
                    $pi['quantity'], $pi['rate'], $pi['gst_percent'], $pi['taxable_amount'], $pi['cgst'], $pi['sgst'], $pi['line_total']
                ]);
            }

            // 4. Record Payment if any
            $payment_id = null;
            if ($paid_amount > 0) {
                $payStmt = $db->prepare("INSERT INTO payments (business_id, store_id, customer_id, invoice_id, amount, payment_method, received_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $payStmt->execute([$bid, $sid, $customer_id, $invoice_id, $paid_amount, $payment_method, $uid]);
                $payment_id = $db->lastInsertId();
            }

            // 5. Credit Transaction if Due
            if ($due_amount > 0 && $customer_id) {
                $credStmt = $db->prepare("INSERT INTO credit_transactions (business_id, store_id, customer_id, invoice_id, transaction_type, amount, created_by) VALUES (?, ?, ?, ?, 'credit_sale', ?, ?)");
                $credStmt->execute([$bid, $sid, $customer_id, $invoice_id, $due_amount, $uid]);
            }

            // Reference update in movements
            $upMove = $db->prepare("UPDATE inventory_movements SET reference_id = ? WHERE reference_type = 'invoice' AND reference_id IS NULL AND created_by = ?");
            $upMove->execute([$invoice_id, $uid]);

            $db->commit();
            
            $this->json(['success' => true, 'invoice_id' => $invoice_id]);
        } catch (Exception $e) {
            $db->rollBack();
            $this->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
