<?php
$pageTitle = 'Riwayat Mutasi Stok';
require_once __DIR__ . '/../../layout/header.php';

$db = getDB();

$filterJenis = $_GET['jenis'] ?? '';

$sql = "SELECT t.*, b.nama_barang, b.kode_barang, b.satuan, s.nama_supplier 
        FROM transaksi_stok t 
        JOIN barang b ON t.barang_id = b.id 
        LEFT JOIN supplier s ON t.supplier_id = s.id ";

$params = [];
if ($filterJenis === 'Masuk' || $filterJenis === 'Keluar') {
    $sql .= " WHERE t.jenis_transaksi = :jenis ";
    $params[':jenis'] = $filterJenis;
}

$sql .= " ORDER BY t.id DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$transaksiList = $stmt->fetchAll();

// Hitung total kuantitas mutasi
$totalMasuk  = $db->query("SELECT SUM(jumlah) FROM transaksi_stok WHERE jenis_transaksi = 'Masuk'")->fetchColumn() ?: 0;
$totalKeluar = $db->query("SELECT SUM(jumlah) FROM transaksi_stok WHERE jenis_transaksi = 'Keluar'")->fetchColumn() ?: 0;
?>

<!-- HEADER MODUL & STATISTIK MUTASI -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
  <div>
    <div class="flex items-center gap-2">
      <span class="text-xs font-bold uppercase tracking-wider text-blue-600 bg-blue-50 px-2 py-0.5 rounded">Modul 4 (CRUD)</span>
      <span class="text-xs text-slate-400">&bull;</span>
      <span class="text-xs text-slate-500 font-medium">Tanggung Jawab Anggota Kelompok 4</span>
    </div>
    <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Riwayat Mutasi Stok Gudang</h1>
    <p class="text-xs text-slate-500 mt-0.5">Pencatatan penerimaan barang masuk dan pengeluaran suku cadang mesin</p>
  </div>

  <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
    <!-- Filter Jenis Transaksi -->
    <form method="GET" class="flex items-center">
      <select name="jenis" onchange="this.form.submit()" 
              class="px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm text-slate-700">
        <option value="">-- Semua Jenis Mutasi --</option>
        <option value="Masuk" <?= $filterJenis === 'Masuk' ? 'selected' : '' ?>>📥 Barang Masuk</option>
        <option value="Keluar" <?= $filterJenis === 'Keluar' ? 'selected' : '' ?>>📤 Barang Keluar</option>
      </select>
    </form>

    <div class="relative flex-1 sm:w-48">
      <input type="text" id="searchTransaksi" placeholder="Cari transaksi..." 
             class="w-full pl-9 pr-4 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition shadow-sm">
      <span class="absolute left-3 top-2.5 text-slate-400 text-xs">🔍</span>
    </div>

    <a href="<?= BASE_URL ?>/modul/transaksi/tambah.php" 
       class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-xl text-xs shadow-md shadow-blue-500/20 transition flex items-center gap-1.5 whitespace-nowrap">
      <span>🔄</span> Catat Mutasi
    </a>
  </div>
</div>

<!-- 2 MINI STAT CARDS -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
  <div class="bg-emerald-50/70 border border-emerald-200 rounded-xl p-4 flex items-center gap-3">
    <div class="w-10 h-10 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-lg">
      📥
    </div>
    <div>
      <div class="text-lg font-black text-emerald-900">+<?= number_format($totalMasuk) ?> Unit</div>
      <div class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Total Akumulasi Barang Masuk</div>
    </div>
  </div>

  <div class="bg-rose-50/70 border border-rose-200 rounded-xl p-4 flex items-center gap-3">
    <div class="w-10 h-10 rounded-lg bg-rose-600 text-white flex items-center justify-center font-bold text-lg">
      📤
    </div>
    <div>
      <div class="text-lg font-black text-rose-900">-<?= number_format($totalKeluar) ?> Unit</div>
      <div class="text-[11px] font-bold text-rose-700 uppercase tracking-wider">Total Akumulasi Barang Keluar</div>
    </div>
  </div>
</div>

