<?php
session_start();
define('BASE_URL', '');

if (isset($_SESSION['id_user'])) { header("Location: dashboard/dashboard.php"); exit(); }

require_once 'config/koneksi.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$email || !$password) {
        $error = 'Email dan password wajib diisi.';
    } else {
        $stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        $ok = false;
        if ($user) {
            if (password_verify($password, $user['password'])) $ok = true;
            elseif ($user['password'] === $password) $ok = true;
        }

        if ($ok) {
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['nama']    = $user['nama'];
            $_SESSION['email']   = $user['email'];
            $_SESSION['role']    = $user['role'];
            header("Location: dashboard/dashboard.php"); exit();
        } else {
            $error = 'Email atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Zahra Laundry</title>
  <link href="assets/css/style.css" rel="stylesheet">
  <style>
    .toggle-pw-btn {
      position: absolute;
      right: 12px; top: 50%;
      transform: translateY(-50%);
      background: none; border: none;
      cursor: pointer; color: #9ca3af;
      font-size: 0.78rem; font-weight: 600;
      padding: 0; font-family: inherit;
    }
    .toggle-pw-btn:hover { color: #6b7280; }
  </style>
</head>
<body>
<div class="login-page">
  <div class="login-card">

    <div class="login-logo" style="font-size:1.1rem;font-weight:800;letter-spacing:1px;">ZL</div>
    <div class="login-title">Zahra Laundry</div>
    <div class="login-sub">Sistem Manajemen Laundry</div>

    <?php if ($error): ?>
    <div class="alert alert-danger" data-auto>
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="login.php">

      <div class="form-group">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control"
               placeholder="Masukkan email"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
               required autofocus>
      </div>

      <div class="form-group" style="margin-bottom:26px;">
        <label class="form-label">Password</label>
        <div style="position:relative;">
          <input type="password" name="password" id="pw" class="form-control"
                 placeholder="Masukkan password"
                 style="padding-right:60px;" required>
          <button type="button" class="toggle-pw-btn" onclick="togglePw('pw','eye-txt')">
            <span id="eye-txt">Lihat</span>
          </button>
        </div>
      </div>

      <button type="submit" class="btn btn-primary btn-block btn-lg">
        Masuk
      </button>

    </form>

    <div style="margin-top:22px;padding-top:18px;border-top:1px solid #e5e7eb;text-align:center;">
      <div style="font-size:0.78rem;color:#9ca3af;margin-bottom:10px;">Pelanggan? Cek status tanpa login</div>
      <a href="pelanggan/cek_status.php"
         style="display:flex;align-items:center;justify-content:center;gap:8px;
                padding:10px 18px;border-radius:9px;
                background:#f5f3ff;color:#7c3aed;
                font-weight:700;font-size:0.875rem;text-decoration:none;
                border:1.5px solid #ddd6fe;transition:background .18s;"
         onmouseover="this.style.background='#ede9fe'"
         onmouseout="this.style.background='#f5f3ff'">
        Cek Status Pesanan
      </a>
    </div>

  </div>
</div>
<script>
function togglePw(inputId, spanId) {
  var inp  = document.getElementById(inputId);
  var span = document.getElementById(spanId);
  if (inp.type === 'password') {
    inp.type   = 'text';
    span.textContent = 'Sembunyikan';
  } else {
    inp.type   = 'password';
    span.textContent = 'Lihat';
  }
}
</script>
</body>
</html>