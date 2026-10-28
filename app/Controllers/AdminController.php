<?php

namespace app\Controllers;

use core\Controller;
use core\Database;
use PDO;

class AdminController extends Controller {

    public function dashboard() {
        $db = Database::getInstance();

        $stats = [
            'total' => $db->query("SELECT COUNT(*) FROM businesses")->fetchColumn(),
            'active' => $db->query("SELECT COUNT(*) FROM businesses WHERE status = 'active'")->fetchColumn(),
            'trial' => $db->query("SELECT COUNT(*) FROM businesses WHERE status = 'trial'")->fetchColumn(),
            'expired' => $db->query("SELECT COUNT(*) FROM businesses WHERE status = 'expired'")->fetchColumn(),
            'suspended' => $db->query("SELECT COUNT(*) FROM businesses WHERE status = 'suspended'")->fetchColumn()
        ];

        $recent_businesses = $db->query("SELECT * FROM businesses ORDER BY created_at DESC LIMIT 5")->fetchAll();
        $recent_activity = $db->query("SELECT a.*, b.business_name FROM audit_logs a LEFT JOIN businesses b ON a.business_id = b.id ORDER BY a.id DESC LIMIT 6")->fetchAll();
        $expiring_subs = $db->query("SELECT s.*, b.business_name FROM subscriptions s JOIN businesses b ON s.business_id = b.id WHERE s.status = 'active' AND s.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 14 DAY) ORDER BY s.expiry_date ASC LIMIT 5")->fetchAll();

        $this->view('admin/dashboard', [
            'stats' => $stats,
            'recent_businesses' => $recent_businesses,
            'recent_activity' => $recent_activity,
            'expiring_subs' => $expiring_subs
        ]);
    }

