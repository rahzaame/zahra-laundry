<?php
define('BASE_URL', '../');
$page_title = 'Pembayaran';
require_once BASE_URL . 'template/header.php';
require_once BASE_URL . 'config/koneksi.php';

$pesan_sukses = $_SESSION['pesan_sukses'] ?? ''; unset($_SESSION['pesan_sukses']);
$pesan_error  = $_SESSION['pesan_error']  ?? ''; unset($_SESSION['pesan_error']);

$pesanan = null;
if (!empty($_GET['kode'])) {
    $kode = mysqli_real_escape_string($koneksi, trim($_GET['kode']));
    $pesanan = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT p.*, b.id_pembayaran FROM pesanan p
         LEFT JOIN pembayaran b ON p.id_pesanan=b.id_pesanan
         WHERE p.kode_pesanan='$kode' LIMIT 1"));
}

$list_selesai = mysqli_query($koneksi,
    "SELECT p.* FROM pesanan p
     LEFT JOIN pembayaran b ON p.id_pesanan=b.id_pesanan
     WHERE p.status='Selesai' AND b.id_pembayaran IS NULL
     ORDER BY p.tanggal_masuk ASC");
?>

<div class="page-header">
  <h4>Pembayaran</h4>
  <ul class="breadcrumb"><li>Dashboard</li><li>Pembayaran</li></ul>
</div>

<?php if($pesan_sukses): ?><div class="alert alert-success" data-auto><?= $pesan_sukses ?></div><?php endif; ?>
<?php if($pesan_error):  ?><div class="alert alert-danger"  data-auto><?= $pesan_error  ?></div><?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start;">

  <div class="card">
    <div class="card-header">Cari Pesanan</div>
    <div class="card-body">
      <form method="GET" action="pembayaran.php">
        <div class="form-group">
          <label class="form-label">Kode Pesanan</label>
          <input type="text" name="kode" class="form-control"
                 placeholder="Contoh: ZL-0001"
                 value="<?= htmlspecialchars($_GET['kode'] ?? '') ?>">
        </div>
        <button type="submit" class="btn btn-primary btn-block">
          Cari
        </button>
      </form>

      <?php if (!empty($_GET['kode']) && !$pesanan): ?>
      <div class="alert alert-danger" style="margin-top:14px;">
        Kode pesanan tidak ditemukan.
      </div>
      <?php endif; ?>

      <?php if ($pesanan): ?>
        <?php if ($pesanan['id_pembayaran']): ?>
        <div class="alert alert-info" style="margin-top:14px;">
          Pesanan ini sudah dibayar.
        </div>
        <?php elseif ($pesanan['status'] !== 'Selesai'): ?>
        <div class="alert alert-warning" style="margin-top:14px;">
          Pesanan belum selesai diproses. Status: <strong><?= $pesanan['status'] ?></strong>
        </div>
        <?php else: ?>
        <div class="divider"></div>
        <div style="background:var(--primary-soft);border-radius:10px;padding:14px;margin-bottom:16px;">
          <div style="font-size:0.78rem;color:var(--text-muted);margin-bottom:4px;">Pesanan ditemukan</div>
          <div style="font-weight:700;color:var(--primary);font-size:1.1rem;"><?= $pesanan['kode_pesanan'] ?></div>
          <div><?= htmlspecialchars($pesanan['nama_pelanggan']) ?> — <?= htmlspecialchars($pesanan['jenis_laundry']) ?></div>
          <div style="font-size:1.3rem;font-weight:800;color:var(--text);margin-top:6px;">
            Rp <?= number_format($pesanan['total_harga'],0,',','.') ?>
          </div>
        </div>
        <form method="POST" action="simpan_pembayaran.php">
          <input type="hidden" name="id_pesanan" value="<?= $pesanan['id_pesanan'] ?>">
          <input type="hidden" name="jumlah_bayar" value="<?= $pesanan['total_harga'] ?>">
          <div class="form-group">
            <label class="form-label">Metode Pembayaran <span style="color:red">*</span></label>
            <select name="metode_pembayaran" class="form-select" required>
              <option value="">-- Pilih Metode --</option>
              <option value="Tunai">Tunai</option>
              <option value="QRIS">QRIS</option>
              <option value="Transfer">Transfer Bank</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Tanggal Bayar</label>
            <input type="date" name="tanggal_bayar" class="form-control" value="<?= date('Y-m-d') ?>">
          </div>
          <button type="submit" class="btn btn-success btn-block">
            Konfirmasi Pembayaran
          </button>
        </form>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-header">Menunggu Pembayaran</div>
    <div class="card-body" style="padding:0;">
      <?php if(mysqli_num_rows($list_selesai)>0): ?>
      <div class="table-wrap">
        <table class="tbl">
          <thead>
            <tr><th>Kode</th><th>Pelanggan</th><th>Total</th><th>Aksi</th></tr>
          </thead>
          <tbody>
          <?php while($r=mysqli_fetch_assoc($list_selesai)): ?>
            <tr>
              <td><code style="color:var(--primary);font-weight:700;"><?= $r['kode_pesanan'] ?></code></td>
              <td><?= htmlspecialchars($r['nama_pelanggan']) ?></td>
              <td>Rp <?= number_format($r['total_harga'],0,',','.') ?></td>
              <td>
                <a href="pembayaran.php?kode=<?= $r['kode_pesanan'] ?>" class="btn btn-primary btn-sm">
                   Bayar
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
      <?php else: ?>
      <div class="empty-state">Semua pesanan sudah dibayar.</div>
      <?php endif; ?>
    </div>
  </div>

</div>

<?php require_once BASE_URL . 'template/footer.php'; ?>