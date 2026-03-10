<?php

session_start();

define('BASE_URL', '../');
$page_title = 'Dashboard';
require_once BASE_URL . 'header.php';
require_once BASE_URL . 'config.php';

$total_pesanan = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM pesanan"))[0] ?? 0;
$diproses      = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM pesanan WHERE status='Diproses'"))[0] ?? 0;
$selesai       = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM pesanan WHERE status='Selesai'"))[0] ?? 0;
$pendapatan    = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(total_bayar) FROM pembayaran WHERE DATE(created_at)=CURDATE()"))[0] ?? 0;
$pesanan_terbaru = mysqli_query($conn,
    "SELECT kode_pesanan,nama_pelanggan,jenis_laundry,total_harga,status,created_at FROM pesanan ORDER BY created_at DESC LIMIT 5");
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>
</nav>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#0d6efd,#0a58ca);">
            <div class="stat-icon"><i class="bi bi-bag-fill"></i></div>
            <div class="stat-value"><?= $total_pesanan ?></div>
            <div class="stat-label">Total Pesanan</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#0dcaf0,#0aa2c0);">
            <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-value"><?= $diproses ?></div>
            <div class="stat-label">Sedang Diproses</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#198754,#157347);">
            <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-value"><?= $selesai ?></div>
            <div class="stat-label">Selesai</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#fd7e14,#e96b02);">
            <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-value" style="font-size:1.2rem;">Rp <?= number_format($pendapatan,0,',','.') ?></div>
            <div class="stat-label">Pendapatan Hari Ini</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history me-2 text-primary"></i>Pesanan Terbaru</span>
        <?php if ($_SESSION['role']==='petugas'): ?>
        <a href="<?= BASE_URL ?>pesanan/daftar_pesanan.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <?php if (mysqli_num_rows($pesanan_terbaru)>0): ?>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr><th>Kode</th><th>Pelanggan</th><th>Jenis</th><th>Total</th><th>Status</th><th>Tanggal</th></tr>
                </thead>
                <tbody>
                <?php while($row=mysqli_fetch_assoc($pesanan_terbaru)): ?>
                    <tr>
                        <td><code><?= htmlspecialchars($row['kode_pesanan']) ?></code></td>
                        <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                        <td><?= htmlspecialchars($row['jenis_laundry']) ?></td>
                        <td>Rp <?= number_format($row['total_harga'],0,',','.') ?></td>
                        <td>
                            <?php $s=$row['status'];
                            $cls=match($s){'Menunggu'=>'status-menunggu','Diproses'=>'status-diproses','Selesai'=>'status-selesai','Lunas'=>'status-lunas',default=>'bg-secondary text-white'}; ?>
                            <span class="badge-status <?= $cls ?>"><?= $s ?></span>
                        </td>
                        <td><?= date('d/m/Y',strtotime($row['created_at'])) ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox" style="font-size:2.5rem;opacity:0.4;"></i>
            <p class="mt-2 mb-0">Belum ada pesanan.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once BASE_URL . 'template/footer.php'; ?>
