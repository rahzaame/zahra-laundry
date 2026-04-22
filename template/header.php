<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: " . BASE_URL . "login.php");
    exit();
}

$role       = $_SESSION['role']  ?? '';
$nama_user  = $_SESSION['nama']  ?? 'Pengguna';
$page_title = $page_title ?? 'Dashboard';
$halaman    = basename($_SERVER['PHP_SELF']);

$akses = [
    'petugas'  => ['dashboard.php','tambah_pesanan.php','simpan_pesanan.php',
                   'daftar_pesanan.php','edit_pesanan.php','hapus_pesanan.php',
                   'cetak_nota.php','pembayaran.php','simpan_pembayaran.php',
                   'struk_pembayaran.php'],
    'produksi' => ['dashboard.php','daftar_produksi.php','update_status.php'],
    'owner'    => ['dashboard.php','laporan_harian.php'],
];

$boleh = $akses[$role] ?? [];
if (!in_array($halaman, $boleh)) {
    header("Location: " . BASE_URL . "dashboard/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title) ?> — Zahra Laundry</title>
  <link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet">
</head>
<body>

<div id="sidebar-overlay"></div>

<div style="display:flex;min-height:100vh;">

  <?php include BASE_URL . 'template/sidebar.php'; ?>

  <div id="main-content">

    <div id="topbar">
      <div class="topbar-left">
        <button id="btn-toggle" title="Toggle Menu">
          <span></span>
          <span></span>
          <span></span>
        </button>
        <div class="topbar-title"><?= htmlspecialchars($page_title) ?></div>
      </div>
      <div class="topbar-right">
        <div class="user-pill">
          <span class="user-name"><?= htmlspecialchars($nama_user) ?></span>
          <span style="opacity:.6;font-weight:400;">(<?= ucfirst($role) ?>)</span>
        </div>
        <a href="<?= BASE_URL ?>logout.php" class="btn-logout">Keluar</a>
      </div>
    </div>

    <div class="page-content">