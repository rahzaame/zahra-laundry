<?php
define('BASE_URL', '../');
$page_title = 'Daftar Pesanan';
require_once BASE_URL . 'template/header.php';
require_once BASE_URL . 'config/koneksi.php';

$pesan_sukses = $_SESSION['pesan_sukses'] ?? ''; unset($_SESSION['pesan_sukses']);
$pesan_error  = $_SESSION['pesan_error']  ?? ''; unset($_SESSION['pesan_error']);

if (isset($_GET['kirim']) && is_numeric($_GET['kirim'])) {
    $id = (int)$_GET['kirim'];
    mysqli_query($koneksi, "UPDATE pesanan SET status='Diproses' WHERE id_pesanan=$id AND status='Menunggu'");
    $_SESSION['pesan_sukses'] = 'Pesanan berhasil dikirim ke produksi!';
    header("Location: daftar_pesanan.php"); exit();
}

$filter  = $_GET['status'] ?? '';
$where   = $filter ? "WHERE status='".mysqli_real_escape_string($koneksi,$filter)."'" : '';
$pesanan = mysqli_query($koneksi, "SELECT * FROM pesanan $where ORDER BY tanggal_masuk DESC");
?>

<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;">
  <div>
    <h4>Daftar Pesanan</h4>
    <ul class="breadcrumb"><li>Dashboard</li><li>Daftar Pesanan</li></ul>
  </div>
  <a href="tambah_pesanan.php" class="btn btn-primary">+ Tambah Pesanan</a>
</div>

<?php if($pesan_sukses): ?><div class="alert alert-success" data-auto><?= $pesan_sukses ?></div><?php endif; ?>
<?php if($pesan_error):  ?><div class="alert alert-danger"  data-auto><?= $pesan_error  ?></div><?php endif; ?>

<div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;">
  <?php foreach([''=>'Semua','Menunggu'=>'Menunggu','Diproses'=>'Diproses','Selesai'=>'Selesai','Diambil'=>'Diambil'] as $val=>$lbl): ?>
  <a href="?status=<?= $val ?>" class="btn btn-sm <?= $filter===$val?'btn-primary':'btn-secondary' ?>"><?= $lbl ?></a>
  <?php endforeach; ?>
</div>

<div class="card">
  <div class="card-header">
    <span>Data Pesanan</span>
    <span style="font-size:0.78rem;color:var(--text-muted);"><?= mysqli_num_rows($pesanan) ?> data</span>
  </div>
  <div class="card-body" style="padding:0;">
    <?php if(mysqli_num_rows($pesanan)>0): ?>
    <div class="table-wrap">
      <table class="tbl">
        <thead>
          <tr>
            <th>#</th><th>Kode</th><th>Pelanggan</th><th>No HP</th>
            <th>Jenis</th><th>Berat</th><th>Total</th>
            <th>Status</th><th>Tgl Masuk</th><th>Tgl Selesai</th>
            <th style="text-align:center;">Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php $no=1; while($r=mysqli_fetch_assoc($pesanan)):
          $cls = match($r['status']) {
            'Menunggu' => 'badge-menunggu',
            'Diproses' => 'badge-diproses',
            'Selesai'  => 'badge-selesai',
            'Diambil'  => 'badge-diambil',
            default    => ''
          };
        ?>
          <tr>
            <td style="color:var(--text-muted);"><?= $no++ ?></td>
            <td><code style="color:var(--primary);font-weight:700;"><?= $r['kode_pesanan'] ?></code></td>
            <td><?= htmlspecialchars($r['nama_pelanggan']) ?></td>
            <td><?= htmlspecialchars($r['no_hp']) ?></td>
            <td><?= htmlspecialchars($r['jenis_laundry']) ?></td>
            <td><?= $r['berat'] ?> kg</td>
            <td>Rp <?= number_format($r['total_harga'],0,',','.') ?></td>
            <td><span class="badge <?= $cls ?>"><?= $r['status'] ?></span></td>
            <td><?= date('d/m/Y', strtotime($r['tanggal_masuk'])) ?></td>
            <td>
              <?php if($r['tanggal_selesai']): ?>
                <span style="color:#059669;font-weight:600;"><?= date('d/m/Y', strtotime($r['tanggal_selesai'])) ?></span>
              <?php else: ?>
                <span style="color:#d1d5db;">-</span>
              <?php endif; ?>
            </td>
            <td style="text-align:center;">
              <div style="display:flex;gap:5px;justify-content:center;flex-wrap:wrap;">
                <?php if($r['status']==='Menunggu'): ?>
                <a href="?kirim=<?= $r['id_pesanan'] ?>" class="btn btn-info btn-sm"
                   data-confirm="Kirim pesanan ini ke produksi?">Kirim</a>
                <a href="edit_pesanan.php?id=<?= $r['id_pesanan'] ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="hapus_pesanan.php?id=<?= $r['id_pesanan'] ?>" class="btn btn-danger btn-sm"
                   data-confirm="Yakin hapus pesanan ini?">Hapus</a>
                <?php endif; ?>
                <?php if($r['status']==='Selesai'): ?>
                <a href="../pembayaran/pembayaran.php?kode=<?= $r['kode_pesanan'] ?>" class="btn btn-success btn-sm">Bayar</a>
                <?php endif; ?>
                <a href="cetak_nota.php?id=<?= $r['id_pesanan'] ?>" class="btn btn-sm btn-secondary" target="_blank">Nota</a>
              </div>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
      <div style="font-size:2rem;margin-bottom:10px;">-</div>
      Belum ada pesanan<?= $filter?" dengan status $filter":'' ?>.
    </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once BASE_URL . 'template/footer.php'; ?>