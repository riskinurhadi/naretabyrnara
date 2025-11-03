<?php
session_start(); // Wajib ada di paling atas

// File ini akan mengimpor semua variabel session dan cek login
require 'layout_header.php'; 
// $nama_user, $role_user, $inisial_user sudah tersedia dari header
?>

<title>Dashboard - Guesthouse Adiputra</title>

<div class="container-fluid">

    <header class="mb-4">
        <div classd-flex justify-content-between align-items-center">
            
            <div class="row">
                <div class="col-md-8">
                    <h1 class="h3 mb-0" style="color: #212529;">Selamat Datang, <?php echo htmlspecialchars($nama_user); ?>!</h1>
                    <p class="text-muted">Berikut adalah ringkasan aktivitas terbaru di Guesthouse Adiputra.</p>
                </div>
                
                <div class="col-md-4 d-flex align-items-center justify-content-end">
                    <div class="user-profile-widget d-flex align-items-center">
                        <div class="user-avatar" style="width: 45px; height: 45px; border-radius: 50%; background-color: var(--brand-blue); color: white; display: inline-flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1.2rem; margin-right: 15px;">
                            <?php echo $inisial_user; ?>
                        </div>
                        <div class="user-info">
                            <span style="font-weight: 600; color: #212529; display: block;"><?php echo htmlspecialchars($nama_user); ?></span>
                            <span style="font-size: 0.85rem; color: #6c757d; display: block;"><?php echo htmlspecialchars($role_user); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </header>

    <div class="row">

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card-new">
                <div class="icon-circle bg-blue">
                    <i class="bi bi-calendar-check-fill"></i>
                </div>
                <div class="info">
                    <div class="info-value">120</div>
                    <div class="info-label">Reservasi</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card-new">
                <div class="icon-circle bg-green">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="info">
                    <div class="info-value">45jt</div>
                    <div class="info-label">Pendapatan</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card-new">
                <div class="icon-circle bg-orange">
                    <i class="bi bi-box-arrow-in-right"></i>
                </div>
                <div class="info">
                    <div class="info-value">8</div>
                    <div class="info-label">Check-in Hari Ini</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card-new">
                <div class="icon-circle bg-purple">
                    <i class="bi bi-box-arrow-out-right"></i>
                </div>
                <div classs="info">
                    <div class="info-value">6</div>
                    <div class="info-label">Check-out Hari Ini</div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-12">
            <div class="content-block">
                <div class="content-block-header">
                    <h5>Overview Kalender</h5>
                    <a href="#" class="link-semua">Lihat Kalender Penuh</a>
                </div>
                <div class="content-block-body" style="min-height: 400px;">
                    <p class="text-muted">Di sini nanti kita akan letakkan kalender reservasi interaktif.</p>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
require 'layout_footer.php'; 
?>