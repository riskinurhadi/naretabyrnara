<?php
// dashboard.php
require_once 'auth_check.php';
require_once 'koneksi.php';

$nama_user = htmlspecialchars($_SESSION['nama_lengkap']);
$role_user = htmlspecialchars($_SESSION['role']); // Variabel ini DIBUTUHKAN oleh sidebar.php

// MENGAMBIL DATA STATISTIK (Tidak berubah)
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
    
    <style>
        /* CSS INTERNAL BARU UNTUK LAYOUT RESPONSIF */
        :root {
            --sidebar-width: 260px;
            --bg-light: #f4f7f6;
            --bg-white: #ffffff;
            --text-dark: #343a40;
            --text-muted: #6c757d;
            --active-bg: #e0f2f1;
            --active-color: #00796b;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .wrapper {
            /* Flex layout tidak diperlukan lagi di sini */
            min-height: 100vh;
        }

        /* * STYLING SIDEBAR (DI ATAS 992px / 'lg') 
         * Menggunakan @media query, kita "memaksa" offcanvas 
         * untuk bertindak sebagai sidebar fixed di desktop.
        */
        @media (min-width: 992px) {
            .offcanvas-lg {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                width: var(--sidebar-width);
                min-height: 100vh;
                /* Membatalkan perilaku offcanvas */
                transform: none !important;
                visibility: visible !important;
                /* Beri shadow seperti desain awal */
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            }

            #main-content {
                /* Beri margin kiri seukuran sidebar di desktop */
                margin-left: var(--sidebar-width);
                width: calc(100% - var(--sidebar-width));
            }
        }

        /* Di bawah 992px, #main-content akan otomatis full-width */
        #main-content {
            padding: 1.5rem;
            width: 100%;
            margin-left: 0;
        }

        /* Styling menu di dalam sidebar */
        .sidebar-nav .nav-item { margin-bottom: 0.25rem; }
        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            color: var(--text-muted);
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            transition: all 0.2s ease-in-out;
        }
        .sidebar-nav .nav-link i { margin-right: 0.75rem; font-size: 1.2rem; width: 20px; text-align: center; }
        .sidebar-nav .nav-link:hover { background-color: var(--bg-light); color: var(--text-dark); }
        .sidebar-nav .nav-link.active { background-color: var(--active-bg); color: var(--active-color); }
        .sidebar-nav .nav-link.active i { color: var(--active-color); }

        /* Styling Header & Konten (Tidak banyak berubah) */
        .main-header {
            background-color: var(--bg-white);
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }
        .user-profile .dropdown-toggle::after { display: none; }
        .user-profile img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
        .stat-card {
            background-color: var(--bg-white);
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease-in-out;
            border: none;
            height: 100%;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 6px 15px rgba(0, 0, 0, 0.07); }
        .stat-icon { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #fff; flex-shrink: 0; }
        .stat-card h3 { font-size: 2rem; font-weight: 700; margin: 0; }
        .stat-card p { font-size: 0.9rem; color: var(--text-muted); margin: 0; }
        .content-card { background-color: var(--bg-white); border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); border: none; height: 100%; }
        .content-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #eee; }
        .content-card-header h5 { margin: 0; font-weight: 600; }
        .content-card-header .btn-link { text-decoration: none; font-size: 0.9rem; }
    </style>
</head>
<body>

<div class="wrapper">
    
    <?php 
        // Memanggil sidebar terpusat
        // Variabel $role_user otomatis ter-scope dari atas
        require_once 'sidebar.php'; 
    ?>

    <div id="main-content">
        
        <header class="main-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-secondary d-lg-none me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                    <i class="bi bi-list"></i>
                </button>
                
                <div>
                    <h5 class="mb-0">Selamat Datang, <?php echo $nama_user; ?>!</h5>
                    <small class="text-muted">Berikut adalah ringkasan aktivitas terbaru.</small>
                </div>
            </div>
            
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
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary me-3"><i class="bi bi-calendar2-check"></i></div>
                        <div>
                            <h3><?php echo $stat_res_aktif; ?></h3>
                            <p>Reservasi Aktif</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success me-3"><i class="bi bi-door-open"></i></div>
                        <div>
                            <h3><?php echo $stat_kamar; ?></h3>
                            <p>Kamar Tersedia</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info me-3"><i class="bi bi-people"></i></div>
                        <div>
                            <h3><?php echo $stat_tamu; ?></h3>
                            <p>Total Tamu Terdaftar</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning me-3"><i class="bi bi-building"></i></div>
                        <div>
                            <h3><?php echo $stat_properti; ?></h3>
                            <p>Total Properti</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
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
                        <a href="#" class="btn btn-link">Lihat Laporan</a>
                    </div>
                    <div classs="text-center text-muted p-4">
                        <p>Tidak ada laporan maintenance baru.</p>
                        <a href="#" class="btn btn-sm btn-outline-primary">Buat Laporan Baru</a>
                    </div>
                </div>
            </div>
        </div>

    </div> </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JS Internal untuk link sidebar aktif
        document.addEventListener("DOMContentLoaded", function() {
            const currentLocation = window.location.pathname.split('/').pop();
            const navLinks = document.querySelectorAll('.sidebar-nav .nav-link');

            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentLocation) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });

            if (currentLocation === 'dashboard.php' || currentLocation === '') {
                 document.querySelector('.sidebar-nav .nav-link[href="dashboard.php"]').classList.add('active');
            }
        });
    </script>
</body>
</html>