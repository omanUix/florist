<?php
require_once 'config.php';
require_once 'includes/functions.php';

if (!isMemberLoggedIn()) {
    redirect('login_member.php');
}

$order_id = $_GET['order_id'] ?? 0;
$db = new Database();

// Get order
$order = $db->fetchOne("SELECT o.*, oi.product_id, oi.product_name, oi.quantity, oi.price FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.id = ? AND o.member_id = ?", [$order_id, $_SESSION['member_id']]);

if (!$order) {
    redirect('member_orders.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_FILES['proof']['name'])) {
        $error = 'Silakan pilih file bukti pembayaran';
    } else {
        $uploaded = uploadImage($_FILES['proof'], 'proof');
        
        if ($uploaded === false) {
            $error = 'Gagal upload file. Format harus JPG/PNG dan ukuran maksimal 5MB';
        } else {
            // Save to database
            $sql = "INSERT INTO payment_proof (order_id, file_path) VALUES (?, ?)";
            $db->query($sql, [$order_id, $uploaded]);
            
            // Update order status
            $db->query("UPDATE orders SET status = 'waiting_verification' WHERE id = ?", [$order_id]);
            
            $success = 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin...';
            
            // Refresh order data
            $order = $db->fetchOne("SELECT o.*, oi.product_id, oi.product_name, oi.quantity, oi.price FROM orders o
                LEFT JOIN order_items oi ON o.id = oi.order_id
                WHERE o.id = ? AND o.member_id = ?", [$order_id, $_SESSION['member_id']]);
        }
    }
}

// Get existing proof if any
$proof = $db->fetchOne("SELECT * FROM payment_proof WHERE order_id = ? LIMIT 1", [$order_id]);

// Get payment settings
$banks = [];
for ($i = 1; $i <= 3; $i++) {
    $name = $db->fetchOne("SELECT setting_value FROM payment_settings WHERE setting_key = ?", ["bank_name_$i"]);
    $account = $db->fetchOne("SELECT setting_value FROM payment_settings WHERE setting_key = ?", ["bank_account_$i"]);
    $holder = $db->fetchOne("SELECT setting_value FROM payment_settings WHERE setting_key = ?", ["bank_holder_$i"]);
    
    if ($name && $account) {
        $banks[] = [
            'name' => $name['setting_value'],
            'account' => $account['setting_value'],
            'holder' => $holder['setting_value'] ?? 'N/A'
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Bukti Pembayaran</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .content {
            padding: 30px;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-error {
            background: #fee;
            color: #c33;
            border-left: 4px solid #c33;
        }

        .alert-success {
            background: #efe;
            color: #3c3;
            border-left: 4px solid #3c3;
        }

        .order-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #555;
        }

        .info-value {
            color: #333;
        }

        .amount-highlight {
            font-size: 20px;
            color: #667eea;
            font-weight: 700;
        }

        .bank-section {
            margin-bottom: 30px;
        }

        .bank-section h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .bank-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            border-left: 4px solid #667eea;
        }

        .bank-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .bank-detail {
            color: #666;
            font-size: 13px;
            margin-bottom: 3px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .file-input-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-input-wrapper input[type="file"] {
            display: none;
        }

        .file-input-label {
            display: block;
            padding: 20px;
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            border: 2px dashed #667eea;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-input-label:hover {
            background: linear-gradient(135deg, #667eea25 0%, #764ba225 100%);
            border-color: #764ba2;
        }

        .file-input-label i {
            font-size: 32px;
            color: #667eea;
            margin-bottom: 10px;
            display: block;
        }

        .file-input-label .text {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .file-input-label .subtext {
            color: #999;
            font-size: 12px;
        }

        .file-name {
            margin-top: 10px;
            color: #666;
            font-size: 14px;
        }

        .existing-proof {
            background: #efe;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #3c3;
        }

        .existing-proof i {
            color: #3c3;
            margin-right: 10px;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-back {
            background: #e9ecef;
            color: #333;
        }

        .btn-back:hover {
            background: #dee2e6;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-verified {
            background: #d4edda;
            color: #155724;
        }

        .status-waiting {
            background: #cfe2ff;
            color: #084298;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-receipt"></i> Upload Bukti Pembayaran</h1>
            <p>Pesanan #<?= htmlspecialchars($order['order_number']) ?></p>
        </div>

        <div class="content">
            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <!-- Order Info -->
            <div class="order-info">
                <div class="info-row">
                    <span class="info-label">Nomor Pesanan:</span>
                    <span class="info-value"><?= htmlspecialchars($order['order_number']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nama Pemesan:</span>
                    <span class="info-value"><?= htmlspecialchars($order['customer_name']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Metode Pembayaran:</span>
                    <span class="info-value">
                        <?php
                        $method_text = $order['payment_method'] === 'qris' ? 'QRIS' : 'Transfer Bank';
                        echo htmlspecialchars($method_text);
                        ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Jumlah:</span>
                    <span class="info-value amount-highlight">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value">
                        <?php
                        $status_class = 'status-pending';
                        $status_text = 'Menunggu Pembayaran';
                        
                        if ($order['status'] === 'waiting_verification') {
                            $status_class = 'status-waiting';
                            $status_text = 'Menunggu Verifikasi';
                        } elseif ($order['status'] === 'confirmed') {
                            $status_class = 'status-verified';
                            $status_text = 'Pembayaran Dikonfirmasi';
                        }
                        ?>
                        <span class="status-badge <?= $status_class ?>"><?= $status_text ?></span>
                    </span>
                </div>
            </div>

            <!-- Bank Information -->
            <?php if ($order['payment_method'] === 'bank_transfer'): ?>
                <div class="bank-section">
                    <h3><i class="fas fa-bank"></i> Silakan transfer ke salah satu rekening berikut:</h3>
                    <?php foreach ($banks as $bank): ?>
                        <div class="bank-card">
                            <div class="bank-name"><?= htmlspecialchars($bank['name']) ?></div>
                            <div class="bank-detail"><strong>No. Rekening:</strong> <?= htmlspecialchars($bank['account']) ?></div>
                            <div class="bank-detail"><strong>Atas Nama:</strong> <?= htmlspecialchars($bank['holder']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="bank-section">
                    <h3><i class="fas fa-qrcode"></i> Scan QRIS Berikut:</h3>
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center;">
                        <img src="assets/img/qris.png" alt="QRIS" style="max-width: 300px; height: auto; display: inline-block;">
                    </div>
                </div>
            <?php endif; ?>

            <!-- Upload Form -->
            <?php if ($proof): ?>
                <div class="existing-proof">
                    <i class="fas fa-check-circle"></i>
                    <strong>Bukti pembayaran sudah diupload</strong> pada <?= date('d M Y H:i', strtotime($proof['uploaded_at'])) ?>
                </div>
            <?php else: ?>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Upload Bukti Pembayaran (Transfer/QRIS)</label>
                        <div class="file-input-wrapper">
                            <input type="file" id="proof-file" name="proof" accept="image/jpeg,image/png" required>
                            <label for="proof-file" class="file-input-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <div class="text">Klik atau drag file ke sini</div>
                                <div class="subtext">Format: JPG, PNG | Ukuran maks: 5MB<br>Upload screenshot transfer bank <b>atau</b> bukti scan QRIS</div>
                            </label>
                            <div class="file-name" id="file-name"></div>
                        </div>
                    </div>

                    <div class="button-group">
                        <button type="submit" class="btn btn-submit">
                            <i class="fas fa-upload"></i> Upload Bukti
                        </button>
                        <a href="member_order_detail.php?id=<?= $order_id ?>" class="btn btn-back">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const fileInput = document.getElementById('proof-file');
        const fileNameDisplay = document.getElementById('file-name');
        const fileLabel = document.querySelector('.file-input-label');

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                fileNameDisplay.textContent = '✓ File dipilih: ' + e.target.files[0].name;
            }
        });

        // Drag and drop
        fileLabel.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileLabel.style.borderColor = '#764ba2';
        });

        fileLabel.addEventListener('dragleave', () => {
            fileLabel.style.borderColor = '#667eea';
        });

        fileLabel.addEventListener('drop', (e) => {
            e.preventDefault();
            fileInput.files = e.dataTransfer.files;
            fileInput.dispatchEvent(new Event('change'));
        });
    </script>
</body>
</html>
