<?php
require_once 'config.php';
require_once 'includes/functions.php';

$is_member = isMemberLoggedIn();
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    
    if ($product_id <= 0 || $quantity <= 0) {
        redirect('products.php');
    }
    
    $product = getProductById($product_id);
    if (!$product) {
        redirect('products.php');
    }
    
    // Store order data in session
    $_SESSION['order_data'] = [
        'product_id' => $product_id,
        'product_name' => $product['name'],
        'price' => $product['price'],
        'quantity' => $quantity,
        'total' => $product['price'] * $quantity,
        'image' => $product['image']
    ];
    
    // Redirect ke payment page
    header('Location: payment.php');
    exit;
} else {
    if (!isset($_SESSION['order_data'])) {
        redirect('products.php');
    }
}

$order = $_SESSION['order_data'];

// Deteksi apakah dari cart (multiple items) atau single product
$is_from_cart = isset($order['items']) && is_array($order['items']) && count($order['items']) > 0;

$page_title = 'Checkout';
?>

<?php include 'includes/header.php'; ?>

<section class="page-header">
    <div class="container">
        <h1><?php echo $page_title; ?></h1>
        <p>Selesaikan pemesanan Anda</p>
    </div>
</section>

<section class="checkout-section">
    <div class="container">
        <div class="checkout-grid">
            <!-- Form -->
            <div class="checkout-form">
                <h2>Data Pemesan</h2>
                
                <form method="POST" action="payment.php">
                    <?php if (!$is_member): ?>
                        <div class="form-group">
                            <label for="name">Nama Lengkap *</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
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
                        <a href="<?php echo $is_from_cart ? 'cart.php' : 'product_detail.php?id=' . $order['product_id']; ?>" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-arrow-right"></i> Lanjut ke Pembayaran
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
                                <p class="item-price"><?php echo formatPrice($item['price']); ?> x <?php echo $item['quantity']; ?></p>
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
                            <p class="item-price"><?php echo formatPrice($order['price']); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="summary-breakdown">
                    <?php if (!$is_from_cart): ?>
                        <div class="breakdown-row">
                            <span>Harga Satuan</span>
                            <span><?php echo formatPrice($order['price']); ?></span>
                        </div>
                        <div class="breakdown-row">
                            <span>Jumlah</span>
                            <span><?php echo $order['quantity']; ?> x</span>
                        </div>
                    <?php endif; ?>
                    <div class="breakdown-row total">
                        <span>Total</span>
                        <span><?php echo formatPrice($order['total']); ?></span>
                    </div>
                </div>
                
                <div class="payment-info">
                    <i class="fas fa-info-circle"></i>
                    <p>Selesaikan pembayaran untuk mengaktifkan pesanan Anda</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.checkout-section {
    padding: 4rem 0;
    background: var(--light-gray);
    min-height: 70vh;
}

.checkout-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 2rem;
}

.checkout-form,
.order-summary {
    background: var(--white);
    padding: 2rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
}

.checkout-form h2,
.order-summary h2 {
    margin-bottom: 2rem;
    color: var(--text-dark);
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

.item-price {
    margin: 0;
    color: var(--primary-color);
    font-weight: bold;
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
    background: #e3f2fd;
    border-left: 4px solid #2196F3;
    padding: 1rem;
    border-radius: 4px;
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}

.payment-info i {
    color: #2196F3;
    flex-shrink: 0;
    margin-top: 0.25rem;
}

.payment-info p {
    margin: 0;
    color: #1565c0;
}

@media (max-width: 768px) {
    .checkout-grid {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
