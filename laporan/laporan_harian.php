<?php
define('BASE_URL', '../');
$page_title = 'Laporan Harian';
require_once BASE_URL . 'template/header.php';
require_once BASE_URL . 'config/koneksi.php';

$tgl_dari  = $_GET['dari']  ?? date('Y-m-01');      // default: awal bulan ini
$tgl_sampai = $_GET['sampai'] ?? date('Y-m-d');     // default: hari ini

$dari_esc   = mysqli_real_escape_string($koneksi, $tgl_dari);
$sampai_esc = mysqli_real_escape_string($koneksi, $tgl_sampai);

$pesanan_data = mysqli_query($koneksi,
    "SELECT * FROM pesanan
     WHERE DATE(tanggal_masuk) BETWEEN '$dari_esc' AND '$sampai_esc'
     ORDER BY tanggal_masuk DESC");

$pendapatan_data = mysqli_query($koneksi,
    "SELECT b.*, p.kode_pesanan, p.nama_pelanggan, p.jenis_laundry
     FROM pembayaran b
     JOIN pesanan p ON b.id_pesanan = p.id_pesanan
     WHERE DATE(b.tanggal_bayar) BETWEEN '$dari_esc' AND '$sampai_esc'
     ORDER BY b.tanggal_bayar DESC");

$total_pesanan   = mysqli_num_rows($pesanan_data);
$total_pendapatan = mysqli_fetch_row(mysqli_query($koneksi,
    "SELECT COALESCE(SUM(jumlah_bayar),0) FROM pembayaran
     WHERE DATE(tanggal_bayar) BETWEEN '$dari_esc' AND '$sampai_esc'"))[0] ?? 0;

$per_status = [];
foreach(['Menunggu','Diproses','Selesai','Diambil'] as $s) {
    $r = mysqli_fetch_row(mysqli_query($koneksi,
        "SELECT COUNT(*) FROM pesanan WHERE status='$s'
         AND DATE(tanggal_masuk) BETWEEN '$dari_esc' AND '$sampai_esc'"));
    $per_status[$s] = $r[0] ?? 0;
}

mysqli_data_seek($pesanan_data, 0);
mysqli_data_seek($pendapatan_data, 0);
?>

<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px;">
  <div>
    <h4>Laporan Harian</h4>
    <ul class="breadcrumb"><li>Dashboard</li><li>Laporan Harian</li></ul>
  </div>
  <button onclick="window.print()" class="btn btn-secondary no-print">
    Cetak Laporan
  </button>
</div>

<div class="card no-print">
  <div class="card-header">Filter Periode</div>
  <div class="card-body">
    <form method="GET" style="display:flex;gap:16px;align-items:flex-end;flex-wrap:wrap;">
      <div class="form-group" style="margin:0;flex:1;min-width:160px;">
        <label class="form-label">Dari Tanggal</label>
        <input type="date" name="dari" class="form-control" value="<?= $tgl_dari ?>">
      </div>
      <div class="form-group" style="margin:0;flex:1;min-width:160px;">
        <label class="form-label">Sampai Tanggal</label>
        <input type="date" name="sampai" class="form-control" value="<?= $tgl_sampai ?>">
      </div>
      <button type="submit" class="btn btn-primary" style="margin-bottom:0;">
        Tampilkan
      </button>
      <a href="laporan_harian.php" class="btn btn-secondary" style="margin-bottom:0;">
        Reset
      </a>
    </form>
  </div>
</div>

<div style="display:none;" class="print-only">
  <div style="text-align:center;margin-bottom:16px;">
    <h2 style="margin:0;">ZAHRA LAUNDRY</h2>
    <div>Laporan Harian Periode: <?= date('d/m/Y',strtotime($tgl_dari)) ?> — <?= date('d/m/Y',strtotime($tgl_sampai)) ?></div>
  </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:16px;margin-bottom:24px;">
  <div class="stat-card" style="background:linear-gradient(135deg,#7c3aed,#6d28d9);">
    <div class="stat-icon"></div>
    <div class="stat-value"><?= $total_pesanan ?></div>
    <div class="stat-label">Total Pesanan</div>
  </div>
  <div class="stat-card" style="background:linear-gradient(135deg,#ec4899,#db2777);">
    <div class="stat-icon"></div>
    <div class="stat-value" style="font-size:1.1rem;">Rp<?= number_format($total_pendapatan,0,',','.') ?></div>
    <div class="stat-label">Total Pendapatan</div>
  </div>
  <div class="stat-card" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
    <div class="stat-icon"></div>
    <div class="stat-value"><?= $per_status['Menunggu'] ?></div>
    <div class="stat-label">Menunggu</div>
  </div>
  <div class="stat-card" style="background:linear-gradient(135deg,#3b82f6,#2563eb);">
    <div class="stat-icon"></div>
    <div class="stat-value"><?= $per_status['Diproses'] ?></div>
    <div class="stat-label">Diproses</div>
  </div>
  <div class="stat-card" style="background:linear-gradient(135deg,#10b981,#059669);">
    <div class="stat-icon"></div>
    <div class="stat-value"><?= $per_status['Selesai'] ?></div>
    <div class="stat-label">Selesai</div>
  </div>
  <div class="stat-card" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);">
    <div class="stat-icon"></div>
    <div class="stat-value"><?= $per_status['Diambil'] ?></div>
    <div class="stat-label">Diambil</div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <span>Rekap Pesanan
      <span style="font-weight:400;font-size:0.78rem;color:var(--text-muted);margin-left:6px;">
        <?= date('d/m/Y',strtotime($tgl_dari)) ?> – <?= date('d/m/Y',strtotime($tgl_sampai)) ?>
      </span>
    </span>
  </div>
  <div class="card-body" style="padding:0;">
    <?php if ($total_pesanan > 0): ?>
    <div class="table-wrap">
      <table class="tbl">
        <thead>
          <tr>
            <th>#</th><th>Kode</th><th>Pelanggan</th><th>No HP</th>
            <th>Jenis</th><th>Berat</th><th>Total</th><th>Status</th><th>Tgl Masuk</th>
          </tr>
        </thead>
        <tbody>
        <?php $no=1; while($r=mysqli_fetch_assoc($pesanan_data)):
          $cls=match($r['status']){'Menunggu'=>'badge-menunggu','Diproses'=>'badge-diproses','Selesai'=>'badge-selesai','Diambil'=>'badge-diambil',default=>''};
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
          </tr>
        <?php endwhile; ?>
        </tbody>
        <tfoot>
          <?php
          $total_baris = mysqli_fetch_row(mysqli_query($koneksi,
              "SELECT COALESCE(SUM(total_harga),0) FROM pesanan WHERE DATE(tanggal_masuk) BETWEEN '$dari_esc' AND '$sampai_esc'"))[0] ?? 0;
          ?>
          <tr style="background:#f5f3ff;">
            <td colspan="6" style="text-align:right;font-weight:700;padding:12px 16px;">TOTAL</td>
            <td style="font-weight:800;color:var(--primary);padding:12px 16px;">
              Rp <?= number_format($total_baris, 0, ',', '.') ?>
            </td>
            <td colspan="2"></td>
          </tr>
        </tfoot>
      </table>
    </div>
    <?php else: ?>
    <div class="empty-state">Tidak ada pesanan pada periode ini.</div>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <div class="card-header">Riwayat Pembayaran</div>
  <div class="card-body" style="padding:0;">
    <?php
    $rows_bayar = mysqli_fetch_all($pendapatan_data, MYSQLI_ASSOC);
    if (count($rows_bayar) > 0):
    ?>
    <div class="table-wrap">
      <table class="tbl">
        <thead>
          <tr><th>#</th><th>Kode Pesanan</th><th>Pelanggan</th><th>Jenis</th><th>Metode</th><th>Jumlah</th><th>Tgl Bayar</th></tr>
        </thead>
        <tbody>
        <?php $no=1; foreach($rows_bayar as $r): ?>
          <tr>
            <td style="color:var(--text-muted);"><?= $no++ ?></td>
            <td><code style="color:var(--primary);font-weight:700;"><?= $r['kode_pesanan'] ?></code></td>
            <td><?= htmlspecialchars($r['nama_pelanggan']) ?></td>
            <td><?= htmlspecialchars($r['jenis_laundry']) ?></td>
            <td>
              <span class="badge" style="background:#ede9fe;color:#5b21b6;"><?= $r['metode_pembayaran'] ?></span>
            </td>
            <td style="font-weight:700;color:#10b981;">Rp <?= number_format($r['jumlah_bayar'],0,',','.') ?></td>
            <td><?= date('d/m/Y', strtotime($r['tanggal_bayar'])) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr style="background:#f0fdf4;">
            <td colspan="5" style="text-align:right;font-weight:700;padding:12px 16px;">TOTAL PENDAPATAN</td>
            <td style="font-weight:800;color:#10b981;padding:12px 16px;">
              Rp <?= number_format($total_pendapatan,0,',','.') ?>
            </td>
            <td></td>
          </tr>
        </tfoot>
      </table>
    </div>
    <?php else: ?>
    <div class="empty-state">Belum ada pembayaran pada periode ini.</div>
    <?php endif; ?>
  </div>
</div>

<style>
  @media print {
    .no-print { display: none !important; }
    .print-only { display: block !important; }
    #sidebar, #topbar, #footer { display: none !important; }
    #main-content { margin-left: 0 !important; }
    .page-content { padding: 0 !important; }
    body { background: #fff !important; }
  }
  .print-only { display: none; }
</style>

<?php require_once BASE_URL . 'template/footer.php'; ?>