<?php
// sidebar.php
if (!isset($role_user)) {
    $role_user = 'guest'; // Pengaman jika variabel tidak ada
}
?>

<div class="offcanvas offcanvas-start offcanvas-lg bg-white" data-bs-scroll="true" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
    
    <div class="offcanvas-header shadow-sm">
        <h5 class="offcanvas-title" id="sidebarMenuLabel">Guesthouse Adiputra</h5>
        <button type="button" class="btn-close d-lg-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-3">
        <ul class="nav flex-column sidebar-nav">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                    <i class="bi bi-grid"></i> Dashboard
                </a>
            </li>
            
            <?php if (in_array($role_user, ['admin', 'front_office'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="reservasi_kalender.php">
                        <i class="bi bi-calendar-check"></i> Reservasi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="tamu_data.php">
                        <i class="bi bi-people"></i> Data Tamu
                    </a>
                </li>
            <?php endif; ?>

            <?php if (in_array($role_user, ['admin', 'housekeeping'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="kamar_status.php">
                        <i class="bi bi-house-check"></i> Status Kamar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="maintenance_laporan.php">
                        <i class="bi bi-wrench-adjustable"></i> Maintenance
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($role_user == 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="laporan_keuangan.php">
                        <i class="bi bi-journal-text"></i> Laporan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pengaturan_properti.php">
                        <i class="bi bi-gear"></i> Pengaturan
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>