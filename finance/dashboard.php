<?php
// 1. Mulai sesi di baris paling atas
session_start();

// 2. "Penjaga" halaman. Jika pengguna belum login, arahkan ke halaman login
if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit();
}

// 3. Sertakan file koneksi dan ambil ID pengguna yang sedang aktif
include 'koneksi.php';
$id_pengguna_aktif = $_SESSION['id_pengguna'];
$nama_pengguna_aktif = $_SESSION['nama_lengkap'];

// --- FUNGSI UNTUK FORMAT RUPIAH ---
function format_rupiah($angka){
    return "Rp " . number_format($angka, 0, ',', '.');
}

// --- PROSES HAPUS TRANSAKSI TUNGGAL (VERSI AMAN) ---
if(isset($_GET['hapus'])){
    $id_to_delete = $_GET['hapus'];
    
    // Query ini hanya akan menghapus transaksi jika id_transaksi DAN id_pengguna cocok.
    // Ini mencegah pengguna menghapus data milik orang lain.
    $sql_delete = "DELETE FROM tabel_transaksi WHERE id_transaksi = ? AND id_pengguna = ?";
    $stmt_delete = mysqli_prepare($koneksi, $sql_delete);
    mysqli_stmt_bind_param($stmt_delete, "ii", $id_to_delete, $id_pengguna_aktif);
    mysqli_stmt_execute($stmt_delete);
    
    header("Location: dashboard.php?status=hapus_sukses");
    exit();
}

// --- MENGAMBIL DATA UNTUK KARTU RINGKASAN (SPESIFIK PENGGUNA) ---
$stmt_pemasukan = mysqli_prepare($koneksi, "SELECT SUM(jumlah) as total FROM tabel_transaksi WHERE jenis_transaksi = 'pemasukan' AND id_pengguna = ?");
mysqli_stmt_bind_param($stmt_pemasukan, "i", $id_pengguna_aktif);
mysqli_stmt_execute($stmt_pemasukan);
$total_pemasukan = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_pemasukan))['total'] ?? 0;

$stmt_pengeluaran = mysqli_prepare($koneksi, "SELECT SUM(jumlah) as total FROM tabel_transaksi WHERE jenis_transaksi = 'pengeluaran' AND id_pengguna = ?");
mysqli_stmt_bind_param($stmt_pengeluaran, "i", $id_pengguna_aktif);
mysqli_stmt_execute($stmt_pengeluaran);
$total_pengeluaran = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_pengeluaran))['total'] ?? 0;

$saldo_total = $total_pemasukan - $total_pengeluaran;

// --- MENGAMBIL DATA UNTUK GRAFIK (SPESIFIK PENGGUNA) ---
$labels_chart = []; $data_chart = [];
for ($i = 6; $i >= 0; $i--) {
    $labels_chart[] = date('D', strtotime("-$i days"));
    $data_chart[date('Y-m-d', strtotime("-$i days"))] = 0;
}
$stmt_chart = mysqli_prepare($koneksi, "SELECT DATE(tanggal_transaksi) as tanggal, SUM(jumlah) as total FROM tabel_transaksi WHERE jenis_transaksi = 'pengeluaran' AND id_pengguna = ? AND tanggal_transaksi >= CURDATE() - INTERVAL 6 DAY GROUP BY DATE(tanggal_transaksi)");
mysqli_stmt_bind_param($stmt_chart, "i", $id_pengguna_aktif);
mysqli_stmt_execute($stmt_chart);
$result_chart = mysqli_stmt_get_result($stmt_chart);
while($row = mysqli_fetch_assoc($result_chart)){
    $data_chart[$row['tanggal']] = $row['total'];
}
$data_chart = array_values($data_chart);

