<?php
require_once 'config.php';
require_once 'includes/db.php';

$db = new Database();

try {
    // 1. Create payment_proof table
    $sql1 = "CREATE TABLE IF NOT EXISTS payment_proof (
        id INT PRIMARY KEY AUTO_INCREMENT,
        order_id INT NOT NULL,
        file_path VARCHAR(255) NOT NULL,
        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        verified_at TIMESTAMP NULL,
        verified_by INT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id),
        FOREIGN KEY (verified_by) REFERENCES admin(id)
    )";
    
    $db->query($sql1);
    echo "✓ payment_proof table created<br>";
    
    // 2. Create payment_settings table
    $sql2 = "CREATE TABLE IF NOT EXISTS payment_settings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        setting_key VARCHAR(100) UNIQUE NOT NULL,
        setting_value LONGTEXT NOT NULL,
        setting_type VARCHAR(20) DEFAULT 'text',
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $db->query($sql2);
    echo "✓ payment_settings table created<br>";
    
    // 3. Insert default payment settings
    $settings = [
        ['bank_name_1', 'BCA', 'text'],
        ['bank_account_1', '1234567890', 'text'],
        ['bank_holder_1', 'Florist & Kerajinan Bulu Kawat', 'text'],
        ['bank_name_2', 'BNI', 'text'],
        ['bank_account_2', '0987654321', 'text'],
        ['bank_holder_2', 'Florist & Kerajinan Bulu Kawat', 'text'],
        ['bank_name_3', 'Mandiri', 'text'],
        ['bank_account_3', '1122334455', 'text'],
        ['bank_holder_3', 'Florist & Kerajinan Bulu Kawat', 'text'],
    ];
    
    foreach ($settings as $setting) {
        $check = $db->query("SELECT id FROM payment_settings WHERE setting_key = ?", [$setting[0]]);
        if (empty($check)) {
            $db->query("INSERT INTO payment_settings (setting_key, setting_value, setting_type) VALUES (?, ?, ?)", $setting);
        }
    }
    echo "✓ Payment settings initialized<br>";
    
    echo "<br><strong>Database migration completed successfully!</strong>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
