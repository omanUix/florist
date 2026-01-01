<?php
require_once 'config.php';
require_once 'includes/functions.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header('Location: products.php');
    exit;
}

$product = getProductById($product_id);

if (!$product) {
    header('Location: products.php');
    exit;
}

$page_title = htmlspecialchars($product['name']);
?>

<?php include 'includes/header.php'; ?>

<section class="page-header">
    <div class="container">
        <h1><?php echo $page_title; ?></h1>
        <p>Detail Produk</p>
    </div>
</section>

<section class="product-detail">
    <div class="container">
        <div class="detail-grid">
            <!-- Gambar Produk -->
            <div class="detail-image">
                <?php if ($product['image']): ?>
                    <img src="assets/img/<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>" class="main-image">
                <?php else: ?>
                    <div class="no-image">
                        <i class="fas fa-image"></i>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Info Produk -->
            <div class="detail-info">
                <h2 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h2>
                
                <div class="product-category">
                    <span class="badge"><?php echo htmlspecialchars($product['category_name']); ?></span>
                </div>

                <div class="product-price">
                    <span class="price"><?php echo formatPrice($product['price']); ?></span>
                </div>

                <div class="product-description">
                    <h3>Deskripsi Produk</h3>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>

                <div class="quantity-selector">
                    <label for="quantity">Jumlah:</label>
                    <div class="qty-control">
                        <button class="qty-btn" onclick="decreaseQty()">-</button>
                        <input type="number" id="quantity" name="quantity" min="1" value="1">
                        <button class="qty-btn" onclick="increaseQty()">+</button>
                    </div>
                </div>

                <div class="order-total">
                    <p>Total: <span id="total-price"><?php echo formatPrice($product['price']); ?></span></p>
                </div>

                <div class="action-buttons">
                    <a href="products.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <form method="POST" action="cart.php" style="display: inline;">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="hidden" id="qty-input" name="quantity" value="1">
                        <button type="submit" name="add_to_cart" class="btn btn-primary">
                            <i class="fas fa-shopping-cart"></i> Tambah ke Keranjang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.product-detail {
    padding: 4rem 0;
    background: var(--light-gray);
}

.detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: start;
}

.detail-image {
    background: var(--white);
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--shadow);
}

.detail-image img {
    width: 100%;
    height: auto;
    display: block;
}

.no-image {
    width: 100%;
    height: 400px;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    color: #ccc;
}

.detail-info {
    background: var(--white);
    padding: 2rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
}

.product-name {
    font-size: 2rem;
    color: var(--text-dark);
    margin-bottom: 1rem;
}

.product-category {
    margin-bottom: 1.5rem;
}

.badge {
    display: inline-block;
    background: var(--primary-color);
    color: var(--white);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
}

.product-price {
    margin-bottom: 2rem;
}

.price {
    font-size: 2rem;
    font-weight: bold;
    color: var(--primary-color);
}

.product-description {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid var(--border-color);
}

.product-description h3 {
    color: var(--text-dark);
    margin-bottom: 1rem;
}

.product-description p {
    color: var(--text-light);
    line-height: 1.6;
}

.quantity-selector {
    margin-bottom: 1.5rem;
}

.quantity-selector label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: var(--text-dark);
}

.qty-control {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    width: fit-content;
}

.qty-btn {
    background: var(--light-gray);
    border: 1px solid var(--border-color);
    width: 40px;
    height: 40px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1.2rem;
    transition: var(--transition);
}

.qty-btn:hover {
    background: var(--primary-color);
    color: var(--white);
    border-color: var(--primary-color);
}

#quantity {
    width: 60px;
    text-align: center;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    padding: 0.5rem;
    font-size: 1rem;
}

.order-total {
    background: var(--light-gray);
    padding: 1rem;
    border-radius: var(--border-radius);
    margin-bottom: 2rem;
    text-align: right;
}

.order-total p {
    color: var(--text-dark);
    margin: 0;
    font-size: 1.2rem;
}

.order-total span {
    font-weight: bold;
    color: var(--primary-color);
    font-size: 1.5rem;
}

.action-buttons {
    display: flex;
    gap: 1rem;
}

.action-buttons .btn {
    flex: 1;
    padding: 1rem;
    text-align: center;
}

@media (max-width: 768px) {
    .detail-grid {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .product-name {
        font-size: 1.5rem;
    }
    
    .price {
        font-size: 1.5rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>

<script>
const productPrice = <?php echo $product['price']; ?>;

function increaseQty() {
    const qty = document.getElementById('quantity');
    qty.value = parseInt(qty.value) + 1;
    updateTotal();
}

function decreaseQty() {
    const qty = document.getElementById('quantity');
    if (parseInt(qty.value) > 1) {
        qty.value = parseInt(qty.value) - 1;
        updateTotal();
    }
}

function updateTotal() {
    const qty = document.getElementById('quantity').value;
    const total = productPrice * qty;
    document.getElementById('qty-input').value = qty;
    document.getElementById('total-price').textContent = formatRupiah(total);
}

function formatRupiah(amount) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.floor(amount));
}

// Update total saat quantity input berubah
document.getElementById('quantity').addEventListener('change', updateTotal);
</script>

<?php include 'includes/footer.php'; ?>
