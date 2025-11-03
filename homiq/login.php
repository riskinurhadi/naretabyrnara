<?php
session_start(); // Wajib ada di paling atas

// Jika user SUDAH login, lempar ke index.php
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require 'koneksi.php';

$error_msg = "";

// Cek jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error_msg = "Username dan password wajib diisi.";
    } else {
        // Ambil data user dari database
        $sql = "SELECT id_user, password, nama_lengkap, role FROM tbl_users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Password benar! Buat session
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['username'] = $username;
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['role'] = $user['role'];
                
                // Redirect ke halaman dashboard
                header("Location: index.php");
                exit();
            } else {
                // Password salah
                $error_msg = "Username atau password salah.";
            }
        } else {
            // Username tidak ditemukan
            $error_msg = "Username atau password salah.";
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Guesthouse Adiputra</title>
    
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

        .login-card {
            width: 100%;
            max-width: 400px;
            background-color: #fff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.07);
        }

        .card-body {
            padding: 30px;
        }

        .brand-logo {
            display: block;
            margin: 0 auto 20px;
            width: 80px; /* Ukuran logo placeholder */
            height: 80px;
            background-color: var(--brand-blue);
            border-radius: 50%;
            /* Nanti bisa diganti <img src="logo.png"> */
            
            /* Placeholder Icon (SVG) */
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: bold;
        }
        
        .login-title {
            text-align: center;
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--brand-dark);
            margin-bottom: 5px;
        }
        
        .login-subtitle {
            text-align: center;
            color: #6c757d;
            margin-bottom: 25px;
        }

        .form-floating > .form-control {
            border-radius: 8px;
        }

        .form-floating > label {
            color: #6c757d;
        }
        
        .form-check {
            padding-left: 0;
        }

        .form-check-label {
            cursor: pointer;
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

    <div class="login-card">
        <div class="card-body">
            
            <div class="brand-logo" title="Logo Adiputra Guesthouse">
                A
            </div>
            
            <h3 class="login-title">Selamat Datang</h3>
            <p class="login-subtitle">Login ke sistem Guesthouse Adiputra</p>

            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                    <label for="username">Username</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    <label for="password">Password</label>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="showPassword">
                        <label class="form-check-label" for="showPassword">
                            Lihat Password
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-brand w-100">Login</button>
            </form>
            
            <p class="text-center mt-4 mb-0">
                Belum punya akun? <a href="register.php" class="text-link">Daftar di sini</a>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Script untuk toggle lihat password
        document.getElementById('showPassword').onclick = function() {
            if (this.checked) {
                document.getElementById('password').type = "text";
            } else {
                document.getElementById('password').type = "password";
            }
        };
    </script>
</body>
</html>