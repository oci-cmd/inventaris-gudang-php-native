<?php
$pageTitle = 'Tambah Supplier / Pemasok';
require_once __DIR__ . '/../../layout/header.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode    = strtoupper(trim($_POST['kode_supplier'] ?? ''));
    $nama    = trim($_POST['nama_supplier'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $alamat  = trim($_POST['alamat'] ?? '');

    if (empty($kode) || empty($nama) || empty($telepon)) {
        $error = "Kode supplier, nama supplier, dan telepon wajib diisi!";
    } else {
        $db = getDB();
        $cek = $db->prepare("SELECT COUNT(*) FROM supplier WHERE kode_supplier = :kode");
        $cek->execute([':kode' => $kode]);
        if ($cek->fetchColumn() > 0) {
            $error = "Kode supplier '{$kode}' sudah digunakan! Harap pakai kode unik lain.";
        } else {
            // Prepared Statement INSERT (P13 / P14)
            $sql = "INSERT INTO supplier (kode_supplier, nama_supplier, telepon, alamat) 
                    VALUES (:kode, :nama, :telp, :alamat)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':kode'   => $kode,
                ':nama'   => $nama,
                ':telp'   => $telepon,
                ':alamat' => $alamat
            ]);

            header('Location: ' . BASE_URL . '/modul/supplier/index.php?status=created');
            exit;
        }
    }
}
?>

<div class="max-w-2xl mx-auto">
  
  <div class="flex items-center gap-2 mb-6">
    <a href="<?= BASE_URL ?>/modul/supplier/index.php" class="text-xs font-semibold text-slate-500 hover:text-blue-600 transition flex items-center gap-1">
      &larr; Kembali ke Daftar Supplier
    </a>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
    <div class="border-b border-slate-100 pb-4 mb-6">
      <div class="text-xs font-bold uppercase tracking-wider text-blue-600">Formulir Tambah Data</div>
      <h1 class="text-xl font-black text-slate-900 tracking-tight mt-0.5">Tambah Supplier / Rekanan</h1>
      <p class="text-xs text-slate-500 mt-1">Daftarkan vendor atau pemasok suku cadang baru</p>
    </div>

    <?php if ($error): ?>
      <div class="bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl p-3.5 mb-5 flex items-start gap-2">
        <span>⚠️</span>
        <span><?= htmlspecialchars($error) ?></span>
      </div>
    <?php endif; ?>

    <form method="POST" action="" class="space-y-5">
      
      <div>
        <label for="kode_supplier" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
          Kode Supplier <span class="text-rose-500">*</span>
        </label>
        <input type="text" id="kode_supplier" name="kode_supplier" required autofocus
               placeholder="Contoh: SUP-006"
               value="<?= htmlspecialchars($_POST['kode_supplier'] ?? '') ?>"
               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono uppercase text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
      </div>

      <div>
        <label for="nama_supplier" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
          Nama Perusahaan / Supplier <span class="text-rose-500">*</span>
        </label>
        <input type="text" id="nama_supplier" name="nama_supplier" required
               placeholder="Contoh: PT Prima Teknik Nusantara"
               value="<?= htmlspecialchars($_POST['nama_supplier'] ?? '') ?>"
               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
      </div>

      <div>
        <label for="telepon" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
          Nomor Kontak / Telepon <span class="text-rose-500">*</span>
        </label>
        <input type="text" id="telepon" name="telepon" required
               placeholder="Contoh: 0812-9876-5432"
               value="<?= htmlspecialchars($_POST['telepon'] ?? '') ?>"
               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
      </div>

      <div>
        <label for="alamat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
          Alamat Lengkap Kantor / Gudang
        </label>
        <textarea id="alamat" name="alamat" rows="3"
                  placeholder="Alamat jalan, kota, dan lokasi gudang distributor..."
                  class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition"><?= htmlspecialchars($_POST['alamat'] ?? '') ?></textarea>
      </div>

      <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
        <a href="<?= BASE_URL ?>/modul/supplier/index.php" 
           class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
          Batal
        </a>
        <button type="submit" 
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2.5 rounded-xl text-xs shadow-md shadow-blue-500/20 transition">
          💾 Simpan Data Supplier
        </button>
      </div>

    </form>
  </div>

</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>

