<?php
session_start(); // Wajib ada di paling atas

// File ini akan mengimpor semua variabel session dan cek login
require 'layout_header.php'; 
?>

<title>Dashboard - Guesthouse Adiputra</title>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0" style="color: var(--brand-dark);">Dashboard</h1>
        </div>

    <div class="row">

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="stat-card">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="stat-card-title">Reservasi (Bulan Ini)</div>
                        <div class="stat-card-value">120</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-calendar-check-fill stat-card-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="stat-card">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="stat-card-title">Pendapatan (Bulan Ini)</div>
                        <div class="stat-card-value">Rp 45jt</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-cash-stack stat-card-icon"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="stat-card">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="stat-card-title">Tamu Check-in Hari Ini</div>
                        <div class="stat-card-value">8</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-box-arrow-in-right stat-card-icon"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-12">
            <div class="main-card">
                <div class="card-header">
                    Overview Kalender (Placeholder)
                </div>
                <div class="card-body" style="min-height: 400px;">
                    <p class="text-muted">Di sini nanti kita akan letakkan kalender reservasi interaktif.</p>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
require 'layout_footer.php'; 
?>