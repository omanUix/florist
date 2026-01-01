<?php
require_once 'config.php';
require_once 'includes/functions.php';

$page_title = 'Login Member';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = cleanInput($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Email dan password harus diisi';
    } else {
        try {
            $member = $db->fetchOne("SELECT * FROM members WHERE email = ?", [$email]);
            
            if ($member && password_verify($password, $member['password'])) {
                if ($member['status'] == 'active') {
                    $_SESSION['member_id'] = $member['id'];
                    $_SESSION['member_name'] = $member['name'];
                    $_SESSION['member_email'] = $member['email'];
                    
                    // Redirect ke halaman yang diminta atau beranda
                    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
                    redirect($redirect);
                } else {
                    $error = 'Akun Anda tidak aktif. Silakan hubungi admin.';
                }
            } else {
                $error = 'Email atau password salah';
            }
        } catch (Exception $e) {
            $error = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1 class="page-title">Login Member</h1>
        <p class="page-description">Masuk ke akun member Anda</p>
    </div>
</section>

<!-- Login Form Section -->
<section class="login-section">
    <div class="container">
        <div class="login-container">
            <div class="login-form-wrapper">
                <h2>Login Member</h2>
                <p>Masukkan email dan password Anda</p>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="login-form">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                               placeholder="contoh@email.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required 
                               placeholder="Masukkan password">
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-large" style="width: 100%;">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>
                
                <div class="login-footer">
                    <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
                    <p><a href="index.php">Kembali ke Beranda</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.login-section {
    padding: 4rem 0;
    background: var(--light-gray);
}

.login-container {
    max-width: 500px;
    margin: 0 auto;
}

.login-form-wrapper {
    background: var(--white);
    padding: 3rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
}

.login-form-wrapper h2 {
    margin-bottom: 0.5rem;
    color: var(--text-dark);
}

.login-form-wrapper > p {
    margin-bottom: 2rem;
    color: var(--text-light);
}

.login-form {
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

.form-group input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    font-family: inherit;
    font-size: 0.9rem;
    transition: var(--transition);
}

.form-group input:focus {
    outline: none;
    border-color: var(--primary-color);
}

.login-footer {
    margin-top: 2rem;
    text-align: center;
    padding-top: 2rem;
    border-top: 1px solid var(--border-color);
}

.login-footer p {
    margin-bottom: 0.5rem;
}

.login-footer a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
}

.login-footer a:hover {
    text-decoration: underline;
}
</style>

<?php include 'includes/footer.php'; ?>

