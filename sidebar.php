<?php

$role = $_SESSION['role'] ?? '';
$current = basename($_SERVER['PHP_SELF']);
?>

<div id="sidebar">
    <div class="sidebar-brand">
        <div class="d-flex align-items-center gap-2">
            <div style="width:36px;height:36px;background:linear-gradient(135deg,#0d6efd,#0a58ca);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-water text-white" style="font-size:1rem;"></i>
            </div>
            <div>
                <h5 class="mb-0">Zahra Laundry</h5>
                <small>Sistem Manajemen</small>
            </div>
        </div>
    </div>

    <nav class="mt-2">

        <div class="nav-section-title">Menu Utama</div>

        <a href="<?= BASE_URL ?>dashboard/dashboard.php"
           class="nav-link <?= ($current == 'dashboard.php') ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>

        <?php if ($role === 'petugas'): ?>
        <div class="nav-section-title">Pesanan</div>

        <a href="<?= BASE_URL ?>pesanan/tambah_pesanan.php"
           class="nav-link <?= ($current == 'tambah_pesanan.php') ? 'active' : '' ?>">
            <i class="bi bi-plus-circle-fill"></i> Tambah Pesanan
        </a>

        <a href="<?= BASE_URL ?>pesanan/daftar_pesanan.php"
           class="nav-link <?= ($current == 'daftar_pesanan.php') ? 'active' : '' ?>">
            <i class="bi bi-list-ul"></i> Daftar Pesanan
        </a>

        <div class="nav-section-title">Pembayaran</div>

        <a href="<?= BASE_URL ?>pembayaran/pembayaran.php"
           class="nav-link <?= ($current == 'pembayaran.php') ? 'active' : '' ?>">
            <i class="bi bi-cash-coin"></i> Catat Pembayaran
        </a>
        <?php endif; ?>

        <?php if ($role === 'produksi'): ?>
        <div class="nav-section-title">Produksi</div>

        <a href="<?= BASE_URL ?>produksi/daftar_produksi.php"
           class="nav-link <?= ($current == 'daftar_produksi.php') ? 'active' : '' ?>">
            <i class="bi bi-basket3-fill"></i> Antrian Produksi
        </a>
        <?php endif; ?>

        <?php if ($role === 'owner'): ?>
        <div class="nav-section-title">Laporan</div>

        <a href="<?= BASE_URL ?>laporan/laporan_harian.php"
           class="nav-link <?= ($current == 'laporan_harian.php') ? 'active' : '' ?>">
            <i class="bi bi-bar-chart-fill"></i> Laporan Harian
        </a>
        <?php endif; ?>

        <div class="nav-section-title">Lainnya</div>

        <a href="<?= BASE_URL ?>pelanggan/cek_status.php"
           class="nav-link <?= ($current == 'cek_status.php') ? 'active' : '' ?>">
            <i class="bi bi-search"></i> Cek Status Pesanan
        </a>

        <a href="<?= BASE_URL ?>logout.php" class="nav-link text-danger">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>

    </nav>
</div>
