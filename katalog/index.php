<?php
session_start(); // Mengaktifkan sesi untuk menyimpan data keranjang

// Data produk (simulasi dari database)
$products = [
    [
        'id' => 1,
        'name' => 'Paket Standar',
        'price' => 2500000,
        'image' => 'https://i.pinimg.com/736x/cb/46/6f/cb466f7899e72eb050df4c189e6d02e8.jpg',
        'description' => 'Website profil sederhana, 5 halaman, desain responsif, dan optimasi dasar SEO.'
    ],
    [
        'id' => 2,
        'name' => 'Paket Bisnis',
        'price' => 5000000,
        'image' => 'https://i.pinimg.com/736x/cb/46/6f/cb466f7899e72eb050df4c189e6d02e8.jpg',
        'description' => 'Website profesional, 10 halaman, galeri, formulir kontak, integrasi Google Maps, dan SEO tingkat lanjut.'
    ],
    [
        'id' => 3,
        'name' => 'Paket Premium',
        'price' => 10000000,
        'image' => 'https://i.pinimg.com/736x/cb/46/6f/cb466f7899e72eb050df4c189e6d02e8.jpg',
        'description' => 'Website kustom, fitur e-commerce, blog, integrasi payment gateway, dan dukungan prioritas 24/7.'
    ],
    [
        'id' => 4,
        'name' => 'Paket Kustom',
        'price' => 0, // Harga akan ditentukan setelah diskusi
        'image' => 'https://i.pinimg.com/736x/cb/46/6f/cb466f7899e72eb050df4c189e6d02e8.jpg',
        'description' => 'Solusi website yang disesuaikan sepenuhnya dengan kebutuhan unik perusahaan Anda.'
    ]
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jasa Pembuatan Website | rnara.id</title>

    <!-- Google Fonts - Poppins untuk font profesional -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS dari CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome untuk ikon keranjang -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Internal CSS untuk kustomisasi dan tema elegan -->
    <style>
        :root {
            --primary-color: #4A4E69; /* Warna biru gelap yang elegan */
            --secondary-color: #F8F9FA; /* Warna abu-abu terang */
            --accent-color: #FFC107; /* Warna emas untuk aksen */
            --text-color: #212529; /* Warna teks gelap */
            --light-gray: #E9ECEF; /* Warna abu-abu untuk border */
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--secondary-color);
            color: var(--text-color);
        }

        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-color: #fff;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .card-img-top {
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
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
        
        /* Tambahkan CSS untuk tombol "Kosongkan Keranjang" */
        .btn-clear-cart {
            background-color: #dc3545; /* Warna merah */
            border-color: #dc3545;
            color: #fff;
        }

        .btn-clear-cart:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        .cart-btn {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 1000;
            background-color: var(--primary-color);
            color: #fff;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .cart-btn:hover {
            background-color: #373A53;
        }

        .modal-content {
            border-radius: 1rem;
        }

        .modal-header, .modal-footer {
            border: none;
        }
        
        .modal-header {
            border-bottom: 1px solid var(--light-gray);
        }
    </style>
