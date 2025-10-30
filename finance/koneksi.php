<?php
// --- Pengaturan Koneksi Database ---

// Alamat server database Anda. Biasanya 'localhost' jika di server yang sama.
$servername = "localhost";

// Username untuk mengakses database. Ganti dengan username database Anda.
$username = "keme7623_finance"; // Contoh untuk XAMPP/lokal. Ganti di hosting!

// Password untuk username di atas. Ganti dengan password database Anda.
$password = "Wiyachan123."; // Contoh untuk XAMPP/lokal. Ganti di hosting!

// Nama database yang akan digunakan.
$database = "keme7623_finance";


// --- Membuat Koneksi ---

// Mencoba membuat koneksi ke database MySQL
$koneksi = mysqli_connect($servername, $username, $password, $database);


// --- Memeriksa Koneksi ---

// Jika koneksi gagal, hentikan skrip dan tampilkan pesan error.
if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Jika berhasil, tidak perlu ada output apa pun.
// File ini akan di-include di file lain yang membutuhkan koneksi.
?>
