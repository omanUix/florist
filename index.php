<?php
require_once 'config.php';

$page_title = 'Beranda';

// Cek apakah database sudah ada
$database_exists = false;
$featured_products = [];
$categories = [];
$slides = [];

try {
    require_once 'includes/functions.php';
    $featured_products = getAllProducts(6);
    $categories = getAllCategories();
    $slides = getAllSlides(true); // Hanya ambil slides yang aktif
    $database_exists = true;
} catch (Exception $e) {
    // Database belum ada atau error
    $database_exists = false;
}
?>


<?php include 'includes/header.php'; ?>

<!-- Slides Carousel Section -->
<?php if ($database_exists && !empty($slides)): ?>
    <section class="slides-carousel-section">
        <div class="container">
            <div class="slides-carousel-wrapper">
                <div class="slides-carousel" id="slidesCarousel">
                    <?php foreach ($slides as $index => $slide): ?>
                        <div class="slide-item <?php echo $index === 0 ? 'active' : ''; ?>" 
                             style="background-image: url('<?php echo SITE_URL; ?>/<?php echo htmlspecialchars($slide['image']); ?>');">
                            <div class="slide-overlay"></div>
                            <div class="slide-content">
                                <div class="slide-text">
                                    <?php if (!empty($slide['subtitle'])): ?>
                                        <p class="slide-subtitle"><?php echo htmlspecialchars($slide['subtitle']); ?></p>
                                    <?php endif; ?>
                                    <h2 class="slide-title"><?php echo htmlspecialchars($slide['title']); ?></h2>
                                    <?php if (!empty($slide['link'])): ?>
                                        <a href="<?php echo htmlspecialchars($slide['link']); ?>" class="btn btn-primary slide-btn">
                                            <?php echo htmlspecialchars($slide['button_text'] ?? 'Lihat Detail'); ?>
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Carousel Controls -->
                <?php if (count($slides) > 1): ?>
                    <button class="carousel-btn carousel-prev" onclick="changeSlide(-1)">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="carousel-btn carousel-next" onclick="changeSlide(1)">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    
                    <!-- Carousel Indicators -->
                    <div class="carousel-indicators">
                        <?php foreach ($slides as $index => $slide): ?>
                            <span class="indicator <?php echo $index === 0 ? 'active' : ''; ?>" 
                                  onclick="goToSlide(<?php echo $index; ?>)"></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php else: ?>
    <!-- Fallback Hero Section jika tidak ada slides -->
    <section class="hero-urban">
        <div class="container">
            <div class="hero-urban-content">
                <h1 class="hero-urban-title">
                    Florist & Kerajinan<br>
                    <span class="hero-urban-subtitle">Koleksi Bunga & Kerajinan Eksklusif</span>
                </h1>
                <p class="hero-urban-description">
                    Temukan keindahan dalam setiap detail dengan koleksi florist dan kerajinan tangan dari bulu kawat yang unik dan estetik. Produk dibuat dengan cinta dan perhatian khusus.
                </p>
                <div class="hero-urban-buttons">
                    <a href="#featured" class="btn btn-primary">Lihat Koleksi</a>
                    <a href="products.php" class="btn btn-outline">Shop All Produk</a>
                </div>
            </div>
        </div>
        <div class="hero-urban-overlay"></div>
    </section>
<?php endif; ?>

<style>
/* Slides Carousel Section */
.slides-carousel-section {
    padding: 2rem 0;
    margin-bottom: 2rem;
}

.slides-carousel-wrapper {
    position: relative;
    width: 100%;
    height: 500px;
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--shadow-hover);
    margin: 0 auto;
}

.slides-carousel {
    position: relative;
    width: 100%;
    height: 100%;
}

.slide-item {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    opacity: 0;
    transition: opacity 0.8s ease-in-out;
    display: flex;
    align-items: center;
    justify-content: center;
}

.slide-item.active {
    opacity: 1;
    z-index: 1;
}

.slide-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.2) 100%);
    z-index: 1;
}

.slide-content {
    position: relative;
    z-index: 2;
    max-width: 1200px;
    width: 100%;
    padding: 0 40px;
    color: #fff;
}

.slide-text {
    max-width: 600px;
}

.slide-subtitle {
    font-size: 1.1rem;
    margin-bottom: 1rem;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 400;
}

