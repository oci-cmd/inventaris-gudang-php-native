<?php
$pageTitle = 'Data Rekanan & Supplier';
require_once __DIR__ . '/../../layout/header.php';

$db = getDB();

$sql = "SELECT s.*, COUNT(t.id) AS total_transaksi 
        FROM supplier s 
        LEFT JOIN transaksi_stok t ON s.id = t.supplier_id 
        GROUP BY s.id 
        ORDER BY s.id DESC";
$supplierList = $db->query($sql)->fetchAll();
?>

<!-- HEADER MODUL & AKSI -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
  <div>
    <div class="flex items-center gap-2">
      <span class="text-xs font-bold uppercase tracking-wider text-blue-600 bg-blue-50 px-2 py-0.5 rounded">Modul 3 (CRUD)</span>
      <span class="text-xs text-slate-400">&bull;</span>
      <span class="text-xs text-slate-500 font-medium">Tanggung Jawab Anggota Kelompok 3</span>
    </div>
    <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Supplier & Rekanan Pabrik</h1>
    <p class="text-xs text-slate-500 mt-0.5">Daftar distributor, vendor, dan pemasok suku cadang resmi</p>
  </div>

  <div class="flex items-center gap-3 w-full sm:w-auto">
    <div class="relative flex-1 sm:w-64">
      <input type="text" id="searchSupplier" placeholder="Cari supplier..." 
             class="w-full pl-9 pr-4 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition shadow-sm">
      <span class="absolute left-3 top-2.5 text-slate-400 text-xs">🔍</span>
    </div>

    <a href="<?= BASE_URL ?>/modul/supplier/tambah.php" 
       class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-xl text-xs shadow-md shadow-blue-500/20 transition flex items-center gap-1.5 whitespace-nowrap">
      <span>➕</span> Tambah Supplier
    </a>
  </div>
</div>

<!-- ALERT NOTIFIKASI -->
<?php if (isset($_GET['status'])): ?>
  <?php if ($_GET['status'] === 'created'): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl p-3 mb-5 auto-dismiss flex items-center gap-2">
      <span>✅</span> <strong>Sukses!</strong> Data supplier baru berhasil didaftarkan.
    </div>
  <?php elseif ($_GET['status'] === 'updated'): ?>
    <div class="bg-blue-50 border border-blue-200 text-blue-800 text-xs rounded-xl p-3 mb-5 auto-dismiss flex items-center gap-2">
      <span>✏️</span> <strong>Sukses!</strong> Rincian supplier berhasil diperbarui.
    </div>
  <?php elseif ($_GET['status'] === 'deleted'): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl p-3 mb-5 auto-dismiss flex items-center gap-2">
      <span>🗑️</span> <strong>Terhapus!</strong> Data supplier berhasil dihapus dari sistem.
    </div>
  <?php endif; ?>
<?php endif; ?>

<!-- TABEL DATA SUPPLIER -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-left text-xs text-slate-600" id="tableSupplier">
      <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[11px] tracking-wider border-b border-slate-200">
        <tr>
          <th class="py-3.5 px-4 w-12 text-center">No</th>
          <th class="py-3.5 px-4">Kode</th>
          <th class="py-3.5 px-4">Nama Perusahaan / Supplier</th>
          <th class="py-3.5 px-4">Kontak / Telepon</th>
          <th class="py-3.5 px-4">Alamat Kantor</th>
          <th class="py-3.5 px-4 text-center">Riwayat Pasokan</th>
          <th class="py-3.5 px-4 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php if (empty($supplierList)): ?>
          <tr>
            <td colspan="7" class="py-8 text-center text-slate-400">Belum ada supplier yang terdaftar.</td>
          </tr>
        <?php else: ?>
          <?php $no = 1; foreach ($supplierList as $s): ?>
            <tr class="hover:bg-slate-50/80 transition">
              <td class="py-3.5 px-4 text-center font-semibold text-slate-400"><?= $no++ ?></td>
              <td class="py-3.5 px-4 font-mono font-bold text-blue-600"><?= htmlspecialchars($s['kode_supplier']) ?></td>
              <td class="py-3.5 px-4 font-bold text-slate-900"><?= htmlspecialchars($s['nama_supplier']) ?></td>
              <td class="py-3.5 px-4 font-medium text-slate-700"><?= htmlspecialchars($s['telepon']) ?></td>
              <td class="py-3.5 px-4 text-slate-500 max-w-xs truncate"><?= htmlspecialchars($s['alamat'] ?: '-') ?></td>
              <td class="py-3.5 px-4 text-center">
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                  <?= $s['total_transaksi'] ?> Kali
                </span>
              </td>
              <td class="py-3.5 px-4 text-right">
                <div class="inline-flex items-center gap-1.5">
                  <a href="<?= BASE_URL ?>/modul/supplier/edit.php?id=<?= $s['id'] ?>" 
                     class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition" title="Ubah Data">
                    ✏️
                  </a>
                  <a href="<?= BASE_URL ?>/modul/supplier/hapus.php?id=<?= $s['id'] ?>" 
                     onclick="return confirm('Apakah Anda yakin ingin menghapus supplier: <?= htmlspecialchars($s['nama_supplier']) ?>?')"
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
    setupTableFilter('searchSupplier', 'tableSupplier');
  });
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>

