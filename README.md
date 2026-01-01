🌸 Florist Web Application

Aplikasi **Web Florist** adalah sistem penjualan bunga berbasis web yang dibangun menggunakan **PHP Native** dan **MySQL**. Aplikasi ini menyediakan fitur manajemen produk, kategori, pesanan, serta dashboard admin untuk mengelola toko bunga secara online.

## ✨ Fitur Utama

### 👤 User

* Melihat daftar produk bunga
* Melihat detail produk
* Melakukan pemesanan bunga
* Halaman error khusus (404 & 500)

### 🛠️ Admin

* Login & Logout admin
* Manajemen produk (tambah, edit, hapus)
* Manajemen kategori produk
* Manajemen pesanan & detail pesanan
* Laporan penjualan
* Manajemen slider/banner
* Pengaturan website
* Manajemen member/admin

## 🧰 Teknologi yang Digunakan

* **Bahasa Pemrograman**: PHP (Native)
* **Database**: MySQL
* **Web Server**: Apache (XAMPP / Laragon)
* **Frontend**: HTML, CSS, JavaScript
* **Konfigurasi Server**: `.htaccess`

## ⚙️ Instalasi & Konfigurasi

1. **Clone repository**

   ```bash
   git clone https://github.com/username/florist.git
   ```

2. **Pindahkan ke folder server**

   ```bash
   htdocs/florist
   ```

3. **Buat database**

   * Buka `phpMyAdmin`
   * Buat database baru (misal: `florist_db`)
   * Import file `.sql`

4. **Atur koneksi database**

   * Buka file konfigurasi database
   * Sesuaikan:

     ```php
     $host = "localhost";
     $user = "root";
     $pass = "";
     $db   = "florist_db";
     ```

5. **Jalankan aplikasi**

   ```
   http://localhost/florist
   ```

## 🔐 Akun Admin (Default)

> Buka Halaman Admin http://localhost/florist/admin/index.php

* **Username**: admin
* **Password**: admin123

## 📄 Lisensi

Project ini dibuat untuk keperluan **pembelajaran dan pengembangan**.
Silakan digunakan dan dimodifikasi sesuai kebutuhan.

## 🖼️ Screenshot Aplikasi

### 🏠 Halaman Utama

Menampilkan Hero Slide, Kategori Produk, Produk Terlaris, dan Tentang Kami

<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/69c759bb-ccb5-40ce-9d81-0a6c878aafdc" />
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/ef225b50-464e-40da-926f-dfcd0b807a93" />
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/6c1e7460-aa46-412b-9ad9-06890e700dc8" />
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/2cd55993-7623-4f93-adc2-58d0f498399d" />


### 🎁 Produk

Menampilkan produk seperti deskripsi, harga, dan informasi tambahan sebelum melakukan pemesanan.

<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/a8f62c5e-8adf-4a45-a476-bb9274b85a46" />

### 🌼 Detail Produk

Menampilkan detail bunga seperti deskripsi, harga, dan informasi tambahan sebelum melakukan pemesanan.

<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/9ffca8a0-b43e-4d31-b625-4740e3a7b453" />


### 🛒 Keranjang dan Halaman Pesanan

Digunakan oleh user untuk melihat dan melakukan proses pemesanan bunga.

<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/9b37349d-2caf-4365-8b6e-45994edaf216" />


### 🔐 Halaman Login Admin

Halaman autentikasi untuk admin sebelum mengakses dashboard.

```md
![Login Admin](screenshots/login-admin.png)
```

### 📊 Dashboard Admin

Dashboard utama admin untuk mengelola produk, kategori, pesanan, dan laporan.

```md
![Dashboard Admin](screenshots/dashboard-admin.png)
```

### 📦 Manajemen Produk

Digunakan admin untuk menambah, mengubah, dan menghapus data produk bunga.

```md
![Manajemen Produk](screenshots/produk-admin.png)
```

---
