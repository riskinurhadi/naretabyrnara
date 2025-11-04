<?php
// dashboard_baru.php
// Menggunakan file dashboard.php Anda sebagai basis,
// namun dengan CSS dan JS yang diperbarui.

require_once 'auth_check.php';
require_once 'koneksi.php';

$nama_user = htmlspecialchars($_SESSION['nama_lengkap']);
$role_user = htmlspecialchars($_SESSION['role']); // DIBUTUHKAN oleh sidebar_baru.php

// MENGAMBIL DATA STATISTIK DARI DATABASE (SAMA SEPERTI SEBELUMNYA)
$stmt_res_aktif = $koneksi->query("SELECT COUNT(*) as total FROM tbl_reservasi WHERE status_booking IN ('Booking', 'Checked-in')");
$stat_res_aktif = $stmt_res_aktif->fetch_assoc()['total'];
$stmt_kamar = $koneksi->query("SELECT COUNT(*) as total FROM tbl_kamar WHERE status = 'Tersedia'");
$stat_kamar = $stmt_kamar->fetch_assoc()['total'];
$stmt_tamu = $koneksi->query("SELECT COUNT(*) as total FROM tbl_tamu");
$stat_tamu = $stmt_tamu->fetch_assoc()['total'];
$stmt_properti = $koneksi->query("SELECT COUNT(*) as total FROM tbl_properti");
$stat_properti = $stmt_properti->fetch_assoc()['total'];
$query_reservasi = "
    SELECT r.tgl_checkin, t.nama_lengkap, k.nama_kamar, p.nama_properti
    FROM tbl_reservasi r
    JOIN tbl_tamu t ON r.id_tamu = t.id_tamu
    JOIN tbl_kamar k ON r.id_kamar = k.id_kamar
    JOIN tbl_properti p ON k.id_properti = p.id_properti
    WHERE r.status_booking IN ('Booking', 'Checked-in')
    ORDER BY r.tgl_checkin ASC
    LIMIT 5
