<?php
// Pengaturan koneksi database
$servername = "localhost"; // Biasanya "localhost"
$username = "naretabyrnara";      // Username default XAMPP
$password = "Wiyachan123.";          // Password default XAMPP (kosong)
$dbname = "naretabyrnara"; // Nama database yang Anda buat

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
  die("Koneksi ke database gagal: " . $conn->connect_error);
}
?>