<?php
require_once __DIR__ . '/../../config/database.php';
checkAuth();

$id = (int)($_GET['id'] ?? 0);
$db = getDB();

if ($id > 0) {
    // 1. Ambil detail transaksi yang akan dibatalkan
    $stmt = $db->prepare("SELECT * FROM transaksi_stok WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $trx = $stmt->fetch();

    if ($trx) {
        try {
            $db->beginTransaction();

            // 2. Kembalikan saldo stok fisik barang
            if ($trx['jenis_transaksi'] === 'Masuk') {
                // Batalkan masuk: kurangi kembali stok
                $sqlRevert = "UPDATE barang SET stok = stok - :jml WHERE id = :bid";
            } else {
                // Batalkan keluar: tambahkan kembali stok
                $sqlRevert = "UPDATE barang SET stok = stok + :jml WHERE id = :bid";
            }
            $stmtRevert = $db->prepare($sqlRevert);
            $stmtRevert->execute([':jml' => $trx['jumlah'], ':bid' => $trx['barang_id']]);

            // 3. Hapus catatan transaksi
            $stmtDel = $db->prepare("DELETE FROM transaksi_stok WHERE id = :id");
            $stmtDel->execute([':id' => $id]);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            die("Gagal membatalkan transaksi: " . $e->getMessage());
        }
    }
}

header('Location: ' . BASE_URL . '/modul/transaksi/index.php?status=deleted');
exit;

