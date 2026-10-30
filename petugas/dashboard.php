<?php
include '../includes/session.php';
cekLogin();
cekRole('petugas');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Petugas</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
    <h2>Dashboard Petugas</h2>
    <p>Selamat datang, <b><?= htmlspecialchars($_SESSION['nama']) ?></b> (<?= $_SESSION['kategori'] ?>)</p>

    <?php if (isset($_GET['sukses'])): ?>
        <div class="alert-success">Laporan berhasil dikirim! Notifikasi telah dikirim ke admin.</div>
    <?php endif; ?>

    <div class="menu-grid">
        <?php if ($_SESSION['kategori'] == 'satpam'): ?>
            <a href="laporan_satpam.php" class="menu-card">
                <div class="menu-icon">🛡️</div>
                <h3>Isi Laporan Satpam</h3>
                <p>Catat kegiatan penjagaan dan keamanan</p>
            </a>
        <?php else: ?>
            <a href="laporan_kebersihan.php" class="menu-card">
                <div class="menu-icon">🧹</div>
                <h3>Isi Laporan Kebersihan</h3>
                <p>Catat kegiatan pembersihan area kantor</p>
            </a>
        <?php endif; ?>
        <a href="riwayat.php" class="menu-card">
            <div class="menu-icon">📋</div>
            <h3>Riwayat Laporan</h3>
            <p>Lihat laporan yang pernah dibuat</p>
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>