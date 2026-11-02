<?php
$pageTitle = 'Tambah Barang / Suku Cadang';
require_once __DIR__ . '/../../layout/header.php';

$db = getDB();
$kategoriList = $db->query("SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori ASC")->fetchAll();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode       = strtoupper(trim($_POST['kode_barang'] ?? ''));
    $kategoriId = (int)($_POST['kategori_id'] ?? 0);
    $nama       = trim($_POST['nama_barang'] ?? '');
    $satuan     = trim($_POST['satuan'] ?? '');
    $hargaBeli  = (float)($_POST['harga_beli'] ?? 0);
    $hargaJual  = (float)($_POST['harga_jual'] ?? 0);
    $stok       = (int)($_POST['stok'] ?? 0);
    $stokMin    = (int)($_POST['stok_min'] ?? 5);

    if (empty($kode) || empty($nama) || empty($satuan) || $kategoriId <= 0) {
        $error = "Kode, nama barang, kategori, dan satuan wajib diisi!";
    } else {
        // Cek duplikasi kode_barang
        $cek = $db->prepare("SELECT COUNT(*) FROM barang WHERE kode_barang = :kode");
        $cek->execute([':kode' => $kode]);
        if ($cek->fetchColumn() > 0) {
            $error = "Kode barang '{$kode}' sudah terdaftar! Harap gunakan kode unik lain.";
        } else {
            // Prepared Statement INSERT (P13 / P14)
            $sql = "INSERT INTO barang (kode_barang, kategori_id, nama_barang, satuan, harga_beli, harga_jual, stok, stok_min) 
                    VALUES (:kode, :kat, :nama, :satuan, :hb, :hj, :stok, :smin)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':kode'   => $kode,
                ':kat'    => $kategoriId,
                ':nama'   => $nama,
                ':satuan' => $satuan,
                ':hb'     => $hargaBeli,
                ':hj'     => $hargaJual,
                ':stok'   => $stok,
                ':smin'   => $stokMin
            ]);

            header('Location: ' . BASE_URL . '/modul/barang/index.php?status=created');
            exit;
        }
    }
}
?>

<div class="max-w-3xl mx-auto">
  
  <div class="flex items-center gap-2 mb-6">
    <a href="<?= BASE_URL ?>/modul/barang/index.php" class="text-xs font-semibold text-slate-500 hover:text-blue-600 transition flex items-center gap-1">
      &larr; Kembali ke Katalog Barang
    </a>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
    <div class="border-b border-slate-100 pb-4 mb-6">
      <div class="text-xs font-bold uppercase tracking-wider text-blue-600">Formulir Tambah Data</div>
      <h1 class="text-xl font-black text-slate-900 tracking-tight mt-0.5">Tambah Barang / Suku Cadang</h1>
      <p class="text-xs text-slate-500 mt-1">Daftarkan item logistik baru ke dalam katalog sistem inventaris</p>
    </div>

    <?php if ($error): ?>
      <div class="bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl p-3.5 mb-5 flex items-start gap-2">
        <span>⚠️</span>
        <span><?= htmlspecialchars($error) ?></span>
      </div>
    <?php endif; ?>

    <form method="POST" action="" class="space-y-5">
      
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
          <label for="kode_barang" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
            Kode Barang <span class="text-rose-500">*</span>
          </label>
          <input type="text" id="kode_barang" name="kode_barang" required autofocus
                 placeholder="Contoh: BRG-009"
                 value="<?= htmlspecialchars($_POST['kode_barang'] ?? '') ?>"
                 class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono uppercase text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
        </div>

        <div>
          <label for="kategori_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
            Kategori Barang <span class="text-rose-500">*</span>
          </label>
          <select id="kategori_id" name="kategori_id" required
                  class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
            <option value="">-- Pilih Kategori --</option>
            <?php foreach ($kategoriList as $kat): ?>
              <option value="<?= $kat['id'] ?>" <?= ((int)($_POST['kategori_id'] ?? 0) === (int)$kat['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($kat['nama_kategori']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="sm:col-span-2">
          <label for="nama_barang" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
            Nama Barang / Suku Cadang <span class="text-rose-500">*</span>
          </label>
          <input type="text" id="nama_barang" name="nama_barang" required
                 placeholder="Contoh: Oil Seal Viton 35x50x8"
                 value="<?= htmlspecialchars($_POST['nama_barang'] ?? '') ?>"
                 class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
        </div>

        <div>
          <label for="satuan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
            Satuan Barang <span class="text-rose-500">*</span>
          </label>
          <input type="text" id="satuan" name="satuan" required
                 placeholder="Pcs / Unit / Box / Can"
                 value="<?= htmlspecialchars($_POST['satuan'] ?? 'Pcs') ?>"
                 class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
          <label for="harga_beli" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
            Harga Beli (Rp)
          </label>
          <input type="number" id="harga_beli" name="harga_beli" min="0" step="100"
                 placeholder="0"
                 value="<?= htmlspecialchars($_POST['harga_beli'] ?? '0') ?>"
                 class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
        </div>

        <div>
          <label for="harga_jual" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
            Harga Jual / Standar (Rp)
          </label>
          <input type="number" id="harga_jual" name="harga_jual" min="0" step="100"
                 placeholder="0"
                 value="<?= htmlspecialchars($_POST['harga_jual'] ?? '0') ?>"
                 class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
          <label for="stok" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
            Stok Awal Fisik
          </label>
          <input type="number" id="stok" name="stok" min="0"
                 placeholder="0"
                 value="<?= htmlspecialchars($_POST['stok'] ?? '0') ?>"
                 class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
        </div>

        <div>
          <label for="stok_min" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
            Batas Stok Minimum (Safety Stock)
          </label>
          <input type="number" id="stok_min" name="stok_min" min="1"
                 placeholder="5"
                 value="<?= htmlspecialchars($_POST['stok_min'] ?? '5') ?>"
                 class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
          <p class="text-[11px] text-slate-400 mt-1">Sistem akan memberi alert peringatan jika stok &le; angka ini.</p>
        </div>
      </div>

      <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
        <a href="<?= BASE_URL ?>/modul/barang/index.php" 
           class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
          Batal
        </a>
        <button type="submit" 
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2.5 rounded-xl text-xs shadow-md shadow-blue-500/20 transition">
          💾 Simpan Data Barang
        </button>
      </div>

    </form>
  </div>

</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>