</head>
<body>

    <!-- Header dengan Logo dan Judul -->
    <div class="container mt-5">
        <header class="text-center mb-5">
            <h1 class="display-4 fw-bold" style="color: var(--primary-color);">rnara.id</h1>
            <p class="lead text-muted">Solusi terdepan untuk website bisnis Anda.</p>
        </header>

        <!-- Daftar Produk -->
        <div class="row g-4" id="product-list">
            <?php foreach ($products as $product): ?>
                <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                    <div class="card h-100">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($product['description']); ?></p>
                            <div class="mt-auto">
                                <?php if ($product['price'] > 0): ?>
                                    <p class="h4 fw-bold">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                                    <button class="btn btn-block btn-add-to-cart mt-2" data-id="<?php echo $product['id']; ?>">
                                        <i class="fas fa-shopping-cart me-2"></i> Tambah ke Keranjang
                                    </button>
                                <?php else: ?>
                                    <p class="h4 fw-bold">Harga Dapat Disesuaikan</p>
                                    <a href="#" class="btn btn-block btn-add-to-cart mt-2">
                                        <i class="fas fa-comment-dots me-2"></i> Konsultasi Sekarang
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Tombol Keranjang Belanja -->
    <button class="btn cart-btn" data-bs-toggle="modal" data-bs-target="#cartModal">
        <i class="fas fa-shopping-cart"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-item-count">0</span>
    </button>

    <!-- Modal Keranjang Belanja -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="cartModalLabel" style="color: var(--primary-color);">Keranjang Belanja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="cart-items">
                        <!-- Item keranjang akan ditampilkan di sini oleh JavaScript -->
                    </div>
                    <div id="cart-empty" class="text-center text-muted mt-3" style="display: none;">
                        Keranjang masih kosong.
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total:</span>
                        <span id="cart-total">Rp 0</span>
                    </div>
                </div>
                <div class="modal-footer d-flex flex-wrap justify-content-between">
                    <button type="button" class="btn btn-secondary flex-grow-1 me-2 mb-2 mb-md-0" data-bs-dismiss="modal">Lanjutkan Belanja</button>
                    <button type="button" class="btn btn-clear-cart flex-grow-1 me-2 mb-2 mb-md-0" id="clear-cart-btn">Kosongkan Keranjang</button>
                    <button type="button" class="btn btn-add-to-cart flex-grow-1" id="checkout-btn">Lanjutkan Pembayaran</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JavaScript dari CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript untuk fungsionalitas keranjang -->
    <script>
        const cartItemsContainer = document.getElementById('cart-items');
        const cartTotal = document.getElementById('cart-total');
        const checkoutBtn = document.getElementById('checkout-btn');
        const cartEmpty = document.getElementById('cart-empty');
        const cartItemCount = document.getElementById('cart-item-count');
        const clearCartBtn = document.getElementById('clear-cart-btn');

        // Fungsi untuk mengambil data keranjang dari server dan menampilkannya
        const fetchCart = async () => {
            const response = await fetch('get_cart.php');
            const data = await response.json();
            
            let total = 0;
            let totalItems = 0;
            
            if (data.cart && Object.keys(data.cart).length > 0) {
                cartEmpty.style.display = 'none';
                cartItemsContainer.innerHTML = '';
                
                for (const productId in data.cart) {
                    const item = data.cart[productId];
                    const itemTotal = item.price * item.quantity;
                    total += itemTotal;
                    totalItems += item.quantity;
                    
                    const itemElement = document.createElement('div');
                    itemElement.classList.add('d-flex', 'justify-content-between', 'align-items-center', 'mb-2');
                    itemElement.innerHTML = `
                        <div>
                            <strong>${item.name}</strong><br>
                            <small class="text-muted">${item.quantity} x Rp ${item.price.toLocaleString('id-ID')}</small>
                        </div>
                        <div>
                            <strong>Rp ${itemTotal.toLocaleString('id-ID')}</strong>
                        </div>
                    `;
                    cartItemsContainer.appendChild(itemElement);
                }
            } else {
                cartEmpty.style.display = 'block';
                cartItemsContainer.innerHTML = '';
            }

            cartTotal.textContent = `Rp ${total.toLocaleString('id-ID')}`;
            cartItemCount.textContent = totalItems;
            cartItemCount.style.display = totalItems > 0 ? 'inline-block' : 'none';
        };

        // Event listener untuk tombol "Tambah ke Keranjang"
        const productList = document.getElementById('product-list');
        productList.addEventListener('click', async (e) => {
            if (e.target.classList.contains('btn-add-to-cart')) {
                const productId = e.target.dataset.id;
                
                const formData = new FormData();
                formData.append('id', productId);
                
                await fetch('add_to_cart.php', {
                    method: 'POST',
                    body: formData
                });
                
                await fetchCart();
            }
        });
        
        // Tambahkan event listener untuk tombol "Kosongkan Keranjang"
        clearCartBtn.addEventListener('click', async () => {
            // Mengirim permintaan ke server untuk menghapus keranjang
            const response = await fetch('clear_cart.php');
            const result = await response.json();
            
            if (result.status === 'success') {
                alert('Keranjang berhasil dikosongkan!');
                await fetchCart(); // Memperbarui tampilan keranjang
            } else {
                alert('Gagal mengosongkan keranjang!');
            }
        });

        // Event listener untuk tombol "Lanjutkan Pembayaran"
        checkoutBtn.addEventListener('click', () => {
            // Mengarahkan pengguna ke halaman checkout.php
            window.location.href = 'checkout.php';
        });
        
        // Panggil fetchCart saat halaman dimuat pertama kali dan saat modal dibuka
        document.addEventListener('DOMContentLoaded', fetchCart);
        const cartModal = document.getElementById('cartModal');
        cartModal.addEventListener('show.bs.modal', fetchCart);
    </script>
</body>
</html>
