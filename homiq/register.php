<?php
session_start();
require_once 'koneksi.php';

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $koneksi->real_escape_string($_POST['username']);
    $nama_lengkap = $koneksi->real_escape_string($_POST['nama_lengkap']);
    $password = $_POST['password'];
    $konfirmasi_password = $_POST['konfirmasi_password'];
    $role = $koneksi->real_escape_string($_POST['role']);

    // Validasi sederhana
    if (empty($username) || empty($nama_lengkap) || empty($password) || empty($role)) {
        $error_message = "Semua field wajib diisi.";
    } elseif ($password !== $konfirmasi_password) {
        $error_message = "Password dan Konfirmasi Password tidak cocok.";
    } else {
        // Cek apakah username sudah ada
        $stmt_check = $koneksi->prepare("SELECT id_user FROM tbl_users WHERE username = ?");
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $error_message = "Username sudah terdaftar. Silakan gunakan username lain.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert ke database
            $stmt_insert = $koneksi->prepare("INSERT INTO tbl_users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)");
            $stmt_insert->bind_param("ssss", $username, $hashed_password, $nama_lengkap, $role);

            if ($stmt_insert->execute()) {
                $_SESSION['success_message'] = "Registrasi berhasil! Silakan login.";
                header("Location: login.php");
                exit;
            } else {
                $error_message = "Registrasi gagal. Silakan coba lagi. Error: " . $stmt_insert->error;
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}
$koneksi->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CMS Guesthouse Adiputra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        /* CSS Internal untuk tampilan clean & modern */
        body {
            background-color: #f8f9fa; /* Latar belakang abu-abu muda */
        }
        .register-container {
            min-height: 100vh;
        }
        .register-card {
            max-width: 500px;
            border: none; /* Hilangkan border card */
            border-radius: 0.75rem; /* Sudut lebih tumpul */
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); /* Shadow lebih halus */
        }
        .brand-logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: #343a40;
        }
    </style>
</head>
<body>

    <div class="container-fluid">
        <div class="row d-flex justify-content-center align-items-center register-container">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card register-card p-4 p-sm-5">
                    <div class="card-body">
                        
                        <div class="text-center mb-4">
                            <span class="brand-logo">Guesthouse Adiputra</span>
                            <h4 class="mt-2 mb-0">Buat Akun Baru</h4>
                            <small class="text-muted">Isi data untuk mendaftarkan user baru</small>
                        </div>

                        <?php if(!empty($error_message)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="register.php" id="registerForm">
                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                            </div>

                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="" disabled selected>-- Pilih Role --</option>
                                    <option value="admin">Admin</option>
                                    <option value="front_office">Front Office</option>
                                    <option value="housekeeping">Housekeeping</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <div class="mb-3">
                                <label for="konfirmasi_password" class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 mt-3">Register</button>
                        </form>
                        
                        <div class="text-center mt-4">
                            <small class="text-muted">Sudah punya akun? <a href="login.php">Login di sini</a></small>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // (Opsional) Anda bisa menambahkan validasi JS di sini
        // Misalnya, cek kesamaan password sebelum submit
    </script>
</body>
</html>