    public function businesses() {
        $db = Database::getInstance();
        
        $q = $_GET['q'] ?? '';
        $status = $_GET['status'] ?? 'all';
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $stats = [
            'total' => $db->query("SELECT COUNT(*) FROM businesses")->fetchColumn(),
            'active' => $db->query("SELECT COUNT(*) FROM businesses WHERE status = 'active'")->fetchColumn(),
            'trial' => $db->query("SELECT COUNT(*) FROM businesses WHERE status = 'trial'")->fetchColumn(),
            'expired' => $db->query("SELECT COUNT(*) FROM businesses WHERE status = 'expired'")->fetchColumn(),
            'suspended' => $db->query("SELECT COUNT(*) FROM businesses WHERE status = 'suspended'")->fetchColumn()
        ];

        $where = ["1=1"];
        $params = [];

        if ($status !== 'all') {
            $where[] = "b.status = :status";
            $params[':status'] = $status;
        }

        if (!empty($q)) {
            $where[] = "(b.business_name LIKE :q1 OR b.phone LIKE :q2 OR b.id = :idq)";
            $params[':q1'] = "%$q%";
            $params[':q2'] = "%$q%";
            $params[':idq'] = $q;
        }

        $whereClause = implode(' AND ', $where);

        $countStmt = $db->prepare("SELECT COUNT(*) FROM businesses b WHERE $whereClause");
        $countStmt->execute($params);
        $totalRows = $countStmt->fetchColumn();
        $totalPages = ceil($totalRows / $limit);

        $orderClause = "CASE b.status 
            WHEN 'active' THEN 1 
            WHEN 'trial' THEN 2 
            WHEN 'expired' THEN 3 
            WHEN 'suspended' THEN 4 
            ELSE 5 END ASC, b.created_at DESC";

        $stmt = $db->prepare("SELECT b.*, p.name as plan_name, 
                             (SELECT expiry_date FROM subscriptions s WHERE s.business_id = b.id ORDER BY id DESC LIMIT 1) as expiry_date
                             FROM businesses b 
                             LEFT JOIN plans p ON b.plan_id = p.id 
                             WHERE $whereClause 
                             ORDER BY $orderClause 
                             LIMIT $limit OFFSET $offset");
        $stmt->execute($params);
        $businesses = $stmt->fetchAll();

        $this->view('admin/businesses', [
            'businesses' => $businesses, 
            'plans' => $db->query("SELECT * FROM plans")->fetchAll(),
            'stats' => $stats,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'status_filter' => $status,
            'search_query' => $q,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    public function deleteBusiness($id) {
        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM businesses WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['temp_msg'] = "Business deleted permanently.";
        $this->redirect('/admin/businesses');
    }

    public function bulkAction() {
        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
        $action = $_POST['action'] ?? '';
        $ids = $_POST['business_ids'] ?? [];

        if (empty($ids) || !is_array($ids)) {
            $this->redirect('/admin/businesses');
        }

        $db = Database::getInstance();
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        if ($action === 'suspend') {
            $stmt = $db->prepare("UPDATE businesses SET status = 'suspended' WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            $_SESSION['temp_msg'] = count($ids) . " businesses suspended.";
        } elseif ($action === 'activate') {
            $stmt = $db->prepare("UPDATE businesses SET status = 'active' WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            $_SESSION['temp_msg'] = count($ids) . " businesses activated.";
        } elseif ($action === 'delete') {
            $stmt = $db->prepare("DELETE FROM businesses WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            $_SESSION['temp_msg'] = count($ids) . " businesses deleted.";
        }

        $this->redirect('/admin/businesses');
    }

    public function storeBusiness() {
        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
        $db = Database::getInstance();
        
        $business_name = trim($_POST['business_name']);
        $owner_name = trim($_POST['owner_name']);
        $phone = trim($_POST['phone']);
        $email = trim($_POST['email']);
        $plan_id = $_POST['plan_id'];

        if (empty($business_name) || empty($owner_name) || empty($phone)) {
            die("Required fields missing");
        }

        try {
            $db->beginTransaction();

            $stmt = $db->prepare("INSERT INTO businesses (plan_id, business_name, owner_name, phone, email, status) VALUES (?, ?, ?, ?, ?, 'active')");
            $stmt->execute([$plan_id, $business_name, $owner_name, $phone, $email]);
            $business_id = $db->lastInsertId();

            // Auto-create an owner user account for this business
            $username = !empty($email) ? $email : $phone;
            $default_password = 'password123';
            $password_hash = password_hash($default_password, PASSWORD_DEFAULT);

            $userStmt = $db->prepare("INSERT INTO users (business_id, store_id, name, username, password_hash, role, status) VALUES (?, NULL, ?, ?, ?, 'owner', 'active')");
            $userStmt->execute([$business_id, $owner_name, $username, $password_hash]);

            $db->commit();
            
            // Redirect with a success message (in a real app we'd use session flash messages)
            $_SESSION['temp_msg'] = "Business created! Owner Login -> Username: $username | Password: $default_password";
            
            $this->redirect('/admin/business/' . $business_id);
        } catch (\Exception $e) {
            $db->rollBack();
            die("Failed to create business: " . $e->getMessage());
        }
    }

    public function showBusiness($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT b.*, p.name as plan_name FROM businesses b LEFT JOIN plans p ON b.plan_id = p.id WHERE b.id = ?");
        $stmt->execute([$id]);
        $business = $stmt->fetch();

        if (!$business) $this->redirect('/admin/businesses');

        // Get Subscriptions
        $subStmt = $db->prepare("SELECT s.*, p.name as plan_name FROM subscriptions s JOIN plans p ON s.plan_id = p.id WHERE s.business_id = ? ORDER BY s.id DESC");
        $subStmt->execute([$id]);
        $subscriptions = $subStmt->fetchAll();

        // Get Plans for upgrade/downgrade
        $plans = $db->query("SELECT * FROM plans")->fetchAll();

        $this->view('admin/show_business', [
            'business' => $business, 
            'subscriptions' => $subscriptions, 
            'plans' => $plans,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    public function updatePermissions($id) {
        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
        $permissions = [
            'screenshot_management' => isset($_POST['perm_screenshot']),
            'review_management' => isset($_POST['perm_review']),
            'theme_management' => isset($_POST['perm_theme']),
            'website_cms' => isset($_POST['perm_cms'])
        ];
        $json = json_encode($permissions);

        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE businesses SET permissions = ? WHERE id = ?");
        $stmt->execute([$json, $id]);

        \App\Helpers\Logger::log('UPDATE_PERMISSIONS', "Super Admin updated permissions for business {$id}", $_SESSION['admin_id'] ?? null);

        $_SESSION['temp_msg'] = "Permissions updated successfully.";
        $this->redirect('/admin/business/' . $id);
    }

    public function updateStatus($id) {
        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
        $status = $_POST['status'];
        
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE businesses SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        
        $this->redirect('/admin/business/' . $id);
    }

    public function updateSubscription($id) {
        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
        
        $plan_id = $_POST['plan_id'];
        $expiry_date = $_POST['expiry_date'];
        $status = $_POST['status'];

        $db = Database::getInstance();
        
        // Update Business Plan ID
        $bStmt = $db->prepare("UPDATE businesses SET plan_id = ?, status = 'active' WHERE id = ?");
        $bStmt->execute([$plan_id, $id]);

        // Record Subscription
        $sStmt = $db->prepare("INSERT INTO subscriptions (business_id, plan_id, start_date, expiry_date, status, activated_by) VALUES (?, ?, CURDATE(), ?, ?, ?)");
        $sStmt->execute([$id, $plan_id, $expiry_date, $status, $_SESSION['admin_id']]);

        $this->redirect('/admin/business/' . $id);
    }

    public function updateBusiness($id) {
        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
        $db = Database::getInstance();
        
        $business_name = trim($_POST['business_name']);
        $owner_name = trim($_POST['owner_name']);
        $phone = trim($_POST['phone']);
        $email = trim($_POST['email']);
        $gstin = trim($_POST['gstin'] ?? '');

        if (empty($business_name) || empty($owner_name) || empty($phone)) {
            die("Required fields missing");
        }

        $stmt = $db->prepare("UPDATE businesses SET business_name = ?, owner_name = ?, phone = ?, email = ?, gstin = ? WHERE id = ?");
        $stmt->execute([$business_name, $owner_name, $phone, $email, $gstin, $id]);

        $this->redirect('/admin/business/' . $id);
    }

    public function subscriptions() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT s.*, b.business_name, p.name as plan_name FROM subscriptions s JOIN businesses b ON s.business_id = b.id JOIN plans p ON s.plan_id = p.id ORDER BY s.id DESC LIMIT 100");
        $this->view('admin/subscriptions', ['subscriptions' => $stmt->fetchAll()]);
    }

    public function users() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT u.*, b.business_name, s.store_name FROM users u JOIN businesses b ON u.business_id = b.id LEFT JOIN stores s ON u.store_id = s.id ORDER BY u.id DESC LIMIT 100");
        $this->view('admin/users', ['users' => $stmt->fetchAll()]);
    }

    public function stores() {
        $db = Database::getInstance();
        $q = $_GET['q'] ?? '';

        $params = [];
        $where = ["1=1"];

        if (!empty($q)) {
            $where[] = "(b.business_name LIKE :q1 OR b.phone LIKE :q2 OR s.store_name LIKE :q3 OR s.store_code LIKE :q4)";
            $params[':q1'] = "%$q%";
            $params[':q2'] = "%$q%";
            $params[':q3'] = "%$q%";
            $params[':q4'] = "%$q%";
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT b.id as business_id, b.business_name, b.owner_name, b.phone, b.status as business_status,
                       s.id as store_id, s.store_name, s.store_code, s.address, s.phone as store_phone, s.status as store_status
                FROM businesses b 
                LEFT JOIN stores s ON b.id = s.business_id 
                WHERE $whereClause 
                ORDER BY b.created_at DESC, s.created_at ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        $businesses = [];
        foreach ($rows as $row) {
            $bid = $row['business_id'];
            if (!isset($businesses[$bid])) {
                $businesses[$bid] = [
                    'id' => $bid,
                    'name' => $row['business_name'],
                    'owner' => $row['owner_name'],
                    'mobile' => $row['phone'],
                    'status' => $row['business_status'],
                    'stores' => []
                ];
            }
            if ($row['store_id']) {
                $businesses[$bid]['stores'][] = [
                    'id' => $row['store_id'],
                    'name' => $row['store_name'],
                    'code' => $row['store_code'],
                    'address' => $row['address'],
                    'phone' => $row['store_phone'],
                    'status' => $row['store_status']
                ];
            }
        }

        // Fetch all businesses for the "Add Store" dropdown just in case
        $all_businesses = $db->query("SELECT id, business_name FROM businesses ORDER BY business_name ASC")->fetchAll();

        $this->view('admin/stores', [
            'businesses' => $businesses, 
            'all_businesses' => $all_businesses,
            'search_query' => $q,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    public function storeAction() {
        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
        $action = $_POST['action'] ?? '';
        $db = Database::getInstance();

        try {
            if ($action === 'add') {
                $business_id = $_POST['business_id'];
                $store_name = trim($_POST['store_name']);
                $store_code = trim($_POST['store_code']);
                $address = trim($_POST['address']);
                $phone = trim($_POST['phone']);

                $stmt = $db->prepare("INSERT INTO stores (business_id, store_name, store_code, address, phone, status) VALUES (?, ?, ?, ?, ?, 'active')");
                $stmt->execute([$business_id, $store_name, $store_code, $address, $phone]);
                $_SESSION['temp_msg'] = "Store added successfully.";
            } 
            elseif ($action === 'edit') {
                $store_id = $_POST['store_id'];
                $store_name = trim($_POST['store_name']);
                $store_code = trim($_POST['store_code']);
                $address = trim($_POST['address']);
                $phone = trim($_POST['phone']);

                $stmt = $db->prepare("UPDATE stores SET store_name=?, store_code=?, address=?, phone=? WHERE id=?");
                $stmt->execute([$store_name, $store_code, $address, $phone, $store_id]);
                $_SESSION['temp_msg'] = "Store updated successfully.";
            }
            elseif ($action === 'suspend' || $action === 'activate') {
                $store_id = $_POST['store_id'];
                $status = ($action === 'suspend') ? 'suspended' : 'active';
                $stmt = $db->prepare("UPDATE stores SET status = ? WHERE id = ?");
                $stmt->execute([$status, $store_id]);
                $_SESSION['temp_msg'] = "Store status updated to $status.";
            }
            elseif ($action === 'delete') {
                $store_id = $_POST['store_id'];
                $stmt = $db->prepare("DELETE FROM stores WHERE id = ?");
                $stmt->execute([$store_id]);
                $_SESSION['temp_msg'] = "Store deleted permanently.";
            }
        } catch (\Exception $e) {
            $_SESSION['error_msg'] = "Action failed: " . $e->getMessage();
        }

        $this->redirect('/admin/stores');
    }

    public function reports() {
        $db = Database::getInstance();
        
        $total_businesses = $db->query("SELECT COUNT(*) FROM businesses")->fetchColumn();
        $total_stores = $db->query("SELECT COUNT(*) FROM stores")->fetchColumn();
        $total_users = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $total_revenue = $db->query("SELECT SUM(p.price) FROM subscriptions s JOIN plans p ON s.plan_id = p.id WHERE s.status = 'active'")->fetchColumn();

        $plan_breakdown = $db->query("SELECT p.name, COUNT(b.id) as count FROM plans p LEFT JOIN businesses b ON p.id = b.plan_id GROUP BY p.id")->fetchAll();

        $this->view('admin/reports', [
            'total_businesses' => $total_businesses,
            'total_stores' => $total_stores,
            'total_users' => $total_users,
            'total_revenue' => $total_revenue ?: 0,
            'plan_breakdown' => $plan_breakdown
        ]);
    }

    public function auditLogs() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT a.*, b.business_name, u.username FROM audit_logs a LEFT JOIN businesses b ON a.business_id = b.id LEFT JOIN users u ON a.user_id = u.id ORDER BY a.id DESC LIMIT 200");
        $this->view('admin/audit_logs', ['logs' => $stmt->fetchAll()]);
    }

    public function support() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT u.id as user_id, u.username, u.name, u.role, b.business_name, b.id as business_id FROM users u JOIN businesses b ON u.business_id = b.id ORDER BY b.business_name ASC, u.role ASC");
        $users = $stmt->fetchAll();
        $this->view('admin/support', ['users' => $users]);
    }

    public function impersonateUser($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT u.*, b.status as business_status FROM users u JOIN businesses b ON u.business_id = b.id WHERE u.id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user_type'] = 'business';
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['business_id'] = $user['business_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            
            // Set first store as context
            $storeStmt = $db->prepare("SELECT id, store_name FROM stores WHERE business_id = ? ORDER BY id ASC LIMIT 1");
            $storeStmt->execute([$user['business_id']]);
            if ($store = $storeStmt->fetch()) {
                $_SESSION['current_store_id'] = $store['id'];
                $_SESSION['current_store_name'] = $store['store_name'];
            }

            $_SESSION['impersonator_admin_id'] = $_SESSION['admin_id'];
            
            \App\Helpers\Logger::log('IMPERSONATE_USER', "Super Admin impersonated user ID {$id} ({$user['username']})", $_SESSION['admin_id'] ?? null);

            $this->redirect('/app');
        }
        $this->redirect('/admin/support');
    }

    public function stopImpersonation() {
        unset($_SESSION['user_type'], $_SESSION['user_id'], $_SESSION['business_id'], $_SESSION['role'], $_SESSION['name'], $_SESSION['current_store_id'], $_SESSION['current_store_name'], $_SESSION['impersonator_admin_id']);
        $this->redirect('/admin/support');
    }

    public function cms() {
        $db = Database::getInstance();
        
        // Load general CMS Content
        $contentRows = $db->query("SELECT section, content_key, content_value FROM cms_content")->fetchAll();
        $cms = [];
        foreach($contentRows as $row) {
            $cms[$row['section']][$row['content_key']] = $row['content_value'];
        }

        // Load Reviews
        $reviews = $db->query("SELECT * FROM cms_reviews ORDER BY created_at DESC")->fetchAll();

        // Load Screenshots
        $screenshots = $db->query("SELECT * FROM cms_screenshots ORDER BY display_order ASC")->fetchAll();

        // Load Plans
        $plans = $db->query("SELECT * FROM plans ORDER BY price ASC")->fetchAll();

        $this->view('admin/cms', [
            'cms' => $cms,
            'reviews' => $reviews,
            'screenshots' => $screenshots,
            'plans' => $plans
        ]);
    }

    public function updateCms() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::getInstance();
            $section = $_POST['section'] ?? '';
            $data = $_POST['cms'] ?? [];
            
            foreach ($data as $key => $value) {
                $stmt = $db->prepare("SELECT id FROM cms_content WHERE section = ? AND content_key = ?");
                $stmt->execute([$section, $key]);
                if ($stmt->fetch()) {
                    $up = $db->prepare("UPDATE cms_content SET content_value = ? WHERE section = ? AND content_key = ?");
                    $up->execute([$value, $section, $key]);
                } else {
                    $in = $db->prepare("INSERT INTO cms_content (section, content_key, content_value) VALUES (?, ?, ?)");
                    $in->execute([$section, $key, $value]);
                }
            }
            
            \App\Helpers\Logger::log('UPDATE_CMS', "Super Admin updated CMS section: {$section}", $_SESSION['admin_id'] ?? null);

            $_SESSION['success'] = "Landing page content updated successfully.";
            $this->redirect('/admin/cms');
        }
    }

    public function settings() {
        $db = Database::getInstance();
        
        // Ensure table exists safely or silently fail
        try {
            $stmt = $db->query("SELECT setting_key, setting_value FROM platform_settings");
            $rows = $stmt->fetchAll();
            $settings = [];
            foreach($rows as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (\Exception $e) {
            $settings = [];
        }

        $this->view('admin/settings', ['settings' => $settings]);
    }

    public function saveSettings() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::getInstance();
            $settings = $_POST['settings'] ?? [];
            
            foreach ($settings as $key => $value) {
                // Upsert logic (Insert or Update)
                $stmt = $db->prepare("SELECT id FROM platform_settings WHERE setting_key = ?");
                $stmt->execute([$key]);
                if ($stmt->fetch()) {
                    $up = $db->prepare("UPDATE platform_settings SET setting_value = ? WHERE setting_key = ?");
                    $up->execute([$value, $key]);
                } else {
                    $in = $db->prepare("INSERT INTO platform_settings (setting_key, setting_value) VALUES (?, ?)");
                    $in->execute([$key, $value]);
                }
            }
            
            // Uncheck checkboxes will not be sent, so we handle boolean toggles manually
            if (!isset($settings['enable_registration'])) {
                $db->query("UPDATE platform_settings SET setting_value = '0' WHERE setting_key = 'enable_registration'");
            }
            if (!isset($settings['enable_maintenance'])) {
                $db->query("UPDATE platform_settings SET setting_value = '0' WHERE setting_key = 'enable_maintenance'");
            }

            // Log action
            \App\Helpers\Logger::log('UPDATE_SETTINGS', 'Super Admin updated platform global settings', $_SESSION['admin_id'] ?? null);

            $_SESSION['success'] = "Platform settings saved successfully.";
            $this->redirect('/admin/settings');
        }
    }

    public function profile() {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM super_admins WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        $admin = $stmt->fetch();
        $this->view('admin/profile', ['admin' => $admin]);
    }
}