<!-- ALERT NOTIFIKASI -->
<?php if (isset($_GET['status'])): ?>
  <?php if ($_GET['status'] === 'created'): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl p-3 mb-5 auto-dismiss flex items-center gap-2">
      <span>✅</span> <strong>Sukses!</strong> Transaksi mutasi stok berhasil dicatat dan kuantitas barang diperbarui otomatis.
    </div>
  <?php elseif ($_GET['status'] === 'deleted'): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl p-3 mb-5 auto-dismiss flex items-center gap-2">
      <span>🗑️</span> <strong>Dibatalkan!</strong> Catatan mutasi stok dihapus dan sisa fisik barang telah dikembalikan.
    </div>
  <?php endif; ?>
<?php endif; ?>

<!-- TABEL DATA TRANSAKSI -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-left text-xs text-slate-600" id="tableTransaksi">
      <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[11px] tracking-wider border-b border-slate-200">
        <tr>
          <th class="py-3.5 px-4 w-10 text-center">No</th>
          <th class="py-3.5 px-4">No. Transaksi</th>
          <th class="py-3.5 px-4">Tanggal</th>
          <th class="py-3.5 px-4">Nama Barang</th>
          <th class="py-3.5 px-4 text-center">Jenis</th>
          <th class="py-3.5 px-4 text-center">Jumlah</th>
          <th class="py-3.5 px-4">Supplier / Keterangan</th>
          <th class="py-3.5 px-4">Petugas</th>
          <th class="py-3.5 px-4 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php if (empty($transaksiList)): ?>
          <tr>
            <td colspan="9" class="py-8 text-center text-slate-400">Belum ada transaksi mutasi stok yang tercatat.</td>
          </tr>
        <?php else: ?>
          <?php $no = 1; foreach ($transaksiList as $t): ?>
            <tr class="hover:bg-slate-50/80 transition">
              <td class="py-3.5 px-4 text-center font-semibold text-slate-400"><?= $no++ ?></td>
              <td class="py-3.5 px-4 font-mono font-bold text-slate-800"><?= htmlspecialchars($t['no_transaksi']) ?></td>
              <td class="py-3.5 px-4 font-medium text-slate-700"><?= date('d M Y', strtotime($t['tanggal'])) ?></td>
              <td class="py-3.5 px-4">
                <div class="font-bold text-slate-900"><?= htmlspecialchars($t['nama_barang']) ?></div>
                <div class="text-[10px] text-slate-400 font-mono"><?= htmlspecialchars($t['kode_barang']) ?></div>
              </td>
              <td class="py-3.5 px-4 text-center">
                <?php if ($t['jenis_transaksi'] === 'Masuk'): ?>
                  <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                    <span>📥</span> Masuk
                  </span>
                <?php else: ?>
                  <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                    <span>📤</span> Keluar
                  </span>
                <?php endif; ?>
              </td>
              <td class="py-3.5 px-4 text-center">
                <span class="font-bold font-mono text-xs <?= $t['jenis_transaksi'] === 'Masuk' ? 'text-emerald-600' : 'text-rose-600' ?>">
                  <?= $t['jenis_transaksi'] === 'Masuk' ? '+' : '-' ?><?= $t['jumlah'] ?> <?= htmlspecialchars($t['satuan']) ?>
                </span>
              </td>
              <td class="py-3.5 px-4 max-w-xs truncate">
                <?php if ($t['jenis_transaksi'] === 'Masuk' && !empty($t['nama_supplier'])): ?>
                  <span class="font-bold text-slate-800 block"><?= htmlspecialchars($t['nama_supplier']) ?></span>
                <?php endif; ?>
                <span class="text-[11px] text-slate-500"><?= htmlspecialchars($t['keterangan'] ?: '-') ?></span>
              </td>
              <td class="py-3.5 px-4 font-medium text-slate-700"><?= htmlspecialchars($t['petugas']) ?></td>
              <td class="py-3.5 px-4 text-right">
                <a href="<?= BASE_URL ?>/modul/transaksi/hapus.php?id=<?= $t['id'] ?>" 
                   onclick="return confirm('Apakah Anda yakin ingin membatalkan transaksi <?= htmlspecialchars($t['no_transaksi']) ?>? Sisa stok barang akan dikembalikan otomatis.')"
                   class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition inline-block" title="Batalkan Transaksi">
                  🗑️
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    setupTableFilter('searchTransaksi', 'tableTransaksi');
  });
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>

