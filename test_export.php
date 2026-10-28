<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('BASE_PATH', __DIR__ . '/');
require_once BASE_PATH . 'config/config.php';

spl_autoload_register(function ($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = BASE_PATH . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Mock Session
session_start();
$_SESSION['business_id'] = 1;
$_SESSION['current_store_id'] = 'all';

try {
    $export = new \app\Controllers\ExportController();
    $export->products();
} catch (Throwable $e) {
    echo "Fatal Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine();
}
