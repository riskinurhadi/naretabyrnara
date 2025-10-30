<?php
session_start();
header('Content-Type: application/json');

// Periksa apakah sesi keranjang ada dan hapus
if (isset($_SESSION['cart'])) {
    unset($_SESSION['cart']);
    echo json_encode(['status' => 'success', 'message' => 'Keranjang berhasil dikosongkan.']);
} else {
    echo json_encode(['status' => 'success', 'message' => 'Keranjang sudah kosong.']);
}
?>
