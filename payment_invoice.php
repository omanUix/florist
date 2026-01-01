<?php
require_once 'config.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['current_order_id'])) {
    redirect('products.php');
}

$order_id = $_SESSION['current_order_id'];
$order_number = $_SESSION['current_order_number'];
$payment_method = $_SESSION['payment_method'];

$db = new Database();
$order = $db->fetchOne("SELECT * FROM orders WHERE id = ?", [$order_id]);
if (!$order) {
    redirect('products.php');
}

$order_items = $db->fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$order_id]);

// Get payment settings from database
// Ambil data bank dan QRIS dari payment_settings
$banks = [];
for ($i = 1; $i <= 3; $i++) {
    $name = $db->fetchOne("SELECT setting_value FROM payment_settings WHERE setting_key = ?", ["bank_name_$i"]);
    $account = $db->fetchOne("SELECT setting_value FROM payment_settings WHERE setting_key = ?", ["bank_account_$i"]);
    $holder = $db->fetchOne("SELECT setting_value FROM payment_settings WHERE setting_key = ?", ["bank_holder_$i"]);
    if ($name && $account) {
        $banks[] = [
            'name' => $name['setting_value'],
            'account' => $account['setting_value'],
            'holder' => $holder['setting_value'] ?? ''
        ];
    }
}
$qris_image = $db->fetchOne("SELECT setting_value FROM payment_settings WHERE setting_key = ?", ["qris_image"]);

$page_title = 'Invoice Pembayaran';
?>

<?php include 'includes/header.php'; ?>

<section class="page-header">
    <div class="container">
        <h1><?php echo $page_title; ?></h1>
        <p>Pesanan: <?php echo htmlspecialchars($order_number); ?></p>
    </div>
</section>

