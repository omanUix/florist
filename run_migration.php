<?php
require_once 'config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 1. Alter products table
    $pdo->exec("ALTER TABLE products MODIFY COLUMN price DECIMAL(10,2)");
    echo "✓ Updated products.price to DECIMAL(10,2)<br>";
    
    // 2. Check if payment_method column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM orders LIKE 'payment_method'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE orders ADD COLUMN payment_method VARCHAR(50) DEFAULT 'bank_transfer'");
        echo "✓ Added payment_method column to orders<br>";
    } else {
        echo "✓ payment_method column already exists<br>";
    }
    
    // 3. Create views
    $pdo->exec("CREATE OR REPLACE VIEW pending_orders AS
    SELECT 
        o.id,
        o.order_number,
        o.customer_name,
        o.customer_phone,
        o.customer_email,
        o.total_amount,
        o.status,
        o.payment_method,
        o.created_at,
        COUNT(oi.id) as item_count
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.status IN ('pending', 'confirmed')
    GROUP BY o.id
    ORDER BY o.created_at DESC");
    echo "✓ Created pending_orders view<br>";
    
    $pdo->exec("CREATE OR REPLACE VIEW sales_report AS
    SELECT 
        DATE_FORMAT(o.created_at, '%Y-%m-%d') as tanggal,
        o.order_number,
        COALESCE(m.name, o.customer_name) as customer_name,
        o.customer_phone,
        p.name as product_name,
        oi.quantity,
        oi.product_price as harga_satuan,
        oi.subtotal,
        o.total_amount,
        o.status,
        o.payment_method,
        o.created_at
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN products p ON oi.product_id = p.id
    LEFT JOIN members m ON o.member_id = m.id
    WHERE o.status IN ('confirmed', 'completed')
    ORDER BY o.created_at DESC");
    echo "✓ Created sales_report view<br>";
    
    echo "<br><strong>Database migration completed successfully!</strong>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
