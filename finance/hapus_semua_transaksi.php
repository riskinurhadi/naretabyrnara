<?php
// 1. Sertakan file koneksi untuk menghubungkan ke database
include 'koneksi.php';

// 2. Jalankan query untuk menghapus semua data dari tabel
// TRUNCATE TABLE lebih efisien daripada DELETE FROM dan akan mereset auto-increment ID.
if (mysqli_query($koneksi, "TRUNCATE TABLE tabel_transaksi")) {
    // Jika berhasil, redirect kembali ke dashboard dengan status sukses
    header("Location: dashboard.php?status=hapus_semua_sukses");
} else {
    // Jika gagal, redirect dengan status error (opsional, untuk debugging)
    header("Location: dashboard.php?status=hapus_semua_gagal");
}

// 3. Tutup koneksi
mysqli_close($koneksi);

// 4. Pastikan tidak ada output lain setelah header redirect
exit();
?>
