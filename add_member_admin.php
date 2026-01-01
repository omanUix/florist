<?php
require_once 'config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Tambahkan member admin jika belum ada
    $email = 'admin@gmail.com';
    $password = 'admin1234';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $name = 'Admin';
    $phone = '081234567890';
    $address = 'Alamat Admin';
    $status = 'active';

    $stmt = $pdo->prepare("SELECT * FROM members WHERE email = ?");
    $stmt->execute([$email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        $stmt = $pdo->prepare("INSERT INTO members (name, email, phone, password, address, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $hashed_password, $address, $status]);
        echo "Member admin berhasil ditambahkan!<br>Email: $email<br>Password: $password<br>";
    } else {
        echo "Member admin sudah ada.<br>Email: $email<br>";
    }
    
    // Tampilkan data member admin
    $stmt = $pdo->prepare("SELECT * FROM members WHERE email = ?");
    $stmt->execute([$email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo '<pre>';
    var_export($row);
    echo '</pre>';
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
