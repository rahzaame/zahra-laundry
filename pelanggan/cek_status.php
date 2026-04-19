<?php
session_start();

$kode  = trim($_GET['kode'] ?? '');
$hasil = null;
$error = '';

if ($kode) {
    require_once __DIR__ . '/../config/koneksi.php';
    $k = mysqli_real_escape_string($koneksi, $kode);

    $hasil = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT p.id_pesanan, p.kode_pesanan, p.nama_pelanggan, p.no_hp,
                p.jenis_laundry, p.berat, p.total_harga,
                p.status, p.tanggal_masuk, p.tanggal_selesai,
                b.metode_pembayaran, b.tanggal_bayar, b.jumlah_bayar
         FROM pesanan p
         LEFT JOIN pembayaran b ON p.id_pesanan = b.id_pesanan
         WHERE p.kode_pesanan = '$k'
         LIMIT 1"));

    if (!$hasil) {
        $error = 'Kode pesanan tidak ditemukan. Pastikan kode sudah benar.';
    }
}

$status_steps = ['Menunggu', 'Diproses', 'Selesai', 'Diambil'];

function getStepIndex($status) {
    global $status_steps;
    $idx = array_search($status, $status_steps);
    return $idx === false ? 0 : $idx;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cek Status Pesanan - Zahra Laundry</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #1e1040 0%, #2d1a5e 50%, #4c1d95 100%);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 40px 16px;
    }

    .brand { text-align: center; color: #fff; margin-bottom: 28px; }
    .brand-logo {
      width: 56px; height: 56px;
      background: rgba(255,255,255,0.15);
      border-radius: 14px;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 14px;
      border: 1px solid rgba(255,255,255,0.2);
      font-size: 1.1rem; font-weight: 800; color: #fff; letter-spacing: 1px;
    }
    .brand h1 { font-size: 1.4rem; font-weight: 800; }
    .brand p  { font-size: 0.82rem; opacity: 0.7; margin-top: 4px; }

    .card {
      background: #fff;
      border-radius: 18px;
      width: 100%; max-width: 500px;
      box-shadow: 0 30px 80px rgba(0,0,0,0.3);
      overflow: hidden;
    }

    .card-top {
      background: linear-gradient(135deg, #7c3aed, #6d28d9);
      padding: 20px 24px; color: #fff;
    }
    .card-top h3 { font-size: 1rem; font-weight: 700; margin-bottom: 2px; }
    .card-top p  { font-size: 0.78rem; opacity: 0.85; }
    .card-body   { padding: 24px; }

    label {
      display: block;
      font-size: 0.82rem; font-weight: 600;
      color: #374151; margin-bottom: 6px;
    }

    input[type=text] {
      width: 100%;
      padding: 10px 13px;
      border: 1.5px solid #e5e7eb;
      border-radius: 9px;
      font-size: 0.9rem;
      font-family: inherit;
      outline: none;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: #1e1b4b;
      transition: border-color .18s, box-shadow .18s;
    }
    input[type=text]:focus {
      border-color: #7c3aed;
      box-shadow: 0 0 0 3px rgba(124,58,237,.12);
    }
    input[type=text]::placeholder { text-transform: none; letter-spacing: 0; color: #d1d5db; }

    .btn-cek {
      width: 100%; margin-top: 12px;
      padding: 11px;
      background: linear-gradient(135deg, #7c3aed, #6d28d9);
      color: #fff; border: none; border-radius: 9px;
      font-size: 0.9rem; font-weight: 700;
      cursor: pointer; font-family: inherit;
      transition: opacity .18s;
    }
    .btn-cek:hover { opacity: .9; }

    .alert-err {
      background: #fef2f2; border: 1px solid #fecaca;
      border-radius: 8px; padding: 11px 14px;
      color: #991b1b; font-size: 0.84rem;
      margin-top: 14px;
    }

    .result { margin-top: 20px; border: 1.5px solid #e5e7eb; border-radius: 12px; overflow: hidden; }

    .result-head {
      background: #f5f3ff; padding: 14px 18px;
      display: flex; align-items: center; justify-content: space-between;
    }
    .result-head .kode { font-weight: 800; font-size: 1.1rem; color: #7c3aed; }
    .result-head .nama { font-size: 0.78rem; color: #6b7280; margin-top: 2px; }

    .badge {
      display: inline-block; padding: 4px 12px;
      border-radius: 20px; font-size: 0.75rem; font-weight: 700;
    }
    .badge-menunggu { background: #fef9c3; color: #854d0e; }
    .badge-diproses  { background: #dbeafe; color: #1e40af; }
    .badge-selesai   { background: #d1fae5; color: #065f46; }
    .badge-diambil   { background: #ede9fe; color: #5b21b6; }

    .timeline-wrap { padding: 18px 18px 10px; }
    .timeline {
      display: flex; align-items: flex-start;
      justify-content: space-between;
      position: relative;
    }
    .timeline::before {
      content: '';
      position: absolute; top: 15px; left: 15px; right: 15px;
      height: 3px; background: #e5e7eb; z-index: 0;
    }
    .tl-step {
      display: flex; flex-direction: column;
      align-items: center; gap: 6px;
      position: relative; z-index: 1; flex: 1;
    }
    .tl-dot {
      width: 32px; height: 32px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 0.75rem; font-weight: 700;
      background: #e5e7eb; color: #9ca3af;
      border: 3px solid #e5e7eb;
    }
    .tl-dot.done   { background: #7c3aed; color: #fff; border-color: #7c3aed; }
    .tl-dot.active { background: #fff; color: #7c3aed; border-color: #7c3aed; box-shadow: 0 0 0 4px rgba(124,58,237,.15); }
    .tl-label { font-size: 0.67rem; font-weight: 600; color: #9ca3af; text-align: center; line-height: 1.3; }
    .tl-label.done   { color: #7c3aed; }
    .tl-label.active { color: #7c3aed; font-weight: 700; }

    .detail-body { padding: 4px 18px 16px; }
    .detail-row {
      display: flex; justify-content: space-between;
      padding: 7px 0; font-size: 0.85rem;
      border-bottom: 1px solid #f3f4f6;
    }
    .detail-row:last-child { border: none; }
    .detail-row .lbl { color: #6b7280; }
    .detail-row .val { font-weight: 600; color: #1e1b4b; text-align: right; }

    .back-link { margin-top: 18px; text-align: center; }
    .back-link a { color: rgba(255,255,255,.7); font-size: 0.82rem; text-decoration: none; }
    .back-link a:hover { color: #fff; }

    footer { color: rgba(255,255,255,.35); font-size: 0.72rem; margin-top: 28px; text-align: center; }
  </style>
</head>
<body>

<div class="brand">
  <div class="brand-logo">ZL</div>
  <h1>Zahra Laundry</h1>
  <p>Cek status pesanan laundry Anda</p>
</div>

<div class="card">
  <div class="card-top">
    <h3>Cek Status Pesanan</h3>
    <p>Masukkan kode pesanan yang tertera di nota Anda</p>
  </div>
  <div class="card-body">

    <form method="GET" action="cek_status.php">
      <label>Kode Pesanan</label>
      <input type="text" name="kode"
             placeholder="Contoh: ZL-0001"
             value="<?= htmlspecialchars($kode) ?>"
             required autofocus>
      <button type="submit" class="btn-cek">Cek Sekarang</button>
    </form>

    <?php if ($error): ?>
    <div class="alert-err"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($hasil):
      $status   = $hasil['status'];
      $step_now = getStepIndex($status);

      $cls = match($status) {
        'Menunggu' => 'badge-menunggu',
        'Diproses' => 'badge-diproses',
        'Selesai'  => 'badge-selesai',
        'Diambil'  => 'badge-diambil',
        default    => ''
      };
    ?>
    <div class="result">

      <div class="result-head">
        <div>
          <div class="kode"><?= htmlspecialchars($hasil['kode_pesanan']) ?></div>
          <div class="nama"><?= htmlspecialchars($hasil['nama_pelanggan']) ?></div>
        </div>
        <span class="badge <?= $cls ?>"><?= $status ?></span>
      </div>

      <div class="timeline-wrap">
        <div class="timeline">
          <?php foreach ($status_steps as $i => $step):
            if ($i < $step_now)      $dot_cls = 'done';
            elseif ($i === $step_now) $dot_cls = 'active';
            else                      $dot_cls = '';
            $lbl_cls = $i <= $step_now ? ($i === $step_now ? 'active' : 'done') : '';
          ?>
          <div class="tl-step">
            <div class="tl-dot <?= $dot_cls ?>">
              <?= $i < $step_now ? '&#10003;' : ($i + 1) ?>
            </div>
            <div class="tl-label <?= $lbl_cls ?>"><?= $step ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="detail-body">
        <div class="detail-row">
          <span class="lbl">Jenis Layanan</span>
          <span class="val"><?= htmlspecialchars($hasil['jenis_laundry']) ?></span>
        </div>
        <div class="detail-row">
          <span class="lbl">Berat</span>
          <span class="val"><?= $hasil['berat'] ?> kg</span>
        </div>
        <div class="detail-row">
          <span class="lbl">Total Biaya</span>
          <span class="val" style="color:#7c3aed;">Rp <?= number_format($hasil['total_harga'],0,',','.') ?></span>
        </div>
        <div class="detail-row">
          <span class="lbl">Tanggal Masuk</span>
          <span class="val"><?= date('d M Y', strtotime($hasil['tanggal_masuk'])) ?></span>
        </div>
        <?php if ($hasil['tanggal_selesai']): ?>
        <div class="detail-row">
          <span class="lbl">Tanggal Selesai</span>
          <span class="val"><?= date('d M Y', strtotime($hasil['tanggal_selesai'])) ?></span>
        </div>
        <?php endif; ?>
        <?php if ($hasil['metode_pembayaran']): ?>
        <div class="detail-row">
          <span class="lbl">Pembayaran</span>
          <span class="val" style="color:#059669;">Lunas - <?= htmlspecialchars($hasil['metode_pembayaran']) ?></span>
        </div>
        <div class="detail-row">
          <span class="lbl">Tanggal Bayar</span>
          <span class="val"><?= date('d M Y', strtotime($hasil['tanggal_bayar'])) ?></span>
        </div>
        <?php endif; ?>
      </div>

    </div>
    <?php endif; ?>

  </div>
</div>

<?php if (isset($_SESSION['id_user'])): ?>
<div class="back-link"><a href="../dashboard/dashboard.php">Kembali ke Dashboard</a></div>
<?php else: ?>
<div class="back-link"><a href="../login.php">Login sebagai Petugas / Produksi / Owner</a></div>
<?php endif; ?>

<footer>&copy; <?= date('Y') ?> Zahra Laundry</footer>
</body>
</html>