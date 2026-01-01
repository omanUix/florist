<?php
require_once 'config.php';
require_once 'includes/functions.php';

$page_title = 'Kontak';
?>

<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1 class="page-title">Hubungi Kami</h1>
        <p class="page-description">Jangan ragu untuk menghubungi kami untuk informasi lebih lanjut</p>
    </div>
</section>

<!-- Contact Section -->
<section class="contact">
    <div class="container">
        <div class="contact-content">
            <div class="contact-info">
                <h2>Informasi Kontak</h2>
                <p>Kami siap membantu Anda dengan pertanyaan atau pemesanan produk. Hubungi kami melalui:</p>
                
                <div class="contact-methods">
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div class="contact-details">
                            <h3>WhatsApp</h3>
                            <p>+62 812-3456-7890</p>
                            <a href="https://wa.me/6281234567890" class="btn btn-whatsapp" target="_blank">
                                <i class="fab fa-whatsapp"></i> Chat Sekarang
                            </a>
                        </div>
                    </div>
                    
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Email</h3>
                            <p>info@florist.com</p>
                            <a href="mailto:info@florist.com" class="btn btn-outline">
                                <i class="fas fa-envelope"></i> Kirim Email
                            </a>
                        </div>
                    </div>
                    
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Telepon</h3>
                            <p>+62 21-1234-5678</p>
                            <a href="tel:+622112345678" class="btn btn-outline">
                                <i class="fas fa-phone"></i> Hubungi
                            </a>
                        </div>
                    </div>
                    
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Alamat</h3>
                            <p>Jl. Contoh No. 123<br>Jakarta Selatan, 12345<br>Indonesia</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="contact-form-section">
                <h2>Kirim Pesan</h2>
                <p>Atau isi form di bawah ini untuk mengirim pesan kepada kami:</p>
                
                <form class="contact-form" id="contact-form">
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Nomor Telepon</label>
                        <input type="tel" id="phone" name="phone">
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subjek</label>
                        <input type="text" id="subject" name="subject" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Pesan</label>
                        <textarea id="message" name="message" rows="5" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-large">
                        <i class="fas fa-paper-plane"></i> Kirim Pesan
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq">
    <div class="container">
        <h2 class="section-title">Frequently Asked Questions</h2>
        
        <div class="faq-content">
            <div class="faq-item">
                <div class="faq-question">
                    <h3>Bagaimana cara memesan produk?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Anda dapat memesan produk dengan mengklik tombol "Pesan via WhatsApp" pada setiap produk, atau menghubungi kami langsung melalui WhatsApp di nomor +62 812-3456-7890.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <h3>Berapa lama waktu pengiriman?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Waktu pengiriman bervariasi tergantung lokasi tujuan. Untuk area Jakarta dan sekitarnya, pengiriman memakan waktu 1-2 hari kerja. Untuk luar kota, pengiriman memakan waktu 3-5 hari kerja.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <h3>Apakah produk bisa dikustomisasi?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Ya, sebagian besar produk kami dapat dikustomisasi sesuai keinginan Anda. Silakan hubungi kami untuk mendiskusikan kebutuhan khusus Anda.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <h3>Bagaimana cara perawatan produk?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Produk florist dan kerajinan bulu kawat kami relatif mudah dirawat. Hindari paparan air berlebihan dan simpan di tempat yang kering. Untuk perawatan khusus, silakan hubungi kami.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
