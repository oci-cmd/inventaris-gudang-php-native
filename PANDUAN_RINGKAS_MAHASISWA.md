# 📌 Panduan Praktikum Singkat & Jelas: AutoStock SCM
**Mata Kuliah:** Pemrograman Web Dasar (Prodi SI & KA)  
**Institusi:** STMIK IKMI Cirebon  
**Dosen Pengampu:** Pak Rosidin  

---

## 🗂️ Struktur Halaman Utama (Super Ringkas)

Hanya ada 2 halaman pintu masuk utama:
1. **`index.php`** 👉 **Halaman Web Depan (Landing Page Publik)**  
   Dapat diakses oleh siapa saja tanpa login untuk melihat profil sistem pabrik, metrik ringkasan gudang, serta mencari suku cadang dan mengecek ketersediaan stok secara real-time.
2. **`dashboard.php`** 👉 **Panel Operasional Petugas (Terproteksi Login)**  
   Halaman kerja setelah login untuk mengelola 4 modul CRUD (Kategori, Barang, Supplier, dan Mutasi Stok).

---

## 🚀 Tutorial Praktikum Langkah demi Langkah (6 Langkah Cepat)

### Langkah 1: Persiapan Database (1 Menit)
1. Pastikan database MariaDB/MySQL aktif di port 3306.
2. Buka peramban ke:  
   👉 **`http://localhost:8383/setup.php`** *(atau via port 8000: `http://localhost:8000/inventaris-gudang-si/setup.php`)*
3. Klik tombol hijau **"Inisialisasi Database Sekarang"**. Seluruh 5 tabel dan data awal otomatis terpasang.

---

### Langkah 2: Eksplorasi Frontend Publik (Tanpa Login)
1. Buka alamat: **`http://localhost:8383/`**
2. Perhatikan 4 kartu statistik ringkasan barang dan stok fisik.
3. Coba ketik kata kunci di kotak pencarian (contoh: *"Bearing"*) atau pilih filter kategori untuk melihat status ketersediaan suku cadang (*Tersedia*, *Stok Kritis*, atau *Habis*).

---

### Langkah 3: Masuk ke Portal Petugas (Login)
1. Klik tombol **"Portal Petugas 🔐"** di navbar atas atau buka:  
   👉 **`http://localhost:8383/auth/login.php`**
2. Masukkan akun praktikum:
   * **Username:** `admin`
   * **Password:** `admin123`
3. Klik tombol **Masuk ke Sistem**. Anda akan langsung diarahkan ke **`dashboard.php`**.

---

### Langkah 4: Pengoperasian 4 Modul CRUD Mandiri
Lakukan penginputan data secara berurutan dan logis:
1. **📁 Kategori (`modul/kategori/`)**: Tambah kategori baru (misal: *Pneumatik Mesin*).
2. **🏢 Supplier (`modul/supplier/`)**: Tambah supplier/vendor resmi baru beserta kontak.
3. **📦 Barang (`modul/barang/`)**: Tambah suku cadang baru dengan memilih relasi kategori dari dropdown.
4. **🔄 Mutasi Stok (`modul/transaksi/`)**: Catat barang masuk (stok fisik otomatis bertambah) atau barang keluar (stok fisik berkurang).

---

### Langkah 5: Uji Validasi Bisnis & Keamanan Web
1. **Uji Anti-Stok Minus:** Coba catat transaksi mutasi keluar dengan jumlah melebihi sisa stok fisik. Sistem wajib menolak dan transaksi dibatalkan (Rollback ACID).
2. **Uji SQL Injection:** Masukkan tanda petik satu (`'`) pada form pencarian. Query tetap aman karena menggunakan PDO Prepared Statements.
3. **Uji Sesi:** Buka tab Incognito dan coba akses langsung `http://localhost:8383/dashboard.php`. Sistem otomatis menolak dan mengalihkan ke form login.

---

### Langkah 6: Logout Bersih
* Untuk kembali melihat tampilan web umum, klik tombol **"🌐 Web Publik"** di navbar atas.
* Untuk mengakhiri sesi, klik tombol **"Keluar (Logout)"** di pojok kanan atas. Sesi dibersihkan dan kembali ke halaman login.

