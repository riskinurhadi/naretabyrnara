<?php
session_start();
if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';
$id_pengguna_aktif = $_SESSION['id_pengguna'];
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jumlah = $_POST['jumlah'];
    $kategori = $_POST['kategori'];
    $tanggal = $_POST['tanggal'];
    $deskripsi = $_POST['deskripsi'];
    $jenis_transaksi = 'pemasukan';
    
    $sql = "INSERT INTO tabel_transaksi (id_pengguna, jumlah, jenis_transaksi, kategori, tanggal_transaksi, deskripsi) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "idssss", $id_pengguna_aktif, $jumlah, $jenis_transaksi, $kategori, $tanggal, $deskripsi);
    
    if (mysqli_stmt_execute($stmt)) {
        $message = '<div class="alert alert-success">Data pemasukan berhasil disimpan.</div>';
    } else {
        $message = '<div class="alert alert-danger">Gagal menyimpan data.</div>';
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pemasukan - Manajemen Keuangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
        .header { background-color: #ffffff; padding: 1rem 0; border-bottom: 1px solid #e0e0e0; }
        .form-card { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); background-color: #fff; }
        .form-control, .form-select { border-radius: 10px; padding: 0.75rem 1rem; }
        .form-control:focus, .form-select:focus { box-shadow: 0 0 0 0.25rem rgba(76, 110, 245, 0.25); border-color: #4c6ef5; }
        .btn-primary { background-color: #4c6ef5; border-color: #4c6ef5; border-radius: 10px; font-weight: 500; }
    </style>
</head>
<body>
    <header class="header">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="dashboard.php" class="text-dark text-decoration-none"><i class="fas fa-arrow-left me-2"></i> Kembali</a>
            <h5 class="mb-0 fw-bold">Tambah Pemasukan</h5>
            <div style="width: 60px;"></div>
        </div>
    </header>
    <main class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <?php if(!empty($message)) echo $message; ?>
                <div class="card form-card"><div class="card-body p-4">
                    <form action="" method="POST">
                        <div class="mb-3"><label for="jumlah" class="form-label">Jumlah Pemasukan</label><div class="input-group"><span class="input-group-text bg-light border-end-0">Rp</span><input type="number" class="form-control border-start-0" id="jumlah" name="jumlah" placeholder="0" required step="any"></div></div>
                        <div class="mb-3"><label for="kategori" class="form-label">Kategori</label><select class="form-select" id="kategori" name="kategori" required><option value="" disabled selected>Pilih kategori...</option><option value="Gaji">Gaji</option><option value="Bonus">Bonus</option><option value="Hasil Usaha">Hasil Usaha</option><option value="Hadiah">Hadiah</option><option value="Lainnya">Lainnya</option></select></div>
                        <div class="mb-3"><label for="tanggal" class="form-label">Tanggal</label><input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= date('Y-m-d'); ?>" required></div>
                        <div class="mb-4"><label for="deskripsi" class="form-label">Deskripsi (Opsional)</label><textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Contoh: Gaji bulan Juli"></textarea></div>
                        <div class="d-grid"><button type="submit" class="btn btn-primary py-2"><i class="fas fa-save me-2"></i>Simpan Pemasukan</button></div>
                    </form>
                </div></div>
            </div>
        </div>
    </main>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
