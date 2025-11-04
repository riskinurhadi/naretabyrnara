<?php
session_start();
require_once 'koneksi.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

$error_message = '';
$success_message = '';

// Tampilkan pesan sukses dari registrasi jika ada
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Hapus pesan setelah ditampilkan
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $koneksi->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error_message = "Username dan Password wajib diisi.";
    } else {
        // Gunakan prepared statement untuk keamanan
        $stmt = $koneksi->prepare("SELECT id_user, username, password, nama_lengkap, role FROM tbl_users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Password benar, buat session
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['role'] = $user['role'];

                // Redirect ke dashboard
                header("Location: dashboard.php");
                exit;
            } else {
                // Password salah
                $error_message = "Username atau Password salah.";
            }
        } else {
            // Username tidak ditemukan
            $error_message = "Username atau Password salah.";
        }
        $stmt->close();
    }
}
$koneksi->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CMS Guesthouse Adiputra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        /* CSS Internal untuk tampilan clean & modern */
        body {
            background-color: #f8f9fa; /* Latar belakang abu-abu muda */
        }
        .login-container {
            min-height: 100vh;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            border: none; /* Hilangkan border card */
            border-radius: 0.75rem; /* Sudut lebih tumpul */
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); /* Shadow lebih halus */
        }
        .brand-logo {
            font-size: 1.75rem;
            font-weight: 700;
            color: #343a40;
        }
    </style>
</head>
<body>

    <div class="container-fluid">
        <div class="row d-flex justify-content-center align-items-center login-container">
            <div class="col-12 col-md-8 col-lg-6 col-xl-4">
                <div class="card login-card p-4 p-sm-5">
                    <div class="card-body">
                        
                        <div class="text-center mb-4">
                            <span class="brand-logo">Guesthouse Adiputra</span>
                            <h4 class="mt-2 mb-0">Selamat Datang</h4>
                            <small class="text-muted">Login untuk mengelola sistem</small>
                        </div>

                        <?php if(!empty($error_message)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($success_message)): ?>
                            <div class="alert alert-success" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <?php echo $success_message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="login.php">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                </div>

                            <button type="submit" class="btn btn-primary w-100 py-2">Login</button>
                        </form>
                        
                        <div class="text-center mt-4">
                            <small class="text-muted">Belum punya akun? <a href="register.php">Register di sini</a></small>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>