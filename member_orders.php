<?php
require_once 'config.php';
require_once 'includes/functions.php';

if (!isMemberLoggedIn()) {
    redirect('login_member.php');
}

$member_id = $_SESSION['member_id'];

// Get all orders for member
$orders = $db->fetchAll(
    "SELECT * FROM orders WHERE member_id = ? ORDER BY created_at DESC",
    [$member_id]
);

$page_title = 'Pesanan Saya';
?>

<?php include 'includes/header.php'; ?>

<section class="page-header">
    <div class="container">
        <h1><?php echo $page_title; ?></h1>
        <p>Kelola semua pesanan Anda</p>
    </div>
</section>

<section class="orders-section">
    <div class="container">
        <div class="orders-header">
            <h2>Daftar Pesanan</h2>
            <a href="products.php" class="btn btn-primary">
                <i class="fas fa-shopping-bag"></i> Belanja Lagi
            </a>
        </div>
        
        <?php if (!empty($orders)): ?>
            <div class="orders-table-wrapper">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>No. Pesanan</th>
                            <th>Tanggal</th>
                            <th>Penerima</th>
                            <th>Total</th>
                            <th>Metode</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="order-number">
                                    <strong><?php echo htmlspecialchars($order['order_number']); ?></strong>
                                </td>
                                <td>
                                    <?php echo date('d M Y', strtotime($order['created_at'])); ?><br>
                                    <small><?php echo date('H:i', strtotime($order['created_at'])); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td>
                                    <strong><?php echo formatPrice($order['total_amount']); ?></strong>
                                </td>
                                <td>
                                    <?php 
                                    $method_label = str_replace('_', ' ', ucfirst($order['payment_method']));
                                    if ($order['payment_method'] == 'bank_transfer') {
                                        echo '<i class="fas fa-university"></i> Bank';
                                    } else if ($order['payment_method'] == 'qris') {
                                        echo '<i class="fas fa-qrcode"></i> QRIS';
                                    } else {
                                        echo $method_label;
                                    }
                                    ?>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo $order['status']; ?>">
                                        <?php 
                                        switch($order['status']) {
                                            case 'pending':
                                                echo '<i class="fas fa-hourglass-half"></i> Menunggu';
                                                break;
                                            case 'confirmed':
                                                echo '<i class="fas fa-check-circle"></i> Dikonfirmasi';
                                                break;
                                            case 'completed':
                                                echo '<i class="fas fa-box"></i> Selesai';
                                                break;
                                            case 'cancelled':
                                                echo '<i class="fas fa-times-circle"></i> Dibatalkan';
                                                break;
                                            default:
                                                echo ucfirst($order['status']);
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="member_order_detail.php?id=<?php echo $order['id']; ?>" class="btn-action">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-shopping-bag"></i>
                <h3>Belum Ada Pesanan</h3>
                <p>Anda belum memiliki pesanan. Mulai berbelanja sekarang!</p>
                <a href="products.php" class="btn btn-primary">
                    <i class="fas fa-shopping-bag"></i> Belanja Sekarang
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.orders-section {
    padding: 4rem 0;
    background: var(--light-gray);
    min-height: 70vh;
}

.orders-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.orders-header h2 {
    margin: 0;
    color: var(--text-dark);
}

.orders-table-wrapper {
    background: var(--white);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    overflow: hidden;
}

.orders-table {
    width: 100%;
    border-collapse: collapse;
}

.orders-table thead {
    background: var(--light-gray);
    border-bottom: 2px solid var(--border-color);
}

.orders-table th {
    padding: 1.25rem;
    text-align: left;
    color: var(--text-dark);
    font-weight: 600;
}

.orders-table td {
    padding: 1.25rem;
    border-bottom: 1px solid var(--border-color);
    color: var(--text-light);
}

.orders-table tbody tr:hover {
    background: #f5f5f5;
}

.order-number {
    color: var(--text-dark) !important;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
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

.status-badge.cancelled {
    background: #f8d7da;
    color: #721c24;
}

.btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--primary-color);
    color: var(--white);
    text-decoration: none;
    transition: var(--transition);
}

.btn-action:hover {
    background: #ff5fa0;
    transform: scale(1.1);
}

.empty-state {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 4rem 2rem;
    text-align: center;
    box-shadow: var(--shadow);
}

.empty-state i {
    font-size: 4rem;
    color: #ccc;
    margin-bottom: 1rem;
}

.empty-state h3 {
    color: var(--text-dark);
}

.empty-state p {
    color: var(--text-light);
    margin-bottom: 2rem;
}

@media (max-width: 768px) {
    .orders-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .orders-header .btn {
        width: 100%;
    }
    
    .orders-table-wrapper {
        overflow-x: auto;
    }
    
    .orders-table th,
    .orders-table td {
        padding: 0.75rem;
        font-size: 0.85rem;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
