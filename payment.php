<?php
require_once 'config.php';
require_once 'includes/functions.php';

$is_member = isMemberLoggedIn();

if (!isset($_SESSION['order_data'])) {
    redirect('products.php');
}

$order = $_SESSION['order_data'];

// Deteksi apakah dari cart (multiple items) atau single product
$is_from_cart = isset($order['items']) && is_array($order['items']) && count($order['items']) > 0;

$error = '';
$customer_name = '';
$customer_email = '';
$customer_phone = '';
$customer_address = '';
$notes = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name = cleanInput($_POST['name'] ?? ($_SESSION['member_name'] ?? ''));
    $customer_email = cleanInput($_POST['email'] ?? ($_SESSION['member_email'] ?? ''));
    $customer_phone = cleanInput($_POST['phone']);
    $customer_address = cleanInput($_POST['address']);
    $notes = cleanInput($_POST['notes'] ?? '');
    $payment_method = $_POST['payment_method'] ?? 'bank_transfer';
    
    if (empty($customer_name) || empty($customer_phone) || empty($customer_address)) {
        $error = 'Silakan isi semua data yang diperlukan';
    } else {
        // Create order
        try {
            $order_number = 'ORD' . date('Ymd') . mt_rand(10000, 99999);
            $member_id = $is_member ? $_SESSION['member_id'] : null;
            
            // Gunakan INSERT yang lebih fleksibel
            $sql = "INSERT INTO orders (order_number, member_id, customer_name, customer_phone, customer_email, customer_address, total_amount, status, notes, payment_method, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $db->query($sql, [
                $order_number,
                $member_id,
                $customer_name,
                $customer_phone,
                $customer_email,
                $customer_address,
                $order['total'],
                'pending',
                $notes,
                $payment_method
            ]);
            
            // Get order ID
            $order_result = $db->fetchOne("SELECT id FROM orders WHERE order_number = ?", [$order_number]);
            $order_id = $order_result['id'];
            
            // Insert order items
            $sql = "INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity, subtotal) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            if ($is_from_cart) {
                // Multiple items from cart
                foreach ($order['items'] as $item) {
                    $db->query($sql, [
                        $order_id,
                        $item['id'],
                        $item['name'],
                        $item['price'],
                        $item['quantity'],
                        $item['price'] * $item['quantity']
                    ]);
                }
            } else {
                // Single product checkout
                $db->query($sql, [
                    $order_id,
                    $order['product_id'],
                    $order['product_name'],
                    $order['price'],
                    $order['quantity'],
                    $order['total']
                ]);
            }
            
            // Store order ID in session
            $_SESSION['current_order_id'] = $order_id;
            $_SESSION['current_order_number'] = $order_number;
            $_SESSION['payment_method'] = $payment_method;
            
            // Kosongkan keranjang setelah order berhasil dibuat
            $_SESSION['cart'] = [];
            
            // Redirect to payment page
            header('Location: payment_invoice.php');
            exit;
        } catch (Exception $e) {
            $error = 'Gagal membuat pesanan: ' . $e->getMessage();
        }
    }
} else {
    if ($is_member) {
        $customer_name = $_SESSION['member_name'];
        $customer_email = $_SESSION['member_email'];
    }
}

$page_title = 'Pembayaran';
?>

<?php include 'includes/header.php'; ?>

<section class="page-header">
    <div class="container">
        <h1><?php echo $page_title; ?></h1>
        <p>Pilih metode pembayaran</p>
    </div>
</section>

