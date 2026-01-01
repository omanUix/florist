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

###🔑 Halaman Daftar dan Login Member
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/5c2e753b-7a1b-436a-aef3-90dbadcdffab" />
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/573d34c1-6273-4d88-8e7a-5eb0edbb9289" />


### 🏠 Halaman Utama

Menampilkan Hero Slide, Kategori Produk, Produk Terlaris, dan Tentang Kami
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/90465701-23bf-4c90-bf9e-2dcc6fc0e066" />
<img width="1364" height="768" alt="image" src="https://github.com/user-attachments/assets/daaebc7e-3ab6-4b94-9651-8b2aca2f30d2" />
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/3d3585cb-ce9c-4dbe-ad5d-9cb2f2e0ce86" />
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/013132a2-4f13-41b7-9967-bba0aaa313dd" />

### 🎁 Produk

Menampilkan produk seperti deskripsi, harga, dan informasi tambahan sebelum melakukan pemesanan.
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/b59953d7-5bc8-469d-9e97-470843e5f3b8" />


### 🌼 Detail Produk

Menampilkan detail bunga seperti deskripsi, harga, dan informasi tambahan sebelum melakukan pemesanan.

<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/083ad1e6-54b7-411e-a91a-b902d0ce55c2" />



### 🛒 Keranjang, Halaman Pesanan, dan Invoice

Digunakan oleh user untuk melihat dan melakukan proses pemesanan bunga.
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/1661af0a-77a7-4aa7-893f-2f9046f02e08" />
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/713088fb-4f38-4127-87c0-dfa16c2232eb" />
<img width="1035" height="768" alt="image" src="https://github.com/user-attachments/assets/0cc558a6-9ab5-4691-ba32-ad77325806f3" />


### 👱‍♂️ Halaman Profile Member
<img width="1365" height="768" alt="image" src="https://github.com/user-attachments/assets/4d2aa755-9a74-4ace-a918-0551d49843b4" />

### Halaman Pemasanan Saya
<img width="1365" height="768" alt="image" src="https://github.com/user-attachments/assets/71203904-77d9-44b8-83a1-d135b8588269" />
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/40709257-ed75-4844-a645-774aadae58b3" />

### Halaman Profile
 <img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/b0c922b4-4f56-42c6-abbf-718a1bbe4e6d" />



### 🔐 Halaman Login Admin

Halaman autentikasi untuk admin sebelum mengakses dashboard.

<img width="1365" height="643" alt="image" src="https://github.com/user-attachments/assets/3cc81652-7429-4421-8526-56084087e646" />


### 📊 Dashboard Admin

Dashboard utama admin untuk mengelola produk, kategori, pesanan, dan laporan.
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/d94cb2c4-2d67-487b-8cb0-bc5b46641ab1" />



### 📦 Manajemen Produk

Digunakan admin untuk menambah, mengubah, dan menghapus data produk bunga.
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/b52e56d4-a92b-4711-80a3-e7a71de304cb" />
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/c7b78bc7-12dd-4b49-ba06-5c50060e153c" />
   <img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/89baf771-d3fe-43da-b608-81b12369eb93" />

### Manajemen Slides
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/4492445c-ce95-43d3-9a5a-7d8bca98edf6" />

### Rekapan
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/e8dfa61c-76ab-419f-a7d2-c0bc54bb35d0" />

### Pesanan
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/5c98fd60-4e58-49a2-8aae-52ec3f9004f6" />

### Pengaturan Pembayaran
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/3628bf6b-d8d4-42d0-a94c-172ecb88d335" />
