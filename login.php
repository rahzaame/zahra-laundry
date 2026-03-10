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

        // Coba password_verify dulu (bcrypt), fallback ke plain text (untuk development)
        $login_ok = false;
        if ($user) {
            if (password_verify($password, $user['password'])) {
                $login_ok = true;
            } elseif ($user['password'] === $password) {
                // plain text fallback - untuk development awal
                $login_ok = true;
            }
        }

        if ($login_ok) {
            $_SESSION['id_user'] = $user['id_user'];
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
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1a2236 0%, #243047 55%, #0d1b2e 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-box {
            background: #fff;
            border-radius: 18px;
            padding: 44px 40px 36px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.35);
        }

        .logo-wrap {
            width: 58px;
            height: 58px;
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            font-size: 1.6rem;
            color: #fff;
        }

        .login-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1a202c;
            text-align: center;
        }

        .login-sub {
            font-size: 0.82rem;
            color: #a0aec0;
            text-align: center;
            margin-bottom: 28px;
        }

        .field-wrap {
            position: relative;
            margin-bottom: 16px;
        }

        .field-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 0.95rem;
            pointer-events: none;
        }

        .field-wrap input {
            width: 100%;
            padding: 10px 40px 10px 38px;
            border: 1.5px solid #e2e8f0;
            border-radius: 9px;
            font-size: 0.875rem;
            color: #2d3748;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            background: #fff;
        }

        .field-wrap input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13,110,253,0.12);
        }

        .field-wrap input::placeholder { color: #c0cdd8; }

        .toggle-pw {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #a0aec0;
            font-size: 0.95rem;
            padding: 0;
            line-height: 1;
        }
        .toggle-pw:hover { color: #4a5568; }

        .btn-login {
            width: 100%;
            padding: 11px;
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            border: none;
            border-radius: 9px;
            color: #fff;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 6px;
            transition: opacity 0.2s, transform 0.1s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-login:hover  { opacity: 0.92; }
        .btn-login:active { transform: scale(0.99); }

        .error-box {
            background: #fff5f5;
            border: 1px solid #fed7d7;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 0.82rem;
            color: #c53030;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 6px;
        }
    </style>
</head>
<body>

<div class="login-box">

    <div class="logo-wrap">
        <i class="bi bi-water"></i>
    </div>
    <div class="login-title">Zahra Laundry</div>
    <div class="login-sub">Sistem Manajemen Laundry</div>

    <?php if ($error): ?>
    <div class="error-box">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="login.php">

        <div style="margin-bottom:16px;">
            <label>Email</label>
            <div class="field-wrap">
                <i class="bi bi-envelope field-icon"></i>
                <input type="email" name="email" placeholder="Masukkan email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       required autofocus>
            </div>
        </div>

        <div style="margin-bottom:24px;">
            <label>Password</label>
            <div class="field-wrap">
                <i class="bi bi-lock field-icon"></i>
                <input type="password" name="password" id="password"
                       placeholder="Masukkan password" required>
                <button type="button" class="toggle-pw" onclick="togglePw()">
                    <i class="bi bi-eye" id="eye-icon"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-login">
            <i class="bi bi-box-arrow-in-right"></i> Masuk
        </button>

    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePw() {
    const pw  = document.getElementById('password');
    const ico = document.getElementById('eye-icon');
    if (pw.type === 'password') {
        pw.type = 'text';
        ico.className = 'bi bi-eye-slash';
    } else {
        pw.type = 'password';
        ico.className = 'bi bi-eye';
    }
}
</script>
</body>
</html>