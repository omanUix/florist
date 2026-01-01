<?php
require_once 'config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Update password admin
    $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE admin SET password = ? WHERE username = 'admin'");
    $stmt->execute([$hashed_password]);
    
    echo "Password admin berhasil diupdate!<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
    echo "Hash: " . $hashed_password . "<br>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
