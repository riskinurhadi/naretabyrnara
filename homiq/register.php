<?php
session_start();
require 'koneksi.php';

// Variabel untuk menyimpan pesan
$error_msg = "";
$success_msg = "";

// Cek jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $nama_lengkap = $conn->real_escape_string($_POST['nama_lengkap']);
    $username     = $conn->real_escape_string($_POST['username']);
    $password     = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];
    $role         = $conn->real_escape_string($_POST['role']);

    // Validasi sederhana
    if (empty($nama_lengkap) || empty($username) || empty($password) || empty($role)) {
        $error_msg = "Semua field wajib diisi.";
    } elseif ($password != $confirm_pass) {
        $error_msg = "Password dan Konfirmasi Password tidak cocok.";
    } elseif (strlen($password) < 6) {
        $error_msg = "Password minimal harus 6 karakter.";
    } else {
        // Cek apakah username sudah ada
        $sql_check = "SELECT id_user FROM tbl_users WHERE username = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $stmt_check->store_result();
        
        if ($stmt_check->num_rows > 0) {
            $error_msg = "Username sudah terdaftar. Silakan gunakan username lain.";
        } else {
            // Username aman, lanjutkan registrasi
            // HASH PASSWORD!
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert ke database menggunakan prepared statement
            $sql_insert = "INSERT INTO tbl_users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("ssss", $username, $hashed_password, $nama_lengkap, $role);
            
            if ($stmt_insert->execute()) {
                $success_msg = "Registrasi Berhasil! Silakan <a href='login.php'>login di sini</a>.";
            } else {
                $error_msg = "Registrasi Gagal. Terjadi kesalahan pada server.";
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Guesthouse Adiputra</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            /* Warna biru dari gambar Anda, sedikit disesuaikan agar profesional */
            --brand-blue: #007BFF; 
            --brand-blue-light: #F0F7FF;
            --brand-dark: #333;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--brand-blue-light);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .register-card {
            width: 100%;
            max-width: 500px; /* Sedikit lebih lebar untuk form registrasi */
            background-color: #fff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.07);
            overflow: hidden; /* Untuk border-radius */
        }

        .card-header-brand {
            background-color: var(--brand-blue);
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .card-header-brand h3 {
            margin: 0;
            font-weight: 600;
        }

        .card-body {
            padding: 30px;
        }

        .form-floating > .form-control {
            border-radius: 8px;
        }

        .form-floating > label {
            color: #6c757d;
        }

        .btn-brand {
            background-color: var(--brand-blue);
            border-color: var(--brand-blue);
            color: #fff;
            padding: 12px;
            font-weight: 500;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .btn-brand:hover {
            background-color: #0069d9;
            border-color: #0062cc;
            color: #fff;
        }
        
        .text-link {
            color: var(--brand-blue);
            text-decoration: none;
        }
        .text-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="register-card">
        <div class="card-header-brand">
            <h3>Buat Akun Baru</h3>
        </div>
        <div class="card-body">

            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success_msg)): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $success_msg; ?>
                </div>
            <?php else: // Sembunyikan form jika sudah sukses ?>
            
            <form action="register.php" method="POST">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" placeholder="Nama Lengkap Anda" required>
                    <label for="nama_lengkap">Nama Lengkap</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Buat Username" required>
                    <label for="username">Username</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Buat Password" required>
                    <label for="password">Password (min. 6 karakter)</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Password" required>
                    <label for="confirm_password">Konfirmasi Password</label>
                </div>

                <div class="form-floating mb-3">
                    <select class="form-select" id="role" name="role" required>
                        <option value="" disabled selected>Pilih Role Akun</option>
                        <option value="admin">Admin</option>
                        <option value="front_office">Front Office</option>
                        <option value="housekeeping">Housekeeping</option>
                    </select>
                    <label for="role">Role</label>
                </div>

                <button type="submit" class="btn btn-brand w-100 mt-3">Daftar</button>
            </form>
            
            <?php endif; ?>

            <p class="text-center mt-4 mb-0">
                Sudah punya akun? <a href="login.php" class="text-link">Login di sini</a>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>