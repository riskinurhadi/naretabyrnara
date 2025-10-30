<?php
// Memulai session di baris paling awal
session_start();

// Memanggil file koneksi. Karena file ini ada di dalam folder 'admin',
// path-nya harus mundur satu level ke '../config.php'
require_once '../config.php';

// Jika pengguna sudah login, alihkan ke dashboard
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("location: dashboard.php");
    exit;
}

// Variabel untuk menampung pesan error atau sukses
$login_err = $register_msg = $register_err = "";

// Proses Registrasi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    // Validasi username
    if (empty(trim($_POST["username"]))) {
        $register_err = "Username tidak boleh kosong.";
    } else {
        // Cek apakah username sudah ada
        $sql = "SELECT id FROM users WHERE username = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = trim($_POST["username"]);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $register_err = "Username ini sudah digunakan.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                $register_err = "Oops! Terjadi kesalahan. Silakan coba lagi nanti.";
            }
            $stmt->close();
        }
    }

    // Validasi password
    if (empty(trim($_POST["password"]))) {
        $register_err = "Password tidak boleh kosong.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $register_err = "Password minimal harus 6 karakter.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validasi konfirmasi password
    if (empty(trim($_POST["confirm_password"]))) {
        $register_err = "Mohon konfirmasi password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($register_err) && ($password != $confirm_password)) {
            $register_err = "Password tidak cocok.";
        }
    }

    // Jika tidak ada error, masukkan data ke database
    if (empty($register_err)) {
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $param_username, $param_password);
            $param_username = $username;
            // Hash password untuk keamanan
            $param_password = password_hash($password, PASSWORD_DEFAULT);

            if ($stmt->execute()) {
                $register_msg = "Registrasi berhasil! Silakan login.";
            } else {
                $register_err = "Terjadi kesalahan saat membuat akun.";
            }
            $stmt->close();
        }
    }
}

// Proses Login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    if (empty(trim($_POST["username"]))) {
        $login_err = "Mohon masukkan username.";
    } else {
        $username = trim($_POST["username"]);
    }

    if (empty(trim($_POST["password"]))) {
        $login_err = "Mohon masukkan password.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($login_err)) {
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = $username;
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $username, $hashed_password);
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            // Password benar, mulai session baru
                            session_start();
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            // Alihkan ke halaman dashboard
                            header("location: dashboard.php");
                        } else {
                            $login_err = "Username atau password salah.";
                        }
                    }
                } else {
                    $login_err = "Username atau password salah.";
                }
            } else {
                $login_err = "Oops! Terjadi kesalahan.";
            }
            $stmt->close();
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autentikasi - rnara.id</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-color: #1a73e8;
            --background-color: #f8f9fa;
            --card-background: #ffffff;
            --shadow-light: 0 4px 20px rgba(0, 0, 0, 0.06);
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .auth-container {
            max-width: 450px;
            width: 100%;
        }
        .auth-card {
            background-color: var(--card-background);
            border: none;
            border-radius: 15px;
            box-shadow: var(--shadow-light);
            overflow: hidden; /* Agar efek di tab tidak keluar card */
        }
        .auth-header {
            text-align: center;
            padding: 30px;
        }
        .auth-header h2 {
            font-weight: 700;
            color: var(--brand-color);
        }
        .nav-tabs {
            border-bottom: none;
            display: flex;
        }
        .nav-tabs .nav-link {
            flex: 1; /* Membuat tab memiliki lebar yang sama */
            text-align: center;
            border: none;
            border-bottom: 3px solid transparent;
            color: #6c757d;
            font-weight: 600;
            padding: 15px;
            transition: all 0.3s ease;
        }
        .nav-tabs .nav-link.active {
            color: var(--brand-color);
            border-bottom: 3px solid var(--brand-color);
            background-color: transparent;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #dee2e6;
        }
        .form-control:focus {
            border-color: var(--brand-color);
            box-shadow: 0 0 0 0.25rem rgba(26, 115, 232, 0.25);
        }
        .btn-primary {
            background-color: var(--brand-color);
            border-color: var(--brand-color);
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #155cb8;
            border-color: #155cb8;
            transform: translateY(-2px);
        }
        .alert {
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <h2><i class="bi bi-link-45deg"></i> rnara.id</h2>
            <p class="text-muted">Selamat Datang di Panel Admin</p>
        </div>
        <div class="auth-card">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login-tab-pane" type="button" role="tab" aria-controls="login-tab-pane" aria-selected="true">Login</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register-tab-pane" type="button" role="tab" aria-controls="register-tab-pane" aria-selected="false">Register</button>
                </li>
            </ul>
            <div class="tab-content p-4">
                <div class="tab-pane fade show active" id="login-tab-pane" role="tabpanel" aria-labelledby="login-tab" tabindex="0">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <?php if (!empty($login_err)): ?>
                            <div class="alert alert-danger"><?php echo $login_err; ?></div>
                        <?php endif; ?>
                        <?php if (!empty($register_msg)): ?>
                            <div class="alert alert-success"><?php echo $register_msg; ?></div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="loginUsername" class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" id="loginUsername" required>
                        </div>
                        <div class="mb-4">
                            <label for="loginPassword" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" id="loginPassword" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="login" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="register-tab-pane" role="tabpanel" aria-labelledby="register-tab" tabindex="0">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <?php if(!empty($register_err)): ?>
                            <div class="alert alert-danger"><?php echo $register_err; ?></div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="registerUsername" class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" id="registerUsername" required>
                        </div>
                        <div class="mb-3">
                            <label for="registerPassword" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" id="registerPassword" required>
                        </div>
                        <div class="mb-4">
                            <label for="confirmPassword" class="form-label">Konfirmasi Password</label>
                            <input type="password" name="confirm_password" class="form-control" id="confirmPassword" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="register" class="btn btn-primary">Register</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>