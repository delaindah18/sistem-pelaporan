<?php
session_start();
if (isset($_SESSION['login'])) {
    if ($_SESSION['role'] == 'petugas') {
        header("Location: ../petugas/dashboard.php");
    } else {
        header("Location: ../admin/dashboard.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Sistem Pelaporan PPPK</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="login-wrapper">
    <div class="login-container">
        <div class="logo-area">
            <img src="../assets/img/logo.png" alt="Logo" class="logo-login">
        </div>
        <h2>Sistem Pelaporan Kegiatan</h2>
        <p class="subtitle">PPPK Satpam & Kebersihan<br>BPS Kota Medan</p>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert-error">Username atau password salah!</div>
        <?php endif; ?>
        <?php if (isset($_GET['logout'])): ?>
            <div class="alert-success">Anda telah logout.</div>
        <?php endif; ?>

        <form action="proses_login.php" method="POST">
            <label>Username</label>
            <input type="text" name="username" placeholder="Masukkan username" required>
            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password" required>
            <label>Login Sebagai</label>
            <select name="role" required>
                <option value="petugas">Petugas</option>
                <option value="admin">Administrator</option>
            </select>
            <button type="submit">Login</button>
        </form>
        <p class="footer-login">&copy; 2026 ITB Indonesia</p>
    </div>
</div>
</body>
</html>