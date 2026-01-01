<?php
require_once 'config.php';
require_once 'includes/functions.php';

$page_title = 'Daftar Member';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = cleanInput($_POST['name']);
    $email = cleanInput($_POST['email']);
    $phone = cleanInput($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $address = cleanInput($_POST['address']);
    
    // Validasi
    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        $error = 'Semua field wajib harus diisi';
    } elseif (!validateEmail($email)) {
        $error = 'Format email tidak valid';
    } elseif ($password !== $confirm_password) {
        $error = 'Password dan konfirmasi password tidak cocok';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter';
    } else {
        try {
            // Cek apakah email sudah terdaftar
            $existing = $db->fetchOne("SELECT id FROM members WHERE email = ?", [$email]);
            if ($existing) {
                $error = 'Email sudah terdaftar';
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert member
                $sql = "INSERT INTO members (name, email, phone, password, address) VALUES (?, ?, ?, ?, ?)";
                $db->query($sql, [$name, $email, $phone, $hashed_password, $address]);
                
                $success = 'Pendaftaran berhasil! Silakan login untuk melanjutkan.';
                // Redirect setelah 2 detik
                header("refresh:2;url=login_member.php");
            }
        } catch (Exception $e) {
            $error = 'Gagal mendaftar: ' . $e->getMessage();
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1 class="page-title">Daftar Member</h1>
        <p class="page-description">Daftar sekarang dan dapatkan keuntungan sebagai member</p>
    </div>
</section>

<!-- Register Form Section -->
<section class="register-section">
    <div class="container">
        <div class="register-container">
            <div class="register-form-wrapper">
                <h2>Form Pendaftaran</h2>
                <p>Isi form di bawah ini untuk menjadi member</p>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="register-form">
                    <div class="form-group">
                        <label for="name">Nama Lengkap *</label>
                        <input type="text" id="name" name="name" required 
                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                               placeholder="Masukkan nama lengkap">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                               placeholder="contoh@email.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Nomor Telepon *</label>
                        <input type="tel" id="phone" name="phone" required 
                               value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                               placeholder="081234567890">
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Alamat</label>
                        <textarea id="address" name="address" rows="3" 
                                  placeholder="Masukkan alamat lengkap"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Password *</label>
                            <input type="password" id="password" name="password" required 
                                   placeholder="Minimal 6 karakter">
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Konfirmasi Password *</label>
                            <input type="password" id="confirm_password" name="confirm_password" required 
                                   placeholder="Ulangi password">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-large">
                        <i class="fas fa-user-plus"></i> Daftar Sekarang
                    </button>
                </form>
                
                <div class="register-footer">
                    <p>Sudah punya akun? <a href="login_member.php">Login di sini</a></p>
                </div>
            </div>
            
            <div class="register-benefits">
                <h3>Keuntungan Menjadi Member</h3>
                <ul class="benefits-list">
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Diskon khusus untuk member</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Prioritas pelayanan</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Update produk terbaru</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Riwayat pemesanan</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Point reward program</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<style>
.register-section {
    padding: 4rem 0;
    background: var(--light-gray);
}

.register-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    max-width: 1000px;
    margin: 0 auto;
}

.register-form-wrapper {
    background: var(--white);
    padding: 3rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
}

.register-form-wrapper h2 {
    margin-bottom: 0.5rem;
    color: var(--text-dark);
}

.register-form-wrapper > p {
    margin-bottom: 2rem;
    color: var(--text-light);
}

.register-form {
    margin-top: 2rem;
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
    padding: 12px 16px;
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    font-family: inherit;
    font-size: 0.9rem;
    transition: var(--transition);
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-color);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.register-footer {
    margin-top: 2rem;
    text-align: center;
    padding-top: 2rem;
    border-top: 1px solid var(--border-color);
}

.register-footer a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
}

.register-footer a:hover {
    text-decoration: underline;
}

.register-benefits {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    padding: 3rem;
    border-radius: var(--border-radius);
    color: var(--white);
}

.register-benefits h3 {
    color: var(--white);
    margin-bottom: 2rem;
    font-size: 1.5rem;
}

.benefits-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.benefits-list li {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    font-size: 1.1rem;
}

.benefits-list li i {
    font-size: 1.5rem;
    color: var(--white);
}

@media (max-width: 768px) {
    .register-container {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>

