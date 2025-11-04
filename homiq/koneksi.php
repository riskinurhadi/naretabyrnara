<?php
// koneksi.php

$host = 'localhost';
$user = 'adiputra'; // Ganti dengan username database Anda
$pass = 'Aloevera21.'; // Ganti dengan password database Anda
$db   = 'adiputra'; // Ganti dengan nama database Anda

$koneksi = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi Gagal: " . $koneksi->connect_error);
}

// Mengatur zona waktu default
date_default_timezone_set('Asia/Jakarta');
?>