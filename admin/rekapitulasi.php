<?php
include '../includes/session.php';
cekLogin();
cekRole('admin');
include '../config/database.php';

$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');
$jenis = $_GET['jenis'] ?? 'satpam';

if ($jenis == 'satpam') {
    $stmt = $pdo->prepare("SELECT l.*, p.nama FROM laporan_satpam l 
                           JOIN petugas p ON l.id_petugas = p.id_petugas 
                           WHERE MONTH(l.tanggal) = ? AND YEAR(l.tanggal) = ?
                           ORDER BY l.tanggal ASC");
} else {
    $stmt = $pdo->prepare("SELECT l.*, p.nama FROM laporan_kebersihan l 
                           JOIN petugas p ON l.id_petugas = p.id_petugas 
                           WHERE MONTH(l.tanggal) = ? AND YEAR(l.tanggal) = ?
                           ORDER BY l.tanggal ASC");
}
$stmt->execute([$bulan, $tahun]);
$data = $stmt->fetchAll();

$nama_bulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Rekapitulasi Laporan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h2>Rekapitulasi Laporan</h2>
        <div class="btn-group no-print">
            <a href="export_excel.php?jenis=<?= $jenis ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn-primary">📊 Export Excel</a>
            <a href="export_pdf.php?jenis=<?= $jenis ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn-primary" target="_blank">📄 Export PDF</a>
            <button onclick="window.print()" class="btn-primary">🖨️ Cetak</button>
        </div>
    </div>

    <form method="GET" class="filter-form no-print">
        <div class="filter-row">
            <div><label>Jenis</label>
                <select name="jenis">
                    <option value="satpam" <?= $jenis=='satpam'?'selected':'' ?>>Satpam</option>
                    <option value="kebersihan" <?= $jenis=='kebersihan'?'selected':'' ?>>Kebersihan</option>
                </select>
            </div>
            <div><label>Bulan</label>
                <select name="bulan">
                    <?php foreach ($nama_bulan as $num => $nm): ?>
                        <option value="<?= $num ?>" <?= $bulan==$num?'selected':'' ?>><?= $nm ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div><label>Tahun</label>
                <select name="tahun">
                    <?php for ($y = 2024; $y <= 2030; $y++): ?>
                        <option value="<?= $y ?>" <?= $tahun==$y?'selected':'' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div><button type="submit">Tampilkan</button></div>
        </div>
    </form>

    <div class="rekap-header">
        <h3>Rekapitulasi Laporan <?= ucfirst($jenis) ?></h3>
        <p>Periode: <?= $nama_bulan[(int)$bulan] ?> <?= $tahun ?> | Total: <b><?= count($data) ?></b> laporan</p>
    </div>

    <table>
        <tr>
            <th>No</th><th>Tanggal</th><th>Petugas</th>
            <?php if ($jenis == 'satpam'): ?>
                <th>Jenis Kegiatan</th><th>Lokasi</th>
            <?php else: ?>
                <th>Area Kerja</th><th>Jenis Pekerjaan</th>
            <?php endif; ?>
            <th>Kondisi</th><th>Status</th>
        </tr>
        <?php if (count($data) > 0): ?>
            <?php $no = 1; foreach ($data as $r): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($r['tanggal']) ?></td>
                <td><?= htmlspecialchars($r['nama']) ?></td>
                <?php if ($jenis == 'satpam'): ?>
                    <td><?= htmlspecialchars($r['jenis_kegiatan']) ?></td>
                    <td><?= htmlspecialchars($r['lokasi']) ?></td>
                <?php else: ?>
                    <td><?= htmlspecialchars($r['area_kerja']) ?></td>
                    <td><?= htmlspecialchars($r['jenis_pekerjaan']) ?></td>
                <?php endif; ?>
                <td><?= htmlspecialchars($r['kondisi']) ?></td>
                <td><?= $r['status'] ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7" style="text-align:center;">Tidak ada data.</td></tr>
        <?php endif; ?>
    </table>

    <br><a href="dashboard.php" class="btn-back no-print">← Kembali</a>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>