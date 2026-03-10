<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_user'])) {
    header("Location: " . BASE_URL . "login.php");
    exit();
}

$nama_user = $_SESSION['nama'] ?? 'Pengguna';
$role_user = $_SESSION['role'] ?? '';
$page_title = $page_title ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - Zahra Laundry</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  
    <link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="d-flex">
    <?php include BASE_URL . 'template/sidebar.php'; ?>

    <div id="main-content" class="flex-fill">

        <div id="topbar">
            <h6 class="page-title">
                <i class="bi bi-chevron-right me-1 text-muted" style="font-size:0.75rem;"></i>
                <?= htmlspecialchars($page_title) ?>
            </h6>
            <div class="d-flex align-items-center gap-3">
                <span class="user-badge">
                    <i class="bi bi-person-fill me-1"></i>
                    <?= htmlspecialchars($nama_user) ?>
                    <span class="text-muted ms-1" style="font-weight:400;">(<?= ucfirst($role_user) ?>)</span>
                </span>
                <a href="<?= BASE_URL ?>logout.php" class="btn btn-sm btn-outline-danger" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="page-content">