<section class="payment-section">
    <div class="container">
        <div class="payment-grid">
            <!-- Form -->
            <div class="payment-form">
                <h2>Pilih Metode Pembayaran</h2>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="payment-methods">
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="bank_transfer" checked>
                            <div class="method-card">
                                <i class="fas fa-university"></i>
                                <div class="method-info">
                                    <h3>Transfer Bank</h3>
                                    <p>Transfer ke rekening Bank BCA, BNI, atau Mandiri</p>
                                </div>
                                <span class="checkmark"></span>
                            </div>
                        </label>
                        
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="qris">
                            <div class="method-card">
                                <i class="fas fa-qrcode"></i>
                                <div class="method-info">
                                    <h3>QRIS</h3>
                                    <p>Scan kode QRIS menggunakan e-wallet Anda</p>
                                </div>
                                <span class="checkmark"></span>
                            </div>
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <label for="name">Nama Pemesan *</label>
                        <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($customer_name); ?>">
                    </div>
                    
                    <?php if (!$is_member): ?>
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($customer_email); ?>">
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($customer_email); ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="phone">Nomor Whatsapp *</label>
                        <input type="tel" id="phone" name="phone" placeholder="08xxxxxxxxxx" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Alamat Pengiriman *</label>
                        <textarea id="address" name="address" rows="4" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Catatan Khusus (Opsional)</label>
                        <textarea id="notes" name="notes" rows="3" placeholder="Tuliskan permintaan khusus..."></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <a href="<?php echo $is_from_cart ? 'checkout.php' : 'product_detail.php?id=' . ($order['product_id'] ?? 0); ?>" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-arrow-right"></i> Lanjut ke Invoice
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Order Summary -->
            <div class="order-summary">
                <h2>Ringkasan Pesanan</h2>
                
                <?php if ($is_from_cart): ?>
                    <!-- Multiple items from cart -->
                    <div class="summary-items-list">
                        <?php foreach ($order['items'] as $item): ?>
                        <div class="summary-item">
                            <div class="item-image">
                                <?php if ($item['image']): ?>
                                    <img src="assets/img/<?php echo htmlspecialchars($item['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <?php else: ?>
                                    <i class="fas fa-image"></i>
                                <?php endif; ?>
                            </div>
                            
                            <div class="item-details">
                                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p class="item-quantity"><?php echo $item['quantity']; ?> x <?php echo formatPrice($item['price']); ?></p>
                            </div>
                            
                            <div class="item-subtotal">
                                <?php echo formatPrice($item['price'] * $item['quantity']); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <!-- Single product -->
                    <div class="summary-item">
                        <div class="item-image">
                            <?php if ($order['image']): ?>
                                <img src="assets/img/<?php echo htmlspecialchars($order['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($order['product_name']); ?>">
                            <?php else: ?>
                                <i class="fas fa-image"></i>
                            <?php endif; ?>
                        </div>
                        
                        <div class="item-details">
                            <h3><?php echo htmlspecialchars($order['product_name']); ?></h3>
                            <p class="item-quantity"><?php echo $order['quantity']; ?> x <?php echo formatPrice($order['price']); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="summary-breakdown">
                    <div class="breakdown-row">
                        <span>Subtotal</span>
                        <span><?php echo formatPrice($order['total']); ?></span>
                    </div>
                    <div class="breakdown-row">
                        <span>Biaya Admin</span>
                        <span>Gratis</span>
                    </div>
                    <div class="breakdown-row total">
                        <span>Total Bayar</span>
                        <span><?php echo formatPrice($order['total']); ?></span>
                    </div>
                </div>
                
                <div class="payment-info">
                    <i class="fas fa-shield-alt"></i>
                    <p>Pembayaran Anda aman dan terlindungi</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.payment-section {
    padding: 4rem 0;
    background: var(--light-gray);
    min-height: 70vh;
}

.payment-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 2rem;
}

.payment-form,
.order-summary {
    background: var(--white);
    padding: 2rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
}

.payment-form h2,
.order-summary h2 {
    margin-bottom: 2rem;
    color: var(--text-dark);
}

.payment-methods {
    margin-bottom: 2rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.payment-option {
    cursor: pointer;
}

.payment-option input {
    display: none;
}

.method-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    transition: var(--transition);
    position: relative;
}

.payment-option input:checked + .method-card {
    border-color: var(--primary-color);
    background: #fff5f8;
}

.method-card:hover {
    border-color: var(--primary-color);
}

.method-card i {
    font-size: 2rem;
    color: var(--primary-color);
    flex-shrink: 0;
}

.method-info {
    flex: 1;
}

.method-info h3 {
    margin: 0 0 0.25rem 0;
    color: var(--text-dark);
}

.method-info p {
    margin: 0;
    color: var(--text-light);
    font-size: 0.9rem;
}

.checkmark {
    width: 24px;
    height: 24px;
    border: 2px solid var(--border-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.payment-option input:checked + .method-card .checkmark {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: var(--white);
}

.payment-option input:checked + .method-card .checkmark::after {
    content: '✓';
    font-size: 0.8rem;
    font-weight: bold;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: var(--text-dark);
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    font-family: inherit;
    font-size: 0.9rem;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(255, 105, 150, 0.1);
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.form-actions .btn {
    flex: 1;
}

.alert-error {
    background: #ffebee;
    border-left: 4px solid #f44336;
    color: #c62828;
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 2rem;
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}

.alert-error i {
    flex-shrink: 0;
}

.summary-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    background: var(--light-gray);
    border-radius: var(--border-radius);
    margin-bottom: 1rem;
    align-items: center;
}

.summary-items-list {
    margin-bottom: 2rem;
    max-height: 400px;
    overflow-y: auto;
}

.item-image {
    width: 80px;
    height: 80px;
    background: var(--white);
    border-radius: var(--border-radius);
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #ccc;
    flex-shrink: 0;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.item-details {
    flex: 1;
}

.item-details h3 {
    margin: 0 0 0.5rem 0;
    color: var(--text-dark);
}

.item-quantity {
    margin: 0;
    color: var(--text-light);
    font-size: 0.9rem;
}

.item-subtotal {
    font-weight: bold;
    color: var(--text-dark);
    min-width: 80px;
    text-align: right;
}

.summary-breakdown {
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    padding: 1rem;
    margin-bottom: 2rem;
}

.breakdown-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    color: var(--text-light);
}

.breakdown-row.total {
    border-top: 2px solid var(--border-color);
    padding-top: 1rem;
    margin-top: 1rem;
    color: var(--text-dark);
    font-weight: bold;
    font-size: 1.1rem;
}

.payment-info {
    background: #e8f5e9;
    border-left: 4px solid #4caf50;
    padding: 1rem;
    border-radius: 4px;
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}

.payment-info i {
    color: #4caf50;
    flex-shrink: 0;
    margin-top: 0.25rem;
}

.payment-info p {
    margin: 0;
    color: #2e7d32;
}

@media (max-width: 768px) {
    .payment-grid {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
