<?php
// 1. Mulai sesi dan lakukan pengecekan login
session_start();
if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit();
}

// 2. Sertakan koneksi dan ambil data pengguna aktif
include 'koneksi.php';
$id_pengguna_aktif = $_SESSION['id_pengguna'];

// --- FUNGSI UNTUK FORMAT RUPIAH ---
function format_rupiah($angka){
    return "Rp " . number_format($angka, 0, ',', '.');
}

// 3. Tentukan rentang tanggal
// Jika ada filter dari form, gunakan itu. Jika tidak, gunakan bulan ini sebagai default.
$tanggal_mulai = isset($_GET['mulai']) && !empty($_GET['mulai']) ? $_GET['mulai'] : date('Y-m-01');
$tanggal_akhir = isset($_GET['akhir']) && !empty($_GET['akhir']) ? $_GET['akhir'] : date('Y-m-t');


// 4. Ambil data ringkasan (pemasukan, pengeluaran) sesuai rentang tanggal
// Pemasukan
$stmt_pemasukan = mysqli_prepare($koneksi, "SELECT SUM(jumlah) as total FROM tabel_transaksi WHERE jenis_transaksi = 'pemasukan' AND id_pengguna = ? AND tanggal_transaksi BETWEEN ? AND ?");
mysqli_stmt_bind_param($stmt_pemasukan, "iss", $id_pengguna_aktif, $tanggal_mulai, $tanggal_akhir);
mysqli_stmt_execute($stmt_pemasukan);
$total_pemasukan = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_pemasukan))['total'] ?? 0;

// Pengeluaran
$stmt_pengeluaran = mysqli_prepare($koneksi, "SELECT SUM(jumlah) as total FROM tabel_transaksi WHERE jenis_transaksi = 'pengeluaran' AND id_pengguna = ? AND tanggal_transaksi BETWEEN ? AND ?");
mysqli_stmt_bind_param($stmt_pengeluaran, "iss", $id_pengguna_aktif, $tanggal_mulai, $tanggal_akhir);
mysqli_stmt_execute($stmt_pengeluaran);
$total_pengeluaran = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_pengeluaran))['total'] ?? 0;

// Selisih
$selisih = $total_pemasukan - $total_pengeluaran;


// 5. Ambil semua data transaksi sesuai rentang tanggal untuk ditampilkan di daftar
$stmt_transaksi = mysqli_prepare($koneksi, "SELECT * FROM tabel_transaksi WHERE id_pengguna = ? AND tanggal_transaksi BETWEEN ? AND ? ORDER BY tanggal_transaksi DESC, id_transaksi DESC");
mysqli_stmt_bind_param($stmt_transaksi, "iss", $id_pengguna_aktif, $tanggal_mulai, $tanggal_akhir);
mysqli_stmt_execute($stmt_transaksi);
$query_transaksi = mysqli_stmt_get_result($stmt_transaksi);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - Financify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
        .header { background-color: #ffffff; padding: 1rem 0; border-bottom: 1px solid #e0e0e0; }
        .form-card, .summary-card, .list-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            background-color: #fff;
        }
        .transaction-list .list-group-item { border-bottom: 1px solid #eee !important; border-radius: 0; }
        .transaction-list .list-group-item:last-child { border-bottom: none !important; }
        .transaction-amount.income { color: #20c997; font-weight: 600; }
        .transaction-amount.expense { color: #fa5252; font-weight: 600; }
        main { padding-bottom: 50px; }
    </style>
</head>
<body>
    <header class="header">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="dashboard.php" class="text-dark text-decoration-none"><i class="fas fa-arrow-left me-2"></i> Kembali</a>
            <h5 class="mb-0 fw-bold">Laporan Keuangan</h5>
            <div style="width: 60px;"></div>
        </div>
    </header>

    <main class="container mt-4">
        <!-- Filter Tanggal -->
        <div class="card form-card mb-4">
            <div class="card-body">
                <form action="laporan.php" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="mulai" class="form-label">Dari Tanggal</label>
                        <input type="date" class="form-control" id="mulai" name="mulai" value="<?= $tanggal_mulai ?>">
                    </div>
                    <div class="col-md-5">
                        <label for="akhir" class="form-label">Sampai Tanggal</label>
                        <input type="date" class="form-control" id="akhir" name="akhir" value="<?= $tanggal_akhir ?>">
                    </div>
                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Ringkasan Laporan -->
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card summary-card text-center h-100">
                    <div class="card-body">
                        <h6 class="text-muted">Total Pemasukan</h6>
                        <h4 class="income"><?= format_rupiah($total_pemasukan) ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card summary-card text-center h-100">
                    <div class="card-body">
                        <h6 class="text-muted">Total Pengeluaran</h6>
                        <h4 class="expense"><?= format_rupiah($total_pengeluaran) ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card summary-card text-center h-100">
                    <div class="card-body">
                        <h6 class="text-muted">Selisih</h6>
                        <h4 class="<?= $selisih >= 0 ? 'income' : 'expense' ?>"><?= format_rupiah($selisih) ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Transaksi -->
        <div class="card list-card mt-4">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="mb-0">Rincian Transaksi</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush transaction-list">
                    <?php if(mysqli_num_rows($query_transaksi) > 0): ?>
                        <?php while($transaksi = mysqli_fetch_assoc($query_transaksi)): ?>
                            <div class="list-group-item px-3 py-2">
                                <div class="d-flex w-100 justify-content-between">
                                    <div>
                                        <h6 class="mb-1"><?= htmlspecialchars($transaksi['kategori']) ?></h6>
                                        <small class="text-muted"><?= htmlspecialchars($transaksi['deskripsi']) ?></small>
                                    </div>
                                    <div class="text-end">
                                        <span class="transaction-amount <?= $transaksi['jenis_transaksi'] ?>">
                                            <?= $transaksi['jenis_transaksi'] == 'pemasukan' ? '+' : '-' ?><?= format_rupiah($transaksi['jumlah']) ?>
                                        </span>
                                        <br>
                                        <small class="text-muted"><?= date('d M Y', strtotime($transaksi['tanggal_transaksi'])) ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center text-muted p-4">Tidak ada data transaksi pada rentang tanggal ini.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
