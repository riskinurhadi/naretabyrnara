<?php
// sidebar.php - Modern & Professional Design
// Pastikan variabel $role_user sudah ada (didefinisikan di file yang memanggil)
if (!isset($role_user)) {
    $role_user = 'guest'; // Pengaman jika variabel tidak ada
}

// Menentukan halaman aktif untuk menandai link
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    /* MODERN SIDEBAR STYLING */
    :root {
        --sidebar-width: 280px;
        --sidebar-bg: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
        --sidebar-text: rgba(255, 255, 255, 0.75);
        --sidebar-text-active: #1e293b;
        --sidebar-active-bg: #ffffff;
        --sidebar-hover-bg: rgba(255, 255, 255, 0.08);
        --sidebar-border: rgba(255, 255, 255, 0.1);
        --sidebar-brand-color: #3b82f6;
    }

    .sidebar-modern.offcanvas {
        width: var(--sidebar-width);
        background: var(--sidebar-bg);
        border-right: none;
        box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .sidebar-modern .offcanvas-header {
        padding: 1.75rem 1.5rem;
        border-bottom: 1px solid var(--sidebar-border);
        background: rgba(255, 255, 255, 0.03);
    }

    .sidebar-modern .offcanvas-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        letter-spacing: -0.025em;
    }

    .sidebar-modern .logo-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--sidebar-brand-color) 0%, #2563eb 100%);
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .sidebar-modern .btn-close {
        filter: invert(1);
        opacity: 0.7;
        transition: opacity 0.2s ease;
    }

    .sidebar-modern .btn-close:hover {
        opacity: 1;
    }

    .sidebar-modern .offcanvas-body {
        padding: 1.5rem 1rem;
        display: flex;
        flex-direction: column;
    }

    .sidebar-modern .sidebar-nav {
        list-style: none;
        padding: 0;
        margin: 0;
        flex-grow: 1;
    }

    .sidebar-modern .nav-item {
        margin-bottom: 0.5rem;
    }

    .sidebar-modern .nav-link {
        display: flex;
        align-items: center;
        padding: 0.875rem 1.25rem;
        color: var(--sidebar-text);
        text-decoration: none;
        border-radius: 0.875rem;
        font-size: 0.95rem;
        font-weight: 500;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .sidebar-modern .nav-link::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: var(--sidebar-brand-color);
        transform: scaleY(0);
        transition: transform 0.2s ease;
        border-radius: 0 4px 4px 0;
    }

    .sidebar-modern .nav-link i {
        font-size: 1.25rem;
        width: 24px;
        margin-right: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .sidebar-modern .nav-link:hover {
        background: var(--sidebar-hover-bg);
        color: #ffffff;
        transform: translateX(4px);
    }

    .sidebar-modern .nav-link:hover::before {
        transform: scaleY(1);
    }

    .sidebar-modern .nav-link.active {
        background: var(--sidebar-active-bg);
        color: var(--sidebar-text-active);
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .sidebar-modern .nav-link.active::before {
        transform: scaleY(1);
        background: var(--sidebar-brand-color);
    }

    .sidebar-modern .nav-link.active i {
        color: var(--sidebar-brand-color);
    }

    /* Section Divider */
    .sidebar-divider {
        height: 1px;
        background: var(--sidebar-border);
        margin: 1rem 1.25rem;
        opacity: 0.5;
    }

    /* Logout Section */
    .sidebar-logout {
        margin-top: auto;
        padding-top: 1rem;
        border-top: 1px solid var(--sidebar-border);
    }

    .sidebar-modern .nav-link-logout {
        color: rgba(255, 255, 255, 0.6);
        opacity: 0.8;
    }

    .sidebar-modern .nav-link-logout:hover {
        background: rgba(220, 53, 69, 0.15);
        color: #ef4444;
        opacity: 1;
    }

    .sidebar-modern .nav-link-logout:hover i {
        color: #ef4444;
    }

    /* Responsive */
    @media (min-width: 992px) {
        .sidebar-modern.offcanvas {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            transform: none !important;
            visibility: visible !important;
        }
    }

    /* Smooth Scrollbar */
    .sidebar-modern .offcanvas-body::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar-modern .offcanvas-body::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar-modern .offcanvas-body::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 3px;
    }

    .sidebar-modern .offcanvas-body::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.3);
    }
</style>

<div class="offcanvas offcanvas-start offcanvas-lg sidebar-modern" data-bs-scroll="true" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
    
    <!-- Header Sidebar dengan Logo -->
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="sidebarMenuLabel">
            <span class="logo-icon">A</span>
            <span>Guesthouse Adiputra</span>
        </h5>
        <button type="button" class="btn-close btn-close-white d-lg-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <!-- Body Sidebar -->
    <div class="offcanvas-body">
        <ul class="nav flex-column sidebar-nav">
            
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link <?php echo (in_array($current_page, ['dashboard.php', 'dashboard_baru.php', 'index.php', '']) || empty($current_page)) ? 'active' : ''; ?>" href="dashboard_baru.php">
                    <i class="bi bi-grid-3x3-gap"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <?php // Tampilkan menu berdasarkan ROLE ?>
            
            <?php if (in_array($role_user, ['admin', 'front_office'])): ?>
                <!-- Divider untuk Section -->
                <div class="sidebar-divider"></div>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'reservasi_kalender.php') ? 'active' : ''; ?>" href="reservasi_kalender.php">
                        <i class="bi bi-calendar-check"></i>
                        <span>Reservasi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'tamu_data.php') ? 'active' : ''; ?>" href="tamu_data.php">
                        <i class="bi bi-people"></i>
                        <span>Data Tamu</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (in_array($role_user, ['admin', 'housekeeping'])): ?>
                <?php if (!in_array($role_user, ['admin', 'front_office'])): ?>
                    <div class="sidebar-divider"></div>
                <?php endif; ?>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'kamar_status.php') ? 'active' : ''; ?>" href="kamar_status.php">
                        <i class="bi bi-house-check"></i>
                        <span>Status Kamar</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'maintenance_laporan.php') ? 'active' : ''; ?>" href="maintenance_laporan.php">
                        <i class="bi bi-wrench-adjustable"></i>
                        <span>Maintenance</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($role_user == 'admin'): ?>
                <div class="sidebar-divider"></div>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'laporan_keuangan.php') ? 'active' : ''; ?>" href="laporan_keuangan.php">
                        <i class="bi bi-journal-text"></i>
                        <span>Laporan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'pengaturan_properti.php') ? 'active' : ''; ?>" href="pengaturan_properti.php">
                        <i class="bi bi-gear-wide-connected"></i>
                        <span>Pengaturan</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>

        <!-- Logout Section di Bawah -->
        <div class="sidebar-logout">
            <ul class="nav flex-column sidebar-nav">
                <li class="nav-item">
                    <a class="nav-link nav-link-logout" href="logout.php">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Keluar</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>