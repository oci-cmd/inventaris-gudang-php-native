<?php
$pageTitle = 'Catat Mutasi Stok';
require_once __DIR__ . '/../../layout/header.php';

$db = getDB();

// Ambil list barang dan supplier untuk dropdown
$barangList   = $db->query("SELECT id, kode_barang, nama_barang, satuan, stok FROM barang ORDER BY nama_barang ASC")->fetchAll();
$supplierList = $db->query("SELECT id, nama_supplier FROM supplier ORDER BY nama_supplier ASC")->fetchAll();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $barangId       = (int)($_POST['barang_id'] ?? 0);
    $jenisTransaksi = $_POST['jenis_transaksi'] ?? 'Masuk';
    $supplierId     = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null;
    $jumlah         = (int)($_POST['jumlah'] ?? 0);
    $tanggal        = $_POST['tanggal'] ?? date('Y-m-d');
    $keterangan     = trim($_POST['keterangan'] ?? '');
    $petugas        = $currentUser['nama'];

    if ($barangId <= 0 || $jumlah <= 0 || empty($tanggal)) {
        $error = "Pilih barang, tanggal, dan masukkan jumlah kuantitas yang valid (lebih dari 0)!";
    } else {
        // 1. Cek sisa stok barang saat ini
        $stmtB = $db->prepare("SELECT nama_barang, stok, satuan FROM barang WHERE id = :id");
        $stmtB->execute([':id' => $barangId]);
        $barangData = $stmtB->fetch();

        if (!$barangData) {
            $error = "Data barang tidak valid atau tidak ditemukan!";
        } elseif ($jenisTransaksi === 'Keluar' && $barangData['stok'] < $jumlah) {
            // Validasi Pencegahan Stok Minus
            $error = "Stok fisik tidak mencukupi untuk dikeluarkan! Sisa stok {$barangData['nama_barang']} saat ini hanya {$barangData['stok']} {$barangData['satuan']}.";
        } else {
            // 2. Generate Nomor Transaksi Unik: TRX-IN/OUT-YYYYMMDD-XXXX
            $prefix = ($jenisTransaksi === 'Masuk') ? 'TRX-IN' : 'TRX-OUT';
            $noTransaksi = $prefix . '-' . date('Ymd', strtotime($tanggal)) . '-' . strtoupper(substr(uniqid(), -4));

            try {
                // Gunakan Transaksi PDO (P13 / P14: Integritas Data ACID)
                $db->beginTransaction();

                // Simpan Riwayat Mutasi ke transaksi_stok
                $sqlInsert = "INSERT INTO transaksi_stok (no_transaksi, barang_id, supplier_id, jenis_transaksi, jumlah, tanggal, keterangan, petugas) 
                              VALUES (:no, :bid, :sid, :jenis, :jml, :tgl, :ket, :petugas)";
                $stmtInsert = $db->prepare($sqlInsert);
                $stmtInsert->execute([
                    ':no'      => $noTransaksi,
                    ':bid'     => $barangId,
                    ':sid'     => ($jenisTransaksi === 'Masuk' ? $supplierId : null),
                    ':jenis'   => $jenisTransaksi,
                    ':jml'     => $jumlah,
                    ':tgl'     => $tanggal,
                    ':ket'     => $keterangan,
                    ':petugas' => $petugas
                ]);

                // Update Kuantitas Stok di Tabel barang
                if ($jenisTransaksi === 'Masuk') {
                    $sqlUpdate = "UPDATE barang SET stok = stok + :jml WHERE id = :id";
                } else {
                    $sqlUpdate = "UPDATE barang SET stok = stok - :jml WHERE id = :id";
                }
                $stmtUpdate = $db->prepare($sqlUpdate);
                $stmtUpdate->execute([':jml' => $jumlah, ':id' => $barangId]);

                $db->commit();

                header('Location: ' . BASE_URL . '/modul/transaksi/index.php?status=created');
                exit;

            } catch (Exception $e) {
                $db->rollBack();
                $error = "Gagal memproses transaksi: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="max-w-3xl mx-auto">
  
  <div class="flex items-center gap-2 mb-6">
    <a href="<?= BASE_URL ?>/modul/transaksi/index.php" class="text-xs font-semibold text-slate-500 hover:text-blue-600 transition flex items-center gap-1">
      &larr; Kembali ke Riwayat Mutasi
    </a>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
    <div class="border-b border-slate-100 pb-4 mb-6">
      <div class="text-xs font-bold uppercase tracking-wider text-blue-600">Formulir Mutasi Stok</div>
      <h1 class="text-xl font-black text-slate-900 tracking-tight mt-0.5">Catat Barang Masuk / Keluar</h1>
      <p class="text-xs text-slate-500 mt-1">Sistem otomatis memperbarui sisa stok barang secara akurat</p>
    </div>

    <?php if ($error): ?>
      <div class="bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl p-3.5 mb-5 flex items-start gap-2">
        <span>⚠️</span>
        <span><?= htmlspecialchars($error) ?></span>
      </div>
    <?php endif; ?>

    <form method="POST" action="" class="space-y-5">
      
      <!-- Pilihan Jenis Transaksi -->
      <div>
        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
          Jenis Mutasi Stok <span class="text-rose-500">*</span>
        </label>
        <div class="grid grid-cols-2 gap-4">
          <label class="relative flex items-center justify-center p-3.5 border rounded-xl cursor-pointer hover:bg-slate-50 transition border-slate-200" id="label-masuk">
            <input type="radio" name="jenis_transaksi" value="Masuk" checked onchange="toggleSupplierField(this.value)" class="sr-only">
            <div class="flex items-center gap-2 font-bold text-xs text-emerald-700">
              <span class="text-base">📥</span> Barang Masuk (Restock / Penerimaan)
            </div>
          </label>
          <label class="relative flex items-center justify-center p-3.5 border rounded-xl cursor-pointer hover:bg-slate-50 transition border-slate-200" id="label-keluar">
            <input type="radio" name="jenis_transaksi" value="Keluar" onchange="toggleSupplierField(this.value)" class="sr-only">
            <div class="flex items-center gap-2 font-bold text-xs text-rose-700">
              <span class="text-base">📤</span> Barang Keluar (Pemakaian Mesin)
            </div>
          </label>
        </div>
      </div>

      <!-- Pilih Barang -->
      <div>
        <label for="barang_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
          Pilih Barang / Suku Cadang <span class="text-rose-500">*</span>
        </label>
        <select id="barang_id" name="barang_id" required
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
          <option value="">-- Pilih Barang --</option>
          <?php foreach ($barangList as $b): ?>
            <option value="<?= $b['id'] ?>" <?= ((int)($_POST['barang_id'] ?? 0) === (int)$b['id']) ? 'selected' : '' ?>>
              [<?= htmlspecialchars($b['kode_barang']) ?>] <?= htmlspecialchars($b['nama_barang']) ?> (Sisa: <?= $b['stok'] ?> <?= htmlspecialchars($b['satuan']) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Supplier (Hanya jika Barang Masuk) -->
      <div id="field-supplier">
        <label for="supplier_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
          Supplier / Pemasok (Opsional)
        </label>
        <select id="supplier_id" name="supplier_id"
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
          <option value="">-- Tidak Memilih Supplier --</option>
          <?php foreach ($supplierList as $s): ?>
            <option value="<?= $s['id'] ?>" <?= ((int)($_POST['supplier_id'] ?? 0) === (int)$s['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($s['nama_supplier']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Jumlah dan Tanggal -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
          <label for="jumlah" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
            Jumlah Kuantitas <span class="text-rose-500">*</span>
          </label>
          <input type="number" id="jumlah" name="jumlah" min="1" required
                 placeholder="Contoh: 10"
                 value="<?= htmlspecialchars($_POST['jumlah'] ?? '1') ?>"
                 class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
        </div>

        <div>
          <label for="tanggal" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
            Tanggal Transaksi <span class="text-rose-500">*</span>
          </label>
          <input type="date" id="tanggal" name="tanggal" required
                 value="<?= htmlspecialchars($_POST['tanggal'] ?? date('Y-m-d')) ?>"
                 class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
        </div>
      </div>

      <!-- Keterangan -->
      <div>
        <label for="keterangan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
          Keterangan / Alasan Mutasi
        </label>
        <textarea id="keterangan" name="keterangan" rows="3"
                  placeholder="Misal: Penerimaan PO #042 atau Perbaikan Mesin Line B..."
                  class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition"><?= htmlspecialchars($_POST['keterangan'] ?? '') ?></textarea>
      </div>

      <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
        <a href="<?= BASE_URL ?>/modul/transaksi/index.php" 
           class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
          Batal
        </a>
        <button type="submit" 
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2.5 rounded-xl text-xs shadow-md shadow-blue-500/20 transition">
          💾 Simpan Mutasi Stok
        </button>
      </div>

    </form>
  </div>

</div>

<script>
  function toggleSupplierField(jenis) {
    const fieldSup = document.getElementById('field-supplier');
    const labelMasuk = document.getElementById('label-masuk');
    const labelKeluar = document.getElementById('label-keluar');

    if (jenis === 'Masuk') {
      fieldSup.style.display = 'block';
      labelMasuk.classList.add('bg-emerald-50', 'border-emerald-500');
      labelKeluar.classList.remove('bg-rose-50', 'border-rose-500');
    } else {
      fieldSup.style.display = 'none';
      labelKeluar.classList.add('bg-rose-50', 'border-rose-500');
      labelMasuk.classList.remove('bg-emerald-50', 'border-emerald-500');
    }
  }

  // Set initial style
  toggleSupplierField('Masuk');
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>