";
$result_reservasi_terbaru = $koneksi->query($query_reservasi);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CMS Guesthouse Adiputra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- 
      ==========================================================
      == CSS INTERNAL MODERN & PROFESIONAL ==
      ==========================================================
    -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        :root {
            /* Ukuran */
            --sidebar-width: 280px;
            
            /* Warna Modern & Profesional */
            --bg-light: #f8fafc;      /* Latar belakang body lebih soft */
            --bg-white: #ffffff;       /* Latar belakang card */
            --text-dark: #1e293b;     /* Teks utama lebih gelap */
            --text-muted: #64748b;     /* Teks abu-abu modern */
            --border-color: #e2e8f0;   /* Garis batas lebih halus */
            
            /* Warna Ikon Statistik dengan Gradien Modern */
            --color-blue: #3b82f6;      --bg-blue-light: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            --color-green: #10b981;     --bg-green-light: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            --color-orange: #f59e0b;    --bg-orange-light: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            --color-purple: #8b5cf6;    --bg-purple-light: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
            
            /* Shadow Modern */
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            color: var(--text-dark);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* * 1. STYLING SIDEBAR BARU (Desktop & Mobile)
         */
        
        /* * PERUBAHAN: Selektor diperkuat dari .sidebar-nav-wrapper
         * menjadi .sidebar-nav-wrapper.offcanvas
         * Ini untuk mengalahkan selektor .offcanvas bawaan Bootstrap
         */
        /* .sidebar-nav-wrapper.offcanvas {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            border-right: none;  
            transition: transform 0.3s ease-in-out;
        }
        
        .sidebar-nav-wrapper .offcanvas-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-weight: 600;
            color: var(--bg-white); 
        } 
        
        .sidebar-nav {
            padding: 1rem;  
        }

        .sidebar-nav .nav-item {
            margin-bottom: 0.25rem;  
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--sidebar-text); 
            padding: 0.8rem 1.25rem; 
            border-radius: 0.75rem;
            transition: all 0.2s ease-in-out;
        }

        .sidebar-nav .nav-link i {
            font-size: 1.2rem;
            margin-right: 1rem;
            width: 24px; 
            text-align: center;
            color: var(--sidebar-text); 
            transition: all 0.2s ease-in-out;
        }
        
        
        .sidebar-nav .nav-link:hover {
            background-color: var(--sidebar-hover-bg);
            color: var(--bg-white);
        }
        .sidebar-nav .nav-link:hover i {
            color: var(--bg-white);
        }
        
        
        .sidebar-nav .nav-link.active {
            background-color: var(--sidebar-active-pill);
            color: var(--sidebar-text-active);
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .sidebar-nav .nav-link.active i {
            color: var(--sidebar-text-active);
        }

       
        .sidebar-nav .nav-link-logout {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--sidebar-text);
            opacity: 0.6;
        }
        .sidebar-nav .nav-link-logout:hover {
            opacity: 1;
            background-color: rgba(220, 53, 69, 0.1); 
            color: #dc3545;
        }
        .sidebar-nav .nav-link-logout:hover i {
            color: #dc3545;
        } */


        /* * 2. STYLING KONTEN UTAMA
         */
        #main-content {
            padding: 2rem; /* Padding lebih besar */
            width: 100%;
            margin-left: 0;
            transition: margin-left 0.3s ease-in-out;
        }

        /* * 3. LOGIKA RESPONSIVE (LAYOUT DESKTOP)
         */
        @media (min-width: 992px) {
            /* PERUBAHAN: Selektor juga diperkuat di sini */
            /* .sidebar-nav-wrapper.offcanvas {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                transform: none !important;
                visibility: visible !important;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); 
            } */
            
            /* #main-content {
                margin-left: var(--sidebar-width);
                width: calc(100% - var(--sidebar-width));
            } */
        }

        /* * 4. STYLING KONTEN (CARD, HEADER, DLL) - MODERN & PROFESIONAL
         */
        .main-header {
            background: var(--bg-white);
            padding: 1.5rem 2rem;
            border-radius: 1.25rem;
            box-shadow: var(--shadow-md);
            margin-bottom: 2.5rem;
            border: 1px solid var(--border-color);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        .main-header:hover {
            box-shadow: var(--shadow-lg);
        }
        
        .user-profile .dropdown-toggle::after { 
            display: none; 
        }
        .user-profile img { 
            width: 42px; 
            height: 42px; 
            border-radius: 50%; 
            object-fit: cover;
            border: 2px solid var(--border-color);
            transition: all 0.3s ease;
        }
        .user-profile:hover img {
            border-color: var(--color-blue);
            transform: scale(1.05);
        }
        
        /* GAYA STAT CARD MODERN */
        .stat-card {
            background: var(--bg-white);
            border-radius: 1.25rem;
            padding: 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
            height: 100%;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--color-blue), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .stat-card:hover { 
            transform: translateY(-8px); 
            box-shadow: var(--shadow-xl);
        }
        .stat-card:hover::before {
            opacity: 1;
        }
        
        /* GAYA IKON STAT MODERN dengan Gradien */
        .stat-icon-circle {
            width: 64px;
            height: 64px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.875rem;
            flex-shrink: 0;
            position: relative;
            transition: all 0.3s ease;
        }
        .stat-card:hover .stat-icon-circle {
            transform: scale(1.1) rotate(5deg);
        }
        
        /* Varian Warna Ikon dengan Gradien */
        .stat-icon-circle.blue { 
            background: var(--bg-blue-light); 
            color: var(--color-blue);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        .stat-icon-circle.green { 
            background: var(--bg-green-light); 
            color: var(--color-green);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        .stat-icon-circle.orange { 
            background: var(--bg-orange-light); 
            color: var(--color-orange);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }
        .stat-icon-circle.purple { 
            background: var(--bg-purple-light); 
            color: var(--color-purple);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }
        
        .stat-card h3 { 
            font-size: 2.5rem; 
            font-weight: 800; 
            margin: 0.5rem 0 0.25rem 0;
            background: linear-gradient(135deg, var(--text-dark) 0%, var(--text-muted) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .stat-card p { 
            font-size: 0.95rem; 
            color: var(--text-muted); 
            margin: 0;
            font-weight: 500;
            letter-spacing: 0.025em;
        }
        
        /* GAYA CONTENT CARD MODERN */
        .content-card { 
            background: var(--bg-white);
            border-radius: 1.25rem;
            padding: 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
            height: 100%;
            transition: all 0.3s ease;
        }
        .content-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }
        
        .content-card-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 1.5rem; 
            padding-bottom: 1.5rem; 
            border-bottom: 2px solid var(--border-color); 
        }
        .content-card-header h5 { 
            margin: 0; 
            font-weight: 700;
            font-size: 1.125rem;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .content-card-header h5 i {
            color: var(--color-blue);
            font-size: 1.25rem;
        }
        .content-card-header .btn-link { 
            text-decoration: none; 
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--color-blue);
            transition: all 0.2s ease;
        }
        .content-card-header .btn-link:hover {
            color: var(--color-purple);
            transform: translateX(4px);
        }
        
        /* STYLING TABEL MODERN */
        .table {
            margin-bottom: 0;
        }
        .table-hover tbody tr {
            transition: all 0.2s ease;
            border-radius: 0.75rem;
        }
        .table-hover tbody tr:hover {
            background-color: #f8fafc;
            transform: scale(1.01);
            box-shadow: var(--shadow-sm);
        }
        .table-borderless tbody tr {
            border-bottom: 1px solid var(--border-color);
        }
        .table-borderless tbody tr:last-child {
            border-bottom: none;
        }
        .badge {
            padding: 0.5rem 0.75rem;
            font-weight: 600;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            letter-spacing: 0.025em;
        }
        .badge.bg-light {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%) !important;
            color: var(--text-dark) !important;
            border: 1px solid var(--border-color);
        }
        
        /* STYLING BUTTON MODERN */
        .btn-outline-primary {
            border-radius: 0.75rem;
            font-weight: 600;
            padding: 0.5rem 1.25rem;
            transition: all 0.3s ease;
            border-width: 2px;
        }
        .btn-outline-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        /* STYLING HEADER WELCOME */
        .main-header h5 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.25rem;
        }
        .main-header small {
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 500;
        }
        
        /* ANIMASI ENTER */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .stat-card {
            animation: fadeInUp 0.6s ease-out;
        }
        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }
        
        .content-card {
            animation: fadeInUp 0.6s ease-out 0.5s both;
        }
        
        /* RESPONSIVE IMPROVEMENTS */
        @media (max-width: 768px) {
            #main-content {
                padding: 1rem;
            }
            .main-header {
                padding: 1rem 1.25rem;
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start !important;
            }
            .stat-card {
                padding: 1.5rem;
            }
            .stat-card h3 {
                font-size: 2rem;
            }
            .content-card {
                padding: 1.5rem;
            }
        }
        
        /* SCROLLBAR MODERN */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: var(--bg-light);
        }
        ::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-muted);
        }
    </style>
