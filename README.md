# 📦 Sistem Informasi Inventaris Gudang & Logistik (AutoStock)

Aplikasi Web Inventaris Gudang & Rantai Pasok Berbasis **PHP Native (PDO)** & **MySQL / MariaDB** dengan Tailwind CSS.  
Didesain sebagai materi praktikum terpadu pemrograman web berbasis objek dan keamanan web dasar (Otentikasi, Transaksi Database ACID, dan Sanitasi SQL Injection).

---

## 🌟 Fitur Utama

1. **Halaman Publik (`index.php`)**:
   - Profil operasional logistik & metrik counter gudang (Total item, unit fisik, peringatan stok kritis, nilai aset).
   - Katalog pencarian suku cadang langsung dengan filter kategori dan indikator badge status ketersediaan.
2. **Dashboard Petugas & Admin (`dashboard.php`)**:
   - Panel ringkasan statistik cepat dan log aktivitas mutasi stok terakhir.
3. **Modul CRUD Mandiri**:
   - **Kategori Barang** (`modul/kategori/`): Manajemen klasifikasi suku cadang.
   - **Supplier / Vendor** (`modul/supplier/`): Manajemen mitra penyedia barang.
   - **Data Barang** (`modul/barang/`): Pengelolaan katalog suku cadang beserta harga beli, harga jual, dan batas stok minimum.
   - **Mutasi Stok Masuk & Keluar** (`modul/transaksi/`): Pencatatan barang masuk/keluar dengan kalkulasi stok otomatis serta validasi transaksi (mencegah stok minus).
4. **Otentikasi & Keamanan**:
   - Proteksi sesi aman (`session_start()`, `checkAuth()`).
   - Password hashing menggunakan algoritma standar `bcrypt` (`password_hash` & `password_verify`).
   - Proteksi SQL Injection menyeluruh menggunakan **PDO Prepared Statements**.
   - Skrip inisialisasi basis data satu-klik via web (`setup.php`).

---

## 🗂️ Struktur Direktori

```text
inventaris-gudang/
├── auth/                    # Modul Login & Logout petugas
│   ├── login.php
│   └── logout.php
├── config/                  # Konfigurasi koneksi PDO & deteksi otomatis URL
│   └── database.php
├── layout/                  # Komponen UI bersama (Header navbar, sidebar, footer)
│   ├── header.php
│   └── footer.php
├── modul/                   # 4 Modul CRUD Terpadu
│   ├── barang/
│   ├── kategori/
│   ├── supplier/
│   └── transaksi/
├── dashboard.php            # Dashboard utama operasional petugas (Terproteksi)
├── database.sql             # Skema DDL & DML basis data MySQL/MariaDB
├── index.php                # Landing page web publik (Tanpa login)
├── setup.php                # Skrip instalasi & inisialisasi basis data via web browser
├── Panduan Projek - Sistem Informasi Inventaris Gudang.docx # Modul panduan lengkap praktikum
└── PANDUAN_RINGKAS_MAHASISWA.md # Ringkasan panduan cepat mahasiswa
```

---

## 🚀 Petunjuk Instalasi & Menjalankan Projek

### Prasyarat
- PHP 8.0 atau lebih tinggi (ekstensi `pdo_mysql` aktif).
- MySQL atau MariaDB Server aktif (port default 3306).

### 1. Clone Repositori
```bash
git clone https://github.com/oci-cmd/inventaris-gudang-php-native.git inventaris-gudang
cd inventaris-gudang
```

### 2. Inisialisasi Basis Data (Pilih Salah Satu Cara)

#### Cara A: Otomatis via Browser (Direkomendasikan)
1. Jalankan web server lokal (lihat langkah 3 di bawah).
2. Buka URL: `http://localhost:8383/setup.php` *(sesuaikan port web server Anda)*.
3. Klik tombol hijau **"Inisialisasi Database Sekarang"**. Sistem otomatis membuat database `db_inventaris_gudang`, seluruh tabel, dan data awal.

#### Cara B: Manual via phpMyAdmin / MySQL CLI
Import file `database.sql` ke server database Anda:
```bash
mysql -u root -p < database.sql
```

### 3. Jalankan Web Server PHP
Anda dapat menggunakan PHP Built-in Server dari terminal:
```bash
php -S localhost:8383
```
Lalu buka peramban ke: `http://localhost:8383`

---

## 🔑 Akun Default Sistem

| Role | Username | Password Default | Hak Akses |
| :--- | :--- | :--- | :--- |
| **Kepala Gudang (Admin)** | `admin` | `admin123` | Akses penuh seluruh master data & mutasi |
| **Staff Logistik (Petugas)** | `petugas1` | `admin123` | Akses operasional harian gudang |

---

## 👨‍🏫 Informasi Akademik
- **Mata Kuliah**: Pemrograman Web Dasar (S1 Sistem Informasi & D3 Komputerisasi Akuntansi)
- **Institusi**: STMIK IKMI Cirebon
- **Tahun Akademik**: 2026/2027

