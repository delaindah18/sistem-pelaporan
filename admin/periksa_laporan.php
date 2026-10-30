<?php
include '../includes/session.php';
cekLogin();
cekRole('admin');
include '../config/database.php';

$id = $_GET['id'];
$jenis = $_GET['jenis'];

if ($jenis == 'satpam') {
    $stmt = $pdo->prepare("SELECT l.*, p.nama, p.email, p.no_hp FROM laporan_satpam l 
                           JOIN petugas p ON l.id_petugas = p.id_petugas WHERE l.id_laporan = ?");
} else {
    $stmt = $pdo->prepare("SELECT l.*, p.nama, p.email, p.no_hp FROM laporan_kebersihan l 
                           JOIN petugas p ON l.id_petugas = p.id_petugas WHERE l.id_laporan = ?");
}
$stmt->execute([$id]);
$data = $stmt->fetch();
if (!$data) die("Laporan tidak ditemukan.");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Periksa Laporan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
    <h2>Periksa Laporan <?= ucfirst($jenis) ?></h2>
    <table class="detail">
        <tr><td>Tanggal</td><td><?= htmlspecialchars($data['tanggal']) ?></td></tr>
        <tr><td>Petugas</td><td><?= htmlspecialchars($data['nama']) ?></td></tr>
        <?php if ($jenis == 'satpam'): ?>
            <tr><td>Waktu</td><td><?= htmlspecialchars($data['waktu']) ?></td></tr>
            <tr><td>Jenis Kegiatan</td><td><?= htmlspecialchars($data['jenis_kegiatan']) ?></td></tr>
            <tr><td>Lokasi</td><td><?= htmlspecialchars($data['lokasi']) ?></td></tr>
            <tr><td>Uraian</td><td><?= htmlspecialchars($data['uraian']) ?></td></tr>
            <tr><td>Rincian Pekerjaan</td><td><?= nl2br(htmlspecialchars($data['rincian_pekerjaan'])) ?></td></tr>
            <tr><td>Kejadian</td><td><?= htmlspecialchars($data['kejadian']) ?></td></tr>
            <tr><td>Tindak Lanjut</td><td><?= htmlspecialchars($data['tindak_lanjut']) ?></td></tr>
        <?php else: ?>
            <tr><td>Area Kerja</td><td><?= htmlspecialchars($data['area_kerja']) ?></td></tr>
            <tr><td>Jenis Pekerjaan</td><td><?= htmlspecialchars($data['jenis_pekerjaan']) ?></td></tr>
            <tr><td>Rincian Pekerjaan</td><td><?= nl2br(htmlspecialchars($data['rincian_pekerjaan'])) ?></td></tr>
            <tr><td>Keterangan</td><td><?= htmlspecialchars($data['keterangan']) ?></td></tr>
        <?php endif; ?>
        <tr><td>Kondisi</td><td><?= htmlspecialchars($data['kondisi']) ?></td></tr>
        <tr><td>Dokumentasi</td><td>
            <?php if ($data['dokumentasi']): ?>
                <img src="../uploads/dokumentasi/<?= htmlspecialchars($data['dokumentasi']) ?>" width="300">
            <?php else: ?>Tidak ada foto<?php endif; ?>
        </td></tr>
        <tr><td>Status</td><td><?= $data['status'] ?></td></tr>
    </table>

    <form action="update_status.php" method="POST">
        <input type="hidden" name="id" value="<?= $id ?>">
        <input type="hidden" name="jenis" value="<?= $jenis ?>">
        <input type="hidden" name="email_petugas" value="<?= htmlspecialchars($data['email']) ?>">
        <input type="hidden" name="no_hp_petugas" value="<?= htmlspecialchars($data['no_hp']) ?>">
        <input type="hidden" name="nama_petugas" value="<?= htmlspecialchars($data['nama']) ?>">
        <label>Status</label>
        <select name="status" required>
            <option value="Disetujui">Disetujui</option>
            <option value="Perlu Perbaikan">Perlu Perbaikan</option>
        </select>
        <label>Catatan Admin</label>
        <textarea name="catatan_admin" rows="3"></textarea>
        <button type="submit">Simpan</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>