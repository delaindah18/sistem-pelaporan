<?php
include '../includes/session.php';
cekLogin();
cekRole('admin');

$keyword = $_GET['keyword'] ?? '';
$jenis = $_GET['jenis'] ?? 'satpam';
$tanggal_dari = $_GET['tanggal_dari'] ?? '';
$tanggal_sampai = $_GET['tanggal_sampai'] ?? '';

$results = [];
$sudah_cari = false;

// Cek apakah user sudah submit form
if (isset($_GET['submit'])) {
    $sudah_cari = true;

    if ($jenis == 'satpam') {
        $sql = "SELECT l.*, p.nama FROM laporan_satpam l 
                JOIN petugas p ON l.id_petugas = p.id_petugas WHERE 1=1";
    } else {
        $sql = "SELECT l.*, p.nama FROM laporan_kebersihan l 
                JOIN petugas p ON l.id_petugas = p.id_petugas WHERE 1=1";
    }
    $params = [];

    if (!empty($keyword)) {
        $sql .= " AND p.nama LIKE ?";
        $params[] = "%$keyword%";
    }
    if (!empty($tanggal_dari)) {
        $sql .= " AND l.tanggal >= ?";
        $params[] = $tanggal_dari;
    }
    if (!empty($tanggal_sampai)) {
        $sql .= " AND l.tanggal <= ?";
        $params[] = $tanggal_sampai;
    }

    $sql .= " ORDER BY l.tanggal DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cari Laporan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .search-header {
            background: linear-gradient(135deg, #1a5276, #2874a6);
            color: #fff;
            padding: 25px 30px;
            border-radius: 12px;
            margin-bottom: 25px;
        }
        .search-header h2 {
            color: #fff;
            margin-bottom: 5px;
        }
        .search-header p {
            opacity: 0.9;
            font-size: 14px;
        }
        .search-form {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            align-items: end;
        }
        .form-row label {
            margin-top: 0;
            font-size: 13px;
            color: #555;
        }
        .form-row input,
        .form-row select {
            margin-top: 5px;
        }
        .btn-search {
            background: #1a5276;
            color: #fff;
            padding: 11px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
        }
        .btn-search:hover {
            background: #154360;
        }
        .result-info {
            background: #eaf3fa;
            padding: 12px 18px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 14px;
            color: #1a5276;
            border-left: 4px solid #1a5276;
        }
        .empty-result {
            text-align: center;
            padding: 60px 20px;
            background: #f8fbfd;
            border-radius: 12px;
            border: 2px dashed #e1eaf2;
        }
        .empty-result .icon {
            font-size: 60px;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        .empty-result h3 {
            color: #666;
            margin-bottom: 8px;
        }
        .empty-result p {
            color: #999;
            font-size: 14px;
        }
    </style>
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">

    <!-- HEADER -->
    <div class="search-header">
        <h2>🔍 Cari Laporan</h2>
        <p>Filter laporan berdasarkan jenis, nama petugas, dan rentang tanggal</p>
    </div>

    <!-- FORM PENCARIAN -->
    <form method="GET" class="search-form">
        <input type="hidden" name="submit" value="1">
        <div class="form-row">
            <div>
                <label>Jenis Laporan</label>
                <select name="jenis">
                    <option value="satpam" <?= $jenis=='satpam'?'selected':'' ?>>🛡️ Satpam</option>
                    <option value="kebersihan" <?= $jenis=='kebersihan'?'selected':'' ?>>🧹 Kebersihan</option>
                </select>
            </div>
            <div>
                <label>Nama Petugas</label>
                <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" placeholder="Contoh: Ahmad">
            </div>
            <div>
                <label>Tanggal Dari</label>
                <input type="date" name="tanggal_dari" value="<?= $tanggal_dari ?>">
            </div>
            <div>
                <label>Tanggal Sampai</label>
                <input type="date" name="tanggal_sampai" value="<?= $tanggal_sampai ?>">
            </div>
            <div>
                <button type="submit" class="btn-search">🔍 Cari Laporan</button>
            </div>
        </div>
    </form>

    <!-- HASIL PENCARIAN -->
    <?php if ($sudah_cari): ?>
        <?php if (count($results) > 0): ?>
            <div class="result-info">
                ✅ Ditemukan <b><?= count($results) ?></b> laporan 
                <?= $jenis == 'satpam' ? '🛡️ Satpam' : '🧹 Kebersihan' ?>
                <?= $keyword ? "dari petugas <b>$keyword</b>" : '' ?>
            </div>
            <table>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Petugas</th>
                    <th><?= $jenis == 'satpam' ? 'Jenis Kegiatan' : 'Area Kerja' ?></th>
                    <th>Kondisi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                <?php $no = 1; foreach ($results as $r): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= date('d/m/Y', strtotime($r['tanggal'])) ?></td>
                    <td><?= htmlspecialchars($r['nama']) ?></td>
                    <td><?= $jenis == 'satpam' ? htmlspecialchars($r['jenis_kegiatan']) : htmlspecialchars($r['area_kerja']) ?></td>
                    <td><?= htmlspecialchars($r['kondisi']) ?></td>
                    <td>
                        <span class="badge badge-<?= strtolower(str_replace(' ','',$r['status'])) ?>">
                            <?= $r['status'] ?>
                        </span>
                    </td>
                    <td>
                        <a href="detail_laporan.php?id=<?= $r['id_laporan'] ?>&jenis=<?= $jenis ?>" style="color:#1a5276;font-weight:600;">👁️ Detail</a> |
                        <a href="periksa_laporan.php?id=<?= $r['id_laporan'] ?>&jenis=<?= $jenis ?>">✏️ Periksa</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <div class="empty-result">
                <div class="icon">🔍</div>
                <h3>Tidak Ada Laporan Ditemukan</h3>
                <p>Coba ubah filter pencarian Anda atau pastikan data laporan sudah ada.</p>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="empty-result">
            <div class="icon">📋</div>
            <h3>Silakan Isi Filter Pencarian</h3>
            <p>Gunakan form di atas untuk mencari laporan berdasarkan kriteria yang diinginkan.</p>
        </div>
    <?php endif; ?>

    <br>
    <a href="dashboard.php" class="btn-back">← Kembali ke Dashboard</a>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>