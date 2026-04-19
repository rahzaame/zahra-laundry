<?php
define('BASE_URL', '../');
$page_title = 'Tambah Pesanan';
require_once BASE_URL . 'template/header.php';
require_once BASE_URL . 'config/koneksi.php';

$pesan_sukses = $_SESSION['pesan_sukses'] ?? ''; unset($_SESSION['pesan_sukses']);
$pesan_error  = $_SESSION['pesan_error']  ?? ''; unset($_SESSION['pesan_error']);

$kode_q = mysqli_query($koneksi, "SELECT kode_pesanan FROM pesanan ORDER BY id_pesanan DESC LIMIT 1");
$last   = mysqli_fetch_assoc($kode_q);
if ($last) {
    $num = (int) substr($last['kode_pesanan'], 3) + 1;
} else {
    $num = 1;
}
$kode_baru = 'ZL-' . str_pad($num, 4, '0', STR_PAD_LEFT);
?>

<div class="page-header">
  <h4>Tambah Pesanan</h4>
  <ul class="breadcrumb"><li class="breadcrumb-item">Dashboard</li><li class="breadcrumb-item">Tambah Pesanan</li></ul>
</div>

<?php if($pesan_sukses): ?><div class="alert alert-success" data-auto><?= $pesan_sukses ?></div><?php endif; ?>
<?php if($pesan_error):  ?><div class="alert alert-danger"  data-auto><?= $pesan_error  ?></div><?php endif; ?>

<div class="card" style="max-width:680px;">
  <div class="card-header">Form Input Pesanan</div>
  <div class="card-body">
    <form method="POST" action="simpan_pesanan.php">

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

        <div class="form-group">
          <label class="form-label">Kode Pesanan</label>
          <input type="text" name="kode_pesanan" class="form-control"
                 value="<?= $kode_baru ?>" readonly>
        </div>

        <div class="form-group">
          <label class="form-label">Tanggal Masuk</label>
          <input type="date" name="tanggal_masuk" class="form-control"
                 value="<?= date('Y-m-d') ?>" required>
        </div>

        <div class="form-group">
          <label class="form-label">Nama Pelanggan <span style="color:red">*</span></label>
          <input type="text" name="nama_pelanggan" class="form-control"
                 placeholder="Nama pelanggan" required>
        </div>

        <div class="form-group">
          <label class="form-label">No. HP <span style="color:red">*</span></label>
          <input type="text" name="no_hp" class="form-control"
                 placeholder="08xxxxxxxxxx" required>
        </div>

        <div class="form-group">
          <label class="form-label">Jenis Laundry <span style="color:red">*</span></label>
          <select name="jenis_laundry" class="form-select" required>
            <option value="">-- Pilih Jenis --</option>
            <option value="Cuci Kering">Cuci Kering</option>
            <option value="Cuci Basah">Cuci Basah</option>
            <option value="Cuci Setrika">Cuci Setrika</option>
            <option value="Setrika Saja">Setrika Saja</option>
            <option value="Dry Cleaning">Dry Cleaning</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Berat (kg) <span style="color:red">*</span></label>
          <input type="number" name="berat" id="berat" class="form-control"
                 placeholder="0.0" step="0.1" min="0.1" required>
        </div>

        <div class="form-group">
          <label class="form-label">Harga/kg (Rp) <span style="color:red">*</span></label>
          <input type="number" name="harga_perkg" id="harga_perkg" class="form-control"
                 placeholder="5000" min="1000" required>
        </div>

        <div class="form-group">
          <label class="form-label">Total Harga (Rp)</label>
          <input type="number" name="total_harga" id="total_harga" class="form-control"
                 placeholder="Otomatis terhitung" readonly
                 style="background:#f5f3ff;color:var(--primary);font-weight:700;">
        </div>

      </div>

      <div style="display:flex;gap:12px;margin-top:8px;">
        <button type="submit" class="btn btn-primary">
          Simpan Pesanan
        </button>
        <a href="daftar_pesanan.php" class="btn btn-secondary">
          Kembali
        </a>
      </div>

    </form>
  </div>
</div>

<?php require_once BASE_URL . 'template/footer.php'; ?>