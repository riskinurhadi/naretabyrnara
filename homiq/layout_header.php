<?php
// session_start() harus ada di setiap halaman yang menggunakan session
// Kita akan panggil ini di halaman utamanya (index.php, manajemen_kamar.php)
// tapi kita siapkan variabelnya di sini.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$nama_user = $_SESSION['nama_lengkap'] ?? 'User';
$role_user = $_SESSION['role'] ?? 'guest';

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
            /* Warna biru dari gambar referensi (disesuaikan) */
            --brand-blue: #0d6efd; /* Bootstrap Primary Blue */
            --sidebar-bg: #001f3f; /* Biru Navy Tua (Mirip Puzzler) */
            --sidebar-link: #adb5bd;
            --sidebar-link-active: #ffffff;
            --sidebar-link-hover: #f8f9fa;
            --content-bg: #f8f9fa;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--content-bg);
            display: flex;
            min-height: 100vh;
        }

        /* --- Sidebar --- */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            transition: width 0.3s ease;
        }
        
        .sidebar-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .sidebar-header .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--sidebar-link-active);
            text-decoration: none;
            /* Placeholder logo 'A' seperti di login */
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sidebar-header .logo .logo-icon {
            width: 40px;
            height: 40px;
            background-color: var(--brand-blue);
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 10px;
        }

        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
            flex-grow: 1; /* Mendorong logout ke bawah */
        }
        .sidebar-nav .nav-item {
            margin-bottom: 5px;
        }
        .sidebar-nav .nav-link {
            color: var(--sidebar-link);
            text-decoration: none;
            padding: 12px 15px;
            display: flex;
            align-items: center;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .sidebar-nav .nav-link i {
            margin-right: 12px;
            font-size: 1.2rem;
            width: 20px; /* Jaga alignment icon */
        }
        .sidebar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--sidebar-link-hover);
        }
        .sidebar-nav .nav-link.active {
            background-color: var(--brand-blue);
            color: var(--sidebar-link-active);
            font-weight: 500;
        }

        .sidebar-footer {
            margin-top: auto;
        }

        /* --- Main Content --- */
        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow: auto; /* Untuk scrolling konten */
        }

        /* --- Top Bar (Header) --- */
        .top-bar {
            background-color: #ffffff;
            padding: 15px 30px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: flex-end; /* Pindahkan search ke kiri nanti */
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .user-profile .dropdown-toggle::after {
            display: none; /* Sembunyikan panah default */
        }
        .user-profile .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
            background-color: var(--brand-blue); /* Placeholder avatar */
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        .user-profile .user-info {
            line-height: 1.2;
            text-align: right;
        }
        .user-profile .user-name {
            font-weight: 600;
            color: #333;
        }
        .user-profile .user-role {
            font-size: 0.85rem;
            color: #6c757d;
        }
        .dropdown-menu-end {
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: none;
        }

        /* --- Konten Halaman --- */
        .content-area {
            padding: 30px;
            flex-grow: 1;
        }
        
        /* Style untuk Tombol Biru (Add New) */
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
        
        /* Style untuk Tabel (dari referensi) */
        .card-table {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden; /* Penting untuk border-radius tabel */
        }
        .table {
            margin-bottom: 0; /* Hapus margin bawah default */
        }
        .table thead th {
            background-color: #f8f9fa; /* Header tabel abu-abu muda */
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
            vertical-align: middle;
            font-size: 0.9rem;
            text-transform: uppercase;
        }
        .table tbody td {
            vertical-align: middle;
            color: #333;
            font-weight: 500;
        }
        .table tbody tr:hover {
            background-color: #f0f7ff; /* Hover biru muda */
        }
        
        /* Style untuk Status (Pending, Active, dll) */
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            font-size: 0.8rem;
            font-weight: 600;
            border-radius: 20px;
            letter-spacing: 0.5px;
        }
        .status-badge-active {
            background-color: #e6f7f0;
            color: #198754;
        }
        .status-badge-pending {
            background-color: #fffbeb;
            color: #ffc107;
        }
        .status-badge-inactive {
            background-color: #f8d7da;
            color: #dc3545;
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
                <a class="nav-link active" href="index.php">
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
                <hr class="text-white-50 my-2">
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-building-fill-gear"></i>
                        <span>Manajemen Properti</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
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
        <header class="top-bar">
            
            <div class="user-profile ms-auto">
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <div class="user-avatar">
                                <?php echo htmlspecialchars(strtoupper(substr($nama_user, 0, 1))); ?>
                            </div>
                            <div class="user-info d-none d-md-block">
                                <span class="user-name"><?php echo htmlspecialchars($nama_user); ?></span><br>
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