<?php
session_start();
// PASTIKAN FILE INI ADA DAN KONFIGURASINYA BENAR
include 'koneksi.php'; 

// Jika sudah login, redirect ke index.php
if (isset($_SESSION['id_user'])) {
    header("Location: index.php");
    exit();
}

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // --- INI ADALAH BAGIAN YANG DIPERBAIKI ---
    
    // 1. Ambil data user dari database
    $stmt = $koneksi->prepare("SELECT id_user, username, password, nama_lengkap, role FROM tbl_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // --- HAPUS BAGIAN TESTING ---
    // (Kode simulasi yang ada di sini sebelumnya sudah dihapus)
    // --- AKHIR HAPUS BAGIAN TESTING ---

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // 2. Verifikasi password yang di-hash
        if (password_verify($password, $user['password'])) {
            // Password benar, simpan session
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];
            
            header("Location: index.php");
            exit();
        } else {
            // Password salah
            $error_message = "Username atau password salah.";
        }
    } else {
        // Username tidak ditemukan
        $error_message = "Username atau password salah.";
    }
    
    $stmt->close();
    // $koneksi->close(); // Jangan ditutup agar session tetap berjalan baik
    
    // --- AKHIR BAGIAN PERBAIKAN ---
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Adiputra CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f0f4f8;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .login-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .login-card-img {
            background-image: url('https://images.unsplash.com/photo-1566073771259-6a8506099945?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3wzNTA5fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA&ixlib=rb-4.0.3&q=80&w=1080');
            background-size: cover;
            background-position: center;
            min-height: 100%;
        }
        .login-card .form-control {
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
        }
        .login-card .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 0.75rem;
            padding: 0.75rem;
            font-weight: 600;
        }
        .login-card .btn-primary:hover {
            background-color: #0056b3;
        }
        .login-header {
            color: #007bff;
            font-weight: 700;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                <div class="card login-card my-5">
                    <div class="row g-0">
                        <div class="col-lg-6 d-none d-lg-block login-card-img">
                            </div>
                        <div class="col-lg-6">
                            <div class="card-body p-4 p-md-5">
                                <div class="text-center mb-4">
                                    <i class="bi bi-buildings-fill fs-1 login-header"></i>
                                    <h2 class="h4 fw-bold mt-2">Guesthouse Adiputra</h2>
                                    <p class="text-muted">Silakan login ke akun Anda</p>
                                </div>
                                
                                <?php if (!empty($error_message)): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo $error_message; ?>
                                    </div>
                                <?php endif; ?>

                                <form method="POST" action="login.php">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">Login</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>