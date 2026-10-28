<?php
/**
 * Invento - Retail Management SaaS
 * Front Controller (Root)
 */

// Define absolute path to project root
define('BASE_PATH', __DIR__ . '/');

// Display errors for development (disable in production)
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Start Session
session_start();

// Prevent aggressive caching on shared hosts (fixes CSRF token mismatch)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Autoloader function
spl_autoload_register(function ($class) {
    // Convert namespace to full file path
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = BASE_PATH . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Load Config
require_once BASE_PATH . 'config/config.php';

// Initialize Router
$router = new \core\Router();

// Load Routes
require_once BASE_PATH . 'app/routes.php';

// Dispatch Request
$url = isset($_GET['url']) ? $_GET['url'] : '/';
$router->dispatch($url);
