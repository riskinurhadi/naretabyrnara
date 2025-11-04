<?php
// sidebar_baru.php
// Pastikan variabel $role_user sudah ada (didefinisikan di file yang memanggil)
if (!isset($role_user)) {
    $role_user = 'guest'; // Pengaman jika variabel tidak ada
}

// Secara otomatis mendeteksi halaman aktif untuk menyorot link
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- 
  SIDEBAR BARU (MODERN)
  Struktur HTML untuk sidebar. 
  Styling (CSS) diatur terpusat di file dashboard_baru.php.
-->
<div class="offcanvas offcanvas-start offcanvas-lg sidebar-nav-wrapper" data-bs-scroll="true" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
    
    <!-- Header Sidebar (Logo/Brand) -->
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="sidebarMenuLabel">
            <i class="bi bi-buildings-fill me-2"></i> Guesthouse Adiputra
        </h5>
        <!-- Tombol close ini hanya tampil di mobile -->
        <button type="button" class="btn-close d-lg-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <!-- Body Sidebar (Menu Navigasi) -->
    <div class="offcanvas-body p-0">
        <!-- 
          nav-pills d-flex flex-column: Mengubah <ul> standar menjadi menu vertikal.
          flex-column h-100: Memastikan navigasi mengisi seluruh ketinggian 
        -->
        <nav class="nav nav-pills d-flex flex-column h-100 sidebar-nav">
            
            <!-- Menu Utama -->
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">
                    <i class="bi bi-grid"></i> Dashboard
                </a>
            </li>
            
            <!-- Menu Role-Based -->
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
                    <a class="nav-link <?php echo ($current_page == 'pengaturan_properti.php') ? 'active' : ''; ?>" href="pengaturan_properti.php">
                        <i class="bi bi-gear"></i> Pengaturan
                    </a>
                </li>
            <?php endif; ?>

            <!-- Menu Logout (diletakkan di bawah) -->
            <li class="nav-item mt-auto"> <!-- 'mt-auto' mendorong item ini ke bagian bawah -->
                 <a class="nav-link nav-link-logout" href="logout.php">
                    <i class="bi bi-box-arrow-left"></i> Logout
                </a>
            </li>
        </nav>
    </div>
</div>
