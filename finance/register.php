<?php
// Mulai sesi di awal
session_start();

// Jika pengguna sudah login, arahkan ke dashboard
if (isset($_SESSION['id_pengguna'])) {
    header("Location: dashboard.php");
    exit();
}

// Sertakan file koneksi
include 'koneksi.php';

$message = '';

// Cek jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    // Validasi dasar
    if (empty($nama_lengkap) || empty($email) || empty($password)) {
        $message = '<div class="alert alert-danger">Semua kolom wajib diisi.</div>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '<div class="alert alert-danger">Format email tidak valid.</div>';
    } else {
        // Cek apakah email sudah terdaftar
        $sql_check = "SELECT id_pengguna FROM tabel_pengguna WHERE email = ?";
        $stmt_check = mysqli_prepare($koneksi, $sql_check);
        mysqli_stmt_bind_param($stmt_check, "s", $email);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);

        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            $message = '<div class="alert alert-danger">Email sudah terdaftar. Silakan gunakan email lain.</div>';
        } else {
            // Hash password untuk keamanan
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert data pengguna baru
            $sql_insert = "INSERT INTO tabel_pengguna (nama_lengkap, email, password) VALUES (?, ?, ?)";
            $stmt_insert = mysqli_prepare($koneksi, $sql_insert);
            mysqli_stmt_bind_param($stmt_insert, "sss", $nama_lengkap, $email, $hashed_password);

            if (mysqli_stmt_execute($stmt_insert)) {
                $message = '<div class="alert alert-success">Registrasi berhasil! Silakan <a href="login.php">login</a>.</div>';
            } else {
                $message = '<div class="alert alert-danger">Registrasi gagal. Silakan coba lagi.</div>';
            }
            mysqli_stmt_close($stmt_insert);
        }
        mysqli_stmt_close($stmt_check);
    }
    mysqli_close($koneksi);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Manajemen Keuangan</title>
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
        }
        .auth-card {
            max-width: 400px;
            width: 100%;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px_12px rgba(0, 0, 0, 0.08);
        }
        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
        }
        .btn-primary {
            background-color: #4c6ef5;
            border-color: #4c6ef5;
            border-radius: 10px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="card auth-card">
        <div class="card-body p-4">
            <h3 class="text-center mb-1 fw-bold">Buat Akun</h3>
            <p class="text-center text-muted mb-4">Mulai kelola keuangan Anda.</p>
            
            <?php if(!empty($message)) echo $message; ?>

            <form action="register.php" method="POST">
                <div class="mb-3">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Alamat Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary py-2">Daftar</button>
                </div>
            </form>
            <p class="text-center mt-3 text-muted">
                Sudah punya akun? <a href="login.php">Login di sini</a>
            </p>
        </div>
    </div>
</body>
</html>
