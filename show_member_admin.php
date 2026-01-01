<?php
require_once 'config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("SELECT * FROM members WHERE email = ?");
    $stmt->execute(['admin@gmail.com']);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo '<pre>';
    var_export($row);
    echo '</pre>';
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