.slide-title {
    font-size: 3rem;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 1.5rem;
    color: #fff;
    text-shadow: 0 2px 16px rgba(0,0,0,0.3);
}

.slide-btn {
    font-size: 1.1rem;
    padding: 14px 28px;
    background: linear-gradient(135deg, var(--primary-color), #ff8fab);
    color: #fff;
    border: none;
    border-radius: var(--border-radius);
    cursor: pointer;
    transition: var(--transition);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 4px 15px rgba(255, 107, 157, 0.4);
}

.slide-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 107, 157, 0.6);
}

.carousel-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.9);
    border: none;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    cursor: pointer;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: var(--text-dark);
    transition: var(--transition);
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

.carousel-btn:hover {
    background: var(--white);
    transform: translateY(-50%) scale(1.1);
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
}

.carousel-prev {
    left: 20px;
}

.carousel-next {
    right: 20px;
}

.carousel-indicators {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 10px;
    z-index: 10;
}

.indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.5);
    cursor: pointer;
    transition: var(--transition);
}

.indicator.active {
    background: var(--white);
    width: 30px;
    border-radius: 6px;
}

/* Fallback Hero Section */
.hero-urban {
    position: relative;
    min-height: 480px;
    background: url('assets/img/hero-florist.jpg') center/cover no-repeat;
    display: flex;
    align-items: center;
    margin-bottom: 2rem;
}

.hero-urban-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(30, 30, 30, 0.25);
    z-index: 1;
}

.hero-urban-content {
    position: relative;
    z-index: 2;
    max-width: 600px;
    padding: 48px 0;
    color: #fff;
}

.hero-urban-title {
    font-size: 3rem;
    font-weight: 700;
    line-height: 1.1;
    margin-bottom: 1rem;
    text-shadow: 0 2px 16px rgba(0,0,0,0.25);
    color: #fff;
}

.hero-urban-subtitle {
    font-size: 2rem;
    font-weight: 400;
}

.hero-urban-description {
    font-size: 1.25rem;
    margin-bottom: 2rem;
    text-shadow: 0 2px 8px rgba(0,0,0,0.15);
    color: #fff;
}

.hero-urban-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.hero-urban-buttons .btn {
    font-size: 1.1rem;
    min-width: 180px;
}

