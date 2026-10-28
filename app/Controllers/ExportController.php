<?php

namespace app\Controllers;

use core\Controller;
use core\Database;
use PDO;
use Shuchkin\SimpleXLSXGen;

require_once BASE_PATH . 'app/Helpers/SimpleXLSXGen.php';

class ExportController extends Controller {

    private function dispatchExport($filename, $headers, $dataRows) {
        $format = $_GET['format'] ?? 'excel';
        
        if ($format === 'csv') {
            $this->outputCsv($filename, $headers, $dataRows);
        } elseif ($format === 'pdf') {
            $this->outputPdf($filename, $headers, $dataRows);
        } else {
            $this->outputExcel($filename, $headers, $dataRows);
        }
    }

    private function outputCsv($filename, $headers, $dataRows) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        $output = fopen('php://output', 'w');
        fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM
        fputcsv($output, $headers);
        foreach ($dataRows as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }

    private function outputPdf($filename, $headers, $dataRows) {
        require_once BASE_PATH . 'app/Helpers/fpdf/fpdf.php';
        $pdf = new \FPDF('L'); // Landscape for tables
        $pdf->AddPage();
        
        // Logo could be added here if exists, mock logo text for now
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Cell(0, 10, 'INVENTO RETAIL', 0, 1, 'C');
        
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, str_replace('_', ' ', $filename), 0, 1, 'C');
        
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(0, 10, 'Generated on: ' . date('Y-m-d H:i:s'), 0, 1, 'C');
        
        // Output filters applied
        $filters = [];
        if (!empty($_SESSION['current_store_id']) && $_SESSION['current_store_id'] !== 'all') {
            $filters[] = "Store ID: " . $_SESSION['current_store_id'];
        } else {
            $filters[] = "Store: All";
        }
        if (!empty($_GET['start_date'])) $filters[] = "From: " . $_GET['start_date'];
        if (!empty($_GET['end_date'])) $filters[] = "To: " . $_GET['end_date'];
        
        $pdf->Cell(0, 10, 'Filters: ' . implode(' | ', $filters), 0, 1, 'C');
        $pdf->Ln(5);
        
        $pdf->SetFont('Arial', 'B', 9);
        $colCount = count($headers);
        $pageWidth = $pdf->GetPageWidth() - 20; // 10 margins
        $colW = $pageWidth / ($colCount > 0 ? $colCount : 1);
        
        foreach ($headers as $h) {
            $pdf->Cell($colW, 8, substr((string)$h, 0, (int)($colW/2)), 1, 0, 'C');
        }
        $pdf->Ln();
        
        $pdf->SetFont('Arial', '', 8);
        foreach ($dataRows as $row) {
            foreach ($row as $val) {
                // Strip tags if any, clean up output
                $val = strip_tags((string)$val);
                $pdf->Cell($colW, 8, substr($val, 0, (int)($colW/1.8)), 1, 0, 'L');
            }
            $pdf->Ln();
        }
        
