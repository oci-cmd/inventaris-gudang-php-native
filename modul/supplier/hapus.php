<?php
require_once __DIR__ . '/../../config/database.php';
checkAuth();

$id = (int)($_GET['id'] ?? 0);
$db = getDB();

if ($id > 0) {
    // Prepared Statement DELETE aman
    $stmt = $db->prepare("DELETE FROM supplier WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

header('Location: ' . BASE_URL . '/modul/supplier/index.php?status=deleted');
exit;

