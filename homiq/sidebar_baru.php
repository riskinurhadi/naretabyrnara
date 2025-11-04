<?php
// sidebar_baru.php
// Pastikan variabel $role_user sudah ada (didefinisikan di file yang memanggil)
if (!isset($role_user)) {
    $role_user = 'guest'; // Pengaman jika variabel tidak ada
}

// Ambil halaman saat ini
$current_page = basename($_SERVER['PHP_SELF']);

// BARU: Cek apakah kita sedang di halaman 'Manajemen'
// Ini akan bernilai 'true' jika halaman adalah 'manajemen_properti.php' atau 'manajemen_kamar.php'
$is_manajemen_page = (strpos($current_page, 'manajemen_') === 0);
?>

<!-- 
  ==========================================================
  == CSS UNTUK SIDEBAR DAN LAYOUT UTAMA ==
  ==========================================================
-->
<style>
    :root {
        /* Warna Sidebar */
        --sidebar-width: 260px;
        --sidebar-dark-bg: #232a4a;
        --sidebar-link: #adb5bd;
        --sidebar-link-hover: #ffffff;
        --sidebar-active-pill: #ffffff;

        /* Warna Konten (referensi) */
        --bg-light: #f9fafb;
    }
    
    body {
        overflow-x: hidden; /* Mencegah horizontal scroll */
    }

    /* * 1. WRAPPER SIDEBAR (BARU)
     */
    .sidebar-nav-wrapper {
        width: var(--sidebar-width);
        min-height: 100vh;
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        z-index: 1030;
        
        background: var(--sidebar-dark-bg);
        background: linear-gradient(195deg, #2a335a 0%, #232a4a 100%);
        
        transition: transform 0.3s ease-in-out;
    }

    /* * 2. MAIN CONTENT WRAPPER
     */
    #main-content {
        background-color: var(--bg-light);
        min-height: 100vh;
        padding: 1.5rem;
        transition: margin-left 0.3s ease-in-out;
    }

    /* * 3. LOGIKA RESPONSIVE
     */
     
    /* Tampilan Desktop (Sidebar terlihat) */
    @media (min-width: 992px) {
        #main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
        }
    }

    /* Tampilan Mobile (Sidebar tersembunyi by default) */
    @media (max-width: 991.98px) {
        .sidebar-nav-wrapper {
            transform: translateX(calc(-1 * var(--sidebar-width)));
        }
        .sidebar-nav-wrapper.offcanvas-lg.show {
            transform: translateX(0);
        }
        #main-content {
            margin-left: 0;
            width: 100%;
        }
    }
    
    /* * 4. STYLING MENU DI DALAM SIDEBAR
     */
    .sidebar-nav-wrapper .offcanvas-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    /* Target h5 di dalam header sidebar (FIX Judul tidak tampil) */
    .sidebar-nav-wrapper .offcanvas-header .offcanvas-title {
        color: var(--sidebar-active-pill, #ffffff); /* Fallback ke #ffffff */
        font-weight: 600;
    }

    .sidebar-nav {
        padding: 1rem; /* Padding untuk keseluruhan grup menu */
    }
    .sidebar-nav-wrapper .nav-pills .nav-item {
        margin-bottom: 0.25rem;
    }
    .sidebar-nav-wrapper .nav-pills .nav-link {
        color: var(--sidebar-link);
        font-weight: 500;
        padding: 0.8rem 1rem;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
        transition: all 0.2s ease-in-out;
    }
    .sidebar-nav-wrapper .nav-pills .nav-link i {
        margin-right: 0.75rem;
        font-size: 1.1rem;
        width: 24px;
        text-align: center;
        line-height: 1;
        vertical-align: -2px;
    }
    .sidebar-nav-wrapper .nav-pills .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.05);
        color: var(--sidebar-link-hover);
    }
    
    /* Link Aktif (Pill Putih) */
    .sidebar-nav-wrapper .nav-pills .nav-link.active {
        background-color: var(--sidebar-active-pill, #ffffff);
        color: var(--sidebar-dark-bg, #232a4a);
        font-weight: 600;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    .sidebar-nav-wrapper .nav-pills .nav-link.active i {
        color: var(--sidebar-dark-bg, #232a4a);
    }
    
    /* Logout Link */
    .sidebar-nav-wrapper .logout-link .nav-link {
        color: var(--sidebar-link);
    }
    .sidebar-nav-wrapper .logout-link .nav-link:hover {
        background-color: rgba(217, 54, 62, 0.1);
        color: #f5c6cb;
    }
    
    /* * CSS BARU UNTUK SUBMENU DROPDOWN 
     */
    .sidebar-nav-wrapper .nav-pills .collapse {
        /* Latar belakang submenu box */
        background-color: rgba(0, 0, 0, 0.15);
        border-radius: 0.375rem;
        margin-top: 0.25rem;
    }
    .sidebar-nav-wrapper .nav-pills .collapse .nav-link {
        /* Link di dalam submenu */
        padding-top: 0.6rem;
        padding-bottom: 0.6rem;
        padding-left: 2.75rem; /* Indentasi (1rem padding + 24px ikon + 0.75rem margin) */
        font-size: 0.9rem;
        color: var(--sidebar-link);
    }
    .sidebar-nav-wrapper .nav-pills .collapse .nav-link:hover {
         /* Submenu hover */
        background-color: transparent;
        color: var(--sidebar-link-hover);
    }
    .sidebar-nav-wrapper .nav-pills .collapse .nav-link.active-submenu {
        /* Link submenu yang aktif (hanya warna teks) */
        color: var(--sidebar-active-pill, #ffffff);
        font-weight: 500;
        background-color: transparent;
    }
    .sidebar-nav-wrapper .nav-pills .nav-link .bi-chevron-down {
        /* Ikon panah */
        transition: transform 0.2s ease-in-out;
        font-size: 0.75rem;
    }
    .sidebar-nav-wrapper .nav-pills .nav-link[aria-expanded="true"] .bi-chevron-down {
        /* Rotasi panah saat dropdown terbuka */
        transform: rotate(180deg);
    }

</style>

<!-- 
  ==========================================================
  == HTML SIDEBAR ==
  ==========================================================
-->
<div class="offcanvas offcanvas-start offcanvas-lg sidebar-nav-wrapper" 
     data-bs-scroll="true" 
     tabindex="-1" 
     id="sidebarMenu" 
     aria-labelledby="sidebarMenuLabel">
    
    <!-- Header Sidebar (Logo/Nama) -->
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="sidebarMenuLabel">Guesthouse Adiputra</h5>
        <button type="button" class="btn-close btn-close-white d-lg-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <!-- Body Sidebar (Menu) -->
    <div class="offcanvas-body p-0">
        <!-- mt-auto membuat grup ini menempel ke bawah -->
        <ul class="nav nav-pills flex-column sidebar-nav">
            
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'dashboard_baru.php') ? 'active' : ''; ?>" href="dashboard_baru.php">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>
            </li>
            
            <?php // Tampilkan menu berdasarkan ROLE ?>
            
            <?php if (in_array($role_user, ['admin', 'front_office'])): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'reservasi_kalender.php') ? 'active' : ''; ?>" href="#"> <!-- Nanti ganti href -->
                        <i class="bi bi-calendar-check-fill"></i> Reservasi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'tamu_data.php') ? 'active' : ''; ?>" href="#"> <!-- Nanti ganti href -->
                        <i class="bi bi-people-fill"></i> Data Tamu
                    </a>
                </li>
            <?php endif; ?>

            <?php if (in_array($role_user, ['admin', 'housekeeping'])): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'kamar_status.php') ? 'active' : ''; ?>" href="#"> <!-- Nanti ganti href -->
                        <i class="bi bi-house-door-fill"></i> Status Kamar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'maintenance_laporan.php') ? 'active' : ''; ?>" href="#"> <!-- Nanti ganti href -->
                        <i class="bi bi-wrench"></i> Maintenance
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($role_user == 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'laporan_keuangan.php') ? 'active' : ''; ?>" href="#"> <!-- Nanti ganti href -->
                        <i class="bi bi-journal-text"></i> Laporan
                    </a>
                </li>
                
                <!-- 
                  ==========================================================
                  == PERUBAHAN: MENU DROPDOWN MANAJEMEN ==
                  ==========================================================
                -->
                <li class="nav-item">
                    <!-- 
                        PERUBAHAN: 
                        1. Link ini sekarang menjadi toggle untuk collapse.
                        2. href="#submenu-pengaturan" menargetkan ID <div> di bawah.
                        3. class 'active' ditambahkan jika $is_manajemen_page true.
                        4. aria-expanded diatur berdasarkan $is_manajemen_page.
                    -->
                    <a class="nav-link <?php echo ($is_manajemen_page) ? 'active' : ''; ?>" 
                       href="#submenu-pengaturan" 
                       data-bs-toggle="collapse" 
                       role="button" 
                       aria-expanded="<?php echo ($is_manajemen_page) ? 'true' : 'false'; ?>" 
                       aria-controls="submenu-pengaturan">
                        <i class="bi bi-gear-wide-connected"></i> 
                        Manajemen 
                        <i class="bi bi-chevron-down ms-auto"></i> <!-- Ikon Panah -->
                    </a>
                    
                    <!-- 
                        PERUBAHAN: 
                        1. Ini adalah wrapper untuk submenu.
                        2. class 'show' ditambahkan jika $is_manajemen_page true agar terbuka.
                    -->
                    <div class="collapse <?php echo ($is_manajemen_page) ? 'show' : ''; ?>" id="submenu-pengaturan">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <!-- 
                                    PERUBAHAN: 
                                    1. Link submenu menggunakan class 'active-submenu'.
                                    2. href diatur ke halaman spesifik.
                                -->
                                <a class="nav-link <?php echo ($current_page == 'manajemen_properti.php') ? 'active-submenu' : ''; ?>" href="manajemen_properti.php">
                                    Properti
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($current_page == 'manajemen_kamar.php') ? 'active-submenu' : ''; ?>" href="manajemen_kamar.php">
                                    Kamar
                                 </a>
                            </li>
                            <!-- Nanti bisa ditambah "Manajemen User" di sini -->
                        </ul>
                    </div>
                </li>
                <!-- ========================================================== -->

            <?php endif; ?>
        </ul>
        
        <!-- Menu Logout (Selalu di Bawah) -->
        <ul class="nav nav-pills flex-column sidebar-nav mt-auto logout-link">
             <li class="nav-item">
                <a class.nav-link" href="logout.php">
                    <i class="bi bi-box-arrow-left"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</div>

