<?php
require_once 'config.php';
require_once 'includes/functions.php';

if (!isMemberLoggedIn()) {
    redirect('login_member.php?redirect=member_dashboard.php');
}

$member_id = $_SESSION['member_id'];
$member = getMemberById($member_id);

// Get orders
$orders = $db->fetchAll(
    "SELECT * FROM orders WHERE member_id = ? ORDER BY created_at DESC",
    [$member_id]
);

// Get order count by status
$stats = [
    'pending' => 0,
    'confirmed' => 0,
    'completed' => 0,
    'cancelled' => 0
];

foreach ($orders as $order) {
    if (isset($stats[$order['status']])) {
        $stats[$order['status']]++;
    }
}

$page_title = 'Dashboard Member';
?>

<?php include 'includes/header.php'; ?>

<section class="member-dashboard">
    <div class="member-container">
        <!-- Sidebar -->
        <div class="member-sidebar">
            <div class="member-profile">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="profile-info">
                    <h3><?php echo htmlspecialchars($member['name']); ?></h3>
                    <p><?php echo htmlspecialchars($member['email']); ?></p>
                </div>
            </div>
            
            <nav class="member-menu">
                <a href="member_dashboard.php" class="menu-item active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="member_orders.php" class="menu-item">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Pesanan Saya</span>
                </a>
                <a href="member_profile.php" class="menu-item">
                    <i class="fas fa-user-circle"></i>
                    <span>Profil</span>
                </a>
                <a href="logout_member.php" class="menu-item logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="member-content">
            <!-- Header -->
            <div class="member-header">
                <div class="header-text">
                    <h1>Selamat Datang Kembali!</h1>
                    <p>Kelola pesanan dan akun Anda dengan mudah</p>
                </div>
                <div style="display:flex;gap:10px;">
                    <a href="products.php" class="btn btn-primary">
                        <i class="fas fa-shopping-bag"></i> Belanja Produk
                    </a>
                    <a href="cart.php" class="btn btn-secondary">
                        <i class="fas fa-shopping-cart"></i> Lihat Keranjang
                    </a>
                </div>
            </div>
            
            <!-- Stats -->
            <div class="stats-grid">
                <a href="member_orders.php?status=pending" class="stat-card" style="text-decoration: none; color: inherit;">
                    <div class="stat-icon pending">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $stats['pending']; ?></div>
                        <div class="stat-label">Menunggu Pembayaran</div>
                    </div>
                </a>
                
                <a href="member_orders.php?status=confirmed" class="stat-card" style="text-decoration: none; color: inherit;">
                    <div class="stat-icon confirmed">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $stats['confirmed']; ?></div>
                        <div class="stat-label">Dikonfirmasi</div>
                    </div>
                </a>
                
                <a href="member_orders.php?status=completed" class="stat-card" style="text-decoration: none; color: inherit;">
                    <div class="stat-icon completed">
                        <i class="fas fa-gift"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $stats['completed']; ?></div>
                        <div class="stat-label">Selesai</div>
                    </div>
                </a>
                
                <a href="member_orders.php" class="stat-card" style="text-decoration: none; color: inherit;">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo count($orders); ?></div>
                        <div class="stat-label">Total Pesanan</div>
                    </div>
                </a>
                
                <a href="member_orders.php" class="stat-card" style="text-decoration: none; color: inherit;">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo count($orders); ?></div>
                        <div class="stat-label">Total Pesanan</div>
                    </div>
                </a>
            </div>
            
            <!-- Recent Orders -->
            <div class="recent-section">
                <div class="section-header">
                    <h2>Pesanan Terbaru</h2>
                    <a href="member_orders.php" class="link-more">
                        Lihat Semua <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <?php if (!empty($orders)): ?>
                    <div class="orders-list">
                        <?php foreach (array_slice($orders, 0, 5) as $order): ?>
                            <div class="order-card">
                                <div class="order-header">
                                    <div>
                                        <h3><?php echo htmlspecialchars($order['order_number']); ?></h3>
                                        <p class="order-date"><?php echo date('d M Y, H:i', strtotime($order['created_at'])); ?></p>
                                    </div>
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
                                            default:
                                                echo ucfirst($order['status']);
                                        }
                                        ?>
                                    </span>
                                </div>
                                
                                <div class="order-body">
                                    <p class="order-info"><strong>Penerima:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                                    <p class="order-info"><strong>Alamat:</strong> <?php 
                                        $address = $order['customer_address'] ?? '';
                                        if ($address) {
                                            echo htmlspecialchars(substr($address, 0, 50)) . (strlen($address) > 50 ? '...' : '');
                                        } else {
                                            echo '<em>Alamat tidak tersedia</em>';
                                        }
                                    ?></p>
                                </div>
                                
                                <div class="order-footer">
                                    <div class="order-amount">
                                        <span>Total:</span>
                                        <strong><?php echo formatPrice($order['total_amount']); ?></strong>
                                    </div>
                                    <a href="member_order_detail.php?id=<?php echo $order['id']; ?>" class="btn btn-small">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-shopping-bag"></i>
                        <h3>Belum Ada Pesanan</h3>
                        <p>Mulai berbelanja produk favorit Anda sekarang</p>
                        <a href="products.php" class="btn btn-primary">
                            <i class="fas fa-shopping-bag"></i> Belanja Sekarang
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
.member-dashboard {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 2rem 0;
}

