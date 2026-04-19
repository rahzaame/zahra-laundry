<?php
define('BASE_URL', '../');
require_once BASE_URL . 'config/koneksi.php';

$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $row = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT status FROM pesanan WHERE id_pesanan=$id"));
    if ($row && $row['status'] === 'Menunggu') {
        mysqli_query($koneksi, "DELETE FROM pesanan WHERE id_pesanan=$id");
        $_SESSION['pesan_sukses'] = 'Pesanan berhasil dihapus.';
    } else {
        $_SESSION['pesan_error'] = 'Pesanan tidak dapat dihapus (sudah diproses).';
    }
}
header("Location: daftar_pesanan.php"); exit();