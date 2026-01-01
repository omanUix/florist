<?php
require_once 'config.php';
require_once 'includes/functions.php';

// Ambil ID produk dari URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header('Location: products.php');
    exit();
}

// Ambil data produk
$product = getProductById($product_id);

if (!$product) {
    header('Location: products.php');
    exit();
}

$page_title = $product['name'];
?>

<?php include 'includes/header.php'; ?>

<!-- Product Detail Section -->
<section class="product-detail">
    <div class="container">
        <div class="breadcrumb">
            <a href="index.php">Beranda</a>
            <span>/</span>
            <a href="products.php">Produk</a>
            <span>/</span>
            <span><?php echo htmlspecialchars($product['name']); ?></span>
        </div>
        
        <div class="product-detail-content">
            <div class="product-images">
                <div class="main-image">
                    <?php if ($product['image']): ?>
                        <img src="assets/img/<?php echo htmlspecialchars($product['image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             id="main-product-image">
                    <?php else: ?>
                        <div class="no-image">
                            <i class="fas fa-image"></i>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="product-info">
                <div class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></div>
                <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                <div class="product-price"><?php echo formatPrice($product['price']); ?></div>
                
                <div class="product-description">
                    <h3>Deskripsi Produk</h3>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>
                
                <div class="product-actions">
                    <a href="https://wa.me/6281234567890?text=Halo, saya mau pesan <?php echo urlencode($product['name']); ?> seharga <?php echo urlencode(formatPrice($product['price'])); ?>" 
                       class="btn btn-whatsapp btn-large" target="_blank">
                        <i class="fab fa-whatsapp"></i> Pesan via WhatsApp
                    </a>
                    <a href="products.php" class="btn btn-outline btn-large">
                        <i class="fas fa-arrow-left"></i> Kembali ke Produk
                    </a>
                </div>
                
                <div class="product-features">
                    <div class="feature">
                        <i class="fas fa-shipping-fast"></i>
                        <span>Pengiriman Cepat</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-shield-alt"></i>
                        <span>Kualitas Terjamin</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-heart"></i>
                        <span>Dibuat dengan Cinta</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Products Section -->
<section class="related-products">
    <div class="container">
        <h2 class="section-title">Produk Terkait</h2>
        
        <?php
        // Ambil produk dari kategori yang sama
        $related_products = getProductsByCategory($product['category_id']);
        // Hapus produk yang sedang dilihat
        $related_products = array_filter($related_products, function($p) use ($product_id) {
            return $p['id'] != $product_id;
        });
        // Ambil maksimal 4 produk
        $related_products = array_slice($related_products, 0, 4);
        ?>
        
        <?php if (!empty($related_products)): ?>
            <div class="products-grid">
                <?php foreach ($related_products as $related_product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if ($related_product['image']): ?>
                                <img src="assets/img/<?php echo htmlspecialchars($related_product['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($related_product['name']); ?>"
                                     loading="lazy">
                            <?php else: ?>
                                <div class="no-image">
                                    <i class="fas fa-image"></i>
                                </div>
                            <?php endif; ?>
                            <div class="product-overlay">
                                <a href="detail.php?id=<?php echo $related_product['id']; ?>" class="btn btn-outline">
                                    <i class="fas fa-eye"></i> Lihat Detail
                                </a>
                            </div>
                        </div>
                        <div class="product-info">
                            <div class="product-category"><?php echo htmlspecialchars($related_product['category_name']); ?></div>
                            <h3 class="product-name"><?php echo htmlspecialchars($related_product['name']); ?></h3>
                            <p class="product-description"><?php echo htmlspecialchars(substr($related_product['description'], 0, 80)) . '...'; ?></p>
                            <div class="product-footer">
                                <span class="product-price"><?php echo formatPrice($related_product['price']); ?></span>
                                <a href="https://wa.me/6281234567890?text=Halo, saya mau pesan <?php echo urlencode($related_product['name']); ?> seharga <?php echo urlencode(formatPrice($related_product['price'])); ?>" 
                                   class="btn btn-whatsapp" target="_blank">
                                    <i class="fab fa-whatsapp"></i> Pesan
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-data">
                <i class="fas fa-info-circle"></i>
                <p>Tidak ada produk terkait</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
