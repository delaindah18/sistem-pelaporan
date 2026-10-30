<?php
include '../includes/session.php';
cekLogin();
cekRole('admin');
include '../config/database.php';

// Data per bulan (12 bulan terakhir)
$data_satpam = [];
$data_kebersihan = [];
$labels = [];
for ($i = 11; $i >= 0; $i--) {
    $bulan = date('m', strtotime("-$i month"));
    $tahun = date('Y', strtotime("-$i month"));
    $labels[] = date('M Y', strtotime("-$i month"));

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM laporan_satpam WHERE MONTH(tanggal)=? AND YEAR(tanggal)=?");
    $stmt->execute([$bulan, $tahun]);
    $data_satpam[] = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM laporan_kebersihan WHERE MONTH(tanggal)=? AND YEAR(tanggal)=?");
    $stmt->execute([$bulan, $tahun]);
    $data_kebersihan[] = (int)$stmt->fetchColumn();
}

// Status
$status_data = $pdo->query("SELECT status, COUNT(*) as total FROM (
    SELECT status FROM laporan_satpam UNION ALL SELECT status FROM laporan_kebersihan
) as t GROUP BY status")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Grafik Statistik</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
    <h2>Grafik Statistik Laporan</h2>

    <div class="chart-grid">
        <div class="chart-box">
            <h3>Jumlah Laporan per Bulan</h3>
            <canvas id="chartBulan"></canvas>
        </div>
        <div class="chart-box">
            <h3>Distribusi Status Laporan</h3>
            <canvas id="chartStatus"></canvas>
        </div>
    </div>

    <br><a href="dashboard.php" class="btn-back">← Kembali</a>
</div>

<script>
const ctx1 = document.getElementById('chartBulan').getContext('2d');
new Chart(ctx1, {
    type: 'line',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [
            {
                label: 'Satpam',
                data: <?= json_encode($data_satpam) ?>,
                borderColor: '#1a5276',
                backgroundColor: 'rgba(26,82,118,0.1)',
                tension: 0.3,
                fill: true
            },
            {
                label: 'Kebersihan',
                data: <?= json_encode($data_kebersihan) ?>,
                borderColor: '#27ae60',
                backgroundColor: 'rgba(39,174,96,0.1)',
                tension: 0.3,
                fill: true
            }
        ]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});

const ctx2 = document.getElementById('chartStatus').getContext('2d');
new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_column($status_data, 'status')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($status_data, 'total')) ?>,
            backgroundColor: ['#f39c12', '#27ae60', '#e74c3c']
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>