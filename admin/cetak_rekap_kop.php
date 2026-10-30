<?php
require '../vendor/autoload.php';
include '../includes/session.php';
cekLogin();
cekRole('admin');

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;

$jenis = $_GET['jenis'] ?? 'satpam';
$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');

$nama_bulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
$bulan_romawi = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

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

$kode_surat = $jenis == 'satpam' ? 'SAT' : 'KBR';
$nomor_surat = "470/{$kode_surat}-REKAP/{$bulan_romawi[(int)$bulan]}/{$tahun}/SR";

// QR
$qr_data = "BPS-MEDAN-REKAP-" . strtoupper($jenis) . "-" . $bulan . "-" . $tahun;
$qrCode = new QrCode(
    data: $qr_data,
    encoding: new Encoding('UTF-8'),
    errorCorrectionLevel: ErrorCorrectionLevel::Low,
    size: 150,
    margin: 5,
    roundBlockSizeMode: RoundBlockSizeMode::Margin,
    foregroundColor: new Color(26, 82, 118),
    backgroundColor: new Color(255, 255, 255)
);
$writer = new PngWriter();
$qr_base64 = base64_encode($writer->write($qrCode)->getString());
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cetak Rekap <?= ucfirst($jenis) ?> - <?= $nama_bulan[(int)$bulan] ?> <?= $tahun ?></title>
    <style>
        @page { size: A4 landscape; margin: 15mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', serif;
            font-size: 11pt;
            color: #000;
            background: #f0f4f8;
            padding: 20px;
        }
        .page {
            background: #fff;
            max-width: 297mm;
            margin: 0 auto;
            padding: 15mm;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .kop-surat {
            border-bottom: 4px double #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .kop-logo { width: 80px; height: 80px; object-fit: contain; }
        .kop-text { flex: 1; text-align: center; }
        .kop-text h1 { font-size: 15pt; font-weight: bold; letter-spacing: 1px; }
        .kop-text h2 { font-size: 17pt; font-weight: bold; letter-spacing: 2px; margin: 2px 0; }
        .kop-text p { font-size: 9pt; font-style: italic; }
        .kop-text .alamat { font-size: 8.5pt; font-style: normal; margin-top: 4px; line-height: 1.3; }

        .judul { text-align: center; margin-bottom: 20px; }
        .judul h3 { font-size: 13pt; font-weight: bold; text-decoration: underline; letter-spacing: 1px; }
        .judul .nomor { font-size: 10.5pt; margin-top: 4px; }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10pt;
        }
        table.data th, table.data td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }
        table.data th {
            background: #1a5276;
            color: #fff;
            text-align: center;
            font-weight: bold;
        }
        table.data tr:nth-child(even) {
            background: #f8fbfd;
        }

        .ttd-wrapper {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .qr-area { text-align: center; }
        .qr-area img { width: 100px; height: 100px; border: 1px solid #ccc; padding: 4px; background: #fff; }
        .qr-area p { font-size: 8pt; color: #555; margin-top: 4px; max-width: 120px; }

        .ttd-area { text-align: center; font-size: 11pt; }
        .ttd-area .kota-tanggal { margin-bottom: 60px; }
        .ttd-area .jabatan { font-size: 10pt; margin-bottom: 5px; }
        .ttd-area .nama { font-weight: bold; text-decoration: underline; }

        .footer-doc {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #999;
            font-size: 8pt;
            text-align: center;
            color: #666;
            font-style: italic;
        }

        .no-print { text-align: center; margin-bottom: 20px; }
        .btn-print {
            background: #1a5276;
            color: #fff;
            padding: 12px 28px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin: 0 5px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-back-print { background: #95a5a6; }

        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none; }
            .page { box-shadow: none; padding: 0; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <button onclick="window.print()" class="btn-print">🖨️ Cetak Sekarang</button>
    <a href="rekapitulasi.php?jenis=<?= $jenis ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn-print btn-back-print">← Kembali</a>
</div>

<div class="page">

    <!-- KOP -->
    <div class="kop-surat">
        <img src="../assets/img/logo-bps.png" alt="Logo BPS" class="kop-logo"
             onerror="this.src='../assets/img/logo.png'">
        <div class="kop-text">
            <h1>BADAN PUSAT STATISTIK</h1>
            <h2>KOTA MEDAN</h2>
            <p>Jl. Gaperta/Brigjen H. Abdul Manaf Lubis No. 311, Medan Helvetia</p>
            <p class="alamat">Telepon: (061) 8449289 | Email: bps1275@bps.go.id | Website: https://medankota.bps.go.id</p>
        </div>
    </div>

    <div class="judul">
        <h3>REKAPITULASI LAPORAN KEGIATAN <?= strtoupper($jenis) ?></h3>
        <p class="nomor">Nomor: <?= $nomor_surat ?></p>
        <p style="margin-top:5px;font-size:10.5pt;">Periode: <?= $nama_bulan[(int)$bulan] ?> <?= $tahun ?></p>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th style="width:40px;">No</th>
                <th style="width:100px;">Tanggal</th>
                <th style="width:130px;">Petugas</th>
                <?php if ($jenis == 'satpam'): ?>
                    <th>Jenis Kegiatan</th>
                    <th style="width:130px;">Lokasi</th>
                <?php else: ?>
                    <th>Area Kerja</th>
                    <th style="width:130px;">Jenis Pekerjaan</th>
                <?php endif; ?>
                <th style="width:140px;">Kondisi</th>
                <th style="width:90px;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($data) > 0): ?>
                <?php $no = 1; foreach ($data as $r): ?>
                <tr>
                    <td style="text-align:center;"><?= $no++ ?></td>
                    <td><?= date('d/m/Y', strtotime($r['tanggal'])) ?></td>
                    <td><?= htmlspecialchars($r['nama']) ?></td>
                    <?php if ($jenis == 'satpam'): ?>
                        <td><?= htmlspecialchars($r['jenis_kegiatan']) ?></td>
                        <td><?= htmlspecialchars($r['lokasi']) ?></td>
                    <?php else: ?>
                        <td><?= htmlspecialchars($r['area_kerja']) ?></td>
                        <td><?= htmlspecialchars($r['jenis_pekerjaan']) ?></td>
                    <?php endif; ?>
                    <td><?= htmlspecialchars($r['kondisi']) ?></td>
                    <td style="text-align:center;"><?= $r['status'] ?></td>
                </tr>
                <?php endforeach; ?>
                <tr style="background:#eaf3fa;font-weight:bold;">
                    <td colspan="<?= $jenis == 'satpam' ? 5 : 5 ?>" style="text-align:right;">Total Laporan</td>
                    <td colspan="2" style="text-align:center;"><?= count($data) ?></td>
                </tr>
            <?php else: ?>
                <tr><td colspan="7" style="text-align:center;">Tidak ada data untuk periode ini.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- TTD & QR -->
    <div class="ttd-wrapper">
        <div class="qr-area">
            <img src="data:image/png;base64,<?= $qr_base64 ?>" alt="QR Code">
            <p>Scan untuk verifikasi keaslian dokumen</p>
        </div>
        <div class="ttd-area">
            <div class="kota-tanggal">Medan, <?= date('d F Y') ?></div>
            <div class="jabatan">Kepala Badan Pusat Statistik<br>Kota Medan</div>
            <br><br><br>
            <div class="nama">( .......................................... )</div>
            <div class="jabatan" style="font-size:10pt;">NIP. ..............................</div>
        </div>
    </div>

    <div class="footer-doc">
        Dokumen ini dicetak dari Sistem Informasi Pelaporan PPPK Satpam & Kebersihan BPS Kota Medan<br>
        Dicetak pada: <?= date('d F Y, H:i:s') ?> WIB
    </div>

</div>

</body>
</html>