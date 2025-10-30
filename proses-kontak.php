<?php
// Mulai session di baris paling atas
session_start();

// Sertakan file koneksi
include 'koneksi.php';

// Periksa apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil data dari form dan bersihkan
    $nama = htmlspecialchars($_POST['nama']);
    $email = htmlspecialchars($_POST['email']);
    $telepon = htmlspecialchars($_POST['telepon']);
    $layanan = htmlspecialchars($_POST['layanan']);
    $pesan = htmlspecialchars($_POST['pesan']);

    // Siapkan statement SQL untuk mencegah SQL Injection
    $stmt = $conn->prepare("INSERT INTO pesan_kontak (nama, email, telepon, layanan, pesan) VALUES (?, ?, ?, ?, ?)");
    
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("sssss", $nama, $email, $telepon, $layanan, $pesan);

    // Eksekusi statement dan siapkan notifikasi
    if ($stmt->execute()) {
        // Jika berhasil, siapkan notifikasi sukses di session
        $_SESSION['alert'] = [
            'type' => 'success',
            'title' => 'Berhasil Terkirim!',
            'text' => 'Terima kasih telah menghubungi kami. Pesan Anda akan segera kami proses.'
        ];
    } else {
        // Jika gagal, siapkan notifikasi error di session
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Oops... Terjadi Kesalahan',
            'text' => 'Pesan Anda gagal dikirim. Silakan coba lagi beberapa saat.'
        ];
    }

    // Tutup statement dan koneksi
    $stmt->close();
    $conn->close();

} else {
    // Jika file diakses langsung, siapkan notifikasi default
    $_SESSION['alert'] = [
        'type' => 'warning',
        'title' => 'Akses Dilarang',
        'text' => 'Anda mencoba mengakses halaman secara tidak benar.'
    ];
}

// Alihkan pengguna kembali ke halaman utama
header('Location: index.php'); // Pastikan nama file ini sesuai dengan file utama Anda
exit(); // Hentikan eksekusi skrip setelah redirect
?>