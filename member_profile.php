<?php
require_once 'config.php';
require_once 'includes/functions.php';

if (!isMemberLoggedIn()) {
    redirect('login_member.php?redirect=member_profile.php');
}

$member_id = $_SESSION['member_id'];
$member = getMemberById($member_id);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = cleanInput($_POST['name']);
    $phone = cleanInput($_POST['phone']);
    $address = cleanInput($_POST['address']);
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    if (empty($name) || empty($phone)) {
        $error = 'Nama dan nomor telepon harus diisi';
    } else {
        try {
            if (!empty($password)) {
                if ($password !== $password_confirm) {
                    $error = 'Password tidak cocok';
                } elseif (strlen($password) < 6) {
                    $error = 'Password minimal 6 karakter';
                } else {
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $db->query(
                        "UPDATE members SET name = ?, phone = ?, address = ?, password = ? WHERE id = ?",
                        [$name, $phone, $address, $hashed, $member_id]
                    );
                    $success = 'Profil berhasil diperbarui';
                }
            } else {
                $db->query(
                    "UPDATE members SET name = ?, phone = ?, address = ? WHERE id = ?",
                    [$name, $phone, $address, $member_id]
                );
                $success = 'Profil berhasil diperbarui';
            }
            
            if (!$error) {
                $_SESSION['member_name'] = $name;
                $member = getMemberById($member_id);
            }
        } catch (Exception $e) {
            $error = 'Gagal menyimpan profil: ' . $e->getMessage();
        }
    }
}

$page_title = 'Profil Saya';
?>

<?php include 'includes/header.php'; ?>

<section class="profile-section">
    <div class="container">
        <div class="profile-grid">
            <!-- Sidebar -->
            <div class="profile-sidebar">
                <div class="profile-card">
                    <div class="profile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <h2><?php echo htmlspecialchars($member['name']); ?></h2>
                    <p><?php echo htmlspecialchars($member['email']); ?></p>
                    <div class="status-indicator">
                        <i class="fas fa-check-circle"></i>
                        <span><?php echo $member['status'] == 'active' ? 'Aktif' : 'Tidak Aktif'; ?></span>
                    </div>
                </div>
                
                <nav class="profile-menu">
                    <a href="member_dashboard.php" class="menu-link">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="member_orders.php" class="menu-link">
                        <i class="fas fa-shopping-bag"></i> Pesanan
                    </a>
                    <a href="member_profile.php" class="menu-link active">
                        <i class="fas fa-user-circle"></i> Profil
                    </a>
                    <a href="logout_member.php" class="menu-link logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="profile-content">
                <h2>Edit Profil</h2>
                
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
                
                <form method="POST" class="profile-form">
                    <div class="form-section">
                        <h3>Informasi Dasar</h3>
                        
                        <div class="form-group">
                            <label for="email">Email (Tidak bisa diubah)</label>
                            <input type="email" id="email" value="<?php echo htmlspecialchars($member['email']); ?>" disabled>
                        </div>
                        
                        <div class="form-group">
                            <label for="name">Nama Lengkap *</label>
                            <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($member['name']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Nomor Telepon *</label>
                            <input type="tel" id="phone" name="phone" required value="<?php echo htmlspecialchars($member['phone']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Alamat</label>
                            <textarea id="address" name="address" rows="4"><?php echo htmlspecialchars($member['address']); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Ubah Password (Opsional)</h3>
                        <p class="section-hint">Kosongkan jika tidak ingin mengubah password</p>
                        
                        <div class="form-group">
                            <label for="password">Password Baru</label>
                            <input type="password" id="password" name="password">
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirm">Konfirmasi Password</label>
                            <input type="password" id="password_confirm" name="password_confirm">
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <a href="member_dashboard.php" class="btn btn-outline">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
                
                <div class="additional-info">
                    <h3>Informasi Akun</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Tanggal Daftar:</span>
                            <span class="info-value"><?php echo date('d M Y', strtotime($member['created_at'])); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Status Akun:</span>
                            <span class="info-value">
                                <span class="badge <?php echo $member['status']; ?>">
                                    <?php echo ucfirst($member['status']); ?>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.profile-section {
    padding: 4rem 0;
    background: var(--light-gray);
    min-height: 80vh;
}

.profile-grid {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 2rem;
}

.profile-sidebar {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.profile-card {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 2rem;
    text-align: center;
    box-shadow: var(--shadow);
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
    margin: 0 auto 1rem;
}

.profile-card h2 {
    margin: 0 0 0.25rem 0;
    color: var(--text-dark);
}

.profile-card p {
    margin: 0 0 1rem 0;
    color: var(--text-light);
    font-size: 0.9rem;
}

.status-indicator {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: #d4edda;
    color: #155724;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.profile-menu {
    background: var(--white);
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--shadow);
}

.menu-link {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.5rem;
    color: var(--text-light);
    text-decoration: none;
    transition: var(--transition);
    border-left: 3px solid transparent;
}

.menu-link:hover,
.menu-link.active {
    background: var(--light-gray);
    color: var(--primary-color);
    border-left-color: var(--primary-color);
}

.menu-link.logout {
    color: #f44336;
}

.menu-link.logout:hover {
    background: #ffebee;
}

.profile-content {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 2rem;
    box-shadow: var(--shadow);
}

.profile-content h2 {
    margin-bottom: 2rem;
    color: var(--text-dark);
}

.alert {
    padding: 1rem;
    border-radius: var(--border-radius);
    margin-bottom: 2rem;
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}

.alert-error {
    background: #ffebee;
    border-left: 4px solid #f44336;
    color: #c62828;
}

.alert-success {
    background: #d4edda;
    border-left: 4px solid #4caf50;
    color: #155724;
}

.form-section {
    margin-bottom: 2.5rem;
    padding-bottom: 2.5rem;
    border-bottom: 1px solid var(--border-color);
}

.form-section:last-of-type {
    border-bottom: none;
}

.form-section h3 {
    color: var(--text-dark);
    margin-bottom: 1rem;
}

.section-hint {
    color: var(--text-light);
    font-size: 0.85rem;
    margin-bottom: 1rem;
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
    padding: 0.75rem;
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    font-family: inherit;
    font-size: 0.9rem;
}

.form-group input:disabled {
    background: var(--light-gray);
    color: var(--text-light);
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(255, 105, 150, 0.1);
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin: 2rem 0 0 0;
}

.form-actions .btn {
    flex: 1;
}

.additional-info {
    margin-top: 2.5rem;
    padding-top: 2.5rem;
    border-top: 1px solid var(--border-color);
}

.additional-info h3 {
    color: var(--text-dark);
    margin-bottom: 1.5rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.info-label {
    font-weight: 500;
    color: var(--text-dark);
}

.info-value {
    color: var(--text-light);
}

.badge {
    display: inline-block;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: capitalize;
}

.badge.active {
    background: #d4edda;
    color: #155724;
}

.badge.inactive {
    background: #f8d7da;
    color: #721c24;
}

@media (max-width: 768px) {
    .profile-grid {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
