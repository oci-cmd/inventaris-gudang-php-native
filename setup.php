<?php
/**
 * Auto-Migrator & Seeder Basis Data 1-Klik
 * STMIK IKMI Cirebon - Pemrograman Web Dasar
 */

require_once __DIR__ . '/config/database.php';

$message = null;
$error = null;

$isCli = (php_sapi_name() === 'cli');
$isPost = isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST';

if ($isCli || $isPost || isset($_GET['auto'])) {
    try {
        // Hubungkan ke MySQL Server tanpa memilih db terlebih dahulu
        $pdo = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4", DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $sqlFile = __DIR__ . '/database.sql';
        if (!file_exists($sqlFile)) {
            throw new Exception("Berkas skema database.sql tidak ditemukan di direktori proyek!");
        }

        $sqlContent = file_get_contents($sqlFile);
        $pdo->exec($sqlContent);
        $message = "Basis data <strong>" . DB_NAME . "</strong> beserta 5 tabel dan data awal (seeder) berhasil diinisialisasi 100%!";
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Setup Basis Data - <?= APP_NAME ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4 font-sans text-slate-800">

  <div class="max-w-xl w-full bg-white rounded-2xl shadow-xl border border-slate-200 p-8">
    <div class="text-center mb-6">
      <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mx-auto text-3xl mb-3 shadow-inner">
        📦
      </div>
      <h1 class="text-2xl font-black text-slate-900 tracking-tight"><?= APP_NAME ?></h1>
      <p class="text-xs font-semibold uppercase tracking-wider text-blue-600 mt-1"><?= CAMPUS_NAME ?> &bull; <?= SEMESTER_TA ?></p>
      <p class="text-sm text-slate-500 mt-2">Auto-Migrator Basis Data untuk Modul Praktikum Pemrograman Web Dasar</p>
    </div>

    <?php if ($message): ?>
      <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 mb-6 text-sm">
        <div class="font-bold flex items-center gap-2 mb-1">
          <span>✅ Inisialisasi Berhasil!</span>
        </div>
        <p><?= $message ?></p>
        <div class="mt-4 pt-3 border-t border-emerald-200 flex gap-3">
          <a href="<?= BASE_URL ?>/auth/login.php" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2 rounded-lg text-xs transition inline-block">
            Menuju Halaman Login &rarr;
          </a>
          <a href="<?= BASE_URL ?>/index.php" class="bg-slate-800 hover:bg-slate-900 text-white font-bold px-4 py-2 rounded-lg text-xs transition inline-block">
            Buka Halaman Utama (Web Publik) &rarr;
          </a>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-xl p-4 mb-6 text-sm">
        <div class="font-bold flex items-center gap-2 mb-1">
          <span>❌ Gagal Migrasi Database!</span>
        </div>
        <p class="font-mono text-xs mt-1"><?= htmlspecialchars($error) ?></p>
      </div>
    <?php endif; ?>

    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs text-slate-600 mb-6 space-y-2">
      <div class="font-bold text-slate-700">Tabel yang Akan Dibuat:</div>
      <ul class="list-disc list-inside space-y-1 text-slate-500">
        <li><strong>users</strong>: Akun admin & staff logistik (Otentikasi & Sesi P12/P15)</li>
        <li><strong>kategori</strong>: Modul 1 (CRUD Kategori & Satuan Barang)</li>
        <li><strong>supplier</strong>: Modul 3 (CRUD Pemasok & Kontak Rekanan)</li>
        <li><strong>barang</strong>: Modul 2 (CRUD Katalog Suku Cadang & Stok Minimum)</li>
        <li><strong>transaksi_stok</strong>: Modul 4 (CRUD Mutasi Barang Masuk & Keluar)</li>
      </ul>
      <div class="pt-2 text-slate-500 border-t border-slate-200">
        Default Akun: <strong>admin</strong> / <code>admin123</code> &bull; <strong>petugas1</strong> / <code>petugas123</code>
      </div>
    </div>

    <form method="POST">
      <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-blue-500/20 text-sm transition">
        ⚡ Inisialisasi Database Sekarang
      </button>
    </form>
  </div>

</body>
</html>
