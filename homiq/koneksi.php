<?php
/*
File: koneksi.php
Deskripsi: File untuk menghubungkan aplikasi ke database MySQL.
*/

// --- Konfigurasi Database ---
$db_host = "localhost";      // Biasanya "localhost"
$db_user = "adiputra";           // Ganti dengan username database Anda
$db_pass = "Aloevera21.";               // Ganti dengan password database Anda
$db_name = "adiputra"; // Ganti dengan nama database Anda
// -----------------------------

// Membuat koneksi menggunakan MySQLi
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Cek koneksi
if ($conn->connect_error) {
    // Jika koneksi gagal, tampilkan pesan error dan hentikan script
    die("Koneksi ke database GAGAL: " . $conn->connect_error);
}

// Opsional: Set karakter encoding ke utf8mb4 (disarankan)
// Ini membantu menangani karakter khusus atau emoji jika ada
$conn->set_charset("utf8mb4");

// Jika Anda butuh menampilkan pesan sukses (biasanya di-disable setelah testing)
// echo "Koneksi ke database BERHASIL";

?>