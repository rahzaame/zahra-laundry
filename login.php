<?php
// login.php — Sprint 1: PBI-002 Halaman login | PBI-003 Role access control
session_start();
define('BASE_URL', '');

if (isset($_SESSION['id_user'])) { header("Location: dashboard/dashboard.php"); exit(); }

require_once 'config/koneksi.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
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
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #1e1040 0%, #2d1a5e 50%, #4c1d95 100%);
      min-height: 100vh;
      display: flex; align-items: center; justify-content: center;
      padding: 20px;
    }

    .card {
      background: #fff; border-radius: 20px; padding: 40px 36px;
      width: 100%; max-width: 420px;
      box-shadow: 0 30px 80px rgba(0,0,0,0.3);
    }

    /* Wordmark */
    .wordmark { display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 6px; }
    .wm-bar   { width: 3px; height: 48px; background: #7c3aed; border-radius: 2px; }
    .wm-text  { display: flex; flex-direction: column; text-align: left; }
    .wm-z     { font-size: 1.7rem; font-weight: 800; color: #1e1b4b; letter-spacing: -0.5px; line-height: 1; }
    .wm-l     { font-size: 1.7rem; font-weight: 300; color: #7c3aed; letter-spacing: 1px; line-height: 1; }
    .wm-sub   { font-size: 0.67rem; font-weight: 500; color: #a78bfa; letter-spacing: 4px; text-transform: uppercase; text-align: center; margin-bottom: 28px; }

    /* Pilihan role */
    .role-title { font-size: 0.82rem; font-weight: 600; color: #6b7280; text-align: center; margin-bottom: 14px; }

    .role-options { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 8px; }

    .role-btn {
      border: 2px solid #e5e7eb; border-radius: 12px;
      padding: 16px 12px; text-align: center;
      cursor: pointer; transition: all 0.2s; background: #fff;
    }
    .role-btn:hover { border-color: #7c3aed; background: #f5f3ff; }
    .role-btn:hover .role-icon { background: #ede9fe; }

    .role-icon {
      width: 44px; height: 44px; border-radius: 10px;
      background: #f3f4f6;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 8px; font-size: 1.3rem;
      transition: all 0.2s;
    }
    .role-name { font-size: 0.82rem; font-weight: 700; color: #1e1b4b; }
    .role-desc { font-size: 0.7rem; color: #9ca3af; margin-top: 2px; }

    /* Form section */
    .form-section { display: none; }
    .form-section.show { display: block; }

    .form-section label {
      display: block; font-size: 0.82rem; font-weight: 600;
      color: #374151; margin-bottom: 6px; margin-top: 14px;
    }
    .form-section label:first-child { margin-top: 0; }

    .form-section input {
      width: 100%; padding: 10px 13px;
      border: 1.5px solid #e5e7eb; border-radius: 9px;
      font-size: 0.9rem; font-family: inherit; outline: none;
      transition: border-color .18s, box-shadow .18s;
    }
    .form-section input:focus { border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,.12); }
    .form-section input::placeholder { color: #d1d5db; }

    .pw-wrap { position: relative; }
    .pw-wrap input { padding-right: 80px; }
    .pw-toggle {
      position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
      background: none; border: none; cursor: pointer;
      color: #9ca3af; font-size: 0.75rem; font-weight: 600;
      font-family: inherit; padding: 0;
    }
    .pw-toggle:hover { color: #6b7280; }

    .btn-submit {
      width: 100%; padding: 11px; margin-top: 20px;
      background: linear-gradient(135deg, #7c3aed, #6d28d9);
      color: #fff; border: none; border-radius: 9px;
      font-size: 0.9rem; font-weight: 700;
      cursor: pointer; font-family: inherit;
      transition: opacity .18s;
    }
    .btn-submit:hover { opacity: .9; }

    .back-btn {
      background: none; border: none; color: #7c3aed;
      font-size: 0.8rem; font-weight: 600; cursor: pointer;
      font-family: inherit; display: block; text-align: center;
      margin-top: 12px; width: 100%; padding: 4px;
    }
    .back-btn:hover { color: #6d28d9; }

    .alert-err {
      background: #fef2f2; border: 1px solid #fecaca;
      border-radius: 8px; padding: 10px 14px;
      color: #991b1b; font-size: 0.84rem;
      margin-bottom: 16px;
    }
  </style>
</head>
<body>
<div class="card">

  <!-- Wordmark -->
  <div class="wordmark">
    <div class="wm-bar"></div>
    <div class="wm-text">
      <span class="wm-z">ZAHRA</span>
      <span class="wm-l">LAUNDRY</span>
    </div>
  </div>
  <div class="wm-sub">Sistem Manajemen Laundry</div>

  <!-- Pilihan Role -->
  <div id="role-picker">
    <div class="role-title">Masuk sebagai</div>
    <div class="role-options">
      <div class="role-btn" onclick="pilih('staff')">
        <div class="role-icon">&#128101;</div>
        <div class="role-name">Staff</div>
        <div class="role-desc">Petugas / Produksi / Owner</div>
      </div>
      <div class="role-btn" onclick="pilih('pelanggan')">
        <div class="role-icon">&#128722;</div>
        <div class="role-name">Pelanggan</div>
        <div class="role-desc">Cek status pesanan</div>
      </div>
    </div>
  </div>

  <!-- Form Staff (login) -->
  <div class="form-section" id="form-staff">

    <?php if ($error): ?>
    <div class="alert-err"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <label>Email</label>
      <input type="email" name="email" placeholder="Masukkan email"
             value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>

      <label>Password</label>
      <div class="pw-wrap">
        <input type="password" name="password" id="pw" placeholder="Masukkan password" required>
        <button type="button" class="pw-toggle" onclick="togglePw('pw','eye')">
          <span id="eye">Lihat</span>
        </button>
      </div>

      <button type="submit" class="btn-submit">Masuk</button>
    </form>

    <button class="back-btn" onclick="kembali()">&#8592; Ganti pilihan</button>
  </div>

  <!-- Form Pelanggan (cek status) — FR-016, PBI-027 -->
  <div class="form-section" id="form-pelanggan">
    <form method="GET" action="pelanggan/cek_status.php">
      <label>Kode Pesanan</label>
      <input type="text" name="kode" placeholder="Contoh: ZL-0001"
             style="text-transform:uppercase;letter-spacing:1px;" required>
      <button type="submit" class="btn-submit">Cek Status</button>
    </form>
    <button class="back-btn" onclick="kembali()">&#8592; Ganti pilihan</button>
  </div>

</div>

<script>
// Kalau ada error login, langsung tampilkan form staff
<?php if ($error): ?>
document.addEventListener('DOMContentLoaded', function() { pilih('staff'); });
<?php endif; ?>

function pilih(tipe) {
  document.getElementById('role-picker').style.display = 'none';
  document.getElementById('form-staff').classList.remove('show');
  document.getElementById('form-pelanggan').classList.remove('show');
  document.getElementById('form-' + tipe).classList.add('show');
}

function kembali() {
  document.getElementById('role-picker').style.display = 'block';
  document.getElementById('form-staff').classList.remove('show');
  document.getElementById('form-pelanggan').classList.remove('show');
}

function togglePw(inputId, spanId) {
  var inp  = document.getElementById(inputId);
  var span = document.getElementById(spanId);
  if (!inp) return;
  if (inp.type === 'password') { inp.type = 'text'; if(span) span.textContent = 'Sembunyikan'; }
  else { inp.type = 'password'; if(span) span.textContent = 'Lihat'; }
}
</script>
</body>
</html>
