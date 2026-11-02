<?php
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika sudah login, langsung ke dashboard
if (!empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Silakan isi nama pengguna (username) dan kata sandi!";
    } else {
        $db = getDB();
        // Prepared Statement PDO (Materi P13 & P14: Anti SQL Injection)
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        // Verifikasi Kata Sandi dengan password_verify (Materi P15: Keamanan Hash)
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']      = $user['id'];
            $_SESSION['username']     = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role']         = $user['role'];

            header('Location: ' . BASE_URL . '/dashboard.php');
            exit;
        } else {
            $error = "Username atau kata sandi tidak valid. Cek kembali akun Anda!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - <?= APP_NAME ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-blue-950 min-h-screen flex items-center justify-center p-4">

  <div class="max-w-md w-full">
    
    <!-- Branding Header -->
    <div class="text-center mb-8">
      <div class="w-16 h-16 bg-blue-600/20 border border-blue-500/30 rounded-2xl flex items-center justify-center mx-auto text-3xl shadow-xl shadow-blue-500/10 mb-3 text-white backdrop-blur">
        📦
      </div>
      <h1 class="text-2xl font-black text-white tracking-tight"><?= APP_NAME ?></h1>
      <p class="text-xs font-semibold uppercase tracking-wider text-blue-400 mt-1"><?= CAMPUS_NAME ?></p>
      <p class="text-xs text-slate-400 mt-1"><?= APP_TITLE ?></p>
    </div>

    <!-- Login Card -->
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 p-8">
      
      <div class="mb-6">
        <h2 class="text-lg font-bold text-slate-900">Masuk ke Sistem</h2>
        <p class="text-xs text-slate-500 mt-0.5">Gunakan kredensial akun dosen/petugas logistik Anda</p>
      </div>

      <?php if (!empty($error)): ?>
        <div class="bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl p-3 mb-5 flex items-start gap-2">
          <span class="text-sm">⚠️</span>
          <span><?= htmlspecialchars($error) ?></span>
        </div>
      <?php endif; ?>

      <?php if (isset($_GET['msg']) && $_GET['msg'] === 'logged_out'): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs rounded-xl p-3 mb-5 flex items-start gap-2">
          <span class="text-sm">👋</span>
          <span>Anda telah berhasil keluar dari sesi sistem.</span>
        </div>
      <?php endif; ?>

      <form method="POST" action="" class="space-y-4">
        
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="username">
            Nama Pengguna (Username)
          </label>
          <input type="text" id="username" name="username" required autofocus
                 value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                 placeholder="Contoh: admin"
                 class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="password">
            Kata Sandi (Password)
          </label>
          <div class="relative">
            <input type="password" id="password" name="password" required
                   placeholder="Masukkan kata sandi..."
                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition pr-10">
            <button type="button" onclick="togglePassword()" 
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 text-sm">
              <span id="eye-icon">👁️</span>
            </button>
          </div>
        </div>

        <button type="submit" 
                class="w-full mt-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-blue-500/25 text-sm transition">
          Masuk ke Dashboard &rarr;
        </button>

      </form>

      <div class="mt-5 pt-4 border-t border-slate-100 text-center">
        <a href="<?= BASE_URL ?>/index.php" class="text-xs font-semibold text-slate-500 hover:text-blue-600 transition inline-flex items-center gap-1.5">
          <span>&larr;</span> Kembali ke Halaman Utama (Landing Page)
        </a>
      </div>

      <!-- Demo Accounts Information -->
      <div class="mt-6 pt-5 border-t border-slate-100 bg-slate-50/70 -mx-8 -mb-8 p-5 rounded-b-2xl text-[11px] text-slate-500">
        <div class="font-bold text-slate-700 mb-1">Akun Default Praktikum:</div>
        <div class="flex justify-between items-center py-0.5">
          <span>Admin Gudang:</span>
          <span class="font-mono bg-white px-1.5 py-0.5 rounded border border-slate-200">admin / admin123</span>
        </div>
        <div class="flex justify-between items-center py-0.5">
          <span>Staff Logistik:</span>
          <span class="font-mono bg-white px-1.5 py-0.5 rounded border border-slate-200">petugas1 / admin123</span>
        </div>
      </div>

    </div>

    <!-- Campus & Academic Year Footer -->
    <div class="text-center mt-6 text-xs text-slate-500">
      &copy; <?= date('Y') ?> <?= CAMPUS_NAME ?> &bull; <?= SEMESTER_TA ?>
    </div>

  </div>

  <script>
    function togglePassword() {
      const pass = document.getElementById('password');
      const eye = document.getElementById('eye-icon');
      if (pass.type === 'password') {
        pass.type = 'text';
        eye.textContent = '🙈';
      } else {
        pass.type = 'password';
        eye.textContent = '👁️';
      }
    }
  </script>

</body>
</html>

