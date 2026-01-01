<?php
require_once 'config.php';
require_once 'includes/functions.php';

if (!isMemberLoggedIn()) {
    redirect('login_member.php');
}

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$member_id = $_SESSION['member_id'];

$order = $db->fetchOne(
    "SELECT * FROM orders WHERE id = ? AND (member_id = ? OR member_id IS NULL)",
    [$order_id, $member_id]
);

if (!$order) {
    redirect('member_dashboard.php');
}

$order_items = $db->fetchAll(
    "SELECT * FROM order_items WHERE order_id = ?",
    [$order_id]
);

$page_title = 'Detail Pesanan';
?>

<?php include 'includes/header.php'; ?>

<section class="page-header">
    <div class="container">
        <h1><?php echo $page_title; ?></h1>
        <p><?php echo htmlspecialchars($order['order_number']); ?></p>
    </div>
</section>

<section class="order-detail">
    <div class="container">
        <!-- Status Timeline -->
        <div class="status-timeline">
            <div class="timeline-item <?php echo in_array($order['status'], ['pending', 'confirmed', 'completed']) ? 'completed' : ''; ?>">
                <div class="timeline-marker">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="timeline-content">
                    <h4>Pesanan Dibuat</h4>
                    <p><?php echo date('d M Y, H:i', strtotime($order['created_at'])); ?></p>
                </div>
            </div>
            
            <div class="timeline-item <?php echo in_array($order['status'], ['confirmed', 'completed']) ? 'completed' : ($order['status'] === 'pending' ? 'active' : ''); ?>">
                <div class="timeline-marker">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="timeline-content">
                    <h4>Menunggu Konfirmasi</h4>
                    <p><?php echo $order['status'] === 'pending' ? 'Silakan upload bukti pembayaran' : 'Pembayaran dikonfirmasi'; ?></p>
                </div>
            </div>
            
            <div class="timeline-item <?php echo $order['status'] === 'completed' ? 'completed' : ''; ?>">
                <div class="timeline-marker">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="timeline-content">
                    <h4>Pesanan Dikonfirmasi</h4>
                    <p>Pesanan sedang disiapkan untuk dikirim</p>
                </div>
            </div>
            
            <div class="timeline-item <?php echo $order['status'] === 'completed' ? 'completed' : ''; ?>">
                <div class="timeline-marker">
                    <i class="fas fa-box"></i>
                </div>
                <div class="timeline-content">
                    <h4>Pesanan Selesai</h4>
                    <p>Pesanan telah diterima</p>
                </div>
            </div>
        </div>
        
        <div class="detail-grid">
            <!-- Order Info -->
            <div class="order-info-section">
                <h2>Informasi Pesanan</h2>
                
                <div class="info-block">
                    <h3>Data Pesanan</h3>
                    <p><strong>No. Pesanan:</strong> <?php echo htmlspecialchars($order['order_number']); ?></p>
                    <p><strong>Tanggal:</strong> <?php echo date('d M Y, H:i', strtotime($order['created_at'])); ?></p>
                    <p><strong>Status:</strong> <span class="status-badge <?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></p>
                    <p><strong>Metode Pembayaran:</strong> <?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?></p>
                </div>
                
                <div class="info-block">
                    <h3>Penerima</h3>
                    <p><strong><?php echo htmlspecialchars($order['customer_name']); ?></strong></p>
                    <p><?php echo htmlspecialchars($order['customer_phone']); ?></p>
                    <p><?php echo htmlspecialchars($order['customer_email']); ?></p>
                    <p><?php echo htmlspecialchars($order['customer_address']); ?></p>
                </div>
                
                <?php if ($order['notes']): ?>
                    <div class="info-block">
                        <h3>Catatan</h3>
                        <p><?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Items & Total -->
            <div class="order-items-section">
                <h2>Detail Item</h2>
                
                <div class="items-list">
                    <?php foreach ($order_items as $item): ?>
                        <div class="item-row">
                            <div class="item-col name">
                                <p><?php echo htmlspecialchars($item['product_name']); ?></p>
                            </div>
                            <div class="item-col price">
                                <p><?php echo formatPrice($item['product_price']); ?></p>
                            </div>
                            <div class="item-col quantity">
                                <p><?php echo $item['quantity']; ?> x</p>
                            </div>
                            <div class="item-col subtotal">
                                <p><?php echo formatPrice($item['subtotal']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="order-total">
                    <div class="total-row">
                        <span>Subtotal</span>
                        <span><?php echo formatPrice($order['total_amount']); ?></span>
                    </div>
                    <div class="total-row">
                        <span>Biaya Admin</span>
                        <span>Gratis</span>
                    </div>
                    <div class="total-row final">
                        <span>Total Pembayaran</span>
                        <span><?php echo formatPrice($order['total_amount']); ?></span>
                    </div>
                </div>
                
                <div class="action-buttons">
                    <a href="member_orders.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <?php if ($order['status'] === 'pending' || $order['status'] === 'waiting_verification'): ?>
                        <a href="payment_proof.php?order_id=<?= $order_id ?>" class="btn btn-primary">
                            <i class="fas fa-receipt"></i> Upload Bukti Pembayaran
                        </a>
                    <?php endif; ?>
                    <a href="https://wa.me/628XXXXXXXXXX?text=Saya%20ingin%20konfirmasi%20pesanan%20<?php echo $order['order_number']; ?>" target="_blank" class="btn btn-primary">
                        <i class="fab fa-whatsapp"></i> Hubungi via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.order-detail {
    padding: 4rem 0;
    background: var(--light-gray);
    min-height: 70vh;
}

.status-timeline {
    background: var(--white);
    padding: 2rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    margin-bottom: 2rem;
    display: flex;
    gap: 2rem;
    overflow-x: auto;
    padding-bottom: 1rem;
}

.timeline-item {
    flex: 1;
    min-width: 150px;
    position: relative;
    padding-top: 3rem;
    text-align: center;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 1.5rem;
    left: 50%;
    width: 0;
    height: 2px;
    background: #ddd;
    transform: translateX(50%);
}

.timeline-item.completed:not(:last-child)::after {
    width: calc(100% + 2rem);
    background: var(--primary-color);
    left: 50%;
}

.timeline-marker {
    width: 50px;
    height: 50px;
    background: #f0f0f0;
    border: 2px solid #ddd;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.2rem;
    color: #999;
}

.timeline-item.completed .timeline-marker {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.timeline-item.active .timeline-marker {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
    box-shadow: 0 0 0 4px rgba(255, 105, 150, 0.2);
}

.timeline-content h4 {
    margin: 0 0 0.5rem 0;
    color: var(--text-dark);
    font-size: 0.95rem;
}

.timeline-content p {
    margin: 0;
    color: var(--text-light);
    font-size: 0.85rem;
}

.detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.order-info-section,
.order-items-section {
    background: var(--white);
    padding: 2rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
}

h2 {
    margin-bottom: 1.5rem;
    color: var(--text-dark);
}

.info-block {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid var(--border-color);
}

.info-block:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.info-block h3 {
    color: var(--text-dark);
    margin-bottom: 1rem;
    font-size: 0.95rem;
}

.info-block p {
    margin: 0.5rem 0;
    color: var(--text-light);
}

.info-block strong {
    color: var(--text-dark);
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    text-transform: capitalize;
}

.status-badge.pending {
    background: #fff3cd;
    color: #856404;
}

.status-badge.confirmed {
    background: #d1ecf1;
    color: #0c5460;
}

.status-badge.completed {
    background: #d4edda;
    color: #155724;
}

.items-list {
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    overflow: hidden;
    margin-bottom: 2rem;
}

.item-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    gap: 1rem;
    padding: 1rem;
    border-bottom: 1px solid var(--border-color);
    align-items: center;
}

.item-row:last-child {
    border-bottom: none;
}

.item-col p {
    margin: 0;
    color: var(--text-dark);
}

.item-col.price,
.item-col.quantity,
.item-col.subtotal {
    text-align: right;
}

.order-total {
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    padding: 1rem;
    margin-bottom: 2rem;
}

.total-row {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    color: var(--text-light);
}

.total-row.final {
    border-top: 2px solid var(--border-color);
    padding-top: 1rem;
    margin-top: 1rem;
    color: var(--text-dark);
    font-weight: bold;
    font-size: 1.1rem;
}

.action-buttons {
    display: flex;
    gap: 1rem;
}

.action-buttons .btn {
    flex: 1;
}

@media (max-width: 768px) {
    .detail-grid {
        grid-template-columns: 1fr;
    }
    
    .item-row {
        grid-template-columns: 1fr 1fr;
    }
    
    .item-col.quantity {
        text-align: left;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
