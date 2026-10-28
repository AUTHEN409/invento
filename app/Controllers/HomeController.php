<?php

namespace app\Controllers;

use core\Controller;

class HomeController extends Controller {
    public function index() {
        if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'business') {
            $this->redirect('/dashboard');
        } elseif (isset($_SESSION['admin_id']) && $_SESSION['user_type'] === 'admin') {
            $this->redirect('/admin');
        } else {
            $this->redirect('/login');
        }
    }
}
