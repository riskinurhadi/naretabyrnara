<?php
// Mulai sesi di baris paling atas
session_start();

// Jika pengguna sudah login, langsung arahkan ke dashboard
if (isset($_SESSION['id_pengguna'])) {
    header("Location: dashboard.php");
    exit();
}

// Sertakan file koneksi
include 'koneksi.php';

$message = '';

// Cek jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $message = '<div class="alert alert-danger">Email dan password wajib diisi.</div>';
    } else {
        // Cari pengguna berdasarkan email
        $sql = "SELECT id_pengguna, nama_lengkap, password FROM tabel_pengguna WHERE email = ? LIMIT 1";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Password benar, buat sesi
                $_SESSION['id_pengguna'] = $user['id_pengguna'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                
                // Arahkan ke dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                // Password salah
                $message = '<div class="alert alert-danger">Email atau password salah.</div>';
            }
        } else {
            // Email tidak ditemukan
            $message = '<div class="alert alert-danger">Email atau password salah.</div>';
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($koneksi);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Manajemen Keuangan</title>
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
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
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
            <h3 class="text-center mb-1 fw-bold">Selamat Datang</h3>
            <p class="text-center text-muted mb-4">Login untuk melanjutkan.</p>
            
            <?php if(!empty($message)) echo $message; ?>

            <form action="login.php" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Alamat Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary py-2">Login</button>
                </div>
            </form>
            <p class="text-center mt-3 text-muted">
                Belum punya akun? <a href="register.php">Daftar di sini</a>
            </p>
        </div>
    </div>
</body>
</html>
