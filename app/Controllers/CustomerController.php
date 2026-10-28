<?php

namespace app\Controllers;

use core\Controller;
use core\Database;
use PDO;

class CustomerController extends Controller {
    
    public function index() {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT c.*, 
                                     COUNT(i.id) as total_purchases, 
                                     MAX(i.invoice_date) as last_purchase_date,
                                     SUM(i.due_amount) as outstanding_credit
                              FROM customers c 
                              LEFT JOIN invoices i ON c.id = i.customer_id 
                              WHERE c.business_id = ? 
                              GROUP BY c.id 
                              ORDER BY c.name ASC");
        $stmt->execute([$_SESSION['business_id']]);
        $customers = $stmt->fetchAll();

        $this->view('customers/index', ['customers' => $customers, 'csrf_token' => $this->generateCsrfToken()]);
    }

    public function store() {
        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
        $db = Database::getInstance();

        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if (empty($name) || empty($phone)) {
            die("Name and phone are required.");
        }

        $stmt = $db->prepare("INSERT INTO customers (business_id, name, phone, address) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_SESSION['business_id'], $name, $phone, $address]);

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            $this->json(['success' => true, 'id' => $db->lastInsertId(), 'name' => $name, 'phone' => $phone]);
        }

        $this->redirect('/customers');
    }

    public function show($id) {
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];
        
        $stmt = $db->prepare("SELECT * FROM customers WHERE id = ? AND business_id = ?");
        $stmt->execute([$id, $bid]);
        $customer = $stmt->fetch();

        if (!$customer) $this->redirect('/customers');

        // Get purchase history (invoices)
        $invStmt = $db->prepare("SELECT * FROM invoices WHERE customer_id = ? AND business_id = ? ORDER BY invoice_date DESC, invoice_time DESC");
        $invStmt->execute([$id, $bid]);
        $invoices = $invStmt->fetchAll();

        $this->view('customers/show', ['customer' => $customer, 'invoices' => $invoices]);
    }

    public function destroy($id) {
        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
        $db = Database::getInstance();
        try {
            $stmt = $db->prepare("DELETE FROM customers WHERE id = ? AND business_id = ?");
            $stmt->execute([$id, $_SESSION['business_id']]);
        } catch (\PDOException $e) {
            $_SESSION['error_msg'] = "Cannot delete this customer because they have existing invoices or credit history.";
        }
        $this->redirect('/customers');
    }
}
