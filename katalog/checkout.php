<?php
session_start();

// Redirect ke index.php jika keranjang kosong
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Toko Online Elegan</title>
    
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
            --accent-color: #FFC107;
            --text-color: #212529;
            --light-gray: #E9ECEF;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--secondary-color);
            color: var(--text-color);
        }
        .container {
            max-width: 800px;
        }
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            background-color: #fff;
        }
        .btn-add-to-cart {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: #fff;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        .btn-add-to-cart:hover {
            background-color: #373A53;
            border-color: #373A53;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="fw-bold" style="color: var(--primary-color);">Checkout</h1>
            <p class="lead text-muted">Lengkapi data Anda untuk melanjutkan pembayaran.</p>
        </div>

        <div class="card p-4">
            <h4 class="mb-3">Ringkasan Pesanan</h4>
            <div class="list-group mb-4">
                <?php
                $totalPrice = 0;
                foreach ($_SESSION['cart'] as $item):
                    $itemTotal = $item['price'] * $item['quantity'];
                    $totalPrice += $itemTotal;
                ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="my-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                        <small class="text-muted"><?php echo $item['quantity']; ?> x Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></small>
                    </div>
                    <span class="text-muted">Rp <?php echo number_format($itemTotal, 0, ',', '.'); ?></span>
                </div>
                <?php endforeach; ?>
                <div class="list-group-item d-flex justify-content-between bg-light fw-bold">
                    <span>Total (IDR)</span>
                    <strong>Rp <?php echo number_format($totalPrice, 0, ',', '.'); ?></strong>
                </div>
            </div>

            <h4 class="mb-3">Informasi Pengiriman</h4>
            <form action="process_payment.php" method="POST">
                <!-- Tambahkan input tersembunyi untuk API Key Midtrans -->
                <div class="row g-3">
                    <div class="col-12">
                        <label for="nama" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama" name="nama" required>
                    </div>
                    <div class="col-12">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="col-12">
                        <label for="alamat" class="form-label">Alamat Lengkap</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                    </div>
                    <div class="col-12">
                        <label for="telepon" class="form-label">Nomor Telepon</label>
                        <input type="tel" class="form-control" id="telepon" name="telepon" required>
                    </div>
                </div>
                
                <hr class="my-4">

                <button class="w-100 btn btn-add-to-cart btn-lg" type="submit">Bayar Sekarang</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap 5 JavaScript dari CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
