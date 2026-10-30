<?php
require '../vendor/autoload.php';
include '../includes/session.php';
cekLogin();
cekRole('admin');
include '../config/database.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$jenis = $_GET['jenis'] ?? 'satpam';
$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');

$nama_bulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];

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

$html = '<style>
    body { font-family: Arial, sans-serif; font-size: 11px; }
    h2 { text-align: center; margin-bottom: 5px; }
    .sub { text-align: center; font-size: 11px; margin-bottom: 15px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #333; padding: 5px; text-align: left; }
    th { background: #1a5276; color: #fff; }
</style>';
$html .= '<h2>REKAPITULASI LAPORAN ' . strtoupper($jenis) . '</h2>';
$html .= '<div class="sub">BPS Kota Medan - Periode: ' . $nama_bulan[(int)$bulan] . ' ' . $tahun . '</div>';
$html .= '<table><thead><tr><th>No</th><th>Tanggal</th><th>Petugas</th>';
if ($jenis == 'satpam') {
    $html .= '<th>Jenis Kegiatan</th><th>Lokasi</th>';
} else {
    $html .= '<th>Area Kerja</th><th>Jenis Pekerjaan</th>';
}
$html .= '<th>Kondisi</th><th>Status</th></tr></thead><tbody>';

$no = 1;
foreach ($data as $r) {
    $html .= '<tr>';
    $html .= '<td>' . $no++ . '</td>';
    $html .= '<td>' . htmlspecialchars($r['tanggal']) . '</td>';
    $html .= '<td>' . htmlspecialchars($r['nama']) . '</td>';
    if ($jenis == 'satpam') {
        $html .= '<td>' . htmlspecialchars($r['jenis_kegiatan']) . '</td>';
        $html .= '<td>' . htmlspecialchars($r['lokasi']) . '</td>';
    } else {
        $html .= '<td>' . htmlspecialchars($r['area_kerja']) . '</td>';
        $html .= '<td>' . htmlspecialchars($r['jenis_pekerjaan']) . '</td>';
    }
    $html .= '<td>' . htmlspecialchars($r['kondisi']) . '</td>';
    $html .= '<td>' . $r['status'] . '</td>';
    $html .= '</tr>';
}
$html .= '</tbody></table>';

$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream('Rekap_' . ucfirst($jenis) . '_' . $nama_bulan[(int)$bulan] . '_' . $tahun . '.pdf', ['Attachment' => false]);
?>