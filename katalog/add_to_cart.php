<?php
session_start();
header('Content-Type: application/json'); // Memberi tahu browser bahwa responsnya adalah JSON

// Data produk yang sama seperti di index.php
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $productId = (int)$_POST['id'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $productToAdd = null;
    foreach ($products as $product) {
        if ($product['id'] === $productId) {
            $productToAdd = $product;
            break;
        }
    }

    if ($productToAdd) {
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity']++;
        } else {
            $_SESSION['cart'][$productId] = [
                'id' => $productToAdd['id'],
                'name' => $productToAdd['name'],
                'price' => $productToAdd['price'],
                'quantity' => 1
            ];
        }

        $totalItems = 0;
        foreach ($_SESSION['cart'] as $item) {
            $totalItems += $item['quantity'];
        }

        echo json_encode(['status' => 'success', 'message' => 'Produk berhasil ditambahkan!', 'totalItems' => $totalItems]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Produk tidak ditemukan!']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Permintaan tidak valid.']);
}
?>