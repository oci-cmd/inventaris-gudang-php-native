<?php
require_once __DIR__ . '/../../config/database.php';
checkAuth();

$id = (int)($_GET['id'] ?? 0);
$db = getDB();

if ($id > 0) {
    // 1. Cek apakah ada barang yang menggunakan kategori ini
    $cek = $db->prepare("SELECT COUNT(*) FROM barang WHERE kategori_id = :id");
    $cek->execute([':id' => $id]);
    $count = $cek->fetchColumn();

    if ($count > 0) {
        // Jangan hapus jika masih ada barang terkait
        header('Location: ' . BASE_URL . '/modul/kategori/index.php?status=error_has_barang');
        exit;
    }

    // 2. Prepared Statement DELETE aman
    $stmt = $db->prepare("DELETE FROM kategori WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

header('Location: ' . BASE_URL . '/modul/kategori/index.php?status=deleted');
exit;

