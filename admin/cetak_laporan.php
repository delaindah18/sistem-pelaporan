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

$id = $_GET['id'] ?? 0;
$jenis = $_GET['jenis'] ?? 'satpam';

if ($jenis == 'satpam') {
    $stmt = $pdo->prepare("SELECT l.*, p.nama, p.kategori_tugas,
                           a.nama AS nama_admin 
                           FROM laporan_satpam l 
                           JOIN petugas p ON l.id_petugas = p.id_petugas 
                           LEFT JOIN administrator a ON l.diperiksa_oleh = a.id_admin
                           WHERE l.id_laporan = ?");
} else {
    $stmt = $pdo->prepare("SELECT l.*, p.nama, p.kategori_tugas,
                           a.nama AS nama_admin 
                           FROM laporan_kebersihan l 
                           JOIN petugas p ON l.id_petugas = p.id_petugas 
                           LEFT JOIN administrator a ON l.diperiksa_oleh = a.id_admin
                           WHERE l.id_laporan = ?");
}
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) die("Laporan tidak ditemukan.");

// Generate kode verifikasi jika belum ada
if (empty($data['kode_verifikasi'])) {
    $kode = strtoupper(bin2hex(random_bytes(4)));
    if ($jenis == 'satpam') {
        $upd = $pdo->prepare("UPDATE laporan_satpam SET kode_verifikasi = ? WHERE id_laporan = ?");
    } else {
        $upd = $pdo->prepare("UPDATE laporan_kebersihan SET kode_verifikasi = ? WHERE id_laporan = ?");
    }
    $upd->execute([$kode, $id]);
    $data['kode_verifikasi'] = $kode;
}

// Generate QR Code (endroid/qr-code 4.x)
try {
    $qr_data = "BPS-MEDAN-LAPORAN-" . strtoupper($jenis) . "-" . $id . "-" . $data['kode_verifikasi'];

    $qrCode = new QrCode(
        data: $qr_data,
        encoding: new Encoding('UTF-8'),
        errorCorrectionLevel: ErrorCorrectionLevel::LOW,
        size: 150,
        margin: 5,
        roundBlockSizeMode: RoundBlockSizeMode::MARGIN,
        foregroundColor: new Color(26, 82, 118),
        backgroundColor: new Color(255, 255, 255)
    );

    $writer = new PngWriter();
    $qr_base64 = base64_encode($writer->write($qrCode)->getString());
} catch (\Throwable $e) {
    $qr_base64 = '';
    error_log("QR Error: " . $e->getMessage());
}

// Nomor surat
$bulan_romawi = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
$bulan = (int)date('m', strtotime($data['tanggal']));
$tahun = date('Y', strtotime($data['tanggal']));
$kode_surat = $jenis == 'satpam' ? 'SAT' : 'KBR';
$nomor_surat = "470/{$kode_surat}-{$id}/{$bulan_romawi[$bulan]}/{$tahun}/SR";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cetak Laporan - <?= $nomor_surat ?></title>
    <style>
        @page { size: A4; margin: 15mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1.5; color: #000; background: #f0f4f8; padding: 20px; }
        .page { background: #fff; max-width: 210mm; margin: 0 auto; padding: 15mm; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .kop-surat { border-bottom: 4px double #000; padding-bottom: 12px; margin-bottom: 25px; display: flex; align-items: center; gap: 20px; }
        .kop-logo { width: 90px; height: 90px; object-fit: contain; flex-shrink: 0; }
        .kop-text { flex: 1; text-align: center; }
        .kop-text h1 { font-size: 16pt; font-weight: bold; letter-spacing: 1px; }
        .kop-text h2 { font-size: 18pt; font-weight: bold; letter-spacing: 2px; margin: 3px 0; }
        .kop-text p { font-size: 10pt; font-style: italic; }
        .kop-text .alamat { font-size: 9pt; font-style: normal; margin-top: 5px; line-height: 1.3; }
        .judul { text-align: center; margin-bottom: 25px; }
        .judul h3 { font-size: 14pt; font-weight: bold; text-decoration: underline; letter-spacing: 1px; margin-bottom: 5px; }
        .judul .nomor { font-size: 11pt; }
        .isi { margin-bottom: 25px; }
        .isi p { margin-bottom: 10px; text-align: justify; }
        .data-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .data-table td { padding: 6px 10px; vertical-align: top; font-size: 11pt; }
        .data-table td:first-child { width: 180px; font-weight: bold; }
        .data-table td:nth-child(2) { width: 15px; text-align: center; }
        .kotak-rincian { border: 1px solid #000; padding: 15px; margin: 15px 0; background: #fafafa; }
        .kotak-rincian h4 { font-size: 11pt; margin-bottom: 10px; text-decoration: underline; }
        .kotak-rincian p { font-size: 11pt; line-height: 1.6; white-space: pre-line; }
        .ttd-wrapper { margin-top: 40px; display: flex; justify-content: space-between; align-items: flex-start; }
        .qr-area { text-align: center; }
        .qr-area img { width: 110px; height: 110px; border: 1px solid #ccc; padding: 5px; background: #fff; }
        .qr-area p { font-size: 8pt; color: #555; margin-top: 5px; max-width: 130px; }
        .qr-area .kode { font-family: monospace; font-weight: bold; font-size: 9pt; background: #1a5276; color: #fff; padding: 2px 8px; border-radius: 4px; display: inline-block; margin-top: 3px; }
        .ttd-area { text-align: center; font-size: 11pt; }
        .ttd-area .kota-tanggal { margin-bottom: 60px; }
        .ttd-area .nama { font-weight: bold; text-decoration: underline; }
        .ttd-area .jabatan { font-size: 10pt; color: #333; }
        .footer-doc { margin-top: 40px; padding-top: 10px; border-top: 1px solid #999; font-size: 8pt; text-align: center; color: #666; font-style: italic; }
        .no-print { text-align: center; margin-bottom: 20px; }
        .btn-print { background: #1a5276; color: #fff; padding: 12px 28px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; margin: 0 5px; text-decoration: none; display: inline-block; }
        .btn-print:hover { background: #154360; }
        .btn-back-print { background: #95a5a6; }
        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none; }
            .page { box-shadow: none; padding: 0; max-width: none; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <button onclick="window.print()" class="btn-print">🖨️ Cetak Sekarang</button>
    <a href="detail_laporan.php?id=<?= $id ?>&jenis=<?= $jenis ?>" class="btn-print btn-back-print">← Kembali</a>
</div>

<div class="page">

    <div class="kop-surat">
        <img src="../assets/img/logo-bps.png" alt="Logo BPS" class="kop-logo"
             onerror="this.src='../assets/img/logo.png'">
        <div class="kop-text">
            <h1>BADAN PUSAT STATISTIK</h1>
            <h2>KOTA MEDAN</h2>
            <p>Jl. Gaperta/Brigjen H. Abdul Manaf Lubis No. 311, Medan Helvetia</p>
            <p class="alamat">
                Telepon: (061) 8449289 | Email: bps1275@bps.go.id<br>
                Website: https://medankota.bps.go.id
            </p>
        </div>
    </div>

    <div class="judul">
        <h3>SURAT KETERANGAN LAPORAN KEGIATAN</h3>
        <p class="nomor">Nomor: <?= $nomor_surat ?></p>
    </div>

    <div class="isi">
        <p>Yang bertanda tangan di bawah ini, Kepala Badan Pusat Statistik Kota Medan, dengan ini menerangkan bahwa telah dilaksanakan kegiatan 
            <?= $jenis == 'satpam' ? 'keamanan (satpam)' : 'kebersihan' ?> sebagai berikut:</p>

        <table class="data-table">
            <tr><td>Nama Petugas</td><td>:</td><td><?= htmlspecialchars($data['nama']) ?></td></tr>
            <tr><td>Kategori Tugas</td><td>:</td><td><?= ucfirst($data['kategori_tugas']) ?></td></tr>
            <tr><td>Tanggal Kegiatan</td><td>:</td><td><?= date('d F Y', strtotime($data['tanggal'])) ?></td></tr>
            <?php if ($jenis == 'satpam'): ?>
            <tr><td>Waktu Pelaksanaan</td><td>:</td><td><?= htmlspecialchars($data['waktu']) ?> WIB</td></tr>
            <tr><td>Jenis Kegiatan</td><td>:</td><td><?= htmlspecialchars($data['jenis_kegiatan']) ?></td></tr>
            <tr><td>Lokasi</td><td>:</td><td><?= htmlspecialchars($data['lokasi']) ?></td></tr>
            <?php else: ?>
            <tr><td>Area Kerja</td><td>:</td><td><?= htmlspecialchars($data['area_kerja']) ?></td></tr>
            <tr><td>Jenis Pekerjaan</td><td>:</td><td><?= htmlspecialchars($data['jenis_pekerjaan']) ?></td></tr>
            <?php endif; ?>
            <tr><td>Kondisi</td><td>:</td><td><?= htmlspecialchars($data['kondisi']) ?></td></tr>
            <tr><td>Status Laporan</td><td>:</td><td><b><?= $data['status'] ?></b></td></tr>
        </table>

        <div class="kotak-rincian">
            <h4>Rincian Pekerjaan:</h4>
            <p><?= htmlspecialchars($data['rincian_pekerjaan']) ?></p>
        </div>

        <?php if ($jenis == 'satpam' && !empty($data['kejadian'])): ?>
        <div class="kotak-rincian">
            <h4>Kejadian yang Perlu Dilaporkan:</h4>
            <p><?= htmlspecialchars($data['kejadian']) ?></p>
        </div>
        <?php endif; ?>

        <?php if ($jenis == 'satpam' && !empty($data['tindak_lanjut'])): ?>
        <div class="kotak-rincian">
            <h4>Tindak Lanjut:</h4>
            <p><?= htmlspecialchars($data['tindak_lanjut']) ?></p>
        </div>
        <?php endif; ?>

        <?php if ($jenis == 'kebersihan' && !empty($data['keterangan'])): ?>
        <div class="kotak-rincian">
            <h4>Keterangan Tambahan:</h4>
            <p><?= htmlspecialchars($data['keterangan']) ?></p>
        </div>
        <?php endif; ?>

        <p style="margin-top:20px;">Demikian surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya.</p>
    </div>

    <div class="ttd-wrapper">
        <div class="qr-area">
            <?php if ($qr_base64): ?>
                <img src="data:image/png;base64,<?= $qr_base64 ?>" alt="QR Code">
            <?php else: ?>
                <p style="color:red;font-size:8pt;">QR gagal digenerate<br>(cek log error)</p>
            <?php endif; ?>
            <p>Scan untuk verifikasi keaslian dokumen</p>
            <div class="kode"><?= $data['kode_verifikasi'] ?></div>
        </div>

        <div class="ttd-area">
            <div class="kota-tanggal">
                Medan, <?= date('d F Y', strtotime($data['diperiksa_pada'] ?? $data['created_at'])) ?>
            </div>
            <div class="jabatan">Kepala Badan Pusat Statistik<br>Kota Medan</div>
            <br><br><br>
            <div class="nama">( .......................................... )</div>
            <div class="jabatan">NIP. ..............................</div>
        </div>
    </div>

    <div class="footer-doc">
        Dokumen ini dicetak dari Sistem Informasi Pelaporan PPPK Satpam & Kebersihan BPS Kota Medan<br>
        Dicetak pada: <?= date('d F Y, H:i:s') ?> WIB | Kode Verifikasi: <?= $data['kode_verifikasi'] ?>
    </div>

</div>

</body>
</html>