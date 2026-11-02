-- ====================================================================
-- SKEMA BASIS DATA: SISTEM INFORMASI INVENTARIS GUDANG & LOGISTIK (AUTOSTOCK)
-- STMIK IKMI Cirebon - Pegangan Dosen Pemrograman Web Dasar (S1 SI & D3 KA)
-- ====================================================================

CREATE DATABASE IF NOT EXISTS `db_inventaris_gudang` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_inventaris_gudang`;

-- 1. TABEL PENGGUNA (Otentikasi & Sesi - Sesuai Materi P12 & P15)
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `nama_lengkap` VARCHAR(100) NOT NULL,
  `role` ENUM('admin', 'petugas') NOT NULL DEFAULT 'petugas',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. TABEL KATEGORI BARANG (Modul CRUD 1 - Anggota Kelompok 1)
DROP TABLE IF EXISTS `kategori`;
CREATE TABLE `kategori` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_kategori` VARCHAR(20) NOT NULL UNIQUE,
  `nama_kategori` VARCHAR(100) NOT NULL,
  `keterangan` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. TABEL SUPPLIER / PEMASOK (Modul CRUD 3 - Anggota Kelompok 3)
DROP TABLE IF EXISTS `supplier`;
CREATE TABLE `supplier` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_supplier` VARCHAR(20) NOT NULL UNIQUE,
  `nama_supplier` VARCHAR(100) NOT NULL,
  `telepon` VARCHAR(20) NOT NULL,
  `alamat` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 4. TABEL BARANG / SUKU CADANG (Modul CRUD 2 - Anggota Kelompok 2)
DROP TABLE IF EXISTS `barang`;
CREATE TABLE `barang` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_barang` VARCHAR(20) NOT NULL UNIQUE,
  `kategori_id` INT NOT NULL,
  `nama_barang` VARCHAR(100) NOT NULL,
  `satuan` VARCHAR(30) NOT NULL,
  `harga_beli` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `harga_jual` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `stok` INT NOT NULL DEFAULT 0,
  `stok_min` INT NOT NULL DEFAULT 5,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`kategori_id`) REFERENCES `kategori`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- 5. TABEL TRANSAKSI MUTASI STOK (Modul CRUD 4 - Anggota Kelompok 4)
DROP TABLE IF EXISTS `transaksi_stok`;
CREATE TABLE `transaksi_stok` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `no_transaksi` VARCHAR(30) NOT NULL UNIQUE,
  `barang_id` INT NOT NULL,
  `supplier_id` INT NULL,
  `jenis_transaksi` ENUM('Masuk', 'Keluar') NOT NULL,
  `jumlah` INT NOT NULL,
  `tanggal` DATE NOT NULL,
  `keterangan` VARCHAR(255) NULL,
  `petugas` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`barang_id`) REFERENCES `barang`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`supplier_id`) REFERENCES `supplier`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ====================================================================
-- SEED DATA AWAL (DUMMY REALISTIS)
-- ====================================================================

-- 1. Pengguna Awal (Password hash bcrypt default: admin123 dan petugas123)
-- Hash generated via password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO `users` (`username`, `password`, `nama_lengkap`, `role`) VALUES
('admin', '$2y$10$NW1mrovrRf3sLMDqgE2rqOicgbBFqB8Aow2CGdS71PESgebJnqNoC', 'Raden Setiawan, M.Kom (Kepala Gudang)', 'admin'),
('petugas1', '$2y$10$NW1mrovrRf3sLMDqgE2rqOicgbBFqB8Aow2CGdS71PESgebJnqNoC', 'Budi Santoso (Staff Logistik)', 'petugas');

-- 2. Data Kategori Barang
INSERT INTO `kategori` (`kode_kategori`, `nama_kategori`, `keterangan`) VALUES
('KAT-01', 'Suku Cadang Mesin', 'Sparepart mekanikal dan komponen penggerak mesin pabrik'),
('KAT-02', 'Pelumas & Cairan Kimia', 'Oli industri, grease bearing, dan cairan pendingin mesin'),
('KAT-03', 'Kelistrikan & Elektronika', 'Kabel instalasi, relay, kontaktor, dan sensor industri'),
('KAT-04', 'Perkakas & Alat Kerja', 'Kunci set, obeng, tang presisi, dan mata bor baja'),
('KAT-05', 'Keselamatan Kerja (K3)', 'Helm proyek, sarung tangan las, kacamata pelindung');

