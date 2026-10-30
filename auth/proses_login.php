<?php
session_start();
include '../config/database.php';

$username = $_POST['username'];
$password = $_POST['password'];
$role     = $_POST['role'];

if ($role == 'petugas') {
    $stmt = $pdo->prepare("SELECT * FROM petugas WHERE username = ?");
} else {
    $stmt = $pdo->prepare("SELECT * FROM administrator WHERE username = ?");
}
$stmt->execute([$username]);
$data = $stmt->fetch();

if ($data && password_verify($password, $data['password'])) {
    $_SESSION['login'] = true;
    $_SESSION['role'] = $role;
    if ($role == 'petugas') {
        $_SESSION['id'] = $data['id_petugas'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['kategori'] = $data['kategori_tugas'];
        $_SESSION['email'] = $data['email'];
        $_SESSION['no_hp'] = $data['no_hp'];
        header("Location: ../petugas/dashboard.php");
    } else {
        $_SESSION['id'] = $data['id_admin'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['email'] = $data['email'];
        header("Location: ../admin/dashboard.php");
    }
} else {
    header("Location: login.php?error=1");
}
?>