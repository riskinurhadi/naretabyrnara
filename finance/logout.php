<?php
// 1. Mulai sesi untuk mengakses data sesi yang ada.
session_start();

// 2. Hapus semua variabel sesi.
$_SESSION = array();

// 3. Hancurkan sesi secara keseluruhan.
session_destroy();

// Di titik ini, pengguna sudah dianggap logout dari sisi server.
// Sekarang kita tampilkan halaman transisi.
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout Berhasil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
        }
        .logout-message {
            max-width: 450px;
        }
    </style>
</head>
<body>
    <div class="logout-message">
        <div class="spinner-border text-primary mb-3" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <h3 class="fw-bold">Logout Berhasil</h3>
        <p class="text-muted">Anda telah berhasil keluar dari akun Anda. Anda akan diarahkan kembali ke halaman login dalam beberapa detik.</p>
        <p class="mt-4">
            <a href="login.php" class="btn btn-primary">Kembali ke Login Sekarang</a>
        </p>
    </div>

    <!-- PERBAIKAN: Menggunakan JavaScript untuk redirect -->
    <script>
        // Arahkan ke halaman login setelah 3 detik (3000 milidetik)
        setTimeout(function() {
            window.location.href = 'login.php';
        }, 3000);
    </script>
</body>
</html>
