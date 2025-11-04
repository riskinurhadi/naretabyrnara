<?php
// sidebar_baru.php
// Menentukan halaman aktif untuk menandai link
$current_page = basename($_SERVER['PHP_SELF']);

// Logika untuk menu Pengaturan (agar tetap aktif di halaman anak)
$is_pengaturan_page = (strpos($current_page, 'manajemen_') === 0);

?>
<!-- 
  ==========================================================
  == CSS KHUSUS UNTUK SIDEBAR DAN LAYOUT UTAMA ==
  == CSS ini sekarang ada di dalam file sidebar ==
  ==========================================================
-->
<style>
    :root {
        /* Ukuran */
        --sidebar-width: 280px;
        
        /* Warna (Palette Baru Sesuai Gambar) */
        --sidebar-bg: linear-gradient(180deg, #232a4a, #1a1f33);
        --sidebar-text: rgba(255, 255, 255, 0.7);
        --sidebar-text-active: #232a4a;
        --sidebar-active-pill: #ffffff;
        --sidebar-hover-bg: rgba(255, 255, 255, 0.05);
    }
    
    /* * 1. STYLING SIDEBAR BARU (Desktop & Mobile)
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
        /* PERUBAHAN: Mengganti var(--bg-white) yang tidak ada 
           dengan var(--sidebar-active-pill) yang sudah ada (#ffffff) */
        color: var(--sidebar-active-pill); 
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
        color: var(--bg-white, #fff);
    }
    .sidebar-nav .nav-link:hover i {
        color: var(--bg-white, #fff);
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

    /* * 2. STYLING KONTEN UTAMA (LAYOUT)
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
</style>

<!-- 
  ==========================================================
  == HTML SIDEBAR
  ==========================================================
-->
<div class="offcanvas offcanvas-start offcanvas-lg sidebar-nav-wrapper" data-bs-scroll="true" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
    
    <!-- Header Sidebar (Logo/Nama) -->
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="sidebarMenuLabel">Guesthouse Adiputra</h5>
        <button type="button" class="btn-close btn-close-white d-lg-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <!-- Body Sidebar (Menu) -->
    <div class="offcanvas-body d-flex flex-column p-0">
        <ul class="nav flex-column sidebar-nav">
            
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'dashboard_baru.php' || $current_page == 'dashboard.php' || $current_page == '') ? 'active' : ''; ?>" href="dashboard_baru.php">
                    <i class="bi bi-grid"></i> Dashboard
                </a>
            </li>
            
            <?php // Tampilkan menu berdasarkan ROLE ?>
            
            <?php if (in_array($role_user, ['admin', 'front_office'])): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'reservasi_kalender.php') ? 'active' : ''; ?>" href="reservasi_kalender.php"> 
                        <i class="bi bi-calendar-check"></i> Reservasi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'tamu_data.php') ? 'active' : ''; ?>" href="tamu_data.php"> 
                        <i class="bi bi-people"></i> Data Tamu
                    </a>
                </li>
            <?php endif; ?>

            <?php if (in_array($role_user, ['admin', 'housekeeping'])): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'kamar_status.php') ? 'active' : ''; ?>" href="kamar_status.php"> 
                        <i class="bi bi-house-check"></i> Status Kamar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'maintenance_laporan.php') ? 'active' : ''; ?>" href="maintenance_laporan.php"> 
                        <i class="bi bi-wrench-adjustable"></i> Maintenance
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($role_user == 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'laporan_keuangan.php') ? 'active' : ''; ?>" href="laporan_keuangan.php"> 
                        <i class="bi bi-journal-text"></i> Laporan
                    </a>
                </li>
                <li class="nav-item">
                    <!-- PERUBAHAN: Link Pengaturan sekarang merujuk ke file baru -->
                    <!-- dan menggunakan logika $is_pengaturan_page -->
                    <a class="nav-link <?php echo ($is_pengaturan_page) ? 'active' : ''; ?>" href="manajemen_properti.php"> 
                        <i class="bi bi-gear"></i> Pengaturan
                    </a>
                </li>
            <?php endif; ?>
        </ul>

        <!-- Tombol Logout di Bawah (mt-auto) -->
        <ul class="nav flex-column sidebar-nav mt-auto">
            <li class_nav-item">
                <a class="nav-link nav-link-logout" href="logout.php">
                    <i class="bi bi-box-arrow-left"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</div>


