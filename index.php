<?php
/**
 * Halaman Web Frontend / Landing Page Publik
 * Sistem Informasi Manajemen Inventaris & Logistik Gudang Pabrik (AutoStock SCM)
 * STMIK IKMI Cirebon - Pemrograman Web Dasar
 */

require_once __DIR__ . '/config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = !empty($_SESSION['user_id']);
$currentUser = [
    'nama' => $_SESSION['nama_lengkap'] ?? '',
    'role' => $_SESSION['role'] ?? '',
    'username' => $_SESSION['username'] ?? ''
];

$db = getDB();

// Parameter Pencarian & Filter Katalog Publik
$search = trim($_GET['q'] ?? '');
$filterKategori = trim($_GET['kategori'] ?? '');

// 1. Data Metrik Ringkasan Gudang (Live Query)
$totalBarang    = $db->query("SELECT COUNT(*) FROM barang")->fetchColumn() ?: 0;
$totalKategori  = $db->query("SELECT COUNT(*) FROM kategori")->fetchColumn() ?: 0;
$totalSupplier  = $db->query("SELECT COUNT(*) FROM supplier")->fetchColumn() ?: 0;
$totalStokFisik = $db->query("SELECT SUM(stok) FROM barang")->fetchColumn() ?: 0;

// 2. Daftar Kategori untuk Filter Dropdown
$daftarKategori = $db->query("SELECT * FROM kategori ORDER BY nama_kategori ASC")->fetchAll();

// 3. Query Katalog Barang Publik (Dengan Filter & Search)
$sql = "SELECT b.id, b.kode_barang, b.nama_barang, b.satuan, b.stok, b.stok_min, k.nama_kategori 
        FROM barang b 
        JOIN kategori k ON b.kategori_id = k.id 
        WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (b.nama_barang LIKE :search OR b.kode_barang LIKE :search)";
    $params[':search'] = "%$search%";
}

if (!empty($filterKategori)) {
    $sql .= " AND k.id = :kategori";
    $params[':kategori'] = $filterKategori;
}

$sql .= " ORDER BY b.nama_barang ASC";
$stmtKatalog = $db->prepare($sql);
$stmtKatalog->execute($params);
$katalogBarang = $stmtKatalog->fetchAll();

