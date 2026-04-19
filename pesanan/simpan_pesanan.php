<?php
define('BASE_URL', '../');
require_once BASE_URL . 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: tambah_pesanan.php"); exit();
}

$kode          = trim($_POST['kode_pesanan']   ?? '');
$nama          = trim($_POST['nama_pelanggan'] ?? '');
$no_hp         = trim($_POST['no_hp']          ?? '');
$jenis         = trim($_POST['jenis_laundry']  ?? '');
$berat         = floatval($_POST['berat']       ?? 0);
$harga_perkg   = floatval($_POST['harga_perkg'] ?? 0);
$tanggal_masuk = $_POST['tanggal_masuk']       ?? date('Y-m-d');

if (!$kode || !$nama || !$no_hp || !$jenis || $berat <= 0 || $harga_perkg <= 0) {
    $_SESSION['pesan_error'] = 'Semua field wajib diisi dengan benar.';
    header("Location: tambah_pesanan.php"); exit();
}

$total_harga = $berat * $harga_perkg;

$sql  = "INSERT INTO pesanan (kode_pesanan, nama_pelanggan, no_hp, jenis_laundry, berat, harga_perkg, total_harga, status, tanggal_masuk) VALUES (?, ?, ?, ?, ?, ?, ?, 'Menunggu', ?)";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, 'ssssddds', $kode, $nama, $no_hp, $jenis, $berat, $harga_perkg, $total_harga, $tanggal_masuk);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['pesan_sukses'] = "Pesanan <strong>$kode</strong> berhasil disimpan!";
    header("Location: daftar_pesanan.php");
} else {
    $_SESSION['pesan_error'] = 'Gagal menyimpan: ' . mysqli_error($koneksi);
    header("Location: tambah_pesanan.php");
}
exit();