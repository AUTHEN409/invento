<?php

namespace app\Controllers;

use core\Controller;
use core\Database;
use PDO;
use Exception;

class CreditController extends Controller {

    public function index() {
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];

        // Customers with outstanding balance > 0
        $query = "SELECT c.id, c.name, c.phone, 
                         SUM(CASE WHEN t.transaction_type = 'credit_sale' THEN t.amount ELSE 0 END) as total_credit,
                         SUM(CASE WHEN t.transaction_type = 'payment' THEN t.amount ELSE 0 END) as total_paid,
                         (SUM(CASE WHEN t.transaction_type = 'credit_sale' THEN t.amount ELSE 0 END) - 
                          SUM(CASE WHEN t.transaction_type = 'payment' THEN t.amount ELSE 0 END)) as outstanding_balance,
                         (SELECT created_at FROM reminder_logs r WHERE r.customer_id = c.id ORDER BY created_at DESC LIMIT 1) as last_reminder
                  FROM customers c
                  JOIN credit_transactions t ON c.id = t.customer_id
                  WHERE c.business_id = ?
                  GROUP BY c.id
                  HAVING outstanding_balance > 0
                  ORDER BY outstanding_balance DESC";
                  
        $stmt = $db->prepare($query);
        $stmt->execute([$bid]);
        $credits = $stmt->fetchAll();

        // Get Business Name for WhatsApp prefill
        $bStmt = $db->prepare("SELECT business_name FROM businesses WHERE id = ?");
        $bStmt->execute([$bid]);
        $business = $bStmt->fetch();