        $pdf->Output('D', $filename . '.pdf');
        exit;
    }

    private function outputExcel($filename, $headers, $dataRows) {
        // Format headers with bold
        $boldHeaders = array_map(function($h) { return '<b>' . $h . '</b>'; }, $headers);
        
        $data = [];
        $data[] = $boldHeaders;
        foreach ($dataRows as $row) {
            $data[] = $row;
        }

        $xlsx = SimpleXLSXGen::fromArray($data);
        
        $xlsx->downloadAs($filename . '.xlsx');
        exit;
    }

    private function columnLetter($c) {
        $c = intval($c);
        if ($c <= 0) return 'A';
        $letter = '';
        while($c != 0) {
            $p = ($c - 1) % 26;
            $c = intval(($c - $p) / 26);
            $letter = chr(65 + $p) . $letter;
        }
        return $letter;
    }

    private function applyDateFilter(&$query, &$params, $dateColumn) {
        $start_date = $_GET['start_date'] ?? null;
        $end_date = $_GET['end_date'] ?? null;
        
        if (!empty($start_date)) {
            $query .= " AND $dateColumn >= :start_date";
            $params[':start_date'] = $start_date . ' 00:00:00';
        }
        if (!empty($end_date)) {
            $query .= " AND $dateColumn <= :end_date";
            $params[':end_date'] = $end_date . ' 23:59:59';
        }
    }

    public function products() {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT p.name, p.sku, c.name as category, p.purchase_price, p.selling_price, p.gst_percent, p.status 
                              FROM products p LEFT JOIN categories c ON p.category_id = c.id 
                              WHERE p.business_id = ?");
        $stmt->execute([$_SESSION['business_id']]);
        
        $this->dispatchExport('Products_Export_' . date('Y-m-d'), 
            ['Product Name', 'SKU', 'Category', 'Purchase Price', 'Selling Price', 'GST', 'Status'], 
            $stmt->fetchAll(PDO::FETCH_NUM)
        );
    }

    public function inventory() {
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];
        $sid = $_SESSION['current_store_id'];

        $query = "SELECT p.name, p.sku, c.name as category, s.store_name, SUM(b.remaining_quantity) as qty, 
                         p.purchase_price, p.selling_price, (SUM(b.remaining_quantity) * p.purchase_price) as stock_val, 
                         sup.name as supplier, MAX(b.updated_at) as last_updated
                  FROM inventory_batches b 
                  JOIN products p ON b.product_id = p.id 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  JOIN stores s ON b.store_id = s.id 
                  LEFT JOIN suppliers sup ON b.supplier_id = sup.id
                  WHERE b.business_id = :bid";
        
        $params = [':bid' => $bid];
        if ($sid !== 'all') {
            $query .= " AND b.store_id = :sid";
            $params[':sid'] = $sid;
        }
        $this->applyDateFilter($query, $params, 'b.created_at');
        $query .= " GROUP BY p.id, b.store_id";

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        
        $this->dispatchExport('Inventory_Export_' . date('Y-m-d'), 
            ['Product Name', 'SKU', 'Category', 'Store', 'Current Stock', 'Purchase Price', 'Selling Price', 'Stock Value', 'Supplier', 'Last Updated'], 
            $stmt->fetchAll(PDO::FETCH_NUM)
        );
    }

    public function customers() {
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];
        $sid = $_SESSION['current_store_id'];
        
        $query = "SELECT c.name, c.phone, s.store_name,
                         (SELECT COUNT(*) FROM invoices i WHERE i.customer_id = c.id) as total_purchases,
                         (SELECT COUNT(*) FROM invoices i WHERE i.customer_id = c.id) as total_bills,
                         (SELECT (SUM(CASE WHEN t.transaction_type='credit_sale' THEN t.amount ELSE 0 END) - SUM(CASE WHEN t.transaction_type='payment' THEN t.amount ELSE 0 END)) FROM credit_transactions t WHERE t.customer_id = c.id) as outstanding,
                         (SELECT invoice_date FROM invoices i2 WHERE i2.customer_id = c.id ORDER BY id DESC LIMIT 1) as last_date
                  FROM customers c 
                  LEFT JOIN stores s ON c.store_id = s.id
                  WHERE c.business_id = :bid";
                  
        $params = [':bid' => $bid];
        if ($sid !== 'all') {
            $query .= " AND c.store_id = :sid";
            $params[':sid'] = $sid;
        }
        $this->applyDateFilter($query, $params, 'c.created_at');

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        
        $this->dispatchExport('Customers_Export_' . date('Y-m-d'), 
            ['Customer Name', 'Phone Number', 'Store', 'Total Purchases', 'Total Bills', 'Outstanding Credit', 'Last Purchase Date'], 
            $stmt->fetchAll(PDO::FETCH_NUM)
        );
    }

    public function billing() {
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];
        $sid = $_SESSION['current_store_id'];

        $query = "SELECT i.invoice_number, i.invoice_date, i.invoice_time, s.store_name, 
                         c.name as customer, c.phone as customer_phone, u.name as staff_name, 
                         (SELECT COUNT(*) FROM invoice_items ii WHERE ii.invoice_id = i.id) as items_count,
                         i.subtotal, i.total_discount, i.total_tax, i.grand_total, i.payment_method, i.payment_status 
                  FROM invoices i 
                  JOIN stores s ON i.store_id = s.id 
                  LEFT JOIN customers c ON i.customer_id = c.id 
                  LEFT JOIN users u ON i.created_by = u.id 
                  WHERE i.business_id = :bid";
        
        $params = [':bid' => $bid];
        if ($sid !== 'all') {
            $query .= " AND i.store_id = :sid";
            $params[':sid'] = $sid;
        }
        $this->applyDateFilter($query, $params, 'i.created_at');
        $query .= " ORDER BY i.created_at DESC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        
        $this->dispatchExport('Sales_Export_' . date('Y-m-d'), 
            ['Invoice No', 'Date', 'Time', 'Store', 'Customer Name', 'Customer Phone', 'Staff Name', 'Items Count', 'Subtotal', 'Discount', 'Tax', 'Final Amount', 'Payment Method', 'Invoice Status'], 
            $stmt->fetchAll(PDO::FETCH_NUM)
        );
    }

    public function deadStock() {
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];
        $sid = $_SESSION['current_store_id'];
        
        $query = "SELECT p.name, c.name as category, s.store_name, b.remaining_quantity, b.purchase_price, b.purchase_date, DATEDIFF(CURDATE(), b.purchase_date) as days_old, (b.remaining_quantity * b.purchase_price) as val
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
        $stmt = $db->prepare($query);
        $stmt->execute($params);

        $this->dispatchExport('DeadStock_Export_' . date('Y-m-d'), 
            ['Product', 'Category', 'Store', 'Current Quantity', 'Purchase Value', 'Purchase Date', 'Days Unsold', 'Dead Stock Value'], 
            $stmt->fetchAll(PDO::FETCH_NUM)
        );
    }

    public function credit() {
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];
        $sid = $_SESSION['current_store_id'];
        
        $query = "SELECT c.name, c.phone, s.store_name,
                         (SUM(CASE WHEN t.transaction_type = 'credit_sale' THEN t.amount ELSE 0 END) - SUM(CASE WHEN t.transaction_type = 'payment' THEN t.amount ELSE 0 END)) as outstanding,
                         SUM(CASE WHEN t.transaction_type = 'credit_sale' THEN t.amount ELSE 0 END) as total_credit,
                         SUM(CASE WHEN t.transaction_type = 'payment' THEN t.amount ELSE 0 END) as total_paid,
                         (SELECT created_at FROM reminder_logs r WHERE r.customer_id = c.id ORDER BY created_at DESC LIMIT 1) as last_reminder,
                         CASE WHEN (SUM(CASE WHEN t.transaction_type = 'credit_sale' THEN t.amount ELSE 0 END) - SUM(CASE WHEN t.transaction_type = 'payment' THEN t.amount ELSE 0 END)) > 0 THEN 'Pending' ELSE 'Cleared' END as status
                  FROM customers c
                  JOIN credit_transactions t ON c.id = t.customer_id
                  LEFT JOIN stores s ON c.store_id = s.id
                  WHERE c.business_id = :bid";
                  
        $params = [':bid' => $bid];
        if ($sid !== 'all') {
            $query .= " AND c.store_id = :sid";
            $params[':sid'] = $sid;
        }
        $this->applyDateFilter($query, $params, 't.created_at');
        
        $query .= " GROUP BY c.id HAVING outstanding > 0";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        
        $this->dispatchExport('Credit_Export_' . date('Y-m-d'), 
            ['Customer Name', 'Phone', 'Store', 'Outstanding Amount', 'Total Credit Given', 'Total Credit Collected', 'Last Reminder Date', 'Status'], 
            $stmt->fetchAll(PDO::FETCH_NUM)
        );
    }

    public function billingDetailed() {
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];
        $sid = $_SESSION['current_store_id'];

        $query = "SELECT i.invoice_number, i.invoice_date, s.store_name, 
                         ii.product_name_snapshot, ii.category_name_snapshot, ii.quantity, ii.rate, ii.tax_amount, ii.line_total
                  FROM invoice_items ii
                  JOIN invoices i ON ii.invoice_id = i.id 
                  JOIN stores s ON i.store_id = s.id 
                  WHERE i.business_id = :bid";
        
        $params = [':bid' => $bid];
        if ($sid !== 'all') {
            $query .= " AND i.store_id = :sid";
            $params[':sid'] = $sid;
        }
        $this->applyDateFilter($query, $params, 'i.created_at');
        $query .= " ORDER BY i.created_at DESC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        
        $this->dispatchExport('Bill_Items_Export_' . date('Y-m-d'), 
            ['Invoice No', 'Date', 'Store', 'Product Name', 'Category', 'Quantity', 'Unit Price', 'Tax', 'Total Amount'], 
            $stmt->fetchAll(PDO::FETCH_NUM)
        );
    }

    public function salesReport() {
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];
        $sid = $_SESSION['current_store_id'];

        $query = "SELECT i.invoice_date, s.store_name, COUNT(i.id) as total_invoices, SUM(i.grand_total) as total_sales, SUM(i.paid_amount) as total_collected, SUM(i.due_amount) as total_credit
                  FROM invoices i 
                  JOIN stores s ON i.store_id = s.id 
                  WHERE i.business_id = :bid";
        
        $params = [':bid' => $bid];
        if ($sid !== 'all') {
            $query .= " AND i.store_id = :sid";
            $params[':sid'] = $sid;
        }
        $this->applyDateFilter($query, $params, 'i.created_at');
        $query .= " GROUP BY i.invoice_date, i.store_id ORDER BY i.invoice_date DESC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        
        $this->dispatchExport('Sales_Report_Export_' . date('Y-m-d'), 
            ['Date', 'Store', 'Total Invoices', 'Total Sales', 'Total Collected', 'Total Credit Given'], 
            $stmt->fetchAll(PDO::FETCH_NUM)
        );
    }

    public function staff() {
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];
        $sid = $_SESSION['current_store_id'];
        
        $query = "SELECT u.name, s.store_name, u.role,
                         (SELECT COUNT(*) FROM invoices i WHERE i.created_by = u.id) as bills_generated,
                         (SELECT COALESCE(SUM(grand_total), 0) FROM invoices i WHERE i.created_by = u.id) as revenue_generated,
                         DATE(u.created_at) as joining_date, u.status
                  FROM users u
                  LEFT JOIN stores s ON u.store_id = s.id
                  WHERE u.business_id = :bid AND u.role != 'owner'";
                  
        $params = [':bid' => $bid];
        if ($sid !== 'all') {
            $query .= " AND u.store_id = :sid";
            $params[':sid'] = $sid;
        }
        $this->applyDateFilter($query, $params, 'u.created_at');

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        
        $this->dispatchExport('Staff_Export_' . date('Y-m-d'), 
            ['Staff Name', 'Store', 'Role', 'Bills Generated', 'Revenue Generated', 'Joining Date', 'Status'], 
            $stmt->fetchAll(PDO::FETCH_NUM)
        );
    }

    public function stores() {
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];
        
        $query = "SELECT s.store_name, b.business_name, s.address, s.phone,
                         (SELECT COALESCE(SUM(grand_total), 0) FROM invoices i WHERE i.store_id = s.id) as total_sales,
                         (SELECT COUNT(*) FROM invoices i WHERE i.store_id = s.id) as total_bills,
                         s.status
                  FROM stores s
                  JOIN businesses b ON s.business_id = b.id
                  WHERE s.business_id = :bid";
                  
        $params = [':bid' => $bid];
        $this->applyDateFilter($query, $params, 's.created_at');

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        
        $this->outputExcel('Stores_Export_' . date('Y-m-d'), 
            ['Store Name', 'Business Name', 'Address', 'Contact', 'Total Sales', 'Total Bills', 'Status'], 
            $stmt->fetchAll(PDO::FETCH_NUM)
        );
    }
}
