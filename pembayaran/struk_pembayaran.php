<?php
define('BASE_URL', '../');
require_once BASE_URL . 'config/koneksi.php';

$id = (int)($_GET['id'] ?? 0);
$row = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT b.*, p.kode_pesanan, p.nama_pelanggan, p.no_hp, p.jenis_laundry,
            p.berat, p.harga_perkg, p.total_harga, p.tanggal_masuk, p.tanggal_selesai
     FROM pembayaran b
     JOIN pesanan p ON b.id_pesanan = p.id_pesanan
     WHERE b.id_pembayaran = $id"));

if (!$row) { echo "Struk tidak ditemukan."; exit(); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Struk — <?= $row['kode_pesanan'] ?></title>
  <style>
    * { box-sizing: border-box; }
    body { font-family: 'Courier New', monospace; max-width: 320px; margin: 0 auto; padding: 20px; font-size: 13px; color: #111; }
    .center { text-align: center; }
    .line { border-top: 1px dashed #555; margin: 8px 0; }
    .row { display: flex; justify-content: space-between; margin: 3px 0; }
    h2 { font-size: 16px; margin: 0; letter-spacing: 2px; }
    .total-box { background: #f5f3ff; border: 2px solid #7c3aed; border-radius: 8px; padding: 10px; margin: 10px 0; text-align: center; }
    .total-box .amount { font-size: 20px; font-weight: bold; color: #7c3aed; }
    .lunas-stamp { display: inline-block; border: 3px solid #10b981; color: #10b981; border-radius: 6px; padding: 2px 14px; font-weight: bold; font-size: 15px; letter-spacing: 3px; margin-top: 6px; }
    .btn-act { padding: 9px 20px; cursor: pointer; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; }
    @media print { .no-print { display: none !important; } }
  </style>
</head>
<body>

<div class="center">
  <h2>ZAHRA LAUNDRY</h2>
  <div style="font-size:11px;color:#555;">Struk Pembayaran</div>
  <div style="font-size:10px;color:#888;margin-top:2px;">Jl. Contoh No.1, Kota Anda</div>
</div>
<div class="line"></div>

<div class="row"><span>No. Struk</span><span><b>#<?= str_pad($row['id_pembayaran'],4,'0',STR_PAD_LEFT) ?></b></span></div>
<div class="row"><span>Tgl Bayar</span><span><?= date('d/m/Y', strtotime($row['tanggal_bayar'])) ?></span></div>
<div class="row"><span>Kode Pesanan</span><span><b><?= $row['kode_pesanan'] ?></b></span></div>
<div class="line"></div>

<div class="row"><span>Pelanggan</span><span><?= htmlspecialchars($row['nama_pelanggan']) ?></span></div>
<div class="row"><span>No. HP</span><span><?= htmlspecialchars($row['no_hp']) ?></span></div>
<div class="line"></div>

<div class="row"><span>Jenis</span><span><?= htmlspecialchars($row['jenis_laundry']) ?></span></div>
<div class="row"><span>Berat</span><span><?= $row['berat'] ?> kg</span></div>
<div class="row"><span>Harga/kg</span><span>Rp <?= number_format($row['harga_perkg'],0,',','.') ?></span></div>
<div class="line"></div>

<div class="total-box">
  <div style="font-size:11px;color:#666;margin-bottom:4px;">TOTAL PEMBAYARAN</div>
  <div class="amount">Rp <?= number_format($row['jumlah_bayar'],0,',','.') ?></div>
  <div style="font-size:11px;margin-top:4px;">Metode: <b><?= $row['metode_pembayaran'] ?></b></div>
</div>

<div class="center">
  <div class="lunas-stamp">✓ LUNAS</div>
</div>
<div class="line"></div>
<div class="center" style="font-size:10px;color:#888;margin-top:6px;">
  Terima kasih telah menggunakan<br>layanan Zahra Laundry!<br>
  Cek status: <b>kode <?= $row['kode_pesanan'] ?></b>
</div>

<div class="no-print" style="margin-top:20px;display:flex;gap:8px;justify-content:center;">
  <button class="btn-act" style="background:#7c3aed;color:#fff;" onclick="window.print()">
    🖨️ Cetak Struk
  </button>
  <a href="pembayaran.php" class="btn-act" style="background:#e5e7eb;text-decoration:none;color:#111;">
    ← Kembali
  </a>
</div>

<script>window.onload = function(){ window.print(); }</script>
</body>
</html>