        $this->view('credit/index', ['credits' => $credits, 'business' => $business, 'csrf_token' => $this->generateCsrfToken()]);
    }

    public function show($customer_id) {
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];

        $stmt = $db->prepare("SELECT * FROM customers WHERE id = ? AND business_id = ?");
        $stmt->execute([$customer_id, $bid]);
        $customer = $stmt->fetch();
        if (!$customer) $this->redirect('/credit');

        $tStmt = $db->prepare("SELECT t.*, u.name as user_name, i.invoice_number 
                               FROM credit_transactions t 
                               LEFT JOIN users u ON t.created_by = u.id
                               LEFT JOIN invoices i ON t.invoice_id = i.id
                               WHERE t.customer_id = ? AND t.business_id = ?
                               ORDER BY t.created_at DESC");
        $tStmt->execute([$customer_id, $bid]);
        $ledger = $tStmt->fetchAll();

        $this->view('credit/show', ['customer' => $customer, 'ledger' => $ledger, 'csrf_token' => $this->generateCsrfToken()]);
    }

    public function pay() {
        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];
        $sid = $_SESSION['current_store_id'];
        $uid = $_SESSION['user_id'];

        if ($sid === 'all') die("Store context required to accept payments.");

        $customer_id = $_POST['customer_id'];
        $amount = (float)$_POST['amount'];
        $payment_method = $_POST['payment_method'] ?? 'cash';
        
        if ($amount <= 0) die("Invalid payment amount");

        try {
            $db->beginTransaction();

            // Record Payment
            $payStmt = $db->prepare("INSERT INTO payments (business_id, store_id, customer_id, amount, payment_method, notes, received_by) VALUES (?, ?, ?, ?, ?, 'Credit Settlement', ?)");
            $payStmt->execute([$bid, $sid, $customer_id, $amount, $payment_method, $uid]);
            $payment_id = $db->lastInsertId();

            // Record Credit Transaction
            $credStmt = $db->prepare("INSERT INTO credit_transactions (business_id, store_id, customer_id, payment_id, transaction_type, amount, created_by) VALUES (?, ?, ?, ?, 'payment', ?, ?)");
            $credStmt->execute([$bid, $sid, $customer_id, $payment_id, $amount, $uid]);

            // Adjust oldest unpaid invoices
            $invStmt = $db->prepare("SELECT id, due_amount FROM invoices WHERE customer_id = ? AND due_amount > 0 ORDER BY invoice_date ASC FOR UPDATE");
            $invStmt->execute([$customer_id]);
            $unpaid = $invStmt->fetchAll();

            $remaining_payment = $amount;
            foreach ($unpaid as $inv) {
                if ($remaining_payment <= 0) break;
                
                $due = (float)$inv['due_amount'];
                $deduct = min($due, $remaining_payment);
                $new_due = $due - $deduct;
                $status = ($new_due == 0) ? 'paid' : 'partial';

                $uInv = $db->prepare("UPDATE invoices SET due_amount = ?, paid_amount = paid_amount + ?, payment_status = ? WHERE id = ?");
                $uInv->execute([$new_due, $deduct, $status, $inv['id']]);

                $remaining_payment -= $deduct;
            }

            // Check new outstanding balance
            $balStmt = $db->prepare("
                SELECT (SUM(CASE WHEN transaction_type = 'credit_sale' THEN amount ELSE 0 END) - 
                        SUM(CASE WHEN transaction_type = 'payment' THEN amount ELSE 0 END)) as outstanding
                FROM credit_transactions WHERE customer_id = ?
            ");
            $balStmt->execute([$customer_id]);
            $new_balance = (float)$balStmt->fetchColumn();

            $cleared_param = '';
            if ($new_balance <= 0) {
                // Add 'Credit Cleared' event to transaction history
                $clearEvent = $db->prepare("INSERT INTO credit_transactions (business_id, store_id, customer_id, transaction_type, amount, created_by) VALUES (?, ?, ?, 'credit_cleared', 0, ?)");
                $clearEvent->execute([$bid, $sid, $customer_id, $uid]);

                // Generate credit-cleared WhatsApp prefilled message & Log it
                $custStmt = $db->prepare("SELECT name, phone FROM customers WHERE id = ?");
                $custStmt->execute([$customer_id]);
                $cust = $custStmt->fetch();

                $bStmt = $db->prepare("SELECT business_name FROM businesses WHERE id = ?");
                $bStmt->execute([$bid]);
                $bName = $bStmt->fetchColumn();

                $msg = "Dear {$cust['name']}, your outstanding credit balance with {$bName} is now fully cleared. Thank you for your prompt payment!";
                
                $remStmt = $db->prepare("INSERT INTO reminder_logs (business_id, store_id, customer_id, message_type, message_text, delivery_method, sent_by) VALUES (?, ?, ?, 'credit_cleared', ?, 'whatsapp_prefill', ?)");
                $remStmt->execute([$bid, $sid, $customer_id, $msg, $uid]);

                $cleared_param = '?cleared=1&msg=' . urlencode($msg) . '&phone=' . urlencode($cust['phone']);
            }

            $db->commit();
            $this->redirect('/credit/' . $customer_id . $cleared_param);
        } catch (Exception $e) {
            $db->rollBack();
            die("Payment error: " . $e->getMessage());
        }
    }

    public function logReminder() {
        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];
        $sid = $_SESSION['current_store_id'] === 'all' ? null : $_SESSION['current_store_id']; // Can be null if owner does it globally
        
        $customer_id = $_POST['customer_id'];
        $msg_text = $_POST['message_text'];
        $msg_type = $_POST['message_type'] ?? 'credit_reminder';

        $stmt = $db->prepare("INSERT INTO reminder_logs (business_id, store_id, customer_id, message_type, message_text, delivery_method, sent_by) VALUES (?, ?, ?, ?, ?, 'whatsapp_prefill', ?)");
        
        // If owner is on 'all' stores, use the first store belonging to the business as a fallback or leave it NULL if schema permits (Schema says store_id NOT NULL)
        if (!$sid) {
             $sStmt = $db->prepare("SELECT id FROM stores WHERE business_id = ? LIMIT 1");
             $sStmt->execute([$bid]);
             $sid = $sStmt->fetchColumn();
        }

        $stmt->execute([$bid, $sid, $customer_id, $msg_type, $msg_text, $_SESSION['user_id']]);
        
        // For AJAX call from WhatsApp open button
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            $this->json(['success' => true]);
        }
        $this->redirect('/credit');
    }
}
