<?php

if (session_status() === PHP_SESSION_NONE) session_start();
$koneksi = mysqli_connect("localhost", "root", "", "zahra_laundry");
if (!$koneksi) die("Koneksi gagal: " . mysqli_connect_error());
mysqli_set_charset($koneksi, "utf8");
?>