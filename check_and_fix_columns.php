<?php
require_once 'config.php';
require_once 'includes/db.php';

try {
    global $db;
    
    // Coba tambahkan kolom customer_address
    try {
        $db->query("ALTER TABLE orders ADD COLUMN customer_address LONGTEXT");
        echo "✓ Kolom customer_address berhasil ditambahkan<br>";
    } catch (Exception $e) {
        // Kolom mungkin sudah ada
        echo "• Kolom customer_address sudah ada atau error: " . $e->getMessage() . "<br>";
    }
    
    // Coba tambahkan kolom notes
    try {
        $db->query("ALTER TABLE orders ADD COLUMN notes LONGTEXT");
        echo "✓ Kolom notes berhasil ditambahkan<br>";
    } catch (Exception $e) {
        echo "• Kolom notes sudah ada atau error: " . $e->getMessage() . "<br>";
    }
    
    // Verifikasi kolom
    $result = $db->fetchAll("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'orders' AND TABLE_SCHEMA = 'florist_db' ORDER BY COLUMN_NAME");
    
    echo "<br><strong>Kolom yang ada di tabel orders:</strong><br>";
    foreach ($result as $row) {
        echo "- " . $row['COLUMN_NAME'] . "<br>";
    }
    
    echo "<br><a href='products.php'>Kembali ke halaman utama</a>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    echo "<br><a href='products.php'>Kembali</a>";
}
?>
