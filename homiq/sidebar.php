<div class="sidebar-wrapper">
    <div class="sidebar-header text-center p-3 mb-2">
        <i class="bi bi-buildings-fill fs-2 text-primary"></i>
        <h5 class="mt-2 mb-0 fw-bold">Adiputra CMS</h5>
        <small class="text-muted">Guesthouse Management</small>
    </div>

    <ul class="nav flex-column sidebar-nav">
        <li class="nav-item">
            <a class="nav-link active" href="index.php">
                <i class="bi bi-grid-fill"></i>
                <span>Dashboard</span>
            </a>
        </li>
        
        <li class="nav-item-header">Front Office</li>
        
        <li class="nav-item">
            <a class="nav-link" href="#"> <i class="bi bi-calendar-check"></i>
                <span>Reservasi</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="bi bi-people-fill"></i>
                <span>Data Tamu</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="bi bi-person-video3"></i>
                <span>Membership</span>
            </a>
        </li>

        <li class="nav-item-header">Operasional</li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="bi bi-door-closed-fill"></i>
                <span>Status Kamar</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="bi bi-tools"></i>
                <span>Laporan Maintenance</span>
            </a>
        </li>

        <li class="nav-item-header">Laporan</li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="bi bi-graph-up-arrow"></i>
                <span>Laporan Keuangan</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="bi bi-pie-chart-fill"></i>
                <span>Laporan Okupansi</span>
            </a>
        </li>

        <?php // if ($_SESSION['role'] == 'admin') : ?>
        <li class="nav-item-header">Pengaturan (Admin)</li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="bi bi-building-gear"></i>
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
            <a class="nav-link" href="#"> <i class="bi bi-person-plus-fill"></i>
                <span>Manajemen User</span>
            </a>
        </li>
        <?php // endif; ?>

        <hr class="sidebar-divider">

        <li class="nav-item">
            <a class="nav-link" href="logout.php"> <i class="bi bi-box-arrow-left"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</div>