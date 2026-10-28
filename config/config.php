<?php

// Configuration settings

define('DB_HOST', getenv('DB_HOST') ?: 'sql308.infinityfree.com');
define('DB_NAME', getenv('DB_NAME') ?: 'if0_43072457_auth');
define('DB_USER', getenv('DB_USER') ?: 'if0_43072457');
define('DB_PASS', getenv('DB_PASS') ?: 'tW0mRZpHsPc3UUD');

// Dynamic URL Generation (Domain Independent & Proxy Aware)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? "https" : "http";
if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
    $protocol = "https";
}
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptPath = dirname($_SERVER['SCRIPT_NAME'] ?? '');
$scriptPath = str_replace('\\', '/', $scriptPath);
if ($scriptPath === '/' || $scriptPath === '.')
    $scriptPath = '';

// Allow override via ENV, but default to dynamic
define('APP_URL', getenv('APP_URL') ?: ($protocol . '://' . $host . $scriptPath));
// Security / Salts
define('APP_SECRET', getenv('APP_SECRET') ?: 'SuperSecretKeyForInventoPart1!2024');

// Paths
define('VIEWS_PATH', BASE_PATH . 'app/Views/');
