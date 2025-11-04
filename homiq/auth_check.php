<?php
// auth_check.php
session_start();

// Cek apakah user sudah login atau belum
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Jika belum, redirect ke halaman login
    header("Location: login.php");
    exit;
}

// Opsional: Anda bisa menambahkan cek role di sini
/*
if ($_SESSION['role'] !== 'admin') {
    die("Anda tidak memiliki akses ke halaman ini.");
}
*/
?>