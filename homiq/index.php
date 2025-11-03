<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

// Ambil data user dari session
$nama_lengkap = $_SESSION['nama_lengkap'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Adiputra CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        /* CSS Internal untuk Dashboard */
        :root {
            --sidebar-width: 280px;
            --primary-light: #e6f2ff; /* Biru langit muda untuk hover/active */
            --primary-dark: #0056b3;
        }

        body {
            background-color: #f8f9fa; /* Latar belakang abu-abu muda */
            font-family: 'Inter', sans-serif; /* Font modern (opsional, ganti/tambahkan link di head) */
        }

        /* --- Sidebar Style --- */
        .sidebar-wrapper {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #ffffff; /* Warna sidebar putih bersih */
            border-right: 1px solid #dee2e6;
            padding-top: 1rem;
            transition: all 0.3s;
        }
        .sidebar-header {
            border-bottom: 1px solid #eee;
        }
        .sidebar-nav {
            padding: 1rem;
        }
        .sidebar-nav .nav-item {
            margin-bottom: 0.25rem;
        }
        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            color: #555; /* Warna teks menu */
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem; /* Sudut rounded untuk link */
        }
        .sidebar-nav .nav-link i {
            font-size: 1.1rem;
            margin-right: 1rem;
            width: 20px;
            color: #888; /* Warna ikon abu-abu */
        }
        .sidebar-nav .nav-link:hover {
            background-color: var(--primary-light);
            color: var(--primary-dark);
        }
        .sidebar-nav .nav-link:hover i {
            color: var(--primary-dark);
        }
        .sidebar-nav .nav-link.active {
            background-color: #0d6efd; /* Biru langit/primary */
            color: #ffffff;
            font-weight: 600;
            box-shadow: 0 4px 8px rgba(13, 110, 253, 0.2);
        }
        .sidebar-nav .nav-link.active i {
            color: #ffffff;
        }
        .sidebar-nav .nav-item-header {
            font-size: 0.8rem;
            font-weight: 600;
            color: #aaa;
            padding: 1rem 1rem 0.5rem;
            text-transform: uppercase;
        }
        .sidebar-divider {
            margin: 1rem;
            border-top: 1px solid #eee;
        }

        /* --- Content Style --- */
        .content-wrapper {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            width: calc(100% - var(--sidebar-width));
        }

        /* --- Header/Navbar Konten --- */
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background: #fff;
            padding: 1rem 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        /* --- Stats Card (Meniru Referensi) --- */
        .stat-card {
            background-color: #fff;
            border: none;
            border-radius: 0.75rem; 
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease-in-out;
            margin-bottom: 1.5rem;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }
        .stat-card .card-body {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .stat-card .icon-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #fff;
        }
        /* Variasi warna ikon seperti di referensi */
        .icon-circle-blue { background-color: #e6f2ff; color: #0d6efd; }
        .icon-circle-green { background-color: #e3fcec; color: #198754; }
        .icon-circle-orange { background-color: #fff3e0; color: #fd7e14; }
        .icon-circle-purple { background-color: #f3e8ff; color: #6f42c1; }
        
        /* Konten Lainnya */
        .main-content-card {
             background-color: #fff;
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <main class="content-wrapper">
        
        <header class="content-header">
            <div>
                <h2 class="h4 fw-bold mb-0">Selamat Datang, <?php echo htmlspecialchars($nama_lengkap); ?>!</h2>
                <p class="text-muted mb-0">Berikut adalah ringkasan aktivitas di guesthouse Anda.</p>
            </div>
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($nama_lengkap); ?>&background=0d6efd&color=fff&rounded=true" alt="" width="32" height="32" class="rounded-circle me-2">
                        <strong><?php echo htmlspecialchars($role); ?></strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end text-small shadow" aria-labelledby="dropdownUser">
                        <li><a class="dropdown-item" href="#">Profil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <div>
                            <h5 class="card-title text-muted fw-normal mb-1">Reservasi Hari Ini</h5>
                            <h2 class="fw-bold mb-0">5</h2> </div>
                        <div class="icon-circle icon-circle-blue">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <div>
                            <h5 class="card-title text-muted fw-normal mb-1">Tamu Check-in</h5>
                            <h2 class="fw-bold mb-0">12</h2> </div>
                        <div class="icon-circle icon-circle-green">
                            <i class="bi bi-box-arrow-in-right"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <div>
                            <h5 class="card-title text-muted fw-normal mb-1">Kamar Tersedia</h5>
                            <h2 class="fw-bold mb-0">8</h2> </div>
                        <div class="icon-circle icon-circle-orange">
                            <i class="bi bi-door-open"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div class="card main-content-card">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="fw-bold">Reservasi Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-center text-muted">
                            (Di sini nanti akan ada tabel Kalender View atau List View reservasi)
                        </p>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama Tamu</th>
                                    <th>Kamar</th>
                                    <th>Check-in</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Budi Santoso</td>
                                    <td>101 - Deluxe</td>
                                    <td>03 Nov 2025</td>
                                    <td><span class="badge bg-success">Checked-in</span></td>
                                </tr>
                                <tr>
                                    <td>Citra Lestari</td>
                                    <td>102 - Standard</td>
                                    <td>03 Nov 2025</td>
                                    <td><span class="badge bg-primary">Booking</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        /* JS Internal bisa ditambahkan di sini */
        
        // Contoh: Script untuk toggle active class di sidebar
        document.addEventListener("DOMContentLoaded", function() {
            const currentLocation = window.location.href;
            const navLinks = document.querySelectorAll(".sidebar-nav .nav-link");

            navLinks.forEach(link => {
                if (link.href === currentLocation) {
                    // Hapus 'active' dari semua link dulu
                    navLinks.forEach(l => l.classList.remove("active"));
                    // Tambahkan 'active' ke link yang cocok
                    link.classList.add("active");
                }
            });
        });
    </script>
</body>
</html>