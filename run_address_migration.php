<?php
require_once 'config.php';
require_once 'includes/functions.php';

if (!isAdminLoggedIn()) {
    die('Unauthorized access');
}

try {
    // Tambahkan kolom customer_address jika belum ada
    $db->query("ALTER TABLE orders ADD COLUMN IF NOT EXISTS customer_address LONGTEXT");
    
    // Tambahkan kolom notes jika belum ada
    $db->query("ALTER TABLE orders ADD COLUMN IF NOT EXISTS notes LONGTEXT");
    
    echo "✓ Migration berhasil! Kolom customer_address dan notes sudah ditambahkan ke tabel orders.";
    echo "<br><a href='admin/orders.php'>Kembali ke daftar pesanan</a>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    echo "<br><a href='admin/orders.php'>Kembali</a>";
}
?>
