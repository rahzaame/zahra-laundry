<?php
define('BASE_URL', '../');
$page_title = 'Antrian Produksi';
require_once BASE_URL . 'template/header.php';
require_once BASE_URL . 'config/koneksi.php';

$pesan_sukses = $_SESSION['pesan_sukses'] ?? ''; unset($_SESSION['pesan_sukses']);

if (isset($_GET['selesai']) && is_numeric($_GET['selesai'])) {
    $id = (int)$_GET['selesai'];
    mysqli_query($koneksi,
        "UPDATE pesanan SET status='Selesai', tanggal_selesai=CURDATE() WHERE id_pesanan=$id AND status='Diproses'");
    $_SESSION['pesan_sukses'] = 'Status pesanan diperbarui menjadi Selesai!';
    header("Location: daftar_produksi.php"); exit();
}

$pesanan = mysqli_query($koneksi,
    "SELECT * FROM pesanan WHERE status IN ('Diproses','Selesai') ORDER BY tanggal_masuk ASC");
?>

<div class="page-header">
  <h4>Antrian Produksi</h4>
  <ul class="breadcrumb"><li>Dashboard</li><li>Antrian Produksi</li></ul>
</div>

<?php if($pesan_sukses): ?>
<div class="alert alert-success" data-auto><?= $pesan_sukses ?></div>
<?php endif; ?>

<!-- Info ringkasan -->
<?php
$jml_diproses = mysqli_fetch_row(mysqli_query($koneksi,"SELECT COUNT(*) FROM pesanan WHERE status='Diproses'"))[0]??0;
$jml_selesai  = mysqli_fetch_row(mysqli_query($koneksi,"SELECT COUNT(*) FROM pesanan WHERE status='Selesai'"))[0]??0;
?>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;max-width:440px;">
  <div class="stat-card" style="background:linear-gradient(135deg,#3b82f6,#2563eb);">
    <div class="stat-icon"></div>
    <div class="stat-value"><?= $jml_diproses ?></div>
    <div class="stat-label">Sedang Diproses</div>
  </div>
  <div class="stat-card" style="background:linear-gradient(135deg,#10b981,#059669);">
    <div class="stat-icon"></div>
    <div class="stat-value"><?= $jml_selesai ?></div>
    <div class="stat-label">Selesai</div>
  </div>
</div>

<div class="card">
  <div class="card-header">Daftar Pesanan Produksi</div>
  <div class="card-body" style="padding:0;">
    <?php if(mysqli_num_rows($pesanan)>0): ?>
    <div class="table-wrap">
      <table class="tbl">
        <thead>
          <tr>
            <th>#</th><th>Kode</th><th>Pelanggan</th><th>No HP</th>
            <th>Jenis</th><th>Berat</th><th>Status</th><th>Tgl Masuk</th><th style="text-align:center;">Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php $no=1; while($r=mysqli_fetch_assoc($pesanan)):
          $cls = match($r['status']) {'Diproses'=>'badge-diproses','Selesai'=>'badge-selesai',default=>''};
        ?>
          <tr>
            <td style="color:var(--text-muted);"><?= $no++ ?></td>
            <td><code style="color:var(--primary);font-weight:700;"><?= $r['kode_pesanan'] ?></code></td>
            <td><?= htmlspecialchars($r['nama_pelanggan']) ?></td>
            <td><?= htmlspecialchars($r['no_hp']) ?></td>
            <td><?= htmlspecialchars($r['jenis_laundry']) ?></td>
            <td><?= $r['berat'] ?> kg</td>
            <td><span class="badge <?= $cls ?>"><?= $r['status'] ?></span></td>
            <td><?= date('d/m/Y', strtotime($r['tanggal_masuk'])) ?></td>
            <td style="text-align:center;">
              <?php if($r['status']==='Diproses'): ?>
              <a href="?selesai=<?= $r['id_pesanan'] ?>" class="btn btn-success btn-sm"
                 data-confirm="Tandai pesanan ini sebagai Selesai?">
                Selesai
              </a>
              <?php else: ?>
              <span style="font-size:0.78rem;color:#10b981;font-weight:600;">
                Selesai
              </span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
        
        <div style="font-weight:700;font-size:1rem;margin-bottom:6px;">Belum ada pesanan masuk</div>
        <div style="font-size:0.82rem;color:var(--text-muted);">
            Antrian akan terisi otomatis setelah Petugas menekan tombol<br>
            <strong>"Kirim ke Produksi"</strong> pada halaman Daftar Pesanan.
        </div>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once BASE_URL . 'template/footer.php'; ?>