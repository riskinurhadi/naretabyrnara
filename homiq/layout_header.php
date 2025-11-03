<?php
// session_start() akan dipanggil di halaman utamanya
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

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
            --brand-blue: #0d6efd; /* Bootstrap Primary Blue (sesuai referensi) */
            --brand-dark: #212529;
            --brand-gray: #6c757d;
            --brand-light-gray: #f8f9fa;
            --sidebar-bg: #ffffff;
            --content-bg: #f8f9fa; /* Latar belakang area konten */
            --card-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            --border-color: #dee2e6;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--content-bg);
            display: flex;
            min-height: 100vh;
        }

        /* --- Sidebar (Light Theme) --- */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            border-right: 1px solid var(--border-color);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }
        
        .sidebar-header {
            margin-bottom: 20px;
            padding-left: 10px; /* Sejajarkan dengan link */
        }
        .sidebar-header .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--brand-dark); /* Teks logo jadi gelap */
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
            color: var(--brand-gray); /* Teks link jadi abu-abu */
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
            background-color: var(--brand-light-gray); /* Hover abu-abu muda */
            color: var(--brand-blue); /* Teks hover jadi biru */
        }
        .sidebar-nav .nav-link.active {
            background-color: var(--brand-blue); /* Latar belakang aktif biru */
            color: #ffffff; /* Teks aktif putih */
            font-weight: 500;
        }

        .sidebar-footer {
            margin-top: auto;
        }
        .sidebar-footer .nav-link {
            color: var(--brand-gray);
        }
        .sidebar-footer .nav-link:hover {
            background-color: #fde2e4; /* Hover merah muda untuk logout */
            color: #dc3545;
        }
        
        /* --- Main Content --- */
        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow: auto;
        }

        /* --- Top Bar (Header) --- */
        .top-bar {
            background-color: #ffffff;
            padding: 15px 30px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            align-items: center;
            /* box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); */
        }
        
        .user-profile .dropdown-toggle::after { display: none; }
        .user-profile .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
            background-color: var(--brand-blue);
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        .user-profile .user-info {
            line-height: 1.2;
            text-align: left;
        }
        .user-profile .user-name {
            font-weight: 600;
            color: var(--brand-dark);
            display: block; /* <-- TAMBAHKAN INI */
        }
        .user-profile .user-role {
            font-size: 0.85rem;
            color: var(--brand-gray);
            display: block; /* <-- TAMBAHKAN INI */
        }
        .dropdown-menu-end {
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            border: none;
        }

        /* --- Konten Halaman --- */
        .content-area {
            padding: 30px;
            flex-grow: 1;
        }
        
        /* --- Tombol --- */
        .btn-brand {
            background-color: var(--brand-blue);
            border-color: var(--brand-blue);
            color: #fff;
            padding: 10px 18px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-brand:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
            color: #fff;
        }
        
        /* --- Style Kartu Dashboard (BARU) --- */
        .stat-card {
            background-color: #ffffff;
            border: none;
            border-radius: 12px; /* Lebih rounded */
            box-shadow: var(--card-shadow);
            padding: 25px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.07);
        }
        .stat-card-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--brand-gray);
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .stat-card-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--brand-dark);
        }
        .stat-card-icon {
            font-size: 2.5rem;
            color: var(--brand-blue);
            opacity: 0.7;
        }
        
        /* --- Style Kartu Konten Utama (BARU) --- */
        .main-card {
            background-color: #ffffff;
            border: none;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            overflow: hidden; /* Penting untuk tabel/header */
        }
        .main-card .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid var(--border-color);
            padding: 20px 25px;
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--brand-dark);
        }
        .main-card .card-body {
            padding: 25px;
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
                <a class="nav-link" href="index.php"> <i class="bi bi-grid-fill"></i>
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
                    <span>Daftar Tamu</span>
                </a>
            </li>
            
            <?php if ($role_user == 'admin'): ?>
                <hr class_ ="my-2" style="border-color: #e9ecef;">
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-building-fill-gear"></i>
                        <span>Properti</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manajemen_kamar.php"> <i class="bi bi-key-fill"></i>
                        <span>Kamar</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-people-fill"></i>
                        <span>User</span>
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
        <header class="top-bar">
            
            <div class="user-profile ms-auto">
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <div class="user-avatar">
                                <?php echo $inisial_user; ?>
                            </div>
                            <div class="user-info d-none d-md-block">
                                <span class="user-name"><?php echo htmlspecialchars($nama_user); ?></span>
                                <span class="user-role"><?php echo htmlspecialchars($role_user); ?></span>
                            </div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="#">Profil Saya</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="content-area">