<?php
include '../includes/session.php';
cekLogin();
cekRole('admin');
include '../config/database.php';

// ============ STATISTIK UTAMA ============
$total_satpam = $pdo->query("SELECT COUNT(*) FROM laporan_satpam")->fetchColumn();
$total_kebersihan = $pdo->query("SELECT COUNT(*) FROM laporan_kebersihan")->fetchColumn();
$total_petugas = $pdo->query("SELECT COUNT(*) FROM petugas")->fetchColumn();

// Laporan menunggu diperiksa
$menunggu = $pdo->query("SELECT 
    (SELECT COUNT(*) FROM laporan_satpam WHERE status='Diajukan') + 
    (SELECT COUNT(*) FROM laporan_kebersihan WHERE status='Diajukan')")->fetchColumn();

// Disetujui
$disetujui = $pdo->query("SELECT 
    (SELECT COUNT(*) FROM laporan_satpam WHERE status='Disetujui') + 
    (SELECT COUNT(*) FROM laporan_kebersihan WHERE status='Disetujui')")->fetchColumn();

// Perlu Perbaikan
$perbaikan = $pdo->query("SELECT 
    (SELECT COUNT(*) FROM laporan_satpam WHERE status='Perlu Perbaikan') + 
    (SELECT COUNT(*) FROM laporan_kebersihan WHERE status='Perlu Perbaikan')")->fetchColumn();

// ============ DATA GRAFIK LINE - 7 HARI TERAKHIR ============
$labels_7hari = [];
$data_satpam_7hari = [];
$data_kebersihan_7hari = [];

for ($i = 6; $i >= 0; $i--) {
    $tanggal = date('Y-m-d', strtotime("-$i day"));
    $labels_7hari[] = date('d M', strtotime("-$i day"));

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM laporan_satpam WHERE tanggal = ?");
    $stmt->execute([$tanggal]);
    $data_satpam_7hari[] = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM laporan_kebersihan WHERE tanggal = ?");
    $stmt->execute([$tanggal]);
    $data_kebersihan_7hari[] = (int)$stmt->fetchColumn();
}

// ============ DATA GRAFIK BAR - PER BULAN ============
$labels_bulan = [];
$data_satpam_bulan = [];
$data_kebersihan_bulan = [];

for ($i = 5; $i >= 0; $i--) {
    $bulan = date('m', strtotime("-$i month"));
    $tahun = date('Y', strtotime("-$i month"));
    $labels_bulan[] = date('M Y', strtotime("-$i month"));

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM laporan_satpam WHERE MONTH(tanggal)=? AND YEAR(tanggal)=?");
    $stmt->execute([$bulan, $tahun]);
    $data_satpam_bulan[] = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM laporan_kebersihan WHERE MONTH(tanggal)=? AND YEAR(tanggal)=?");
    $stmt->execute([$bulan, $tahun]);
    $data_kebersihan_bulan[] = (int)$stmt->fetchColumn();
}

// ============ LAPORAN TERBARU ============
$satpam = $pdo->query("SELECT l.*, p.nama FROM laporan_satpam l 
                       JOIN petugas p ON l.id_petugas = p.id_petugas 
                       ORDER BY l.created_at DESC LIMIT 5")->fetchAll();
$kebersihan = $pdo->query("SELECT l.*, p.nama FROM laporan_kebersihan l 
                           JOIN petugas p ON l.id_petugas = p.id_petugas 
                           ORDER BY l.created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #1a5276, #2874a6);
            color: #fff;
            padding: 25px 30px;
            border-radius: 12px;
            margin-bottom: 25px;
        }
        .dashboard-header h2 {
            color: #fff;
            margin-bottom: 5px;
        }
        .dashboard-header p {
            opacity: 0.9;
            font-size: 14px;
        }
        .stat-grid-4 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }
        .stat-card-new {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            transition: transform 0.3s;
        }
        .stat-card-new:hover {
            transform: translateY(-3px);
        }
        .stat-icon-new {
            width: 55px;
            height: 55px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: #fff;
        }
        .bg-blue { background: linear-gradient(135deg, #1a5276, #2874a6); }
        .bg-green { background: linear-gradient(135deg, #27ae60, #2ecc71); }
        .bg-orange { background: linear-gradient(135deg, #e67e22, #f39c12); }
        .bg-red { background: linear-gradient(135deg, #c0392b, #e74c3c); }
        .stat-info-new h3 {
            font-size: 26px;
            color: #1a5276;
            margin-bottom: 2px;
        }
        .stat-info-new p {
            font-size: 13px;
            color: #777;
        }
        .chart-grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        .chart-box-new {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        .chart-box-new h3 {
            color: #1a5276;
            font-size: 15px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f4f8;
        }
        .chart-box-full {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }
        .chart-box-full h3 {
            color: #1a5276;
            font-size: 15px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f4f8;
        }
        .chart-container {
            position: relative;
            height: 280px;
        }
    </style>
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">

    <!-- HEADER -->
    <div class="dashboard-header">
        <h2>👋 Selamat Datang, <?= htmlspecialchars($_SESSION['nama']) ?>!</h2>
        <p>Dashboard Administrator - Sistem Pelaporan PPPK Satpam & Kebersihan BPS Kota Medan</p>
    </div>

    <?php if (isset($_GET['sukses'])): ?>
        <div class="alert-success">✅ Data berhasil diproses!</div>
    <?php endif; ?>

    <!-- STATISTIK 4 KARTU -->
    <div class="stat-grid-4">
        <div class="stat-card-new">
            <div class="stat-icon-new bg-blue">🛡️</div>
            <div class="stat-info-new">
                <h3><?= $total_satpam ?></h3>
                <p>Laporan Satpam</p>
            </div>
        </div>
        <div class="stat-card-new">
            <div class="stat-icon-new bg-green">🧹</div>
            <div class="stat-info-new">
                <h3><?= $total_kebersihan ?></h3>
                <p>Laporan Kebersihan</p>
            </div>
        </div>
        <div class="stat-card-new">
            <div class="stat-icon-new bg-orange">⏳</div>
            <div class="stat-info-new">
                <h3><?= $menunggu ?></h3>
                <p>Menunggu Diperiksa</p>
            </div>
        </div>
        <div class="stat-card-new">
            <div class="stat-icon-new bg-red">✅</div>
            <div class="stat-info-new">
                <h3><?= $disetujui ?></h3>
                <p>Disetujui</p>
            </div>
        </div>
    </div>

    <!-- GRAFIK LINE & DONUT -->
    <div class="chart-grid-2">
        <div class="chart-box-new">
            <h3>📈 Tren Laporan 7 Hari Terakhir</h3>
            <div class="chart-container">
                <canvas id="chartLine"></canvas>
            </div>
        </div>
        <div class="chart-box-new">
            <h3>🥧 Distribusi Status Laporan</h3>
            <div class="chart-container">
                <canvas id="chartDonut"></canvas>
            </div>
        </div>
    </div>

    <!-- GRAFIK BAR -->
    <div class="chart-box-full">
        <h3>📊 Perbandingan Laporan 6 Bulan Terakhir</h3>
        <div class="chart-container" style="height: 300px;">
            <canvas id="chartBar"></canvas>
        </div>
    </div>

    <!-- MENU CEPAT -->
    <h3>⚡ Menu Cepat</h3>
    <div class="menu-grid">
        <a href="kelola_petugas.php" class="menu-card">
            <div class="menu-icon">👥</div>
            <h3>Kelola Petugas</h3>
            <p>Tambah, ubah, hapus</p>
        </a>
        <a href="cari_laporan.php" class="menu-card">
            <div class="menu-icon">🔍</div>
            <h3>Cari Laporan</h3>
            <p>Filter berdasarkan kriteria</p>
        </a>
        <a href="rekapitulasi.php" class="menu-card">
            <div class="menu-icon">📊</div>
            <h3>Rekapitulasi</h3>
            <p>Rekap per periode</p>
        </a>
        <a href="grafik.php" class="menu-card">
            <div class="menu-icon">📈</div>
            <h3>Grafik Detail</h3>
            <p>Analisis mendalam</p>
        </a>
    </div>

    <!-- LAPORAN TERBARU -->
    <h3>🛡️ Laporan Satpam Terbaru</h3>
    <table>
        <tr><th>Tanggal</th><th>Petugas</th><th>Kegiatan</th><th>Status</th><th>Aksi</th></tr>
        <?php if (count($satpam) > 0): ?>
            <?php foreach ($satpam as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['tanggal']) ?></td>
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= htmlspecialchars($row['jenis_kegiatan']) ?></td>
                <td><span class="badge badge-<?= strtolower(str_replace(' ','',$row['status'])) ?>"><?= $row['status'] ?></span></td>
                <td>
                    <a href="detail_laporan.php?id=<?= $row['id_laporan'] ?>&jenis=satpam" style="color:#1a5276;font-weight:600;">👁️ Detail</a> |
                    <a href="periksa_laporan.php?id=<?= $row['id_laporan'] ?>&jenis=satpam">✏️ Periksa</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5" style="text-align:center;">Belum ada laporan.</td></tr>
        <?php endif; ?>
    </table>

    <h3>🧹 Laporan Kebersihan Terbaru</h3>
    <table>
        <tr><th>Tanggal</th><th>Petugas</th><th>Area</th><th>Status</th><th>Aksi</th></tr>
        <?php if (count($kebersihan) > 0): ?>
            <?php foreach ($kebersihan as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['tanggal']) ?></td>
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= htmlspecialchars($row['area_kerja']) ?></td>
                <td><span class="badge badge-<?= strtolower(str_replace(' ','',$row['status'])) ?>"><?= $row['status'] ?></span></td>
                <td>
                    <a href="detail_laporan.php?id=<?= $row['id_laporan'] ?>&jenis=kebersihan" style="color:#1a5276;font-weight:600;">👁️ Detail</a> |
                    <a href="periksa_laporan.php?id=<?= $row['id_laporan'] ?>&jenis=kebersihan">✏️ Periksa</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5" style="text-align:center;">Belum ada laporan.</td></tr>
        <?php endif; ?>
    </table>
</div>

<!-- ============ CHART.JS ============ -->
<script>
// LINE CHART
const ctxLine = document.getElementById('chartLine').getContext('2d');
new Chart(ctxLine, {
    type: 'line',
    data: {
        labels: <?= json_encode($labels_7hari) ?>,
        datasets: [
            {
                label: 'Satpam',
                data: <?= json_encode($data_satpam_7hari) ?>,
                borderColor: '#1a5276',
                backgroundColor: 'rgba(26,82,118,0.15)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#1a5276',
                pointRadius: 5,
                pointHoverRadius: 7
            },
            {
                label: 'Kebersihan',
                data: <?= json_encode($data_kebersihan_7hari) ?>,
                borderColor: '#27ae60',
                backgroundColor: 'rgba(39,174,96,0.15)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#27ae60',
                pointRadius: 5,
                pointHoverRadius: 7
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' }
        },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});

// DONUT CHART
const ctxDonut = document.getElementById('chartDonut').getContext('2d');
new Chart(ctxDonut, {
    type: 'doughnut',
    data: {
        labels: ['Menunggu', 'Disetujui', 'Perlu Perbaikan'],
        datasets: [{
            data: [<?= $menunggu ?>, <?= $disetujui ?>, <?= $perbaikan ?>],
            backgroundColor: ['#f39c12', '#27ae60', '#e74c3c'],
            borderWidth: 3,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' }
        },
        cutout: '60%'
    }
});

// BAR CHART
const ctxBar = document.getElementById('chartBar').getContext('2d');
new Chart(ctxBar, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels_bulan) ?>,
        datasets: [
            {
                label: 'Satpam',
                data: <?= json_encode($data_satpam_bulan) ?>,
                backgroundColor: '#1a5276',
                borderRadius: 6
            },
            {
                label: 'Kebersihan',
                data: <?= json_encode($data_kebersihan_bulan) ?>,
                backgroundColor: '#27ae60',
                borderRadius: 6
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' }
        },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>