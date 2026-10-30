<?php
include '../includes/session.php';
cekLogin();
cekRole('petugas');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kebersihan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
    <h2>Form Laporan Kegiatan Kebersihan</h2>
    <form action="simpan_laporan.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="jenis" value="kebersihan">
        <label>Tanggal</label>
        <input type="date" name="tanggal" required>
        <label>Area Kerja</label>
        <input type="text" name="area_kerja" placeholder="Contoh: Ruang Kerja, Toilet, Halaman" required>
        <label>Jenis Pekerjaan</label>
        <input type="text" name="jenis_pekerjaan" placeholder="Contoh: Pembersihan Rutin" required>
        <label>Rincian Pekerjaan</label>
        <textarea name="rincian_pekerjaan" rows="5" placeholder="1. Membersihkan ruang kerja&#10;2. Membersihkan toilet&#10;3. Menyapu halaman" required></textarea>
        <label>Kondisi</label>
        <input type="text" name="kondisi" placeholder="Contoh: Lingkungan kantor bersih dan rapi" required>
        <label>Keterangan</label>
        <textarea name="keterangan" rows="3"></textarea>
        <label>Dokumentasi Foto</label>
        <input type="file" name="dokumentasi" accept="image/*">
        <button type="submit">Kirim Laporan</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>