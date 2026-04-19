<?php
define('BASE_URL', '../');
$page_title = 'Edit Pesanan';
require_once BASE_URL . 'template/header.php';
require_once BASE_URL . 'config/koneksi.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header("Location: daftar_pesanan.php"); exit(); }

$row = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pesanan WHERE id_pesanan=$id"));
if (!$row) { header("Location: daftar_pesanan.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama        = trim($_POST['nama_pelanggan'] ?? '');
    $no_hp       = trim($_POST['no_hp']          ?? '');
    $jenis       = trim($_POST['jenis_laundry']  ?? '');
    $berat       = floatval($_POST['berat']       ?? 0);
    $harga_perkg = floatval($_POST['harga_perkg'] ?? 0);
    $total_harga = $berat * $harga_perkg;

    $stmt = mysqli_prepare($koneksi,
        "UPDATE pesanan SET nama_pelanggan=?,no_hp=?,jenis_laundry=?,berat=?,harga_perkg=?,total_harga=? WHERE id_pesanan=?");
    mysqli_stmt_bind_param($stmt, 'sssdddi', $nama, $no_hp, $jenis, $berat, $harga_perkg, $total_harga, $id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['pesan_sukses'] = 'Pesanan berhasil diperbarui!';
    } else {
        $_SESSION['pesan_error'] = 'Gagal memperbarui: ' . mysqli_error($koneksi);
    }
    header("Location: daftar_pesanan.php"); exit();
}
?>

<div class="page-header">
  <h4>Edit Pesanan</h4>
  <ul class="breadcrumb"><li>Dashboard</li><li>Daftar Pesanan</li><li>Edit</li></ul>
</div>

<div class="card" style="max-width:680px;">
  <div class="card-header">Edit Data Pesanan — <?= $row['kode_pesanan'] ?></div>
  <div class="card-body">
    <form method="POST">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

        <div class="form-group">
          <label class="form-label">Kode Pesanan</label>
          <input type="text" class="form-control" value="<?= $row['kode_pesanan'] ?>" readonly>
        </div>
        <div class="form-group">
          <label class="form-label">Status</label>
          <input type="text" class="form-control" value="<?= $row['status'] ?>" readonly>
        </div>
        <div class="form-group">
          <label class="form-label">Nama Pelanggan *</label>
          <input type="text" name="nama_pelanggan" class="form-control"
                 value="<?= htmlspecialchars($row['nama_pelanggan']) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">No. HP *</label>
          <input type="text" name="no_hp" class="form-control"
                 value="<?= htmlspecialchars($row['no_hp']) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Jenis Laundry *</label>
          <select name="jenis_laundry" class="form-select" required>
            <?php foreach(['Cuci Kering','Cuci Basah','Cuci Setrika','Setrika Saja','Dry Cleaning'] as $j): ?>
            <option value="<?= $j ?>" <?= $row['jenis_laundry']===$j?'selected':'' ?>><?= $j ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Berat (kg) *</label>
          <input type="number" name="berat" id="berat" class="form-control"
                 value="<?= $row['berat'] ?>" step="0.1" min="0.1" required>
        </div>
        <div class="form-group">
          <label class="form-label">Harga/kg (Rp) *</label>
          <input type="number" name="harga_perkg" id="harga_perkg" class="form-control"
                 value="<?= $row['harga_perkg'] ?>" min="1000" required>
        </div>
        <div class="form-group">
          <label class="form-label">Total Harga (Rp)</label>
          <input type="number" name="total_harga" id="total_harga" class="form-control"
                 value="<?= $row['total_harga'] ?>" readonly
                 style="background:#f5f3ff;color:var(--primary);font-weight:700;">
        </div>

      </div>
      <div style="display:flex;gap:12px;margin-top:8px;">
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="daftar_pesanan.php" class="btn btn-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>

<?php require_once BASE_URL . 'template/footer.php'; ?>