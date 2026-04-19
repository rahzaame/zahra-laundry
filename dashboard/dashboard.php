<?php
define('BASE_URL', '../');
$page_title = 'Dashboard';
require_once BASE_URL . 'template/header.php';
require_once BASE_URL . 'config/koneksi.php';

$total   = mysqli_fetch_row(mysqli_query($koneksi,"SELECT COUNT(*) FROM pesanan"))[0] ?? 0;
$menunggu= mysqli_fetch_row(mysqli_query($koneksi,"SELECT COUNT(*) FROM pesanan WHERE status='Menunggu'"))[0] ?? 0;
$diproses= mysqli_fetch_row(mysqli_query($koneksi,"SELECT COUNT(*) FROM pesanan WHERE status='Diproses'"))[0] ?? 0;
$selesai = mysqli_fetch_row(mysqli_query($koneksi,"SELECT COUNT(*) FROM pesanan WHERE status='Selesai'"))[0] ?? 0;
$diambil = mysqli_fetch_row(mysqli_query($koneksi,"SELECT COUNT(*) FROM pesanan WHERE status='Diambil'"))[0] ?? 0;
$pendapatan = mysqli_fetch_row(mysqli_query($koneksi,"SELECT COALESCE(SUM(jumlah_bayar),0) FROM pembayaran WHERE DATE(tanggal_bayar)=CURDATE()"))[0] ?? 0;

$pesanan_terbaru = mysqli_query($koneksi,
  "SELECT kode_pesanan,nama_pelanggan,jenis_laundry,berat,total_harga,status,tanggal_masuk
   FROM pesanan ORDER BY tanggal_masuk DESC LIMIT 6");
?>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:16px;margin-bottom:24px;">

  <div class="stat-card" style="background:linear-gradient(135deg,#7c3aed,#6d28d9);">
    <div class="stat-icon"></div>
    <div class="stat-value"><?= $total ?></div>
    <div class="stat-label">Total Pesanan</div>
  </div>

  <div class="stat-card" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
    <div class="stat-icon"></div>
    <div class="stat-value"><?= $menunggu ?></div>
    <div class="stat-label">Menunggu</div>
  </div>

  <div class="stat-card" style="background:linear-gradient(135deg,#3b82f6,#2563eb);">
    <div class="stat-icon"></div>
    <div class="stat-value"><?= $diproses ?></div>
    <div class="stat-label">Diproses</div>
  </div>

  <div class="stat-card" style="background:linear-gradient(135deg,#10b981,#059669);">
    <div class="stat-icon"></div>
    <div class="stat-value"><?= $selesai ?></div>
    <div class="stat-label">Selesai</div>
  </div>

  <div class="stat-card" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);">
    <div class="stat-icon"></div>
    <div class="stat-value"><?= $diambil ?></div>
    <div class="stat-label">Diambil</div>
  </div>

  <div class="stat-card" style="background:linear-gradient(135deg,#ec4899,#db2777);">
    <div class="stat-icon"></div>
    <div class="stat-value" style="font-size:1.15rem;">Rp<?= number_format($pendapatan,0,',','.') ?></div>
    <div class="stat-label">Pendapatan Hari Ini</div>
  </div>

</div>

<div class="card">
  <div class="card-header">
    <span>Pesanan Terbaru</span>
    <?php if($role==='petugas'): ?>
    <a href="<?= BASE_URL ?>pesanan/daftar_pesanan.php" class="btn btn-outline btn-sm">Lihat Semua</a>
    <?php endif; ?>
  </div>
  <div class="card-body" style="padding:0;">
    <?php if(mysqli_num_rows($pesanan_terbaru)>0): ?>
    <div class="table-wrap">
      <table class="tbl">
        <thead>
          <tr>
            <th>Kode</th><th>Pelanggan</th><th>Jenis</th>
            <th>Berat</th><th>Total</th><th>Status</th><th>Tanggal</th>
          </tr>
        </thead>
        <tbody>
        <?php while($r=mysqli_fetch_assoc($pesanan_terbaru)):
          $cls = match($r['status']) {
            'Menunggu' => 'badge-menunggu',
            'Diproses' => 'badge-diproses',
            'Selesai'  => 'badge-selesai',
            'Diambil'  => 'badge-diambil',
            default    => 'badge-menunggu'
          };
          $status_label = $r['status'] ?: 'Menunggu';
        ?>
          <tr>
            <td><code style="color:var(--primary);font-weight:700;"><?= $r['kode_pesanan'] ?></code></td>
            <td><?= htmlspecialchars($r['nama_pelanggan']) ?></td>
            <td><?= htmlspecialchars($r['jenis_laundry']) ?></td>
            <td><?= $r['berat'] ?> kg</td>
            <td>Rp <?= number_format($r['total_harga'],0,',','.') ?></td>
            <td><span class="badge <?= $cls ?>"><?= $status_label ?></span></td>
            <td><?= date('d/m/Y', strtotime($r['tanggal_masuk'])) ?></td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <div class="empty-state">Belum ada pesanan.</div>
    <?php endif; ?>
  </div>
</div>

<?php require_once BASE_URL . 'template/footer.php'; ?>