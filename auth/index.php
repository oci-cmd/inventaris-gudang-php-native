<?php
/**
 * Fallback Router Folder Auth
 * Mengalihkan secara otomatis ke login.php jika ada pengguna yang mengakses /auth/ atau /auth/index.php
 */
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/dashboard.php');
} else {
    header('Location: ' . BASE_URL . '/auth/login.php');
}
exit;

