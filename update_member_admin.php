<?php
require_once 'config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Update email dan password member admin
    $hashed_password = password_hash('admin1234', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE members SET email = ?, password = ? WHERE email = 'admin@florist.com' OR email = 'admin@gmail.com'");
    $stmt->execute(['admin@gmail.com', $hashed_password]);
    
    echo "Member admin berhasil diupdate!<br>";
    echo "Email: admin@gmail.com<br>";
    echo "Password: admin1234<br>";
    echo "Hash: " . $hashed_password . "<br>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
