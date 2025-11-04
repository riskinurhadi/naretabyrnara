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
      == CSS INTERNAL BARU (GAYA DARI GAMBAR) ==
      ==========================================================
    -->
    <style>
        :root {
            /* Ukuran */
            --sidebar-width: 280px;
            
            /* Warna (Palette Baru Sesuai Gambar) */
            /* PERUBAHAN: Menggunakan gradien untuk background sidebar */
            --sidebar-bg: linear-gradient(180deg, #232a4a, #1a1f33);
            --sidebar-text: rgba(255, 255, 255, 0.7);
            --sidebar-text-active: #232a4a;
            --sidebar-active-pill: #ffffff;
            --sidebar-hover-bg: rgba(255, 255, 255, 0.05);
            
            /* Warna Konten */
            --bg-light: #f9fafb;      /* Latar belakang body lebih cerah */
            --bg-white: #ffffff;      /* Latar belakang card */
            --text-dark: #212529;     /* Teks utama */
            --text-muted: #6c757d;    /* Teks abu-abu */
            --border-color: #f0f0f0;  /* Garis batas sangat tipis */
            
            /* Warna Ikon Statistik */
            --color-blue: #0d6efd;      --bg-blue-light: #e7f0ff;
            --color-green: #198754;     --bg-green-light: #e8f9ee;
            --color-orange: #fd7e14;    --bg-orange-light: #fff3e8;
            --color-purple: #6f42c1;    --bg-purple-light: #f3effc;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            overflow-x: hidden; /* Mencegah horizontal scroll */
        }

        /* * 1. STYLING SIDEBAR BARU (Desktop & Mobile)
         */
        
        /* * PERUBAHAN: Selektor diperkuat dari .sidebar-nav-wrapper
         * menjadi .sidebar-nav-wrapper.offcanvas
         * Ini untuk mengalahkan selektor .offcanvas bawaan Bootstrap
         */
        .sidebar-nav-wrapper.offcanvas {
            width: var(--sidebar-width);
            background: var(--sidebar-bg); /* <-- Menggunakan 'background' untuk gradien */
            border-right: none; /* Hapus border, ganti shadow */
            transition: transform 0.3s ease-in-out;
        }
        
        .sidebar-nav-wrapper .offcanvas-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-weight: 600;
            color: var(--bg-white); /* Teks header jadi putih */
        }
        
        .sidebar-nav {
            padding: 1rem; /* Padding untuk keseluruhan grup menu */
        }

        .sidebar-nav .nav-item {
            margin-bottom: 0.25rem; /* Jarak antar item menu */
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--sidebar-text); /* Teks sidebar jadi terang */
            padding: 0.8rem 1.25rem; /* Padding internal link */
            border-radius: 0.75rem; /* Sudut lebih melengkung */
            transition: all 0.2s ease-in-out;
        }

        .sidebar-nav .nav-link i {
            font-size: 1.2rem;
            margin-right: 1rem;
            width: 24px; /* Lebar ikon tetap */
            text-align: center;
            color: var(--sidebar-text); /* Ikon sidebar jadi terang */
            transition: all 0.2s ease-in-out;
        }
        
        /* Efek Hover */
        .sidebar-nav .nav-link:hover {
            background-color: var(--sidebar-hover-bg);
            color: var(--bg-white);
        }
        .sidebar-nav .nav-link:hover i {
            color: var(--bg-white);
        }
        
        /* Status Aktif (Gaya "Pill" Putih) */
        .sidebar-nav .nav-link.active {
            background-color: var(--sidebar-active-pill);
            color: var(--sidebar-text-active);
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .sidebar-nav .nav-link.active i {
            color: var(--sidebar-text-active);
        }

        /* Tombol Logout Khusus */
        .sidebar-nav .nav-link-logout {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--sidebar-text);
            opacity: 0.6;
        }
        .sidebar-nav .nav-link-logout:hover {
            opacity: 1;
            background-color: rgba(220, 53, 69, 0.1); /* Hover merah */
            color: #dc3545;
        }
        .sidebar-nav .nav-link-logout:hover i {
            color: #dc3545;
        }


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
            .sidebar-nav-wrapper.offcanvas {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                transform: none !important;
                visibility: visible !important;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); /* Shadow pemisah */
            }
            
            #main-content {
                margin-left: var(--sidebar-width);
                width: calc(100% - var(--sidebar-width));
            }
        }

        /* * 4. STYLING KONTEN (CARD, HEADER, DLL)
         */
        .main-header {
            background-color: var(--bg-white);
            padding: 1rem 1.5rem;
            border-radius: 1rem; /* Lebih bulat */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
            margin-bottom: 2rem; /* Jarak lebih besar */
            border: 1px solid var(--border-color);
        }
        .user-profile .dropdown-toggle::after { display: none; }
        .user-profile img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
        
        /* GAYA STAT CARD BARU */
        .stat-card {
            background-color: var(--bg-white);
            border-radius: 1rem; /* Sangat bulat */
            padding: 1.5rem;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
            height: 100%;
            transition: all 0.2s ease-in-out;
        }
        .stat-card:hover { 
            transform: translateY(-4px); 
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.07); 
        }
        
        /* GAYA IKON STAT BARU (Lingkaran Berwarna) */
        .stat-icon-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem; /* Ikon lebih besar */
            flex-shrink: 0;
        }
        /* Varian Warna Ikon */
        .stat-icon-circle.blue { background-color: var(--bg-blue-light); color: var(--color-blue); }
        .stat-icon-circle.green { background-color: var(--bg-green-light); color: var(--color-green); }
        .stat-icon-circle.orange { background-color: var(--bg-orange-light); color: var(--color-orange); }
        .stat-icon-circle.purple { background-color: var(--bg-purple-light); color: var(--color-purple); }
        
        .stat-card h3 { font-size: 2rem; font-weight: 700; margin: 0; }
        .stat-card p { font-size: 0.9rem; color: var(--text-muted); margin: 0; }
        
        /* GAYA CONTENT CARD BAWAH */
        .content-card { 
            background-color: var(--bg-white);
            border-radius: 1rem; /* Sangat bulat */
            padding: 1.5rem;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
            height: 100%; 
        }
        .content-card-header { 
            display: flex; justify-content: space-between; align-items: center; 
            margin-bottom: 1.25rem; padding-bottom: 1.25rem; 
            border-bottom: 1px solid var(--border-color); 
        }
        .content-card-header h5 { margin: 0; font-weight: 600; }
        .content-card-header .btn-link { text-decoration: none; font-size: 0.9rem; }
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
        <header class="main-header d-flex justify-content-between align-items: center">
            <div class="d-flex align-items-center">
                <!-- Tombol Toggler Sidebar (Hanya tampil di Mobile) -->
                <button class="btn btn-outline-secondary d-lg-none me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                    <i class="bi bi-list"></i>
                </button>
                
                <!-- Judul Halaman / Selamat Datang -->
                <div>
                    <h5 class="mb-0">Selamat Datang, <?php echo $nama_user; ?>!</h5>
                    <small class="text-muted">Berikut adalah ringkasan aktivitas terbaru.</small>
                </div>
            </div>
            
            <!-- Profil User (Dropdown di Header) -->
            <div class="user-profile">
                <div class="dropdown">
                    <a class="dropdown-toggle d-flex align-items-center text-decoration-none" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($nama_user); ?>&background=0D6EFD&color=fff" alt="User" class="me-2">
                        <div class="lh-sm">
                            <span class="d-none d-md-inline text-dark"><strong><?php echo $nama_user; ?></strong></span><br>
                            <small class="d-none d-md-inline text-muted"><?php echo ucwords(str_replace('_', ' ', $role_user)); ?></small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="#">Profil Saya</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-muted" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout (dari Sidebar)</a></li>
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
                    <div class="d-flex align-itemsT-center">
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
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($row['nama_lengkap']); ?></strong><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($row['nama_kamar']); ?> (<?php echo htmlspecialchars($row['nama_properti']); ?>)</small>
                                            </td>
                                            <td class="text-end">
                                                <span class="badge bg-light text-dark">Check-in: <?php echo date('d M Y', strtotime($row['tgl_checkin'])); ?></span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="2" class="text-center text-muted"><i class="bi bi-moon-stars me-2"></i> Tidak ada reservasi mendatang.</td></tr>
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
                        <a href="#" class="btn btn-link">Liat Laporan</a>
                    </div>
                    <!-- Perbaikan kecil: 'classs' menjadi 'class' -->
                    <div class="text-center text-muted p-4"> 
                        <p>Tidak ada laporan maintenance baru.</p>
                        <a href="#" class="btn btn-sm btn-outline-primary">Buat Laporan Baru</a>
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