// 4. Daftar Supplier Rekanan Resmi
$daftarSupplier = $db->query("SELECT * FROM supplier ORDER BY nama_supplier ASC LIMIT 6")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= APP_NAME ?> - <?= APP_TITLE ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }
  </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-blue-600 selection:text-white">

  <!-- TOP BAR STATUS SESI (JIKA SUDAH LOGIN) -->
  <?php if ($isLoggedIn): ?>
  <aside aria-label="Status Login" class="bg-slate-900 text-white text-xs px-4 py-2 border-b border-slate-800">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
      <div class="flex items-center gap-2">
        <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
        <span>Anda sedang login sebagai <b><?= htmlspecialchars($currentUser['nama']) ?></b> (Role: <?= htmlspecialchars($currentUser['role']) ?>)</span>
      </div>
      <div class="flex items-center gap-3">
        <a href="<?= BASE_URL ?>/dashboard.php" class="text-blue-400 hover:text-blue-300 font-bold underline flex items-center gap-1">
          Buka Dashboard Petugas &rarr;
        </a>
        <span class="text-slate-600">|</span>
        <a href="<?= BASE_URL ?>/auth/logout.php" class="text-rose-400 hover:text-rose-300">Keluar</a>
      </div>
    </div>
  </aside>
  <?php endif; ?>

  <!-- NAVBAR PUBLIK -->
  <header class="bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-20">
        
        <!-- Logo & Branding -->
        <a href="<?= BASE_URL ?>/index.php" class="flex items-center gap-3 group">
          <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-700 to-indigo-600 text-white flex items-center justify-center font-black text-2xl shadow-lg shadow-blue-600/25 group-hover:scale-105 transition">
            📦
          </div>
          <div>
            <div class="font-black text-slate-900 text-lg leading-tight tracking-tight flex items-center gap-2">
              <?= APP_NAME ?>
              <span class="text-[10px] bg-blue-100 text-blue-700 font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">Industrial SCM</span>
            </div>
            <p class="text-xs text-slate-500 font-medium leading-none mt-1">
              <?= CAMPUS_NAME ?>
            </p>
          </div>
        </a>

        <!-- Menu Navigasi Desktop -->
        <nav class="hidden md:flex items-center gap-8">
          <a href="#beranda" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">Beranda</a>
          <a href="#katalog" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">Katalog Stok</a>
          <a href="#fitur" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">Fitur Sistem</a>
          <a href="#supplier" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">Mitra Rekanan</a>
        </nav>

        <!-- Tombol Aksi Login / Dashboard -->
        <div class="flex items-center gap-3">
          <?php if ($isLoggedIn): ?>
            <a href="<?= BASE_URL ?>/dashboard.php" 
               class="bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold px-5 py-2.5 rounded-xl shadow-md shadow-blue-600/20 transition flex items-center gap-2">
              <span>📊</span>
              <span>Panel Dashboard</span>
            </a>
          <?php else: ?>
            <a href="<?= BASE_URL ?>/auth/login.php" 
               class="bg-slate-900 hover:bg-blue-600 text-white text-xs sm:text-sm font-bold px-5 py-2.5 rounded-xl shadow-md transition flex items-center gap-2">
              <span>🔐</span>
              <span>Portal Petugas</span>
            </a>
          <?php endif; ?>
        </div>

      </div>
    </div>
  </header>

  <!-- HERO SECTION -->
  <section id="beranda" class="relative overflow-hidden pt-12 pb-20 lg:pt-20 lg:pb-28 bg-gradient-to-b from-white to-slate-50 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-3xl mx-auto">
        
        <div class="inline-flex items-center gap-2 bg-blue-50 border border-blue-200/80 px-3.5 py-1.5 rounded-full text-xs font-bold text-blue-700 mb-6 shadow-sm">
          <span>🏭</span>
          <span>Sistem Informasi Inventaris & Logistik Gudang Pabrik</span>
        </div>

        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-[1.15]">
          Monitoring Suku Cadang & Mutasi Stok <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Secara Real-Time</span>
        </h1>

        <p class="mt-6 text-base sm:text-lg text-slate-600 leading-relaxed max-w-2xl mx-auto">
          Aplikasi web terpadu untuk pencatatan stok masuk dari supplier, pengeluaran suku cadang mesin produksi, serta deteksi otomatis stok kritis demi kelancaran operasional manufaktur pabrik.
        </p>

        <!-- CTA Buttons -->
        <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
          <a href="#katalog" 
             class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3.5 rounded-xl text-sm shadow-lg shadow-blue-600/25 transition flex items-center gap-2">
            <span>🔍</span>
            <span>Cari & Cek Stok Barang</span>
          </a>
          <a href="<?= BASE_URL ?>/auth/login.php" 
             class="bg-white hover:bg-slate-50 text-slate-800 font-bold px-6 py-3.5 rounded-xl text-sm border border-slate-300 shadow-sm transition flex items-center gap-2">
            <span>🔐</span>
            <span>Masuk ke Sistem Petugas</span>
          </a>
        </div>

        <!-- Tag Akademik -->
        <div class="mt-8 pt-8 border-t border-slate-200 flex items-center justify-center gap-6 text-xs text-slate-500 font-medium">
          <span>🎓 Prodi Sistem Informasi & Komputerisasi Akuntansi</span>
          <span>&bull;</span>
          <span><?= CAMPUS_NAME ?></span>
          <span>&bull;</span>
          <span><?= SEMESTER_TA ?></span>
        </div>

      </div>
    </div>
  </section>

  <!-- METRIC COUNTER GUDANG -->
  <section class="py-12 bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 text-center">
        
        <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100">
          <div class="text-3xl sm:text-4xl font-black text-blue-600"><?= number_format($totalBarang) ?></div>
          <div class="text-xs font-bold uppercase tracking-wider text-slate-500 mt-1">Item Suku Cadang</div>
          <div class="text-[11px] text-slate-400 mt-0.5">Katalog terdaftar</div>
        </div>

        <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100">
          <div class="text-3xl sm:text-4xl font-black text-indigo-600"><?= number_format($totalStokFisik) ?></div>
          <div class="text-xs font-bold uppercase tracking-wider text-slate-500 mt-1">Unit Stok Fisik</div>
          <div class="text-[11px] text-slate-400 mt-0.5">Total barang di gudang</div>
        </div>

        <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100">
          <div class="text-3xl sm:text-4xl font-black text-emerald-600"><?= number_format($totalKategori) ?></div>
          <div class="text-xs font-bold uppercase tracking-wider text-slate-500 mt-1">Kategori Logistik</div>
          <div class="text-[11px] text-slate-400 mt-0.5">Klasifikasi suku cadang</div>
        </div>

        <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100">
          <div class="text-3xl sm:text-4xl font-black text-purple-600"><?= number_format($totalSupplier) ?></div>
          <div class="text-xs font-bold uppercase tracking-wider text-slate-500 mt-1">Supplier Rekanan</div>
          <div class="text-[11px] text-slate-400 mt-0.5">Distributor terverifikasi</div>
        </div>

      </div>
    </div>
  </section>

  <!-- LIVE KATALOG & CEK STOK PUBLIK -->
  <section id="katalog" class="py-16 bg-slate-50 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
        <div>
          <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Katalog Publik Terbuka</span>
          <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">Cek Ketersediaan Stok Suku Cadang</h2>
          <p class="text-xs text-slate-500 mt-1">Data langsung terhubung dengan database operasional gudang secara real-time</p>
        </div>

        <!-- FORM PENCARIAN & FILTER -->
        <form method="GET" action="#katalog" class="flex flex-wrap items-center gap-2.5">
          <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" 
                 placeholder="Cari kode atau nama barang..." 
                 class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-52 sm:w-64">
          
          <select name="kategori" class="px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Kategori</option>
            <?php foreach ($daftarKategori as $kat): ?>
              <option value="<?= $kat['id'] ?>" <?= $filterKategori == $kat['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($kat['nama_kategori']) ?>
              </option>
            <?php endforeach; ?>
          </select>

          <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs shadow-md transition">
            Filter
          </button>

          <?php if (!empty($search) || !empty($filterKategori)): ?>
            <a href="<?= BASE_URL ?>/index.php#katalog" class="text-xs text-rose-600 font-semibold px-2 py-2 hover:underline">
              Reset
            </a>
          <?php endif; ?>
        </form>
      </div>

      <!-- TABEL KATALOG STOK -->
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs sm:text-sm">
            <thead class="bg-slate-100 text-slate-600 uppercase text-[11px] font-bold border-b border-slate-200">
              <tr>
                <th class="px-6 py-3.5">Kode Barang</th>
                <th class="px-6 py-3.5">Nama Suku Cadang</th>
                <th class="px-6 py-3.5">Kategori</th>
                <th class="px-6 py-3.5 text-center">Satuan</th>
                <th class="px-6 py-3.5 text-center">Jumlah Stok</th>
                <th class="px-6 py-3.5 text-right">Status Ketersediaan</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium">
              <?php if (empty($katalogBarang)): ?>
                <tr>
                  <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                    <div class="text-2xl mb-1">🔍</div>
                    Tidak ada barang yang cocok dengan kriteria pencarian Anda.
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($katalogBarang as $b): ?>
                  <tr class="hover:bg-slate-50/80 transition">
                    <td class="px-6 py-4 font-mono font-bold text-blue-600">
                      <?= htmlspecialchars($b['kode_barang']) ?>
                    </td>
                    <td class="px-6 py-4 font-bold text-slate-900">
                      <?= htmlspecialchars($b['nama_barang']) ?>
                    </td>
                    <td class="px-6 py-4 text-slate-500">
                      <span class="inline-block bg-slate-100 text-slate-700 px-2.5 py-1 rounded-md text-xs font-semibold">
                        <?= htmlspecialchars($b['nama_kategori']) ?>
                      </span>
                    </td>
                    <td class="px-6 py-4 text-center text-slate-600">
                      <?= htmlspecialchars($b['satuan']) ?>
                    </td>
                    <td class="px-6 py-4 text-center font-bold text-slate-800">
                      <?= number_format($b['stok']) ?>
                    </td>
                    <td class="px-6 py-4 text-right">
                      <?php if ($b['stok'] <= 0): ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700">
                          <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> Stok Habis
                        </span>
                      <?php elseif ($b['stok'] <= $b['stok_min']): ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                          <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> Stok Kritis (&le; <?= $b['stok_min'] ?>)
                        </span>
                      <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                          <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Tersedia
                        </span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </section>

  <!-- FITUR UTAMA & ARSITEKTUR SISTEM -->
  <section id="fitur" class="py-16 bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <div class="text-center max-w-2xl mx-auto mb-12">
        <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Keunggulan & Desain Sistem</span>
        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">Mengapa Memilih AutoStock SCM?</h2>
        <p class="text-xs sm:text-sm text-slate-500 mt-2">Dibangun dengan filosofi web dasar yang bersih, tanpa framework berlebihan, dan mudah dipelajari mahasiswa.</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 hover:border-blue-300 transition">
          <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-2xl font-bold mb-4">
            🔄
          </div>
          <h3 class="text-base font-bold text-slate-900 mb-2">Manajemen Mutasi Transaksional (ACID)</h3>
          <p class="text-xs text-slate-500 leading-relaxed">
            Pencatatan barang masuk dan keluar dijamin konsisten menggunakan fitur Transaksi Database PDO (commit & rollback) sehingga tidak ada selisih stok fisik.
          </p>
        </div>

        <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 hover:border-indigo-300 transition">
          <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-2xl font-bold mb-4">
            🛡️
          </div>
          <h3 class="text-base font-bold text-slate-900 mb-2">Keamanan Standar Industri (OWASP)</h3>
          <p class="text-xs text-slate-500 leading-relaxed">
            Terbebas dari kerentanan SQL Injection dengan PDO Prepared Statements, pencegahan XSS dengan htmlspecialchars(), serta enkripsi kata sandi BCRYPT.
          </p>
        </div>

        <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 transition">
          <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl font-bold mb-4">
            ⚡
          </div>
          <h3 class="text-base font-bold text-slate-900 mb-2">Native arm64 Super Ringan</h3>
          <p class="text-xs text-slate-500 leading-relaxed">
            Dijalankan menggunakan PHP 8.3 native macOS tanpa emulator, hemat penggunaan memori RAM, dan waktu respons halaman mendekati 0 milidetik.
          </p>
        </div>

      </div>

    </div>
  </section>

  <!-- MITRA REKANAN SUPPLIER -->
  <section id="supplier" class="py-16 bg-slate-50 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <div class="text-center max-w-2xl mx-auto mb-10">
        <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Jaringan Logistik</span>
        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">Mitra Supplier Resmi Terdaftar</h2>
        <p class="text-xs text-slate-500 mt-1">Distributor resmi penyedia suku cadang mesin dan bahan pendukung pabrik</p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($daftarSupplier as $sup): ?>
          <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-bold shrink-0">
              🏢
            </div>
            <div>
              <div class="text-sm font-bold text-slate-900"><?= htmlspecialchars($sup['nama_supplier']) ?></div>
              <div class="text-[11px] font-mono text-blue-600 mt-0.5"><?= htmlspecialchars($sup['kode_supplier']) ?></div>
              <div class="text-xs text-slate-500 mt-1.5 flex items-center gap-1.5">
                <span>📞</span> <?= htmlspecialchars($sup['telepon'] ?: '-') ?>
              </div>
              <div class="text-xs text-slate-400 mt-0.5">
                <?= htmlspecialchars($sup['alamat'] ?: '-') ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
  </section>

  <!-- FOOTER -->
  <footer class="bg-slate-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8 pb-8 border-b border-slate-800 text-xs text-slate-400">
        
        <div class="md:col-span-2">
          <div class="flex items-center gap-2.5 text-white font-bold text-base mb-3">
            <span>📦</span> <?= APP_NAME ?>
          </div>
          <p class="leading-relaxed max-w-sm">
            <?= APP_TITLE ?>. Dikembangkan sebagai materi praktikum terstruktur Mata Kuliah Pemrograman Web Dasar di <?= CAMPUS_NAME ?>.
          </p>
          <div class="mt-4 text-[11px] text-slate-500">
            Dosen Pengampu: <span class="text-white font-semibold">Pak Rosidin</span>
          </div>
        </div>

        <div>
          <h4 class="text-white font-bold mb-3 uppercase tracking-wider text-[11px]">Tautan Cepat</h4>
          <ul class="space-y-2">
            <li><a href="#beranda" class="hover:text-white transition">Halaman Utama</a></li>
            <li><a href="#katalog" class="hover:text-white transition">Katalog Stok Publik</a></li>
            <li><a href="#fitur" class="hover:text-white transition">Fitur Sistem</a></li>
            <li><a href="#supplier" class="hover:text-white transition">Mitra Supplier</a></li>
          </ul>
        </div>

        <div>
          <h4 class="text-white font-bold mb-3 uppercase tracking-wider text-[11px]">Akses Internal</h4>
          <ul class="space-y-2">
            <li><a href="<?= BASE_URL ?>/auth/login.php" class="hover:text-white transition flex items-center gap-1"><span>🔐</span> Portal Login Petugas</a></li>
            <li><a href="<?= BASE_URL ?>/setup.php" class="hover:text-white transition flex items-center gap-1"><span>⚙️</span> Inisialisasi Database</a></li>
            <li><a href="http://localhost:8000/phpmyadmin/" target="_blank" class="hover:text-white transition flex items-center gap-1"><span>🗄️</span> phpMyAdmin Database</a></li>
          </ul>
        </div>

      </div>

      <div class="text-center text-xs text-slate-500">
        &copy; <?= date('Y') ?> <?= APP_NAME ?> &bull; <?= CAMPUS_NAME ?> &bull; <?= SEMESTER_TA ?>. All rights reserved.
      </div>
    </div>
  </footer>

</body>
</html>
