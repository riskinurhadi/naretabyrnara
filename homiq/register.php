<?php
// include 'koneksi.php'; // Asumsikan Anda punya file ini untuk koneksi database ($koneksi)
$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_lengkap = $_POST['nama_lengkap'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validasi sederhana
    if (empty($nama_lengkap) || empty($username) || empty($password) || empty($role)) {
        $error_message = "Semua field wajib diisi.";
    } else {
        // HASH PASSWORD! Ini sangat penting.
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        /*
        // Query untuk insert ke database
        $stmt = $koneksi->prepare("INSERT INTO tbl_users (nama_lengkap, username, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nama_lengkap, $username, $hashed_password, $role);

        if ($stmt->execute()) {
            $success_message = "User baru (".$username.") berhasil ditambahkan.";
        } else {
            $error_message = "Gagal menambahkan user. Error: " . $stmt->error;
        }
        $stmt->close();
        $koneksi->close();
        */
        
        // --- HANYA UNTUK TESTING (Hapus jika sudah konek DB) ---
        $success_message = "TESTING: User '$username' dengan role '$role' berhasil dibuat (password: $password).";
        // --- AKHIR BAGIAN TESTING ---
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi User - Adiputra CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f4f8;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .register-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px; /* Batasi lebar form */
        }
        .register-card .form-control {
            border-radius: 0.75rem;
        }
        .register-card .btn-primary {
            background-color: #0d6efd;
            border: none;
            border-radius: 0.75rem;
        }
        .register-card .btn-secondary {
            border-radius: 0.75rem;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card register-card my-5">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="text-center fw-bold text-primary mb-4">Tambah User Baru</h3>
                        <p class="text-center text-muted small">Halaman ini seharusnya hanya bisa diakses oleh Admin.</p>
                        
                        <?php if (!empty($success_message)): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo $success_message; ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="register.php">
                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-4">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">-- Pilih Role --</option>
                                    <option value="admin">Admin</option>
                                    <option value="front_office">Front Office</option>
                                    <option value="housekeeping">Housekeeping</option>
                                </select>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Daftarkan User</button>
                                <a href="login.php" class="btn btn-secondary btn-sm">Kembali ke Login</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>