.member-container {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 2rem;
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Sidebar */
.member-sidebar {
    background: var(--white);
    border-radius: var(--border-radius);
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    padding: 2rem;
    height: fit-content;
    position: sticky;
    top: 2rem;
}

.member-profile {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid var(--border-color);
}

.profile-avatar {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--primary-color), #ff9ccc);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: 2rem;
    margin-bottom: 1rem;
}

.profile-info h3 {
    margin: 0 0 0.25rem 0;
    color: var(--text-dark);
}

.profile-info p {
    margin: 0;
    color: var(--text-light);
    font-size: 0.85rem;
}

.member-menu {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.menu-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    color: var(--text-light);
    text-decoration: none;
    border-radius: var(--border-radius);
    transition: var(--transition);
}

.menu-item:hover,
.menu-item.active {
    background: var(--light-gray);
    color: var(--primary-color);
}

.menu-item.logout {
    color: #f44336;
}

.menu-item.logout:hover {
    background: #ffebee;
}

/* Main Content */
.member-content {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.member-header {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 3rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.header-text h1 {
    margin: 0 0 0.5rem 0;
    color: var(--text-dark);
}

.header-text p {
    margin: 0;
    color: var(--text-light);
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.stat-card {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 2rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    transition: var(--transition);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 50px rgba(0,0,0,0.15);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--white);
    background: linear-gradient(135deg, var(--primary-color), #ff9ccc);
    flex-shrink: 0;
}

.stat-icon.pending {
    background: linear-gradient(135deg, #ffc107, #ff9800);
}

.stat-icon.confirmed {
    background: linear-gradient(135deg, #2196f3, #00bcd4);
}

.stat-icon.completed {
    background: linear-gradient(135deg, #4caf50, #8bc34a);
}

.stat-value {
    font-size: 1.8rem;
    font-weight: bold;
    color: var(--text-dark);
}

.stat-label {
    color: var(--text-light);
    font-size: 0.85rem;
}

/* Recent Orders */
.recent-section {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 2rem;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.section-header h2 {
    margin: 0;
    color: var(--text-dark);
}

.link-more {
    color: var(--primary-color);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: var(--transition);
}

.link-more:hover {
    gap: 1rem;
}

.orders-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.order-card {
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    transition: var(--transition);
}

.order-card:hover {
    border-color: var(--primary-color);
    box-shadow: 0 5px 20px rgba(255, 105, 150, 0.1);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.order-header h3 {
    margin: 0 0 0.25rem 0;
    color: var(--text-dark);
    font-size: 1rem;
}

.order-date {
    margin: 0;
    color: var(--text-light);
    font-size: 0.85rem;
}

.status-badge {
    display: inline-block;
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

.order-body {
    margin-bottom: 1rem;
    padding: 1rem 0;
    border-top: 1px solid var(--border-color);
    border-bottom: 1px solid var(--border-color);
}

.order-info {
    margin: 0.25rem 0;
    color: var(--text-light);
    font-size: 0.9rem;
}

.order-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.order-amount {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.order-amount span {
    color: var(--text-light);
    font-size: 0.85rem;
}

.order-amount strong {
    color: var(--primary-color);
    font-size: 1.2rem;
}

.btn-small {
    padding: 0.5rem 1.5rem !important;
    font-size: 0.85rem !important;
}

.empty-state {
    text-align: center;
    padding: 3rem 2rem;
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
    .member-container {
        grid-template-columns: 1fr;
    }
    
    .member-sidebar {
        position: relative;
        top: 0;
    }
    
    .member-header {
        flex-direction: column;
        gap: 2rem;
        text-align: center;
    }
    
    .member-header .btn {
        width: 100%;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .order-footer {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .order-footer .btn-small {
        width: 100%;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
