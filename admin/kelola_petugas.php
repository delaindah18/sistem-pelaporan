<?php
include '../includes/session.php';
cekLogin();
cekRole('admin');
include '../config/database.php';

$petugas = $pdo->query("SELECT * FROM petugas ORDER BY kategori_tugas, nama")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Data Petugas</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h2>Kelola Data Petugas</h2>
        <a href="tambah_petugas.php" class="btn-primary">+ Tambah Petugas</a>
    </div>

    <?php if (isset($_GET['sukses'])): ?>
        <div class="alert-success">Data petugas berhasil diproses!</div>
    <?php endif; ?>

    <table>
        <tr><th>No</th><th>Nama</th><th>Kategori</th><th>Username</th><th>Email</th><th>No HP</th><th>Aksi</th></tr>
        <?php $no = 1; foreach ($petugas as $p): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($p['nama']) ?></td>
            <td><?= ucfirst($p['kategori_tugas']) ?></td>
            <td><?= htmlspecialchars($p['username']) ?></td>
            <td><?= htmlspecialchars($p['email']) ?></td>
            <td><?= htmlspecialchars($p['no_hp']) ?></td>
            <td>
                <a href="edit_petugas.php?id=<?= $p['id_petugas'] ?>">Edit</a> |
                <a href="hapus_petugas.php?id=<?= $p['id_petugas'] ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <br>
    <a href="dashboard.php" class="btn-back">← Kembali ke Dashboard</a>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>