<?php

namespace app\Controllers;

use core\Controller;
use core\Database;
use PDO;

class InvoiceController extends Controller {

    public function index() {
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];
        $sid = $_SESSION['current_store_id'];

        $query = "SELECT i.*, c.name as customer_name, s.store_name, u.name as created_by_name 
                  FROM invoices i 
                  LEFT JOIN customers c ON i.customer_id = c.id
                  JOIN stores s ON i.store_id = s.id
                  LEFT JOIN users u ON i.created_by = u.id
                  WHERE i.business_id = :bid";
        
        $params = [':bid' => $bid];

        if ($sid !== 'all') {
            $query .= " AND i.store_id = :sid";
            $params[':sid'] = $sid;
        }

        $query .= " ORDER BY i.created_at DESC LIMIT 100";

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $invoices = $stmt->fetchAll();

        $this->view('invoices/index', ['invoices' => $invoices]);
    }

    public function show($id) {
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];
        $sid = $_SESSION['current_store_id'];

        $query = "SELECT i.*, c.name as customer_name, c.phone as customer_phone, s.store_name, s.address as store_address, s.phone as store_phone, s.gstin as store_gst, b.business_name, u.name as created_by_name 
                  FROM invoices i 
                  LEFT JOIN customers c ON i.customer_id = c.id
                  JOIN stores s ON i.store_id = s.id
                  JOIN businesses b ON i.business_id = b.id
                  LEFT JOIN users u ON i.created_by = u.id
                  WHERE i.id = :id AND i.business_id = :bid";
        
        $params = [':id' => $id, ':bid' => $bid];
        if ($sid !== 'all') {
            $query .= " AND i.store_id = :sid";
            $params[':sid'] = $sid;
        }

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $invoice = $stmt->fetch();

        if (!$invoice) $this->redirect('/invoices');

        $itemStmt = $db->prepare("SELECT * FROM invoice_items WHERE invoice_id = ?");
        $itemStmt->execute([$invoice['id']]);
        $items = $itemStmt->fetchAll();

        $this->view('invoices/show', ['invoice' => $invoice, 'items' => $items]);
    }
}
