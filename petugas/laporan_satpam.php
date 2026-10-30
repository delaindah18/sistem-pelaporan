<?php
include '../includes/session.php';
cekLogin();
cekRole('petugas');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Satpam</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
    <h2>Form Laporan Kegiatan Satpam</h2>
    <form action="simpan_laporan.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="jenis" value="satpam">
        <label>Tanggal</label>
        <input type="date" name="tanggal" required>
        <label>Waktu</label>
        <input type="time" name="waktu" required>
        <label>Jenis Kegiatan</label>
        <input type="text" name="jenis_kegiatan" placeholder="Contoh: Pemeriksaan Lingkungan" required>
        <label>Lokasi</label>
        <input type="text" name="lokasi" placeholder="Contoh: Kantor BPS Kota Medan" required>
        <label>Uraian Kegiatan</label>
        <textarea name="uraian" rows="3" required></textarea>
        <label>Rincian Pekerjaan</label>
        <textarea name="rincian_pekerjaan" rows="5" placeholder="1. Memeriksa ruangan&#10;2. Mengawasi tamu&#10;3. Patroli area" required></textarea>
        <label>Kejadian yang Perlu Dilaporkan</label>
        <textarea name="kejadian" rows="3"></textarea>
        <label>Tindak Lanjut</label>
        <textarea name="tindak_lanjut" rows="3"></textarea>
        <label>Kondisi</label>
        <input type="text" name="kondisi" placeholder="Contoh: Aman dan terkendali" required>
        <label>Dokumentasi Foto</label>
        <input type="file" name="dokumentasi" accept="image/*">
        <button type="submit">Kirim Laporan</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>