<?php
session_start();

// Di dunia nyata, di sini Anda akan memvalidasi pembayaran
// dengan API payment gateway menggunakan nomor transaksi.
// Untuk contoh ini, kita hanya menampilkan halaman sukses statis.

// Misal, nomor transaksi didapat dari parameter URL setelah redirect
$transactionId = isset($_GET['transaction_id']) ? htmlspecialchars($_GET['transaction_id']) : 'BELUM-ADA-ID';
$customerName = 'Pelanggan'; // Di dunia nyata, ini diambil dari database
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil | Toko Online Elegan</title>
    
    <!-- Google Fonts - Poppins untuk font profesional -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS dari CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Internal CSS untuk kustomisasi -->
    <style>
        :root {
            --primary-color: #4A4E69;
            --secondary-color: #F8F9FA;
            --text-color: #212529;
            --success-color: #28a745;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--secondary-color);
            color: var(--text-color);
        }
        .container {
            max-width: 600px;
        }
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            background-color: #fff;
        }
        .icon-success {
            font-size: 5rem;
            color: var(--success-color);
        }
        .btn-home {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: #fff;
            transition: background-color 0.3s ease;
        }
        .btn-home:hover {
            background-color: #373A53;
            border-color: #373A53;
        }
    </style>
    <!-- Font Awesome untuk ikon centang -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="container py-5">
        <div class="card p-5 text-center">
            <div class="mb-4">
                <i class="fas fa-check-circle icon-success"></i>
            </div>
            <h1 class="fw-bold mb-3" style="color: var(--primary-color);">Pembayaran Berhasil!</h1>
            <p class="lead">Terima kasih, **<?php echo $customerName; ?>**. Pesanan Anda telah berhasil diproses.</p>
            <p>Nomor Transaksi: <strong><?php echo $transactionId; ?></strong></p>
            <hr>
            <p class="text-muted">Rincian pesanan dan informasi pengiriman akan dikirimkan ke email Anda.</p>
            <a href="index.php" class="btn btn-home btn-lg mt-4">Kembali ke Beranda</a>
        </div>
    </div>

    <!-- Bootstrap 5 JavaScript dari CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
