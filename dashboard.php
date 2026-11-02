<?php
$pageTitle = 'Dashboard Utama';
require_once __DIR__ . '/layout/header.php';

$db = getDB();

// 1. Statistik Ringkasan Data
$totalBarang    = $db->query("SELECT COUNT(*) FROM barang")->fetchColumn();
$totalKategori  = $db->query("SELECT COUNT(*) FROM kategori")->fetchColumn();
$totalSupplier  = $db->query("SELECT COUNT(*) FROM supplier")->fetchColumn();
$totalStokUnit  = $db->query("SELECT SUM(stok) FROM barang")->fetchColumn() ?: 0;
$totalKritis    = $db->query("SELECT COUNT(*) FROM barang WHERE stok <= stok_min")->fetchColumn();

// 2. Daftar Barang dengan Stok Kritis (Mendekati / Melebihi Batas Minimum)
$stmtKritis = $db->query("SELECT b.*, k.nama_kategori 
                          FROM barang b 
                          JOIN kategori k ON b.kategori_id = k.id 
                          WHERE b.stok <= b.stok_min 
                          ORDER BY b.stok ASC 
                          LIMIT 5");
$barangKritis = $stmtKritis->fetchAll();

// 3. Riwayat 5 Transaksi Mutasi Stok Terakhir
$stmtTrx = $db->query("SELECT t.*, b.nama_barang, b.kode_barang, b.satuan, s.nama_supplier 
                       FROM transaksi_stok t 
                       JOIN barang b ON t.barang_id = b.id 
                       LEFT JOIN supplier s ON t.supplier_id = s.id 
                       ORDER BY t.id DESC 
                       LIMIT 5");
$transaksiTerakhir = $stmtTrx->fetchAll();
?>

<!-- WELCOME BANNER -->
<div class="bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 rounded-2xl p-6 sm:p-8 text-white shadow-xl shadow-blue-600/15 mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
  <div>
    <div class="inline-flex items-center gap-2 bg-white/15 px-3 py-1 rounded-full text-xs font-semibold backdrop-blur mb-3">
      <span>🏭</span>
      <span>Logistik & Pergudangan Pabrik Terpadu</span>
    </div>
    <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
      Selamat Datang, <?= htmlspecialchars($currentUser['nama']) ?>!
    </h1>
    <p class="text-blue-100 text-sm mt-1.5 max-w-2xl leading-relaxed">
      Sistem Informasi Pengelolaan Inventaris, Manajemen Suku Cadang, dan Pencatatan Mutasi Stok. Dirancang ramah pemula untuk praktikum Pemrograman Web Dasar.
    </p>
  </div>

  <div class="flex flex-wrap gap-2.5">
    <a href="<?= BASE_URL ?>/modul/transaksi/tambah.php" 
       class="bg-white hover:bg-blue-50 text-blue-700 font-bold px-4 py-2.5 rounded-xl text-xs shadow-md transition flex items-center gap-2">
      <span>🔄</span> Catat Mutasi Stok
    </a>
    <a href="<?= BASE_URL ?>/modul/barang/tambah.php" 
       class="bg-blue-800/60 hover:bg-blue-800 text-white font-bold px-4 py-2.5 rounded-xl text-xs border border-white/20 transition flex items-center gap-2">
      <span>➕</span> Tambah Barang
    </a>
  </div>
</div>

<!-- 4 STAT CARDS -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
  
  <!-- Total Barang -->
  <a href="<?= BASE_URL ?>/modul/barang/index.php" class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md hover:border-blue-300 transition group">
    <div class="flex items-center justify-between">
      <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold group-hover:scale-110 transition">
        📦
      </div>
      <span class="text-xs font-semibold text-blue-600 flex items-center gap-1">Kelola &rarr;</span>
    </div>
    <div class="mt-4">
      <div class="text-2xl font-black text-slate-900"><?= number_format($totalBarang) ?></div>
      <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">Item Barang / Produk</div>
      <div class="text-[11px] text-slate-400 mt-1">Total fisik: <?= number_format($totalStokUnit) ?> unit</div>
    </div>
  </a>

  <!-- Total Kategori -->
  <a href="<?= BASE_URL ?>/modul/kategori/index.php" class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md hover:border-indigo-300 transition group">
    <div class="flex items-center justify-between">
      <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold group-hover:scale-110 transition">
        📁
      </div>
      <span class="text-xs font-semibold text-indigo-600 flex items-center gap-1">Kelola &rarr;</span>
    </div>
    <div class="mt-4">
      <div class="text-2xl font-black text-slate-900"><?= number_format($totalKategori) ?></div>
      <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">Kategori Barang</div>
      <div class="text-[11px] text-slate-400 mt-1">Pengelompokan logistik pabrik</div>
    </div>
  </a>

  <!-- Total Supplier -->
  <a href="<?= BASE_URL ?>/modul/supplier/index.php" class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md hover:border-emerald-300 transition group">
    <div class="flex items-center justify-between">
      <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold group-hover:scale-110 transition">
        🏢
      </div>
      <span class="text-xs font-semibold text-emerald-600 flex items-center gap-1">Kelola &rarr;</span>
    </div>
    <div class="mt-4">
      <div class="text-2xl font-black text-slate-900"><?= number_format($totalSupplier) ?></div>
      <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">Supplier / Rekanan</div>
      <div class="text-[11px] text-slate-400 mt-1">Distributor & vendor terdaftar</div>
    </div>
  </a>

  <!-- Stok Kritis -->
  <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm <?= $totalKritis > 0 ? 'bg-rose-50/40 border-rose-200' : '' ?>">
    <div class="flex items-center justify-between">
      <div class="w-12 h-12 rounded-xl <?= $totalKritis > 0 ? 'bg-rose-100 text-rose-600' : 'bg-emerald-50 text-emerald-600' ?> flex items-center justify-center text-xl font-bold">
        <?= $totalKritis > 0 ? '⚠️' : '✅' ?>
      </div>
      <span class="text-xs font-bold uppercase tracking-wider <?= $totalKritis > 0 ? 'text-rose-600' : 'text-emerald-600' ?>">
        <?= $totalKritis > 0 ? 'Perlu Restock' : 'Stok Aman' ?>
      </span>
    </div>
    <div class="mt-4">
      <div class="text-2xl font-black <?= $totalKritis > 0 ? 'text-rose-600' : 'text-slate-900' ?>"><?= number_format($totalKritis) ?></div>
      <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">Barang Stok Kritis</div>
      <div class="text-[11px] text-slate-400 mt-1">Sisa stok &le; stok minimum</div>
    </div>
  </div>

</div>

<!-- DUA KOLOM: STOK KRITIS & TRANSAKSI TERAKHIR -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

  <!-- KOLOM KIRI: PERINGATAN STOK MENIPIS -->
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
      <div>
        <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
          <span>⚠️</span> Peringatan Stok Menipis (Restock Alert)
        </h2>
        <p class="text-[11px] text-slate-400 mt-0.5">Barang dengan sisa stok mendekati atau di bawah batas minimum</p>
      </div>
      <a href="<?= BASE_URL ?>/modul/barang/index.php" class="text-xs font-semibold text-blue-600 hover:text-blue-800">Lihat Semua &rarr;</a>
    </div>

    <div class="p-4">
      <?php if (empty($barangKritis)): ?>
        <div class="text-center py-8 text-slate-400 text-xs">
          <span>🎉</span> Seluruh stok barang berada dalam kondisi aman di atas batas minimum.
        </div>
      <?php else: ?>
        <div class="space-y-3">
          <?php foreach ($barangKritis as $k): ?>
            <div class="p-3.5 rounded-xl border border-rose-100 bg-rose-50/50 flex items-center justify-between gap-4">
              <div>
                <div class="font-bold text-xs text-slate-900"><?= htmlspecialchars($k['nama_barang']) ?></div>
                <div class="text-[11px] text-slate-500 mt-0.5">
                  Kode: <span class="font-mono text-slate-700"><?= htmlspecialchars($k['kode_barang']) ?></span> &bull; 
                  <?= htmlspecialchars($k['nama_kategori']) ?>
                </div>
              </div>
              <div class="text-right">
                <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold bg-rose-600 text-white">
                  Sisa: <?= $k['stok'] ?> <?= htmlspecialchars($k['satuan']) ?>
                </span>
                <div class="text-[10px] text-rose-700 mt-0.5 font-medium">Min: <?= $k['stok_min'] ?> <?= htmlspecialchars($k['satuan']) ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- KOLOM KANAN: 5 MUTASI STOK TERAKHIR -->
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
      <div>
        <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
          <span>🔄</span> Aktivitas Mutasi Stok Terakhir
        </h2>
        <p class="text-[11px] text-slate-400 mt-0.5">Catatan riwayat keluar-masuk barang terbaru</p>
      </div>
      <a href="<?= BASE_URL ?>/modul/transaksi/index.php" class="text-xs font-semibold text-blue-600 hover:text-blue-800">Semua Mutasi &rarr;</a>
    </div>

    <div class="divide-y divide-slate-100">
      <?php if (empty($transaksiTerakhir)): ?>
        <div class="text-center py-8 text-slate-400 text-xs">Belum ada catatan mutasi stok.</div>
      <?php else: ?>
        <?php foreach ($transaksiTerakhir as $t): ?>
          <div class="px-6 py-3.5 flex items-center justify-between hover:bg-slate-50 transition">
            <div class="flex items-center gap-3">
              <span class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold <?= $t['jenis_transaksi'] === 'Masuk' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' ?>">
                <?= $t['jenis_transaksi'] === 'Masuk' ? '📥' : '📤' ?>
              </span>
              <div>
                <div class="text-xs font-bold text-slate-900"><?= htmlspecialchars($t['nama_barang']) ?></div>
                <div class="text-[11px] text-slate-400 mt-0.5">
                  <?= date('d M Y', strtotime($t['tanggal'])) ?> &bull; Oleh: <?= htmlspecialchars($t['petugas']) ?>
                </div>
              </div>
            </div>
            <div class="text-right">
              <span class="text-xs font-bold <?= $t['jenis_transaksi'] === 'Masuk' ? 'text-emerald-600' : 'text-rose-600' ?>">
                <?= $t['jenis_transaksi'] === 'Masuk' ? '+' : '-' ?><?= $t['jumlah'] ?> <?= htmlspecialchars($t['satuan']) ?>
              </span>
              <div class="text-[10px] text-slate-400 font-mono"><?= htmlspecialchars($t['no_transaksi']) ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>

