<?php
// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'florist_db');

// Konfigurasi Website
define('SITE_NAME', 'Florist & Kerajinan Bulu Kawat');
define('SITE_URL', 'http://localhost/florist');
define('ADMIN_EMAIL', 'admin@florist.com');

// Konfigurasi Upload
define('UPLOAD_PATH', 'assets/img/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Start session
session_start();

// Error reporting (matikan di production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