</head>
<body>

<div class="d-flex flex-row min-vh-100">
    
    <?php 
        // MEMANGGIL SIDEBAR BARU
        require_once 'sidebar_baru.php'; 
    ?>

    <!-- KONTEN UTAMA -->
    <div id="main-content">
        
        <!-- Header Utama (Konten) -->
        <header class="main-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center flex-grow-1">
                <!-- Tombol Toggler Sidebar (Hanya tampil di Mobile) -->
                <button class="btn btn-outline-secondary d-lg-none me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" style="border-radius: 0.75rem;">
                    <i class="bi bi-list"></i>
                </button>
                
                <!-- Judul Halaman / Selamat Datang -->
                <div>
                    <h5 class="mb-0">Selamat Datang, <span style="color: var(--color-blue);"><?php echo $nama_user; ?></span>! 👋</h5>
                    <small class="text-muted">Berikut adalah ringkasan aktivitas terbaru Anda.</small>
                </div>
            </div>
            
            <!-- Profil User (Dropdown di Header) -->
            <div class="user-profile">
                <div class="dropdown">
                    <a class="dropdown-toggle d-flex align-items-center text-decoration-none" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="gap: 0.75rem;">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($nama_user); ?>&background=3b82f6&color=fff&size=128&bold=true" alt="User">
                        <div class="lh-sm d-none d-md-block">
                            <span class="text-dark d-block" style="font-weight: 600; font-size: 0.95rem;"><?php echo $nama_user; ?></span>
                            <small class="text-muted" style="font-size: 0.8rem;"><?php echo ucwords(str_replace('_', ' ', $role_user)); ?></small>
                        </div>
                        <i class="bi bi-chevron-down d-none d-md-inline text-muted" style="font-size: 0.75rem;"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" aria-labelledby="userDropdown" style="border-radius: 1rem; padding: 0.5rem; margin-top: 0.5rem;">
                        <li><a class="dropdown-item" href="#" style="border-radius: 0.5rem; padding: 0.75rem 1rem;"><i class="bi bi-person-circle me-2"></i> Profil Saya</a></li>
                        <li><hr class="dropdown-divider" style="margin: 0.5rem 0;"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php" style="border-radius: 0.5rem; padding: 0.75rem 1rem;"><i class="bi bi-box-arrow-right me-2"></i> Keluar</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Konten Statistik (HTML TELAH DIUBAH) -->
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card">
                    <!-- PERUBAHAN HTML: .stat-icon -> .stat-icon-circle .blue -->
                    <div class="d-flex align-items-center">
                        <div class="stat-icon-circle blue me-3"><i class="bi bi-calendar2-check"></i></div>
                        <div>
                            <h3><?php echo $stat_res_aktif; ?></h3>
                            <p>Reservasi Aktif</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card">
                    <!-- PERUBAHAN HTML: .stat-icon -> .stat-icon-circle .green -->
                    <div class="d-flex align-items-center">
                        <div class="stat-icon-circle green me-3"><i class="bi bi-door-open"></i></div>
                        <div>
                            <h3><?php echo $stat_kamar; ?></h3>
                            <p>Kamar Tersedia</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card">
                    <!-- PERUBAHAN HTML: .stat-icon -> .stat-icon-circle .orange -->
                    <div class="d-flex align-items-center">
                        <!-- Mengganti bg-info dengan gaya oranye dari gambar -->
                        <div class="stat-icon-circle orange me-3"><i class="bi bi-people"></i></div>
                        <div>
                            <h3><?php echo $stat_tamu; ?></h3>
                            <p>Total Tamu Terdaftar</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card">
                    <!-- PERUBAHAN HTML: .stat-icon -> .stat-icon-circle .purple -->
                    <div class="d-flex align-items-center">
                        <!-- Mengganti bg-warning dengan gaya ungu dari gambar -->
                        <div class="stat-icon-circle purple me-3"><i class="bi bi-building"></i></div>
                        <div>
                            <h3><?php echo $stat_properti; ?></h3>
                            <p>Total Properti</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Konten Card Bawah (SAMA SEPERTI SEBELUMNYA) -->
        <div class="row">
            <div class="col-lg-7 mb-4">
                <div class="content-card">
                    <div class="content-card-header">
                        <h5><i class="bi bi-calendar-event me-2"></i> Reservasi Mendatang</h5>
                        <a href="reservasi_daftar.php" class="btn btn-link">Lihat Semua</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless">
                            <tbody>
                                <?php if ($result_reservasi_terbaru->num_rows > 0): ?>
                                    <?php while($row = $result_reservasi_terbaru->fetch_assoc()): ?>
                                        <tr style="padding: 1rem 0;">
                                            <td style="padding: 1rem;">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3" style="width: 40px; height: 40px; background: var(--bg-blue-light); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; color: var(--color-blue);">
                                                        <i class="bi bi-person-fill"></i>
                                                    </div>
                                                    <div>
                                                        <strong style="color: var(--text-dark); font-size: 0.95rem; display: block; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($row['nama_lengkap']); ?></strong>
                                                        <small class="text-muted" style="font-size: 0.85rem;">
                                                            <i class="bi bi-door-open me-1"></i><?php echo htmlspecialchars($row['nama_kamar']); ?> 
                                                            <span style="color: var(--border-color);">•</span> 
                                                            <?php echo htmlspecialchars($row['nama_properti']); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end" style="padding: 1rem; vertical-align: middle;">
                                                <span class="badge bg-light text-dark">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    <?php echo date('d M Y', strtotime($row['tgl_checkin'])); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="2" class="text-center text-muted" style="padding: 3rem 1rem;">
                                            <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;">
                                                <i class="bi bi-inbox"></i>
                                            </div>
                                            <p style="margin: 0; font-weight: 500;">Tidak ada reservasi mendatang.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 mb-4">
                <div class="content-card">
                    <div class="content-card-header">
                        <h5><i class="bi bi-clipboard2-check me-2"></i> Aktivitas Housekeeping</h5>
                        <a href="#" class="btn btn-link">Lihat Laporan</a>
                    </div>
                    <div class="text-center p-5" style="min-height: 200px; display: flex; flex-direction: column; align-items: center; justify-content: center;"> 
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 1rem; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; color: var(--color-orange); font-size: 2rem;">
                            <i class="bi bi-clipboard-check"></i>
                        </div>
                        <p class="text-muted mb-3" style="font-weight: 500;">Tidak ada laporan maintenance baru.</p>
                        <a href="#" class="btn btn-outline-primary">
                            <i class="bi bi-plus-circle me-2"></i>Buat Laporan Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- Penutup untuk #main-content -->
</div> <!-- Penutup untuk .wrapper (d-flex) -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
<script>
    // Script JavaScript kustom bisa ditambahkan di sini jika perlu.
    // Untuk saat ini, Bootstrap sudah menangani buka/tutup sidebar.
</script>

</body>
</html>

