<?php
session_start();

// Mengambil URL QRIS dan Nomor Pesanan dari parameter URL
// Data ini dikirim dari halaman process_payment.php
$qrisUrl = isset($_GET['qris_url']) ? urldecode($_GET['qris_url']) : null;
$orderId = isset($_GET['order_id']) ? htmlspecialchars($_GET['order_id']) : 'Tidak ditemukan';

// Jika URL QRIS tidak ada, arahkan kembali ke halaman utama
// Ini mencegah halaman diakses secara langsung tanpa data yang valid
if (!$qrisUrl) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran dengan QRIS</title>
    
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
        .qris-code {
            width: 100%;
            max-width: 300px;
            display: block;
            margin: 0 auto;
        }
    </style>
    <!-- Font Awesome untuk ikon centang -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="container py-5">
        <div class="card p-5 text-center">
            <h1 class="fw-bold mb-3" style="color: var(--primary-color);">Lakukan Pembayaran</h1>
            <p class="lead">Silakan scan QR Code di bawah ini untuk menyelesaikan pembayaran Anda.</p>
            <div class="mb-4">
                <img src="<?php echo $qrisUrl; ?>" alt="QRIS Code" class="qris-code">
            </div>
            <p>Nomor Pesanan: <strong><?php echo $orderId; ?></strong></p>
            <p class="text-muted">Pembayaran akan dikonfirmasi secara otomatis.</p>
            <a href="index.php" class="btn btn-home btn-lg mt-4">Kembali ke Beranda</a>
        </div>
    </div>

    <!-- Bootstrap 5 JavaScript dari CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
