<?php
require_once 'config.php';
require_once 'includes/functions.php';

session_start();

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle tambah/hapus item
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_to_cart'])) {
        $product_id = (int)$_POST['product_id'];
        $qty = max(1, (int)$_POST['quantity']);
        // Ambil data produk
        $product = getProductById($product_id);
        if ($product) {
            // Jika sudah ada di keranjang, update qty
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $qty;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'id' => $product_id,
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'image' => $product['image'],
                    'quantity' => $qty
                ];
            }
        }
    }
    if (isset($_POST['remove_from_cart'])) {
        $product_id = (int)$_POST['product_id'];
        unset($_SESSION['cart'][$product_id]);
    }
    if (isset($_POST['clear_cart'])) {
        $_SESSION['cart'] = [];
    }
    if (isset($_POST['checkout'])) {
        // Validasi keranjang tidak kosong
        if (!empty($_SESSION['cart'])) {
            // Hitung total
            $checkout_total = 0;
            foreach ($_SESSION['cart'] as $item) {
                $checkout_total += $item['price'] * $item['quantity'];
            }
            // Simpan ke session order_data untuk checkout
            $_SESSION['order_data'] = [
                'items' => $_SESSION['cart'],
                'total' => $checkout_total
            ];
            header('Location: checkout.php');
            exit;
        }
    }
    header('Location: cart.php');
    exit;
}

// Hitung total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

$page_title = 'Keranjang Belanja';
?>
<?php include 'includes/header.php'; ?>

<section class="page-header">
    <div class="container">
        <h1><?php echo $page_title; ?></h1>
        <p>Review produk sebelum checkout</p>
    </div>
</section>

<section class="cart-section">
    <div class="container">
        <?php if (!empty($_SESSION['cart'])): ?>
            <form method="POST">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['cart'] as $item): ?>
                        <tr>
                            <td>
                                <img src="assets/img/<?php echo htmlspecialchars($item['image']); ?>" alt="" style="width:60px;height:60px;border-radius:8px;vertical-align:middle;"> 
                                <?php echo htmlspecialchars($item['name']); ?>
                            </td>
                            <td><?php echo formatPrice($item['price']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                            <td>
                                <button type="submit" name="remove_from_cart" value="1" class="btn btn-danger" onclick="this.form.product_id.value='<?php echo $item['id']; ?>'">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="cart-total">
                    <strong>Total: <?php echo formatPrice($total); ?></strong>
                </div>
                <div class="cart-actions">
                    <button type="submit" name="clear_cart" class="btn btn-outline">
                        <i class="fas fa-trash"></i> Kosongkan Keranjang
                    </button>
                    <button type="submit" name="checkout" class="btn btn-primary">
                        <i class="fas fa-arrow-right"></i> Checkout
                    </button>
                </div>
            </form>
        <?php else: ?>
            <div class="no-data">
                <i class="fas fa-shopping-cart"></i>
                <h3>Keranjang kosong</h3>
                <a href="products.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Belanja Produk
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
