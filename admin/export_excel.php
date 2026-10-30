<?php
require '../vendor/autoload.php';
include '../includes/session.php';
cekLogin();
cekRole('admin');
include '../config/database.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header
$sheet->setCellValue('A1', 'REKAPITULASI LAPORAN ' . strtoupper($jenis));
$sheet->mergeCells('A1:H1');
$sheet->setCellValue('A2', 'BPS Kota Medan - Periode: ' . $nama_bulan[(int)$bulan] . ' ' . $tahun);
$sheet->mergeCells('A2:H2');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Header tabel
$headers = ['No', 'Tanggal', 'Petugas'];
if ($jenis == 'satpam') {
    $headers = array_merge($headers, ['Jenis Kegiatan', 'Lokasi', 'Kondisi', 'Status']);
} else {
    $headers = array_merge($headers, ['Area Kerja', 'Jenis Pekerjaan', 'Kondisi', 'Status']);
}
$col = 'A';
foreach ($headers as $h) {
    $sheet->setCellValue($col . '4', $h);
    $sheet->getStyle($col . '4')->getFont()->setBold(true);
    $sheet->getStyle($col . '4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF1A5276');
    $sheet->getStyle($col . '4')->getFont()->getColor()->setARGB('FFFFFFFF');
    $col++;
}

$row = 5; $no = 1;
foreach ($data as $r) {
    $sheet->setCellValue('A' . $row, $no++);
    $sheet->setCellValue('B' . $row, $r['tanggal']);
    $sheet->setCellValue('C' . $row, $r['nama']);
    if ($jenis == 'satpam') {
        $sheet->setCellValue('D' . $row, $r['jenis_kegiatan']);
        $sheet->setCellValue('E' . $row, $r['lokasi']);
        $sheet->setCellValue('F' . $row, $r['kondisi']);
        $sheet->setCellValue('G' . $row, $r['status']);
    } else {
        $sheet->setCellValue('D' . $row, $r['area_kerja']);
        $sheet->setCellValue('E' . $row, $r['jenis_pekerjaan']);
        $sheet->setCellValue('F' . $row, $r['kondisi']);
        $sheet->setCellValue('G' . $row, $r['status']);
    }
    $row++;
}

// Border
$lastCol = $jenis == 'satpam' ? 'G' : 'G';
$sheet->getStyle('A4:' . $lastCol . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// Auto width
foreach (range('A', $lastCol) as $c) $sheet->getColumnDimension($c)->setAutoSize(true);

$filename = 'Rekap_' . ucfirst($jenis) . '_' . $nama_bulan[(int)$bulan] . '_' . $tahun . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>