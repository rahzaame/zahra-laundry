<?php
define('BASE_URL', '../');
require_once BASE_URL . 'config/koneksi.php';

$id  = (int)($_GET['id'] ?? 0);
$row = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT * FROM pesanan WHERE id_pesanan=$id"));
if (!$row) { echo "Pesanan tidak ditemukan."; exit(); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Nota — <?= $row['kode_pesanan'] ?></title>
  <style>
    body { font-family: 'Courier New', monospace; max-width: 320px; margin: 0 auto; padding: 20px; font-size: 13px; }
    .center { text-align: center; }
    .line { border-top: 1px dashed #000; margin: 8px 0; }
    .row { display: flex; justify-content: space-between; margin: 3px 0; }
    .bold { font-weight: bold; }
    h2 { font-size: 16px; margin: 0; }
    .total { font-size: 15px; font-weight: bold; margin-top: 4px; }
    @media print { .no-print { display: none; } }
  </style>
</head>
<body>
<div class="center">
  <h2>ZAHRA LAUNDRY</h2>
  <div>Nota Pesanan</div>
  <div style="font-size:11px;color:#555;">Terima kasih telah mempercayai kami</div>
</div>
<div class="line"></div>
<div class="row"><span>Kode</span><span class="bold"><?= $row['kode_pesanan'] ?></span></div>
<div class="row"><span>Tanggal</span><span><?= date('d/m/Y', strtotime($row['tanggal_masuk'])) ?></span></div>
<div class="line"></div>
<div class="row"><span>Pelanggan</span><span><?= htmlspecialchars($row['nama_pelanggan']) ?></span></div>
<div class="row"><span>No. HP</span><span><?= htmlspecialchars($row['no_hp']) ?></span></div>
<div class="line"></div>
<div class="row"><span>Jenis</span><span><?= htmlspecialchars($row['jenis_laundry']) ?></span></div>
<div class="row"><span>Berat</span><span><?= $row['berat'] ?> kg</span></div>
<div class="row"><span>Harga/kg</span><span>Rp <?= number_format($row['harga_perkg'],0,',','.') ?></span></div>
<div class="line"></div>
<div class="row total"><span>TOTAL</span><span>Rp <?= number_format($row['total_harga'],0,',','.') ?></span></div>
<div class="line"></div>
<div class="row"><span>Status</span><span class="bold"><?= $row['status'] ?></span></div>
<?php if($row['tanggal_selesai']): ?>
<div class="row"><span>Est. Selesai</span><span><?= date('d/m/Y', strtotime($row['tanggal_selesai'])) ?></span></div>
<?php endif; ?>
<div class="line"></div>
<div class="center" style="font-size:11px;margin-top:8px;">
  Cek status: <?= $_SERVER['HTTP_HOST'] ?>/zahra-laundry/pelanggan/cek_status.php<br>
  Gunakan kode: <strong><?= $row['kode_pesanan'] ?></strong>
</div>

<div class="no-print" style="margin-top:20px;text-align:center;">
  <button onclick="window.print()" style="padding:8px 20px;cursor:pointer;background:#7c3aed;color:#fff;border:none;border-radius:6px;font-size:13px;">
    🖨️ Cetak Nota
  </button>
  <button onclick="window.close()" style="padding:8px 20px;cursor:pointer;margin-left:8px;background:#e5e7eb;border:none;border-radius:6px;font-size:13px;">
    Tutup
  </button>
</div>
<script>window.onload = function(){ window.print(); }</script>
</body>
</html>