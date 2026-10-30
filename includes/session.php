<?php
session_start();

// Include koneksi database agar $pdo bisa diakses di header (untuk notifikasi)
if (!isset($pdo)) {
    include_once __DIR__ . '/../config/database.php';
}

function cekLogin() {
    if (!isset($_SESSION['login'])) {
        header("Location: ../auth/login.php");
        exit;
    }
}

function cekRole($role) {
    if ($_SESSION['role'] != $role) {
        header("Location: ../auth/login.php");
        exit;
    }
}
?>