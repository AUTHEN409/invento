<?php

namespace app\Controllers;

use core\Controller;
use core\Database;
use PDO;

class SettingsController extends Controller {

    public function index() {
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];
        
        $stmt = $db->prepare("SELECT * FROM businesses WHERE id = ?");
        $stmt->execute([$bid]);
        $business = $stmt->fetch();

        $this->view('settings/index', ['business' => $business, 'csrf_token' => $this->generateCsrfToken()]);
    }

    public function update() {
        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');
        $db = Database::getInstance();
        $bid = $_SESSION['business_id'];

        $business_name = trim($_POST['business_name']);
        $owner_name = trim($_POST['owner_name']);
        $phone = trim($_POST['phone']);
        $email = trim($_POST['email']);
        $gstin = trim($_POST['gstin']);
        $address = trim($_POST['address']);

        $stmt = $db->prepare("UPDATE businesses SET business_name=?, owner_name=?, phone=?, email=?, gstin=?, address=? WHERE id=?");
        $stmt->execute([$business_name, $owner_name, $phone, $email, $gstin, $address, $bid]);

        $this->redirect('/settings');
    }
}
