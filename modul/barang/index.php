<?php
$pageTitle = 'Katalog Suku Cadang & Barang';
require_once __DIR__ . '/../../layout/header.php';

$db = getDB();

// Filter Kategori jika dipilih
$kategoriFilter = (int)($_GET['kategori_id'] ?? 0);

$query = "SELECT b.*, k.nama_kategori 
          FROM barang b 
          JOIN kategori k ON b.kategori_id = k.id ";

$params = [];
if ($kategoriFilter > 0) {
    $query .= " WHERE b.kategori_id = :kat_id ";
    $params[':kat_id'] = $kategoriFilter;
}

$query .= " ORDER BY b.id DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$barangList = $stmt->fetchAll();

// Ambil semua kategori untuk dropdown filter
$kategoriList = $db->query("SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori ASC")->fetchAll();
?>

<!-- HEADER MODUL & AKSI -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
  <div>
    <div class="flex items-center gap-2">
      <span class="text-xs font-bold uppercase tracking-wider text-blue-600 bg-blue-50 px-2 py-0.5 rounded">Modul 2 (CRUD)</span>
      <span class="text-xs text-slate-400">&bull;</span>
      <span class="text-xs text-slate-500 font-medium">Tanggung Jawab Anggota Kelompok 2</span>
    </div>
    <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Katalog Suku Cadang & Barang</h1>
    <p class="text-xs text-slate-500 mt-0.5">Master data suku cadang mesin, sparepart, dan stok minimum logistik</p>
  </div>

  <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
    <!-- Filter Kategori Dropdown -->
    <form method="GET" class="flex items-center">
      <select name="kategori_id" onchange="this.form.submit()" 
              class="px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm text-slate-700">
        <option value="0">-- Semua Kategori --</option>
        <?php foreach ($kategoriList as $kat): ?>
          <option value="<?= $kat['id'] ?>" <?= $kategoriFilter === (int)$kat['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($kat['nama_kategori']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>

    <div class="relative flex-1 sm:w-48">
      <input type="text" id="searchBarang" placeholder="Cari barang..." 
             class="w-full pl-9 pr-4 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition shadow-sm">
      <span class="absolute left-3 top-2.5 text-slate-400 text-xs">🔍</span>
    </div>

    <a href="<?= BASE_URL ?>/modul/barang/tambah.php" 
       class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-xl text-xs shadow-md shadow-blue-500/20 transition flex items-center gap-1.5 whitespace-nowrap">
      <span>➕</span> Tambah Barang
    </a>
  </div>
</div>

<!-- ALERT NOTIFIKASI -->
<?php if (isset($_GET['status'])): ?>
  <?php if ($_GET['status'] === 'created'): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl p-3 mb-5 auto-dismiss flex items-center gap-2">
      <span>✅</span> <strong>Sukses!</strong> Data barang baru berhasil didaftarkan.
    </div>
  <?php elseif ($_GET['status'] === 'updated'): ?>
    <div class="bg-blue-50 border border-blue-200 text-blue-800 text-xs rounded-xl p-3 mb-5 auto-dismiss flex items-center gap-2">
      <span>✏️</span> <strong>Sukses!</strong> Data barang berhasil diperbarui.
    </div>
  <?php elseif ($_GET['status'] === 'deleted'): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl p-3 mb-5 auto-dismiss flex items-center gap-2">
      <span>🗑️</span> <strong>Terhapus!</strong> Data barang berhasil dihapus dari sistem.
    </div>
  <?php endif; ?>
<?php endif; ?>

<!-- TABEL DATA BARANG -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-left text-xs text-slate-600" id="tableBarang">
      <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[11px] tracking-wider border-b border-slate-200">
        <tr>
          <th class="py-3.5 px-4 w-10 text-center">No</th>
          <th class="py-3.5 px-4">Kode</th>
          <th class="py-3.5 px-4">Nama Barang & Kategori</th>
          <th class="py-3.5 px-4">Satuan</th>
          <th class="py-3.5 px-4 text-right">Harga Beli</th>
          <th class="py-3.5 px-4 text-right">Harga Jual</th>
          <th class="py-3.5 px-4 text-center">Sisa Stok</th>
          <th class="py-3.5 px-4 text-center">Status</th>
          <th class="py-3.5 px-4 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php if (empty($barangList)): ?>
          <tr>
            <td colspan="9" class="py-8 text-center text-slate-400">Tidak ada data barang yang sesuai kriteria pencarian.</td>
          </tr>
        <?php else: ?>
          <?php $no = 1; foreach ($barangList as $b): ?>
            <?php 
              $isKritis = $b['stok'] <= $b['stok_min'];
            ?>
            <tr class="hover:bg-slate-50/80 transition <?= $isKritis ? 'bg-rose-50/30' : '' ?>">
              <td class="py-3.5 px-4 text-center font-semibold text-slate-400"><?= $no++ ?></td>
              <td class="py-3.5 px-4 font-mono font-bold text-blue-600"><?= htmlspecialchars($b['kode_barang']) ?></td>
              <td class="py-3.5 px-4">
                <div class="font-bold text-slate-900"><?= htmlspecialchars($b['nama_barang']) ?></div>
                <div class="text-[11px] text-slate-400 mt-0.5"><?= htmlspecialchars($b['nama_kategori']) ?></div>
              </td>
              <td class="py-3.5 px-4 font-medium text-slate-700"><?= htmlspecialchars($b['satuan']) ?></td>
              <td class="py-3.5 px-4 text-right font-mono">Rp <?= number_format($b['harga_beli'], 0, ',', '.') ?></td>
              <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">Rp <?= number_format($b['harga_jual'], 0, ',', '.') ?></td>
              <td class="py-3.5 px-4 text-center">
                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold <?= $isKritis ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-800' ?>">
                  <?= $b['stok'] ?>
                </span>
                <span class="text-[10px] text-slate-400 block mt-0.5">Min: <?= $b['stok_min'] ?></span>
              </td>
              <td class="py-3.5 px-4 text-center">
                <?php if ($isKritis): ?>
                  <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-600 text-white">
                    <span>⚠️</span> Kritis
                  </span>
                <?php else: ?>
                  <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                    <span>✅</span> Aman
                  </span>
                <?php endif; ?>
              </td>
              <td class="py-3.5 px-4 text-right">
                <div class="inline-flex items-center gap-1.5">
                  <a href="<?= BASE_URL ?>/modul/barang/edit.php?id=<?= $b['id'] ?>" 
                     class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition" title="Ubah Data">
                    ✏️
                  </a>
                  <a href="<?= BASE_URL ?>/modul/barang/hapus.php?id=<?= $b['id'] ?>" 
                     onclick="return confirm('Apakah Anda yakin ingin menghapus barang: <?= htmlspecialchars($b['nama_barang']) ?>?')"
                     class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus Data">
                    🗑️
                  </a>
                </div>
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
    setupTableFilter('searchBarang', 'tableBarang');
  });
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>

