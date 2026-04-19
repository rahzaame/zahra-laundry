<?php
define('BASE_URL', '../');
require_once BASE_URL . 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: pembayaran.php"); exit(); }

$id_pesanan        = (int)($_POST['id_pesanan']        ?? 0);
$jumlah_bayar      = floatval($_POST['jumlah_bayar']   ?? 0);
$metode_pembayaran = trim($_POST['metode_pembayaran']  ?? '');
$tanggal_bayar     = $_POST['tanggal_bayar']           ?? date('Y-m-d');

if (!$id_pesanan || !$metode_pembayaran || $jumlah_bayar <= 0) {
    $_SESSION['pesan_error'] = 'Data pembayaran tidak lengkap.';
    header("Location: pembayaran.php"); exit();
}

$row = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT * FROM pesanan WHERE id_pesanan=$id_pesanan AND status='Selesai'"));
if (!$row) {
    $_SESSION['pesan_error'] = 'Pesanan tidak valid atau belum selesai diproses.';
    header("Location: pembayaran.php"); exit();
}

$cek = mysqli_fetch_row(mysqli_query($koneksi,
    "SELECT COUNT(*) FROM pembayaran WHERE id_pesanan=$id_pesanan"));
if ($cek[0] > 0) {
    $_SESSION['pesan_error'] = 'Pesanan ini sudah pernah dibayar.';
    header("Location: pembayaran.php"); exit();
}

$stmt = mysqli_prepare($koneksi,
    "INSERT INTO pembayaran (id_pesanan, metode_pembayaran, jumlah_bayar, tanggal_bayar)
     VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, 'isds', $id_pesanan, $metode_pembayaran, $jumlah_bayar, $tanggal_bayar);

if (mysqli_stmt_execute($stmt)) {
    $id_pembayaran = mysqli_insert_id($koneksi);

    mysqli_query($koneksi, "UPDATE pesanan SET status='Diambil' WHERE id_pesanan=$id_pesanan");
    $_SESSION['pesan_sukses'] = 'Pembayaran berhasil! Status pesanan diperbarui menjadi Diambil.';

    header("Location: struk_pembayaran.php?id=$id_pembayaran");
} else {
    $_SESSION['pesan_error'] = 'Gagal menyimpan pembayaran: ' . mysqli_error($koneksi);
    header("Location: pembayaran.php");
}
exit();