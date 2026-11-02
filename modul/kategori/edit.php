<?php
$pageTitle = 'Ubah Kategori Barang';
require_once __DIR__ . '/../../layout/header.php';

$db = getDB();
$id = (int)($_GET['id'] ?? 0);

// Ambil data kategori lama
$stmt = $db->prepare("SELECT * FROM kategori WHERE id = :id");
$stmt->execute([':id' => $id]);
$kategori = $stmt->fetch();

if (!$kategori) {
    header('Location: ' . BASE_URL . '/modul/kategori/index.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = strtoupper(trim($_POST['kode_kategori'] ?? ''));
    $nama = trim($_POST['nama_kategori'] ?? '');
    $ket  = trim($_POST['keterangan'] ?? '');

    if (empty($kode) || empty($nama)) {
        $error = "Kode kategori dan nama kategori wajib diisi!";
    } else {
        // Cek duplikasi kode pada ID lain
        $cekStmt = $db->prepare("SELECT COUNT(*) FROM kategori WHERE kode_kategori = :kode AND id != :id");
        $cekStmt->execute([':kode' => $kode, ':id' => $id]);
        if ($cekStmt->fetchColumn() > 0) {
            $error = "Kode kategori '{$kode}' sudah digunakan oleh kategori lain!";
        } else {
            // Prepared Statement UPDATE (P13)
            $sql = "UPDATE kategori SET kode_kategori = :kode, nama_kategori = :nama, keterangan = :ket WHERE id = :id";
            $updateStmt = $db->prepare($sql);
            $updateStmt->execute([
                ':kode' => $kode,
                ':nama' => $nama,
                ':ket'  => $ket,
                ':id'   => $id
            ]);

            header('Location: ' . BASE_URL . '/modul/kategori/index.php?status=updated');
            exit;
        }
    }
}
?>

<div class="max-w-2xl mx-auto">
  
  <div class="flex items-center gap-2 mb-6">
    <a href="<?= BASE_URL ?>/modul/kategori/index.php" class="text-xs font-semibold text-slate-500 hover:text-blue-600 transition flex items-center gap-1">
      &larr; Kembali ke Daftar Kategori
    </a>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
    <div class="border-b border-slate-100 pb-4 mb-6">
      <div class="text-xs font-bold uppercase tracking-wider text-blue-600">Formulir Ubah Data</div>
      <h1 class="text-xl font-black text-slate-900 tracking-tight mt-0.5">Ubah Kategori Barang</h1>
      <p class="text-xs text-slate-500 mt-1">Perbarui rincian data kategori ID #<?= $kategori['id'] ?></p>
    </div>

    <?php if ($error): ?>
      <div class="bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl p-3.5 mb-5 flex items-start gap-2">
        <span>⚠️</span>
        <span><?= htmlspecialchars($error) ?></span>
      </div>
    <?php endif; ?>

    <form method="POST" action="" class="space-y-5">
      
      <div>
        <label for="kode_kategori" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
          Kode Kategori <span class="text-rose-500">*</span>
        </label>
        <input type="text" id="kode_kategori" name="kode_kategori" required
               value="<?= htmlspecialchars($_POST['kode_kategori'] ?? $kategori['kode_kategori']) ?>"
               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono uppercase text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
      </div>

      <div>
        <label for="nama_kategori" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
          Nama Kategori <span class="text-rose-500">*</span>
        </label>
        <input type="text" id="nama_kategori" name="nama_kategori" required
               value="<?= htmlspecialchars($_POST['nama_kategori'] ?? $kategori['nama_kategori']) ?>"
               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
      </div>

      <div>
        <label for="keterangan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
          Keterangan / Deskripsi
        </label>
        <textarea id="keterangan" name="keterangan" rows="3"
                  class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition"><?= htmlspecialchars($_POST['keterangan'] ?? $kategori['keterangan']) ?></textarea>
      </div>

      <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
        <a href="<?= BASE_URL ?>/modul/kategori/index.php" 
           class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
          Batal
        </a>
        <button type="submit" 
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2.5 rounded-xl text-xs shadow-md shadow-blue-500/20 transition">
          💾 Simpan Perubahan
        </button>
      </div>

    </form>
  </div>

</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>

