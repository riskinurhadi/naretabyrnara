<?php
session_start(); // Wajib ada di paling atas

// Cek apakah user BELUM login
if (!isset($_SESSION['user_id'])) {
    // Jika belum, lempar kembali ke login.php
    header("Location: login.php");
    exit();
}

// Ambil data user dari session
$nama_user = $_SESSION['nama_lengkap'];
$role_user = $_SESSION['role'];

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Guesthouse Adiputra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .navbar-brand {
            color: #007BFF !important;
            font-weight: 600;
        }
    </style>
</head>
<body>
    
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                Adiputra Guesthouse CMS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Reservasi</a>
                    </li>
                    <?php if ($role_user == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Manajemen Kamar</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Manajemen User</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <?php echo htmlspecialchars($nama_user); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#">Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h1>Selamat Datang, <?php echo htmlspecialchars($nama_user); ?>!</h1>
                        <p class.="lead">Anda login sebagai: <strong><?php echo htmlspecialchars($role_user); ?></strong>.</p>
                        <hr>
                        <p>Ini adalah halaman dashboard utama. Dari sini Anda akan mengelola reservasi, kamar, dan pengaturan lainnya.</p>
                        
                        <?php if ($role_user == 'admin'): ?>
                            <a href="#" class="btn btn-primary">Kelola Properti (Admin)</a>
                        <?php else: ?>
                            <a href="#" class="btn btn-primary">Lihat Kalender Reservasi</a>
                        <?php endif; ?>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>