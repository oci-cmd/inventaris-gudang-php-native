<?php
$pageTitle = 'Data Kategori Barang';
require_once __DIR__ . '/../../layout/header.php';

$db = getDB();

// Query Read dengan penghitungan jumlah barang terkait
$sql = "SELECT k.*, COUNT(b.id) AS total_barang 
        FROM kategori k 
        LEFT JOIN barang b ON k.id = b.kategori_id 
        GROUP BY k.id 
        ORDER BY k.id DESC";
$kategoriList = $db->query($sql)->fetchAll();
?>

<!-- HEADER MODUL & AKSI -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
  <div>
    <div class="flex items-center gap-2">
      <span class="text-xs font-bold uppercase tracking-wider text-blue-600 bg-blue-50 px-2 py-0.5 rounded">Modul 1 (CRUD)</span>
      <span class="text-xs text-slate-400">&bull;</span>
      <span class="text-xs text-slate-500 font-medium">Tanggung Jawab Anggota Kelompok 1</span>
    </div>
    <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Kategori Barang</h1>
    <p class="text-xs text-slate-500 mt-0.5">Pengelompokan jenis suku cadang dan perlengkapan logistik pabrik</p>
  </div>

  <div class="flex items-center gap-3 w-full sm:w-auto">
    <div class="relative flex-1 sm:w-64">
      <input type="text" id="searchKategori" placeholder="Cari kategori..." 
             class="w-full pl-9 pr-4 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition shadow-sm">
      <span class="absolute left-3 top-2.5 text-slate-400 text-xs">🔍</span>
    </div>

    <a href="<?= BASE_URL ?>/modul/kategori/tambah.php" 
       class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-xl text-xs shadow-md shadow-blue-500/20 transition flex items-center gap-1.5 whitespace-nowrap">
      <span>➕</span> Tambah Kategori
    </a>
  </div>
</div>

<!-- ALERT NOTIFIKASI -->
<?php if (isset($_GET['status'])): ?>
  <?php if ($_GET['status'] === 'created'): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl p-3 mb-5 auto-dismiss flex items-center gap-2">
      <span>✅</span> <strong>Sukses!</strong> Data kategori baru berhasil disimpan.
    </div>
  <?php elseif ($_GET['status'] === 'updated'): ?>
    <div class="bg-blue-50 border border-blue-200 text-blue-800 text-xs rounded-xl p-3 mb-5 auto-dismiss flex items-center gap-2">
      <span>✏️</span> <strong>Sukses!</strong> Perubahan data kategori berhasil diperbarui.
    </div>
  <?php elseif ($_GET['status'] === 'deleted'): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl p-3 mb-5 auto-dismiss flex items-center gap-2">
      <span>🗑️</span> <strong>Terhapus!</strong> Data kategori berhasil dihapus dari sistem.
    </div>
  <?php elseif ($_GET['status'] === 'error_has_barang'): ?>
    <div class="bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl p-3 mb-5 auto-dismiss flex items-center gap-2">
      <span>⚠️</span> <strong>Gagal Hapus!</strong> Kategori tidak dapat dihapus karena masih digunakan oleh beberapa item barang.
    </div>
  <?php endif; ?>
<?php endif; ?>

<!-- TABEL DATA KATEGORI -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-left text-xs text-slate-600" id="tableKategori">
      <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[11px] tracking-wider border-b border-slate-200">
        <tr>
          <th class="py-3.5 px-4 w-12 text-center">No</th>
          <th class="py-3.5 px-4">Kode Kategori</th>
          <th class="py-3.5 px-4">Nama Kategori</th>
          <th class="py-3.5 px-4">Keterangan / Deskripsi</th>
          <th class="py-3.5 px-4 text-center">Jumlah Barang</th>
          <th class="py-3.5 px-4 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php if (empty($kategoriList)): ?>
          <tr>
            <td colspan="6" class="py-8 text-center text-slate-400">Belum ada data kategori yang terdaftar.</td>
          </tr>
        <?php else: ?>
          <?php $no = 1; foreach ($kategoriList as $k): ?>
            <tr class="hover:bg-slate-50/80 transition">
              <td class="py-3.5 px-4 text-center font-semibold text-slate-400"><?= $no++ ?></td>
              <td class="py-3.5 px-4 font-mono font-bold text-blue-600"><?= htmlspecialchars($k['kode_kategori']) ?></td>
              <td class="py-3.5 px-4 font-bold text-slate-900"><?= htmlspecialchars($k['nama_kategori']) ?></td>
              <td class="py-3.5 px-4 text-slate-500 max-w-xs truncate"><?= htmlspecialchars($k['keterangan'] ?: '-') ?></td>
              <td class="py-3.5 px-4 text-center">
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                  <?= $k['total_barang'] ?> Item
                </span>
              </td>
              <td class="py-3.5 px-4 text-right">
                <div class="inline-flex items-center gap-1.5">
                  <a href="<?= BASE_URL ?>/modul/kategori/edit.php?id=<?= $k['id'] ?>" 
                     class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition" title="Ubah Data">
                    ✏️
                  </a>
                  <a href="<?= BASE_URL ?>/modul/kategori/hapus.php?id=<?= $k['id'] ?>" 
                     onclick="return confirm('Apakah Anda yakin ingin menghapus kategori: <?= htmlspecialchars($k['nama_kategori']) ?>?')"
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
    setupTableFilter('searchKategori', 'tableKategori');
  });
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>

