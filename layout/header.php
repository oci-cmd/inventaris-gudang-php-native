<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
checkAuth();

$currentUser = [
    'nama' => $_SESSION['nama_lengkap'] ?? 'Petugas Gudang',
    'role' => $_SESSION['role'] ?? 'petugas',
    'username' => $_SESSION['username'] ?? 'user'
];

$currentPage = $_SERVER['PHP_SELF'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? 'Dashboard' ?> - <?= APP_NAME ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }
  </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">

  <!-- TOPBAR NAVIGATION -->
  <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        
        <!-- Logo & Brand -->
        <div class="flex items-center gap-3">
          <a href="<?= BASE_URL ?>/dashboard.php" class="flex items-center gap-2.5 group">
            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-blue-500/20 group-hover:scale-105 transition">
              📦
            </div>
            <div>
              <div class="font-black text-slate-900 text-base leading-none tracking-tight flex items-center gap-1.5">
                <?= APP_NAME ?>
                <span class="text-[10px] bg-blue-100 text-blue-700 font-bold px-1.5 py-0.5 rounded uppercase tracking-wider">SI & KA</span>
              </div>
              <div class="text-[11px] text-slate-400 font-medium leading-none mt-1">
                <?= CAMPUS_NAME ?>
              </div>
            </div>
          </a>
        </div>

        <!-- Main Navigation Links -->
        <nav class="hidden md:flex items-center gap-1">
          <a href="<?= BASE_URL ?>/dashboard.php" 
             class="px-3.5 py-2 rounded-lg text-sm font-semibold transition <?= str_contains($currentPage, 'dashboard.php') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' ?>">
            📊 Dashboard
          </a>

          <a href="<?= BASE_URL ?>/modul/kategori/index.php" 
             class="px-3.5 py-2 rounded-lg text-sm font-semibold transition <?= str_contains($currentPage, 'modul/kategori') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' ?>">
            📁 Kategori
          </a>

          <a href="<?= BASE_URL ?>/modul/barang/index.php" 
             class="px-3.5 py-2 rounded-lg text-sm font-semibold transition <?= str_contains($currentPage, 'modul/barang') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' ?>">
            📦 Barang
          </a>

          <a href="<?= BASE_URL ?>/modul/supplier/index.php" 
             class="px-3.5 py-2 rounded-lg text-sm font-semibold transition <?= str_contains($currentPage, 'modul/supplier') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' ?>">
            🏢 Supplier
          </a>

          <a href="<?= BASE_URL ?>/modul/transaksi/index.php" 
             class="px-3.5 py-2 rounded-lg text-sm font-semibold transition <?= str_contains($currentPage, 'modul/transaksi') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' ?>">
            🔄 Mutasi Stok
          </a>

          <a href="<?= BASE_URL ?>/index.php" target="_blank"
             class="px-3 py-2 rounded-lg text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition flex items-center gap-1.5 ml-2" title="Lihat Website Frontend Publik">
            <span>🌐</span>
            <span>Web Publik</span>
          </a>
        </nav>

        <!-- User Profile & Logout -->
        <div class="flex items-center gap-3">
          <div class="text-right hidden sm:block">
            <div class="text-xs font-bold text-slate-800 leading-tight"><?= htmlspecialchars($currentUser['nama']) ?></div>
            <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Role: <span class="text-blue-600"><?= htmlspecialchars($currentUser['role']) ?></span></div>
          </div>
          <a href="<?= BASE_URL ?>/auth/logout.php" 
             onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?')"
             class="p-2 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 transition text-sm font-semibold flex items-center gap-1.5" title="Logout">
            <span class="hidden sm:inline text-xs">Keluar</span>
            <span>🚪</span>
          </a>
        </div>

      </div>
    </div>
  </header>

  <!-- MAIN CONTAINER -->
  <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

