<?php
session_start();
header('Content-Type: application/json');

// Kirim data keranjang dari sesi ke JavaScript
echo json_encode(['cart' => $_SESSION['cart'] ?? []]);
?>