<?php

session_start();
define('BASE_URL', '');

if (isset($_SESSION['id_user'])) {
    header("Location: dashboard.php");
    exit();
}

require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email dan password wajib diisi.';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id_user, nama, email, password, role FROM users WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user   = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['id_user'] = $user['id'];
            $_SESSION['nama']    = $user['nama'];
            $_SESSION['email']   = $user['email'];
            $_SESSION['role']    = $user['role'];

            header("Location: dashboard.php");
            exit();
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .login-wrapper {
            background: linear-gradient(135deg, #1a2236 0%, #243047 60%, #0d1b2e 100%);
        }
        .login-input-icon {
            position: relative;
        }
        .login-input-icon i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 0.95rem;
        }
        .login-input-icon input {
            padding-left: 36px;
        }
        .toggle-pw {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #a0aec0;
            background: none;
            border: none;
            padding: 0;
            font-size: 0.95rem;
        }
        .toggle-pw:hover { color: #4a5568; }

    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">

        <div class="text-center mb-4">
            <div class="login-logo">
                <i class="bi bi-water"></i>
            </div>
            <h4 class="fw-700 mb-1" style="font-weight:700;color:#1a202c;">Zahra Laundry</h4>
            <p class="text-muted mb-0" style="font-size:0.85rem;">Sistem Manajemen Laundry</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible auto-dismiss d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span><?= htmlspecialchars($error) ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <form method="POST" action="login.php" novalidate>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <div class="login-input-icon">
                    <i class="bi bi-envelope"></i>
                    <input type="email" name="email" id="email" class="form-control"
                           placeholder="Masukkan email..."
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="login-input-icon" style="position:relative;">
                    <i class="bi bi-lock"></i>
                    <input type="password" name="password" id="password" class="form-control"
                           placeholder="Masukkan password..." required style="padding-right:40px;">
                    <button type="button" class="toggle-pw" onclick="togglePassword()">
                        <i class="bi bi-eye" id="eye-icon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2" style="font-weight:600;">
                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
            </button>

        </form>



    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>
<script>
function togglePassword() {
    const pw  = document.getElementById('password');
    const ico = document.getElementById('eye-icon');
    if (pw.type === 'password') {
        pw.type  = 'text';
        ico.className = 'bi bi-eye-slash';
    } else {
        pw.type  = 'password';
        ico.className = 'bi bi-eye';
    }
}
</script>
</body>
</html>