// --- MENGAMBIL DATA TRANSAKSI TERAKHIR (SPESIFIK PENGGUNA) ---
$stmt_transaksi = mysqli_prepare($koneksi, "SELECT * FROM tabel_transaksi WHERE id_pengguna = ? ORDER BY tanggal_transaksi DESC, id_transaksi DESC LIMIT 5");
mysqli_stmt_bind_param($stmt_transaksi, "i", $id_pengguna_aktif);
mysqli_stmt_execute($stmt_transaksi);
$query_transaksi_terakhir = mysqli_stmt_get_result($stmt_transaksi);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Manajemen Keuangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
        .navbar { background-color: #ffffff; border-bottom: 1px solid #e0e0e0; }
        .navbar-brand { font-weight: 600; color: #333; }
        .profile-pic { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
        .summary-card { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .summary-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12); }
        .summary-card .card-body { padding: 1.25rem; }
        .summary-card .icon { font-size: 1.8rem; padding: 15px; border-radius: 12px; color: #fff; }
        .icon-balance { background-color: #4c6ef5; }
        .icon-income { background-color: #20c997; }
        .icon-expense { background-color: #fa5252; }
        .chart-card { border-radius: 15px; border: none; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); }
        .chart-wrapper { height: 250px; position: relative; }
        .transaction-list .list-group-item { border-radius: 12px; margin-bottom: 10px; border: 1px solid #e9ecef; padding: 1rem; }
        .transaction-icon { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.2rem; }
        .icon-plus { background-color: #20c997; }
        .icon-minus { background-color: #fa5252; }
        .transaction-amount { font-weight: 600; }
        .transaction-amount.income { color: #20c997; }
        .transaction-amount.expense { color: #fa5252; }
        .bottom-nav { box-shadow: 0 -2px 10px rgba(0,0,0,0.1); border-top-left-radius: 20px; border-top-right-radius: 20px; }
        .bottom-nav a { color: #888; text-decoration: none; transition: color 0.3s; }
        .bottom-nav a.active { color: #4c6ef5; font-weight: 600; }
        .bottom-nav a i { font-size: 1.4rem; }
        main { padding-bottom: 100px; }
        .dropdown-item i { width: 20px; }
        @media (max-width: 420px) {
            .summary-card .card-body { padding: 0.7rem; }
            .summary-card .icon { font-size: 1.1rem; padding: 9px; margin-right: 0.6rem !important; }
            .summary-card h5.card-title { font-size: 0.85rem; font-weight: 600; }
            .summary-card h6.card-subtitle { font-size: 0.65rem; }
        }
    </style>
</head>
<body>
    <header class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-wallet me-2"></i>Financify</a>
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://i.pravatar.cc/150?u=<?= $id_pengguna_aktif ?>" alt="Profil" class="profile-pic me-2">
                        <i class="fas fa-ellipsis-v"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="input_pemasukan.php"><i class="fas fa-plus me-2"></i>Tambah Pemasukan</a></li>
                        <li><a class="dropdown-item" href="input_pengeluaran.php"><i class="fas fa-minus me-2"></i>Tambah Pengeluaran</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="hapus_semua_transaksi.php" onclick="return confirm('PERINGATAN! Anda akan menghapus SEMUA data transaksi Anda secara permanen. Lanjutkan?');"><i class="fas fa-trash-alt me-2"></i>Hapus Semua Data</a></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    <main class="container mt-4">
        <section class="mb-4">
            <h5>Halo, <?= htmlspecialchars(explode(' ', $nama_pengguna_aktif)[0]) ?>! 👋</h5>
            <p class="text-muted">Selamat datang kembali di dashboard keuanganmu.</p>
        </section>
        <section class="row">
            <div class="col-md-12 mb-4"><div class="summary-card bg-white"><div class="card-body d-flex align-items-center"><div class="icon icon-balance me-3"><i class="fas fa-landmark"></i></div><div><h6 class="card-subtitle text-muted">Saldo Total</h6><h4 class="card-title fw-bold mb-0"><?= format_rupiah($saldo_total) ?></h4></div></div></div></div>
            <div class="col-6 col-md-6 mb-4"><div class="summary-card bg-white"><div class="card-body"><div class="d-flex align-items-start"><div class="icon icon-income me-3"><i class="fas fa-arrow-down"></i></div><div style="min-width: 0;"><h6 class="card-subtitle text-muted">Uang Masuk</h6><h5 class="card-title fw-bold mb-0"><?= format_rupiah($total_pemasukan) ?></h5></div></div></div></div></div>
            <div class="col-6 col-md-6 mb-4"><div class="summary-card bg-white"><div class="card-body"><div class="d-flex align-items-start"><div class="icon icon-expense me-3"><i class="fas fa-arrow-up"></i></div><div style="min-width: 0;"><h6 class="card-subtitle text-muted">Uang Keluar</h6><h5 class="card-title fw-bold mb-0"><?= format_rupiah($total_pengeluaran) ?></h5></div></div></div></div></div>
        </section>
        <section class="mb-4"><div class="card chart-card"><div class="card-body"><h6 class="card-title mb-3">Rekap Pengeluaran 7 Hari Terakhir</h6><div class="chart-wrapper"><canvas id="weeklyExpenseChart"></canvas></div></div></div></section>
        <section>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Transaksi Terakhir</h6>
                <a href="#" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="list-group transaction-list">
                <?php if(mysqli_num_rows($query_transaksi_terakhir) > 0): ?>
                    <?php while($transaksi = mysqli_fetch_assoc($query_transaksi_terakhir)): ?>
                        <div class="list-group-item"><div class="d-flex w-100 justify-content-between align-items-center"><div class="d-flex align-items-center" style="min-width: 0;"><div class="transaction-icon <?= $transaksi['jenis_transaksi'] == 'pemasukan' ? 'icon-plus' : 'icon-minus' ?> me-3"><i class="fas <?= $transaksi['jenis_transaksi'] == 'pemasukan' ? 'fa-briefcase' : 'fa-shopping-cart' ?>"></i></div><div style="min-width: 0;"><h6 class="mb-0 text-truncate"><?= htmlspecialchars($transaksi['kategori']) ?></h6><small class="text-muted"><?= date('d M Y', strtotime($transaksi['tanggal_transaksi'])) ?></small></div></div><div class="d-flex align-items-center"><span class="transaction-amount <?= $transaksi['jenis_transaksi'] ?> text-nowrap me-3"><?= $transaksi['jenis_transaksi'] == 'pemasukan' ? '+' : '-' ?><?= format_rupiah($transaksi['jumlah']) ?></span><a href="dashboard.php?hapus=<?= $transaksi['id_transaksi'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Anda yakin ingin menghapus transaksi ini?');"><i class="fas fa-trash-alt"></i></a></div></div></div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-center text-muted">Belum ada data transaksi.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <nav class="navbar fixed-bottom bg-white d-md-none bottom-nav">
        <div class="container d-flex justify-content-around">
            <a href="dashboard.php" class="text-center active"><i class="fas fa-home"></i><br><small>Home</small></a>
            <a href="transaksi.php" class="text-center"><i class="fas fa-list-ul"></i><br><small>Transaksi</small></a>
            <a href="input_pemasukan.php" class="text-center"><i class="fas fa-plus-circle fa-2x text-primary"></i></a>
            <a href="laporan.php" class="text-center"><i class="fas fa-chart-pie"></i><br><small>Laporan</small></a>
            <a href="#" class="text-center"><i class="fas fa-cog"></i><br><small>Profil</small></a>
        </div>
    </nav>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('weeklyExpenseChart').getContext('2d');
        new Chart(ctx, { type: 'bar', data: { labels: <?= json_encode($labels_chart) ?>, datasets: [{ label: 'Pengeluaran', data: <?= json_encode($data_chart) ?>, backgroundColor: 'rgba(76, 110, 245, 0.8)', borderColor: 'rgba(76, 110, 245, 1)', borderWidth: 1, borderRadius: 8, barThickness: 15 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { callbacks: { label: (c) => `${c.dataset.label || ''}: ${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(c.parsed.y)}` } } }, scales: { y: { beginAtZero: true, ticks: { callback: (v) => v >= 1e6 ? `Rp ${v/1e6} Jt` : (v >= 1e3 ? `Rp ${v/1e3} K` : `Rp ${v}`) } }, x: { grid: { display: false } } } } });
    </script>
</body>
</html>
<?php mysqli_close($koneksi); ?>