@media (max-width: 768px) {
    .slides-carousel-section {
        padding: 1rem 0;
    }
    
    .slides-carousel-wrapper {
        height: 400px;
    }
    
    .slide-title {
        font-size: 2rem;
    }
    
    .slide-subtitle {
        font-size: 0.95rem;
    }
    
    .slide-content {
        padding: 0 20px;
    }
    
    .carousel-btn {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .carousel-prev {
        left: 10px;
    }
    
    .carousel-next {
        right: 10px;
    }
    
    .hero-urban {
        min-height: 400px;
    }
    
    .hero-urban-title {
        font-size: 2rem;
    }
    
    .hero-urban-subtitle {
        font-size: 1.5rem;
    }
    
    .hero-urban-description {
        font-size: 1rem;
    }
    
    .hero-urban-buttons {
        flex-direction: column;
    }
    
    .hero-urban-buttons .btn {
        width: 100%;
        min-width: auto;
    }
}
</style>

<script>
let currentSlide = 0;
const slides = document.querySelectorAll('.slide-item');
const indicators = document.querySelectorAll('.indicator');
const totalSlides = slides.length;

function showSlide(index) {
    // Remove active class from all slides and indicators
    slides.forEach(slide => slide.classList.remove('active'));
    indicators.forEach(indicator => indicator.classList.remove('active'));
    
    // Add active class to current slide and indicator
    if (slides[index]) {
        slides[index].classList.add('active');
    }
    if (indicators[index]) {
        indicators[index].classList.add('active');
    }
}

function changeSlide(direction) {
    currentSlide += direction;
    
    if (currentSlide >= totalSlides) {
        currentSlide = 0;
    } else if (currentSlide < 0) {
        currentSlide = totalSlides - 1;
    }
    
    showSlide(currentSlide);
}

function goToSlide(index) {
    currentSlide = index;
    showSlide(currentSlide);
}

// Auto-play carousel
if (totalSlides > 1) {
    setInterval(() => {
        changeSlide(1);
    }, 5000); // Change slide every 5 seconds
}
</script>

<!-- Categories Section -->
<section class="categories">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Kategori Produk</h2>
            <p class="section-description">Pilih kategori yang sesuai dengan kebutuhan Anda</p>
        </div>
        
        <div class="categories-grid">
            <?php if (!$database_exists): ?>
                <div class="no-data" style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
                    <i class="fas fa-database" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                    <h3>Database Belum Dikonfigurasi</h3>
                    <p>Silakan jalankan instalasi database terlebih dahulu</p>
                    <a href="install.php" class="btn btn-primary" style="margin-top: 1rem;">
                        <i class="fas fa-cog"></i> Jalankan Instalasi
                    </a>
                </div>
            <?php elseif (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <div class="category-card">
                        <div class="category-icon">
                            <?php if (!empty($category['icon'])): ?>
                                <img src="<?php echo $category['icon']; ?>" alt="icon" style="width:56px;height:56px;object-fit:cover;border-radius:50%;background:#eee;">
                            <?php else: ?>
                                <i class="fas fa-tag"></i>
                            <?php endif; ?>
                        </div>
                        <h3 class="category-name"><?php echo htmlspecialchars($category['name']); ?></h3>
                        <a href="products.php?category=<?php echo $category['id']; ?>" class="category-link">
                            Lihat Produk <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-info-circle"></i>
                    <p>Belum ada kategori produk</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section id="featured" class="featured-products">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Produk Terlaris</h2>
            <p class="section-description">Koleksi terbaik dari produk kami</p>
        </div>
        
        <div class="products-grid">
            <?php if (!$database_exists): ?>
                <div class="no-data" style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
                    <i class="fas fa-shopping-bag" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                    <h3>Database Belum Dikonfigurasi</h3>
                    <p>Silakan jalankan instalasi database terlebih dahulu untuk melihat produk</p>
                    <a href="install.php" class="btn btn-primary" style="margin-top: 1rem;">
                        <i class="fas fa-cog"></i> Jalankan Instalasi
                    </a>
                </div>
            <?php elseif (!empty($featured_products)): ?>
                <?php foreach ($featured_products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if ($product['image']): ?>
                                <img src="<?php echo SITE_URL; ?>/assets/img/<?php echo htmlspecialchars($product['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     loading="lazy">
                            <?php else: ?>
                                <div class="no-image">
                                    <i class="fas fa-image"></i>
                                </div>
                            <?php endif; ?>
                            <div class="product-overlay">
                                <a href="detail.php?id=<?php echo $product['id']; ?>" class="btn btn-outline">
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
                                <?php if (isMemberLoggedIn()): ?>
                                    <form method="post" action="cart.php" style="display:inline;">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" name="add_to_cart" class="btn btn-primary btn-cart">
                                            <i class="fas fa-shopping-cart"></i> Tambah ke Keranjang
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <a href="login_member.php" class="btn btn-primary btn-cart">
                                        <i class="fas fa-shopping-cart"></i> Login untuk Keranjang
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-shopping-bag"></i>
                    <p>Belum ada produk yang tersedia</p>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if ($database_exists): ?>
        <div class="section-footer">
            <a href="products.php" class="btn btn-outline">
                <i class="fas fa-arrow-right"></i> Lihat Semua Produk
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- About Section -->
<section class="about">
    <div class="container">
        <div class="about-content">
            <div class="about-text">
                <h2 class="section-title">Tentang Kami</h2>
                <p class="about-description">
                    Kami adalah pengrajin yang berdedikasi untuk menciptakan produk florist dan kerajinan tangan 
                    dari bulu kawat yang unik dan berkualitas tinggi. Setiap produk dibuat dengan tangan dengan 
                    perhatian khusus pada detail dan estetika.
                </p>
                <div class="about-features">
                    <div class="feature">
                        <i class="fas fa-heart"></i>
                        <span>Dibuat dengan Cinta</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-star"></i>
                        <span>Kualitas Terbaik</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-shipping-fast"></i>
                        <span>Pengiriman Cepat</span>
                    </div>
                </div>
            </div>
            <div class="about-image">
                <div class="about-decoration">
                    <i class="fas fa-seedling"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
