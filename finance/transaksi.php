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

// --- PROSES HAPUS TRANSAKSI ---
if(isset($_GET['hapus'])){
    $id_to_delete = $_GET['hapus'];
    $stmt_delete = mysqli_prepare($koneksi, "DELETE FROM tabel_transaksi WHERE id_transaksi = ? AND id_pengguna = ?");
    mysqli_stmt_bind_param($stmt_delete, "ii", $id_to_delete, $id_pengguna_aktif);
    mysqli_stmt_execute($stmt_delete);
    
    // Redirect kembali ke halaman transaksi untuk refresh data
    header("Location: transaksi.php");
    exit();
}

// 3. Logika untuk filter tanggal
// Jika ada filter dari form, gunakan itu. Jika tidak, gunakan bulan ini sebagai default.
$tanggal_mulai = isset($_GET['mulai']) && !empty($_GET['mulai']) ? $_GET['mulai'] : date('Y-m-01');
$tanggal_akhir = isset($_GET['akhir']) && !empty($_GET['akhir']) ? $_GET['akhir'] : date('Y-m-t');

$where_clause = "WHERE id_pengguna = ? AND tanggal_transaksi BETWEEN ? AND ?";
$params = [$id_pengguna_aktif, $tanggal_mulai, $tanggal_akhir];
$types = "iss";

// 4. Ambil semua data transaksi sesuai filter
$sql = "SELECT * FROM tabel_transaksi $where_clause ORDER BY tanggal_transaksi DESC, id_transaksi DESC";
$stmt_transaksi = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt_transaksi, $types, ...$params);
mysqli_stmt_execute($stmt_transaksi);
$query_transaksi = mysqli_stmt_get_result($stmt_transaksi);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semua Transaksi - Financify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
        .header { background-color: #ffffff; padding: 1rem 0; border-bottom: 1px solid #e0e0e0; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); }
        .transaction-list .list-group-item { border-bottom: 1px solid #eee !important; }
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
            <h5 class="mb-0 fw-bold">Semua Transaksi</h5>
            <div style="width: 60px;"></div>
        </div>
    </header>

    <main class="container mt-4">
        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="card-title">Filter Transaksi</h6>
                <form action="transaksi.php" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="mulai" class="form-label">Dari Tanggal</label>
                        <input type="date" class="form-control" id="mulai" name="mulai" value="<?= htmlspecialchars($tanggal_mulai) ?>">
                    </div>
                    <div class="col-md-5">
                        <label for="akhir" class="form-label">Sampai Tanggal</label>
                        <input type="date" class="form-control" id="akhir" name="akhir" value="<?= htmlspecialchars($tanggal_akhir) ?>">
                    </div>
                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Daftar Transaksi -->
        <div class="card">
            <div class="card-body p-0">
                <div class="list-group list-group-flush transaction-list">
                    <?php if(mysqli_num_rows($query_transaksi) > 0): ?>
                        <?php while($transaksi = mysqli_fetch_assoc($query_transaksi)): ?>
                            <div class="list-group-item px-3 py-2">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <div class="d-flex align-items-center" style="min-width: 0;">
                                        <div class="me-3 fs-4">
                                            <?php if($transaksi['jenis_transaksi'] == 'pemasukan'): ?>
                                                <i class="fas fa-arrow-circle-down text-success"></i>
                                            <?php else: ?>
                                                <i class="fas fa-arrow-circle-up text-danger"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div style="min-width: 0;">
                                            <h6 class="mb-0 text-truncate"><?= htmlspecialchars($transaksi['kategori']) ?></h6>
                                            <small class="text-muted"><?= date('d M Y', strtotime($transaksi['tanggal_transaksi'])) ?></small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="transaction-amount <?= $transaksi['jenis_transaksi'] ?> text-nowrap me-3">
                                            <?= $transaksi['jenis_transaksi'] == 'pemasukan' ? '+' : '-' ?><?= format_rupiah($transaksi['jumlah']) ?>
                                        </span>
                                        <a href="transaksi.php?hapus=<?= $transaksi['id_transaksi'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Anda yakin ingin menghapus transaksi ini?');">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center text-muted p-4">Tidak ada data transaksi pada periode ini.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
