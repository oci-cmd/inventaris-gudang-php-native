<?php
/**
 * Konfigurasi Koneksi Basis Data MySQL / MariaDB Menggunakan PDO
 * Pemrograman Web Dasar (Sesuai Materi P13 & P14)
 */

// Parameter Koneksi Default (Mendukung XAMPP / MAMP / Native MariaDB Mac)
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'db_inventaris_gudang');
define('DB_USER', 'root');
define('DB_PASS', '');

// ==============================================================================
// 1. PENENTUAN BASE_URL SECARA OTOMATIS & MULTI-ENVIRONMENT
// ==============================================================================

// Deteksi protokol web: Jika HTTPS aktif gunakan 'https://', jika tidak gunakan 'http://'
// (Menggunakan operator ternary untuk memeriksa variabel superglobal $_SERVER['HTTPS'])
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";

// Ambil alamat host dan nomor port yang sedang aktif di peramban (misal: 'localhost:8383' atau '127.0.0.1:8000')
// Operator Null Coalescing (??) digunakan sebagai nilai cadangan (fallback) ke 'localhost'
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Ambil path berkas skrip PHP yang sedang diakses (contoh: '/auth/login.php')
// str_replace digunakan untuk mengubah backslash Windows (\) menjadi forward slash (/) demi kompatibilitas multi-OS
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');

$projectFolder = basename(dirname(__DIR__)); // Dinamis: menghasilkan nama folder 'inventaris-gudang-si'
$pos = strpos($scriptName, '/' . $projectFolder);

if ($pos !== false) {
    // KONDISI A: Proyek berjalan di dalam subfolder (misal: http://localhost:8000/inventaris-gudang-si/...)
    // Ambil path URL dari awal hingga batas nama folder proyek
    $baseSub = substr($scriptName, 0, $pos + strlen('/' . $projectFolder));
} else {
    // KONDISI B: Proyek dijalankan di root domain / port mandiri (misal: http://localhost:8383/...)
    // Subfolder dikosongkan karena root server langsung mengarah ke proyek ini
    $baseSub = '';
}
// Gabungkan protokol, host, dan subfolder menjadi konstanta BASE_URL yang bersih tanpa garis miring penutup
define('BASE_URL', rtrim($protocol . $host . $baseSub, '/'));

// Konstanta Identitas Aplikasi
define('APP_NAME', 'AutoStock SCM');
define('APP_TITLE', 'Sistem Informasi Inventaris & Logistik Gudang Pabrik');
define('CAMPUS_NAME', 'STMIK IKMI Cirebon');
define('SEMESTER_TA', 'Semester Ganjil 2026/2027');

/**
 * Fungsi Pembantu Mendapatkan Objek Koneksi PDO
 * @return PDO
 */
function getDB(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        // String identitas sumber data koneksi (Data Source Name) yang jelas dan deskriptif
        $data_source_name = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false, // Menutup celah SQL Injection
        ];
        try {
            $pdo = new PDO($data_source_name, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("<div style='font-family:sans-serif;padding:24px;background:#FEE2E2;border:1px solid #FCA5A5;color:#991B1B;border-radius:10px;max-width:650px;margin:50px auto;line-height:1.6;'>"
                . "<h3 style='margin-top:0;'>⚠️ Gagal Terhubung ke Basis Data MySQL!</h3>"
                . "<p>Pastikan MySQL server aktif di port <strong>" . DB_PORT . "</strong> dan database <strong>" . DB_NAME . "</strong> sudah diinisialisasi.</p>"
                . "<p>Silakan klik <a href='" . BASE_URL . "/setup.php' style='color:#2563EB;font-weight:bold;text-decoration:underline;'>setup.php</a> untuk migrasi otomatis database 1-klik.</p>"
                . "<small style='color:#6B7280;'>Pesan Teknis: " . htmlspecialchars($e->getMessage()) . "</small>"
                . "</div>");
        }
    }
    return $pdo;
}

/**
 * Fungsi Pembantu Proteksi Autentikasi Halaman (Materi Perkuliahan P12: Session Handling)
 * Digunakan untuk memproteksi halaman internal (dashboard & modul CRUD) agar hanya bisa diakses setelah login.
 */
function checkAuth()
{
    // 1. Cek status sesi: Jika sesi PHP belum aktif, aktifkan dengan session_start()
    // Konstanta PHP_SESSION_NONE memastikan sesi hanya dimulai sekali dan mencegah pesan warning "session already started"
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // 2. Cek apakah pengguna sudah memiliki data sesi aktif ($_SESSION['user_id'])
    // Jika $_SESSION['user_id'] kosong (belum login / sesi habis), paksa pengalihan ke form login
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . '/auth/login.php');
        exit; // WAJIB: Hentikan pemrosesan skrip seketika agar kode sensitif di bawahnya tidak bocor/dieksekusi
    }
}

