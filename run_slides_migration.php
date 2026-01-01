<?php
/**
 * Script untuk menjalankan migrasi tabel slides
 * Jalankan script ini sekali untuk membuat tabel slides di database
 */

require_once 'config.php';
require_once 'includes/db.php';

try {
    // Baca file SQL
    $sql = file_get_contents('slides_migration.sql');
    
    // Eksekusi query
    $db->query($sql);
    
    echo "✓ Migrasi tabel slides berhasil!\n";
    echo "Tabel 'slides' telah dibuat di database.\n";
    echo "\nAnda sekarang dapat:\n";
    echo "1. Login ke admin panel\n";
    echo "2. Buka menu 'Kelola Slides'\n";
    echo "3. Tambahkan slides baru untuk ditampilkan di homepage\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "\nKemungkinan tabel sudah ada. Jika ingin membuat ulang, hapus tabel 'slides' terlebih dahulu.\n";
}
?>

