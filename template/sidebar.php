<?php
$role = $_SESSION['role'] ?? '';
$cur  = basename($_SERVER['PHP_SELF']);
?>
<div id="sidebar">
  <div class="sidebar-brand">
    <div class="brand-icon">ZL</div>
    <div class="brand-text">
      <h5>Zahra Laundry</h5>
      <small>Sistem Manajemen</small>
    </div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section">Utama</div>
    <a href="<?= BASE_URL ?>dashboard/dashboard.php" class="<?= $cur==='dashboard.php'?'active':'' ?>">
      Dashboard
    </a>

    <?php if ($role === 'petugas'): ?>
    <div class="nav-section">Pesanan</div>
    <a href="<?= BASE_URL ?>pesanan/tambah_pesanan.php" class="<?= $cur==='tambah_pesanan.php'?'active':'' ?>">
      Tambah Pesanan
    </a>
    <a href="<?= BASE_URL ?>pesanan/daftar_pesanan.php" class="<?= $cur==='daftar_pesanan.php'?'active':'' ?>">
      Daftar Pesanan
    </a>
    <div class="nav-section">Pembayaran</div>
    <a href="<?= BASE_URL ?>pembayaran/pembayaran.php" class="<?= $cur==='pembayaran.php'?'active':'' ?>">
      Pembayaran
    </a>
    <?php endif; ?>

    <?php if ($role === 'produksi'): ?>
    <div class="nav-section">Produksi</div>
    <a href="<?= BASE_URL ?>produksi/daftar_produksi.php" class="<?= $cur==='daftar_produksi.php'?'active':'' ?>">
      Antrian Produksi
    </a>
    <?php endif; ?>

    <?php if ($role === 'owner'): ?>
    <div class="nav-section">Laporan</div>
    <a href="<?= BASE_URL ?>laporan/laporan_harian.php" class="<?= $cur==='laporan_harian.php'?'active':'' ?>">
      Laporan Harian
    </a>
    <?php endif; ?>

  </nav>

  <div class="sidebar-footer">
    <a href="<?= BASE_URL ?>logout.php" style="color:#f87171;text-decoration:none;font-size:0.82rem;font-weight:600;">
      Logout
    </a>
  </div>
</div>