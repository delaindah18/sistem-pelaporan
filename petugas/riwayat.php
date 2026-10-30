<?php
include '../includes/session.php';
cekLogin();
cekRole('petugas');
include '../config/database.php';

$id = $_SESSION['id'];
$kategori = $_SESSION['kategori'];

if ($kategori == 'satpam') {
    $stmt = $pdo->prepare("SELECT * FROM laporan_satpam WHERE id_petugas = ? ORDER BY created_at DESC");
} else {
    $stmt = $pdo->prepare("SELECT * FROM laporan_kebersihan WHERE id_petugas = ? ORDER BY created_at DESC");
}
$stmt->execute([$id]);
$laporan = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Laporan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
    <h2>Riwayat Laporan Saya</h2>
    <table>
        <tr>
            <th>Tanggal</th>
            <?php if ($kategori == 'satpam'): ?>
                <th>Jenis Kegiatan</th>
                <th>Lokasi</th>
            <?php else: ?>
                <th>Area Kerja</th>
                <th>Jenis Pekerjaan</th>
            <?php endif; ?>
            <th>Kondisi</th>
            <th>Status</th>
            <th>Catatan Admin</th>
        </tr>
        <?php foreach ($laporan as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['tanggal']) ?></td>
            <?php if ($kategori == 'satpam'): ?>
                <td><?= htmlspecialchars($row['jenis_kegiatan']) ?></td>
                <td><?= htmlspecialchars($row['lokasi']) ?></td>
            <?php else: ?>
                <td><?= htmlspecialchars($row['area_kerja']) ?></td>
                <td><?= htmlspecialchars($row['jenis_pekerjaan']) ?></td>
            <?php endif; ?>
            <td><?= htmlspecialchars($row['kondisi']) ?></td>
            <td><span class="badge badge-<?= strtolower(str_replace(' ','',$row['status'])) ?>"><?= $row['status'] ?></span></td>
            <td><?= htmlspecialchars($row['catatan_admin']) ?: '-' ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>