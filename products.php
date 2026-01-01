<?php
require_once 'config.php';
require_once 'includes/functions.php';

$page_title = 'Produk';

// Ambil parameter pencarian dan kategori
$search = isset($_GET['search']) ? cleanInput($_GET['search']) : '';
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Ambil produk berdasarkan filter
if (!empty($search)) {
    $products = searchProducts($search);
    $page_title = "Hasil Pencarian: $search";
} elseif ($category_id > 0) {
    $products = getProductsByCategory($category_id);
    $category = getCategoryById($category_id);
    $page_title = "Kategori: " . ($category ? $category['name'] : 'Tidak Ditemukan');
} else {
    $products = getAllProducts();
}

$categories = getAllCategories();
?>

<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1 class="page-title"><?php echo $page_title; ?></h1>
        <p class="page-description">Temukan produk yang sesuai dengan kebutuhan Anda</p>
    </div>
</section>

<!-- Filters Section -->
<section class="filters">
    <div class="container">
        <div class="filters-content">
            <!-- Search Form -->
            <form class="search-form" method="GET">
                <div class="search-input-group">
                    <input type="text" name="search" placeholder="Cari produk..." 
                           value="<?php echo htmlspecialchars($search); ?>" class="search-input">
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
            
            <!-- Category Filter -->
            <div class="category-filter">
                <a href="products.php" class="filter-btn <?php echo $category_id == 0 ? 'active' : ''; ?>">
                    Semua Kategori
                </a>
                <?php foreach ($categories as $category): ?>
                    <a href="products.php?category=<?php echo $category['id']; ?>" 
                       class="filter-btn <?php echo $category_id == $category['id'] ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Products Section -->
<section class="products">
    <div class="container">
        <?php if (!empty($products)): ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if ($product['image']): ?>
                                <img src="assets/img/<?php echo htmlspecialchars($product['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     loading="lazy">
                            <?php else: ?>
                                <div class="no-image">
                                    <i class="fas fa-image"></i>
                                </div>
                            <?php endif; ?>
                            <div class="product-overlay">
                                <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn btn-outline">
                                    <i class="fas fa-eye"></i> Lihat Detail
                                </a>
                            </div>
                        </div>
                        <div class="product-info">
                            <div class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></div>
                            <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-description"><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p>
                            <div class="product-footer">
                                <span class="product-price"><?php echo formatPrice($product['price']); ?></span>
                                <form method="post" action="cart.php" style="display:inline;">
                                    <?php if (isMemberLoggedIn()): ?>
                                        <form method="post" action="cart.php" style="display:inline;">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <input type="hidden" name="action" value="add">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-shopping-cart"></i> Tambah ke Keranjang
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <a href="login_member.php" class="btn btn-primary">
                                            <i class="fas fa-shopping-cart"></i> Login untuk Keranjang
                                        </a>
                                    <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-data">
                <i class="fas fa-search"></i>
                <h3>Tidak ada produk ditemukan</h3>
                <p>
                    <?php if (!empty($search)): ?>
                        Tidak ada produk yang sesuai dengan pencarian "<?php echo htmlspecialchars($search); ?>"
                    <?php elseif ($category_id > 0): ?>
                        Belum ada produk dalam kategori ini
                    <?php else: ?>
                        Belum ada produk yang tersedia
                    <?php endif; ?>
                </p>
                <a href="products.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Semua Produk
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