-- 3. Data Supplier / Rekanan
INSERT INTO `supplier` (`kode_supplier`, `nama_supplier`, `telepon`, `alamat`) VALUES
('SUP-001', 'PT Cirebon Baja Mandiri', '0231-209887', 'Kawasan Industri Kedawung, Cirebon'),
('SUP-002', 'CV Mitra Mesin Utama', '081234567890', 'Jl. Tuparev No. 120, Cirebon'),
('SUP-003', 'PT Delta Kimia Industri', '021-5544332', 'Kawasan Industri MM2100, Bekasi'),
('SUP-004', 'Toko Logistik Teknik Makmur', '085711223344', 'Jl. Tentara Pelajar No. 45, Kesambi Cirebon'),
('SUP-005', 'CV Mandiri Safety Pratama', '081399887766', 'Jl. Brigjen Dharsono No. 88, Cirebon');

-- 4. Data Barang
INSERT INTO `barang` (`kode_barang`, `kategori_id`, `nama_barang`, `satuan`, `harga_beli`, `harga_jual`, `stok`, `stok_min`) VALUES
('BRG-001', 1, 'Bearing Deep Groove 6205-2RS', 'Pcs', 35000.00, 50000.00, 40, 10),
('BRG-002', 1, 'V-Belt Mesin Fan B-52', 'Pcs', 45000.00, 65000.00, 15, 5),
('BRG-003', 2, 'Oli Mesin Hidrolik ISO VG 46 (Drum 20L)', 'Drum', 850000.00, 1100000.00, 4, 5), -- Status Kritis (< stok_min)
('BRG-004', 2, 'Grease Lithium High-Temp 500g', 'Can', 60000.00, 85000.00, 25, 8),
('BRG-005', 3, 'Miniature Circuit Breaker (MCB) 16A 3-Phase', 'Unit', 125000.00, 175000.00, 12, 5),
('BRG-006', 3, 'Sensor Kedekatan Proximity NPN DC 24V', 'Unit', 95000.00, 140000.00, 3, 5), -- Status Kritis (< stok_min)
('BRG-007', 4, 'Mata Gerinda Potong 4 Inch Ultra Cut', 'Box', 110000.00, 150000.00, 30, 10),
('BRG-008', 5, 'Sarung Tangan Las Kulit Sapi 14 Inch', 'Pasang', 35000.00, 55000.00, 50, 15);

-- 5. Data Transaksi Stok Masuk & Keluar
INSERT INTO `transaksi_stok` (`no_transaksi`, `barang_id`, `supplier_id`, `jenis_transaksi`, `jumlah`, `tanggal`, `keterangan`, `petugas`) VALUES
('TRX-IN-20261101-01', 1, 1, 'Masuk', 50, '2026-11-01', 'Penerimaan stok awal dari supplier', 'Budi Santoso'),
('TRX-OUT-20261101-02', 1, NULL, 'Keluar', 10, '2026-11-01', 'Pemakaian perbaikan Mesin Bubut Line A', 'Budi Santoso'),
('TRX-IN-20261102-03', 3, 3, 'Masuk', 5, '2026-11-02', 'Pasokan oli hidrolik bulanan', 'Budi Santoso'),
('TRX-OUT-20261102-04', 3, NULL, 'Keluar', 1, '2026-11-02', 'Penggantian oli unit press hidrolik', 'Budi Santoso'),
('TRX-IN-20261102-05', 7, 4, 'Masuk', 30, '2026-11-02', 'Pengadaan mata gerinda bengkel', 'Budi Santoso'),
('TRX-OUT-20261102-06', 6, NULL, 'Keluar', 2, '2026-11-02', 'Penggantian sensor conveyor yang rusak', 'Budi Santoso');

