<?php
session_start(); // Wajib ada di paling atas

// File ini akan mengimpor semua variabel session dan cek login
require 'layout_header.php'; 
?>

<title>Dashboard - Guesthouse Adiputra</title>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        </div>

    <div class="row">

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Reservasi (Bulan Ini)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">120</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-check-fill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Pendapatan (Bulan Ini)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp 45.000.000</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-cash-stack fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Tamu Check-in Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">8</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-box-arrow-in-right fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Overview Kalender (Placeholder)</h6>
                </div>
                <div class="card-body" style="min-height: 400px;">
                    <p>Di sini nanti kita akan letakkan kalender reservasi interaktif.</p>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
require 'layout_footer.php'; 
?>