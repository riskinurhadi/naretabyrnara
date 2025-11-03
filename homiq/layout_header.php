<?php
// session_start() akan dipanggil di halaman utamanya
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Variabel ini akan kita gunakan di halaman (seperti index.php)
$nama_user = $_SESSION['nama_lengkap'] ?? 'User';
$role_user = $_SESSION['role'] ?? 'guest';
$inisial_user = htmlspecialchars(strtoupper(substr($nama_user, 0, 1)));

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand-blue: #0d6efd; 
            --sidebar-bg: rgba(13, 30, 45, 0.85); /* Warna navy semi-transparan */
            --sidebar-link: #adb5bd;
            --sidebar-link-active: #ffffff;
            --content-bg: #f5f7fa; /* Latar belakang abu-abu sangat muda */
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            --border-color: #dee2e6;
        }

        body {
            font-family: 'Poppins', sans-serif;
            /* Latar belakang ini penting untuk efek glassmorphism */
            background-color: var(--content-bg);
            background-image: linear-gradient(180deg, #f5f7fa 0%, #e8ecf1 100%);
            display: flex;
            min-height: 100vh;
        }

        /* --- Sidebar (Glassmorphism) --- */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            /* Efek Glassmorphism */
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            transition: width 0.3s ease;
        }
        
        .sidebar-header {
            margin-bottom: 20px;
            padding-left: 10px;
        }
        .sidebar-header .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--sidebar-link-active); /* Teks logo putih */
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        .sidebar-header .logo .logo-icon {
            width: 40px;
            height: 40px;
            background-color: var(--brand-blue);
            color: white;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 12px;
        }

        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
            flex-grow: 1; 
        }
        .sidebar-nav .nav-item {
            margin-bottom: 5px;
        }
        .sidebar-nav .nav-link {
            color: var(--sidebar-link); /* Teks link abu-abu */
            text-decoration: none;
            padding: 12px 15px;
            display: flex;
            align-items: center;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .sidebar-nav .nav-link i {
            margin-right: 12px;
            font-size: 1.2rem;
            width: 20px;
        }
        .sidebar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--sidebar-link-active);
        }
        .sidebar-nav .nav-link.active {
            background-color: var(--brand-blue);
            color: #ffffff;
            font-weight: 500;
        }
        /* Garis pemisah di sidebar */
        .sidebar-divider {
            height: 1px;
            background-color: rgba(255, 255, 255, 0.15);
            margin: 1rem 0;
        }

        .sidebar-footer {
            margin-top: auto;
        }
        .sidebar-footer .nav-link {
            color: var(--sidebar-link);
        }
        .sidebar-footer .nav-link:hover {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
        
        /* --- Main Content --- */
        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow: auto; /* Konten bisa di-scroll */
        }
        
        /* KONTEN HALAMAN (TIDAK ADA TOP-BAR) */
        .content-area {
            padding: 30px;
            flex-grow: 1;
        }
        
        /* --- Style Kartu Statistik (BARU) --- */
        /* (Kita akan pindahkan ini ke index.php nanti, tapi siapkan dasarnya) */
        .stat-card-new {
            background-color: #ffffff;
            border: none;
            border-radius: 16px; /* Lebih rounded */
            box-shadow: var(--card-shadow);
            padding: 25px;
            display: flex;
            align-items: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card-new:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.07);
        }
        .stat-card-new .icon-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 1.8rem;
            color: #fff;
        }
        .stat-card-new .info {
            line-height: 1.2;
        }
        .stat-card-new .info-value {
            font-size: 2.2rem;
            font-weight: 700;
            color: #212529;
        }
        .stat-card-new .info-label {
            font-size: 0.95rem;
            color: #6c757d;
        }
        
        /* Warna-warna untuk ikon */
        .bg-blue { background-color: #0d6efd; }
        .bg-green { background-color: #198754; }
        .bg-orange { background-color: #fd7e14; }
        .bg-purple { background-color: #6f42c1; }
        
        /* --- Style Konten Utama (BARU) --- */
        .content-block {
            background-color: #ffffff;
            border: none;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }
        .content-block-header {
            padding: 20px 25px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .content-block-header h5 {
            margin: 0;
            font-weight: 600;
        }
        .content-block-body {
            padding: 25px;
        }
        .link-semua {
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
        }

    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <a href="index.php" class="logo">
                <span class="logo-icon">A</span>
                <span>Adiputra CMS</span>
            </a>
        </div>
        
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="bi bi-grid-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-calendar-check-fill"></i>
                    <span>Reservasi</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-person-fill"></i>
                    <span>Tamu</span>
                </a>
            </li>
            
            <?php if ($role_user == 'admin'): ?>
                <div class="sidebar-divider"></div>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-building-fill-gear"></i>
                        <span>Manajemen Properti</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manajemen_kamar.php">
                        <i class="bi bi-key-fill"></i>
                        <span>Manajemen Kamar</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-people-fill"></i>
                        <span>Manajemen User</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>

        <div class="sidebar-footer">
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">
                        <i class="bi bi-box-arrow-left"></i>
                        <span>Log out</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <main class="content-area">