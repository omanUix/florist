<?php
/**
 * Website Florist & Kerajinan Bulu Kawat
 * Installation Script
 * 
 * Jalankan file ini untuk menginstall database dan setup awal
 */

// Cek apakah sudah diinstall
if (file_exists('config.php')) {
    die('Website sudah terinstall. Hapus file install.php setelah instalasi selesai.');
}

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($step == 1) {
        // Step 1: Database configuration
        $db_host = $_POST['db_host'] ?? 'localhost';
        $db_user = $_POST['db_user'] ?? 'root';
        $db_pass = $_POST['db_pass'] ?? '';
        $db_name = $_POST['db_name'] ?? 'florist_db';
        $site_name = $_POST['site_name'] ?? 'Florist & Kerajinan';
        $site_url = $_POST['site_url'] ?? 'http://localhost/florist';
        $admin_username = $_POST['admin_username'] ?? 'admin';
        $admin_password = $_POST['admin_password'] ?? 'admin123';
        
        // Test database connection
        try {
            $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create database if not exists
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `$db_name`");
            
            // Create config.php
            $config_content = "<?php
// Konfigurasi Database
define('DB_HOST', '$db_host');
define('DB_USER', '$db_user');
define('DB_PASS', '$db_pass');
define('DB_NAME', '$db_name');

// Konfigurasi Website
define('SITE_NAME', '$site_name');
define('SITE_URL', '$site_url');
define('ADMIN_EMAIL', 'admin@florist.com');

// Konfigurasi Upload
define('UPLOAD_PATH', 'assets/img/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Start session
session_start();

// Error reporting (matikan di production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>";
            
            file_put_contents('config.php', $config_content);
            
            // Store data for next step
            session_start();
            $_SESSION['install_data'] = [
                'db_host' => $db_host,
                'db_user' => $db_user,
                'db_pass' => $db_pass,
                'db_name' => $db_name,
                'site_name' => $site_name,
                'site_url' => $site_url,
                'admin_username' => $admin_username,
                'admin_password' => $admin_password
            ];
            
            header('Location: install.php?step=2');
            exit;
            
        } catch (PDOException $e) {
            $error = 'Koneksi database gagal: ' . $e->getMessage();
        }
    } elseif ($step == 2) {
        // Step 2: Create tables and insert data
        session_start();
        $data = $_SESSION['install_data'] ?? null;
        
        if (!$data) {
            header('Location: install.php');
            exit;
        }
        
        try {
            $pdo = new PDO("mysql:host={$data['db_host']};dbname={$data['db_name']}", $data['db_user'], $data['db_pass']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Read and execute SQL file
            $sql = file_get_contents('database.sql');
            $pdo->exec($sql);
            
            // Update admin password
            $hashed_password = password_hash($data['admin_password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE admin SET username = ?, password = ? WHERE id = 1");
            $stmt->execute([$data['admin_username'], $hashed_password]);
            
            // Clean up
            unset($_SESSION['install_data']);
            
            $success = 'Installasi berhasil! Website siap digunakan.';
            $step = 3;
            
        } catch (Exception $e) {
            $error = 'Gagal membuat tabel: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installasi - Website Florist & Kerajinan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #ff6b9d, #a8e6cf);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .install-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
        }
        
        .install-header {
            background: linear-gradient(135deg, #ff6b9d, #a8e6cf);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .install-header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .install-header p {
            opacity: 0.9;
        }
        
        .install-content {
            padding: 2rem;
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }
        
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .step.active {
            background: #ff6b9d;
            color: white;
        }
        
        .step.completed {
            background: #a8e6cf;
            color: #2c3e50;
        }
        
        .step.pending {
            background: #e9ecef;
            color: #6c757d;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #2c3e50;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.9rem;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #ff6b9d;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            justify-content: center;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #ff6b9d, #ff8fab);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 157, 0.4);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border: 1px solid #f5c6cb;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border: 1px solid #c3e6cb;
        }
        
        .success-info {
            text-align: center;
            padding: 2rem;
        }
        
        .success-info i {
            font-size: 4rem;
            color: #a8e6cf;
            margin-bottom: 1rem;
        }
        
        .success-info h2 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        
        .success-info p {
            color: #6c757d;
            margin-bottom: 2rem;
        }
        
        .admin-info {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
        }
        
        .admin-info h3 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .admin-info p {
            color: #6c757d;
            margin: 0;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .install-container {
                margin: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="install-header">
            <h1><i class="fas fa-seedling"></i> Installasi Website</h1>
            <p>Website Florist & Kerajinan Bulu Kawat</p>
        </div>
        
        <div class="install-content">
            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step <?php echo $step >= 1 ? ($step > 1 ? 'completed' : 'active') : 'pending'; ?>">1</div>
                <div class="step <?php echo $step >= 2 ? ($step > 2 ? 'completed' : 'active') : 'pending'; ?>">2</div>
                <div class="step <?php echo $step >= 3 ? 'active' : 'pending'; ?>">3</div>
            </div>
            
            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($step == 1): ?>
                <h2>Konfigurasi Database</h2>
                <p>Masukkan informasi database MySQL Anda:</p>
                
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="db_host">Host Database</label>
                            <input type="text" id="db_host" name="db_host" value="localhost" required>
                        </div>
                        <div class="form-group">
                            <label for="db_name">Nama Database</label>
                            <input type="text" id="db_name" name="db_name" value="florist_db" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="db_user">Username Database</label>
                            <input type="text" id="db_user" name="db_user" value="root" required>
                        </div>
                        <div class="form-group">
                            <label for="db_pass">Password Database</label>
                            <input type="password" id="db_pass" name="db_pass">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="site_name">Nama Website</label>
                        <input type="text" id="site_name" name="site_name" value="Florist & Kerajinan" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="site_url">URL Website</label>
                        <input type="url" id="site_url" name="site_url" value="http://localhost/florist" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="admin_username">Username Admin</label>
                            <input type="text" id="admin_username" name="admin_username" value="admin" required>
                        </div>
                        <div class="form-group">
                            <label for="admin_password">Password Admin</label>
                            <input type="password" id="admin_password" name="admin_password" value="admin123" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-arrow-right"></i> Lanjutkan
                    </button>
                </form>
                
            <?php elseif ($step == 2): ?>
                <h2>Membuat Tabel Database</h2>
                <p>Sedang membuat tabel dan data awal...</p>
                
                <div style="text-align: center; padding: 2rem;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #ff6b9d;"></i>
                    <p style="margin-top: 1rem;">Mohon tunggu...</p>
                </div>
                
                <script>
                    // Auto submit form
                    document.addEventListener('DOMContentLoaded', function() {
                        setTimeout(function() {
                            document.querySelector('form').submit();
                        }, 2000);
                    });
                </script>
                
                <form method="POST" style="display: none;">
                    <button type="submit">Continue</button>
                </form>
                
            <?php elseif ($step == 3): ?>
                <div class="success-info">
                    <i class="fas fa-check-circle"></i>
                    <h2>Installasi Berhasil!</h2>
                    <p>Website Florist & Kerajinan Bulu Kawat telah berhasil diinstall dan siap digunakan.</p>
                    
                    <div class="admin-info">
                        <h3>Informasi Login Admin</h3>
                        <p>Username: admin</p>
                        <p>Password: admin123</p>
                        <p><strong>PENTING:</strong> Ganti password default setelah login pertama kali!</p>
                    </div>
                    
                    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                        <a href="index.php" class="btn btn-primary">
                            <i class="fas fa-home"></i> Lihat Website
                        </a>
                        <a href="admin/login.php" class="btn btn-secondary">
                            <i class="fas fa-user-shield"></i> Login Admin
                        </a>
                    </div>
                    
                    <div style="margin-top: 2rem; padding: 1rem; background: #fff3cd; border-radius: 8px; border: 1px solid #ffeaa7;">
                        <p style="color: #856404; margin: 0;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Keamanan:</strong> Hapus file install.php setelah instalasi selesai untuk keamanan website Anda.
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
