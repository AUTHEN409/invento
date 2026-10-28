<?php

namespace app\Controllers;

use core\Controller;
use core\Database;
use PDO;

class DashboardController extends Controller {

    public function index() {
        $db = Database::getInstance();
        
        $business_id = $_SESSION['business_id'];
        $store_id = $_SESSION['current_store_id'];

        // Get Business info
        $bStmt = $db->prepare("SELECT business_name FROM businesses WHERE id = ?");
        $bStmt->execute([$business_id]);
        $business = $bStmt->fetch();

        // Get Available Stores for the user based on role
        if ($_SESSION['role'] === 'owner') {
            $sStmt = $db->prepare("SELECT id, store_name FROM stores WHERE business_id = ?");
            $sStmt->execute([$business_id]);
        } else {
            // Manager / Cashier can only see their assigned store
            $sStmt = $db->prepare("SELECT id, store_name FROM stores WHERE business_id = ? AND id = ?");
            $sStmt->execute([$business_id, $_SESSION['current_store_id']]);
        }
        $stores = $sStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch basic stats (Total Invoices today, Total Sales) 
        // using proper isolation by business_id AND store_id
        $salesQuery = "SELECT COUNT(id) as total_invoices, SUM(grand_total) as total_sales FROM invoices WHERE business_id = :bid AND DATE(invoice_date) = CURDATE()";
        $params = [':bid' => $business_id];
        
        if ($store_id !== 'all') {
            $salesQuery .= " AND store_id = :sid";
            $params[':sid'] = $store_id;
        }

        $statsStmt = $db->prepare($salesQuery);
        $statsStmt->execute($params);
        $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

        // Current Stock Value
        $stockQuery = "SELECT COALESCE(SUM(remaining_quantity * purchase_price), 0) FROM inventory_batches WHERE business_id = :bid AND remaining_quantity > 0";
        $stockParams = [':bid' => $business_id];
        if ($store_id !== 'all') {
            $stockQuery .= " AND store_id = :sid";
            $stockParams[':sid'] = $store_id;
        }
        $stockStmt = $db->prepare($stockQuery);
        $stockStmt->execute($stockParams);
        $stats['stock_value'] = $stockStmt->fetchColumn();

        // Dead Stock Value (Older than 90 days)
        $deadQuery = "SELECT COALESCE(SUM(remaining_quantity * purchase_price), 0) FROM inventory_batches WHERE business_id = :bid AND remaining_quantity > 0 AND DATEDIFF(CURDATE(), purchase_date) >= 90";
        if ($store_id !== 'all') {
            $deadQuery .= " AND store_id = :sid";
        }
        $deadStmt = $db->prepare($deadQuery);
        $deadStmt->execute($stockParams);
        $stats['dead_stock_value'] = $deadStmt->fetchColumn();

        // Total Products
        $prodQuery = "SELECT COUNT(*) FROM products WHERE business_id = ?";
        $prodStmt = $db->prepare($prodQuery);
        $prodStmt->execute([$business_id]);
        $stats['total_products'] = $prodStmt->fetchColumn();

        // Outstanding Credit
        $creditQuery = "SELECT COALESCE((SUM(CASE WHEN transaction_type='credit_sale' THEN amount ELSE 0 END) - SUM(CASE WHEN transaction_type='payment' THEN amount ELSE 0 END)), 0) FROM credit_transactions WHERE business_id = ?";
        $creditStmt = $db->prepare($creditQuery);
        $creditStmt->execute([$business_id]);
        $stats['outstanding_credit'] = $creditStmt->fetchColumn();

        $this->view('dashboard/index', [
            'business' => $business,
            'stores' => $stores,
            'stats' => $stats
        ]);
    }

    public function switchStore($id) {
        // Only owners can switch stores arbitrarily
        if ($_SESSION['role'] !== 'owner') {
            die("Unauthorized to switch stores.");
        }

        if ($id === 'all') {
            $_SESSION['current_store_id'] = 'all';
            $this->redirect('/dashboard');
        }

        // Validate the store belongs to the business
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id FROM stores WHERE id = ? AND business_id = ?");
        $stmt->execute([$id, $_SESSION['business_id']]);
        
        if ($stmt->fetch()) {
            $_SESSION['current_store_id'] = $id;
        }

        $this->redirect('/dashboard');
    }
}