<section class="invoice-section">
    <div class="container">
        <div class="invoice-container">
            <!-- Invoice Card -->
            <div class="invoice-card">
                <div class="invoice-header">
                    <div class="invoice-title">
                        <h2>Pesanan Anda Telah Dibuat</h2>
                        <p>No. Pesanan: <strong><?php echo htmlspecialchars($order_number); ?></strong></p>
                    </div>
                    <div class="invoice-status">
                        <span class="status-badge pending">
                            <i class="fas fa-hourglass-half"></i> Menunggu Pembayaran
                        </span>
                    </div>
                </div>
                
                <div class="invoice-details">
                    <div class="detail-section">
                        <h3>Data Pemesan</h3>
                        <p><strong><?php echo htmlspecialchars($order['customer_name'] ?? ''); ?></strong></p>
                        <p><?php echo htmlspecialchars($order['customer_phone'] ?? ''); ?></p>
                        <p><?php echo htmlspecialchars($order['customer_email'] ?? ''); ?></p>
                        <?php if (!empty($order['customer_address'])): ?>
                            <p><?php echo htmlspecialchars($order['customer_address']); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="detail-section">
                        <h3>Detail Pesanan</h3>
                        <?php foreach ($order_items as $item): ?>
                            <div class="item-detail">
                                <div class="item-info">
                                    <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                                    <span><?php echo $item['quantity']; ?> x <?php echo formatPrice($item['product_price']); ?></span>
                                </div>
                                <div class="item-subtotal">
                                    <?php echo formatPrice($item['subtotal']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <div class="total-line">
                            <span>Total Pembayaran</span>
                            <span class="total-amount"><?php echo formatPrice($order['total_amount']); ?></span>
                        </div>
                    </div>
                </div>
                
                <?php if ($payment_method == 'bank_transfer'): ?>
                    <div class="payment-instruction">
                        <h3><i class="fas fa-university"></i> Instruksi Transfer Bank</h3>
                        <div class="bank-options">
                            <?php foreach ($banks as $bank): ?>
                                <div class="bank-card">
                                    <h4><?php echo htmlspecialchars($bank['name']); ?></h4>
                                    <div class="bank-detail">
                                        <span class="label">Nomor Rekening:</span>
                                        <span class="value" onclick="copyToClipboard('<?php echo htmlspecialchars($bank['account']); ?>')">
                                            <?php echo htmlspecialchars($bank['account']); ?>
                                            <i class="fas fa-copy"></i>
                                        </span>
                                    </div>
                                    <div class="bank-detail">
                                        <span class="label">Atas Nama:</span>
                                        <span class="value"><?php echo htmlspecialchars($bank['holder']); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="transfer-note">
                            <i class="fas fa-exclamation-circle"></i>
                            <p><strong>Penting!</strong> Setelah mentransfer, silakan kirim bukti transfer melalui WhatsApp ke nomor kami atau upload di sistem untuk konfirmasi pembayaran.</p>
                        </div>
                    </div>
                    <div class="payment-instruction">
                        <h3><i class="fas fa-qrcode"></i> Pembayaran QRIS</h3>
                        <div class="qris-container">
                            <?php if ($qris_image && !empty($qris_image['setting_value'])): ?>
                                <img src="<?php echo htmlspecialchars($qris_image['setting_value']); ?>" alt="QRIS Code" class="qris-image" style="max-width:250px;display:block;margin:0 auto 10px;">
                                <p class="amount" style="text-align:center;font-size:1.2em;font-weight:bold;">Rp <?php echo number_format($order['total_amount'],0,',','.'); ?></p>
                            <?php else: ?>
                                <div class="qris-placeholder">
                                    <i class="fas fa-qrcode"></i>
                                    <p>Kode QRIS untuk pembayaran</p>
                                    <p class="amount"><?php echo formatPrice($order['total_amount']); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="qris-note">
                            <i class="fas fa-info-circle"></i>
                            <p>Scan kode QRIS dengan e-wallet Anda (GoPay, OVO, Dana, dll.) untuk melakukan pembayaran.</p>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="payment-actions">
                    <a href="products.php" class="btn btn-outline">
                        <i class="fas fa-shopping-bag"></i> Lanjut Belanja
                    </a>
                    <a href="javascript:window.print()" class="btn btn-secondary">
                        <i class="fas fa-print"></i> Cetak Invoice
                    </a>
                    <a href="https://wa.me/628988206096?text=Saya%20sudah%20mentransfer%20atau%20scan%20QRIS%20untuk%20pesanan%20<?php echo $order_number; ?>" target="_blank" class="btn btn-primary">
                        <i class="fab fa-whatsapp"></i> Konfirmasi via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.invoice-section {
    padding: 4rem 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
}

.invoice-container {
    max-width: 800px;
    margin: 0 auto;
}

.invoice-card {
    background: var(--white);
    border-radius: var(--border-radius);
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    overflow: hidden;
}

.invoice-header {
    background: linear-gradient(135deg, var(--primary-color), #ff9ccc);
    color: var(--white);
    padding: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 2rem;
}

.invoice-title h2 {
    margin: 0 0 0.5rem 0;
}

.invoice-title p {
    margin: 0;
    opacity: 0.9;
}

.invoice-status {
    display: flex;
    align-items: center;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255,255,255,0.2);
    padding: 0.75rem 1.5rem;
    border-radius: 20px;
    font-weight: 500;
    font-size: 0.9rem;
}

.status-badge.pending {
    background: rgba(255,255,255,0.3);
}

.invoice-details {
    padding: 2rem;
    border-bottom: 1px solid var(--border-color);
}

.detail-section {
    margin-bottom: 2rem;
}

.detail-section:last-child {
    margin-bottom: 0;
}

.detail-section h3 {
    color: var(--text-dark);
    margin-bottom: 1rem;
    font-size: 1rem;
}

.detail-section p {
    margin: 0.25rem 0;
    color: var(--text-light);
    line-height: 1.5;
}

.item-detail {
    display: flex;
    justify-content: space-between;
    padding: 1rem 0;
    border-bottom: 1px solid var(--border-color);
}

.item-detail:last-child {
    border-bottom: none;
}

.item-info {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.item-info strong {
    color: var(--text-dark);
}

.item-info span {
    color: var(--text-light);
    font-size: 0.9rem;
}

.item-subtotal {
    font-weight: bold;
    color: var(--primary-color);
}

.total-line {
    display: flex;
    justify-content: space-between;
    padding: 1rem 0;
    border-top: 2px solid var(--border-color);
    font-weight: bold;
    color: var(--text-dark);
}

.total-amount {
    color: var(--primary-color);
    font-size: 1.2rem;
}

.payment-instruction {
    padding: 2rem;
    border-bottom: 1px solid var(--border-color);
}

.payment-instruction h3 {
    color: var(--text-dark);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.bank-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.bank-card {
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    background: var(--light-gray);
}

.bank-card h4 {
    margin: 0 0 1rem 0;
    color: var(--text-dark);
}

.bank-detail {
    margin-bottom: 1rem;
    display: flex;
    flex-direction: column;
}

.bank-detail .label {
    color: var(--text-light);
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
}

.bank-detail .value {
    color: var(--text-dark);
    font-weight: bold;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    padding: 0.5rem;
    background: var(--white);
    border-radius: 4px;
    transition: var(--transition);
}

.bank-detail .value:hover {
    background: var(--primary-color);
    color: var(--white);
}

.transfer-note,
.qris-note {
    background: #fff3cd;
    border-left: 4px solid #ffc107;
    padding: 1rem;
    border-radius: 4px;
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}

.transfer-note i,
.qris-note i {
    color: #ffc107;
    flex-shrink: 0;
}

.transfer-note p,
.qris-note p {
    margin: 0;
    color: #856404;
}

.qris-container {
    margin-bottom: 1.5rem;
}

.qris-placeholder {
    background: var(--light-gray);
    border: 2px dashed var(--border-color);
    border-radius: var(--border-radius);
    padding: 3rem 2rem;
    text-align: center;
}

.qris-placeholder i {
    font-size: 4rem;
    color: #ccc;
    margin-bottom: 1rem;
}

.qris-placeholder p {
    margin: 0.5rem 0;
    color: var(--text-light);
}

.qris-placeholder .amount {
    font-size: 1.5rem;
    color: var(--primary-color);
    font-weight: bold;
}

.payment-actions {
    padding: 2rem;
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    justify-content: center;
}

.payment-actions .btn {
    flex: 0 1 auto;
    padding: 0.75rem 1.5rem;
}

@media (max-width: 768px) {
    .invoice-header {
        flex-direction: column;
    }
    
    .bank-options {
        grid-template-columns: 1fr;
    }
    
    .payment-actions {
        flex-direction: column;
    }
    
    .payment-actions .btn {
        flex: 1;
    }
}

@media print {
    .payment-actions {
        display: none;
    }
    
    body {
        background: white;
    }
    
    .invoice-section {
        padding: 0;
        background: white;
    }
}
</style>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Nomor rekening disalin ke clipboard!');
    });
}
</script>

<?php include 'includes/footer.php'; ?>
