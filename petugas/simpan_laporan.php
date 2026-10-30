<?php
include '../includes/session.php';
cekLogin();
cekRole('petugas');
include '../config/database.php';
include '../notifikasi/email.php';
include '../notifikasi/whatsapp.php';

$id_petugas = $_SESSION['id'];
$nama_petugas = $_SESSION['nama'];
$jenis = $_POST['jenis'];
$tanggal = $_POST['tanggal'];
$dokumentasi = '';

if (isset($_FILES['dokumentasi']) && $_FILES['dokumentasi']['name'] != '') {
    $folder = '../uploads/dokumentasi/';
    if (!is_dir($folder)) mkdir($folder, 0777, true);
    $nama_file = time() . '_' . basename($_FILES['dokumentasi']['name']);
    $target = $folder . $nama_file;
    if (move_uploaded_file($_FILES['dokumentasi']['tmp_name'], $target)) {
        $dokumentasi = $nama_file;
    }
}

try {
    if ($jenis == 'satpam') {
        $sql = "INSERT INTO laporan_satpam 
                (id_petugas, tanggal, waktu, jenis_kegiatan, rincian_pekerjaan, lokasi, uraian, kejadian, tindak_lanjut, kondisi, dokumentasi, status) 
                VALUES 
                (:id_petugas, :tanggal, :waktu, :jenis_kegiatan, :rincian, :lokasi, :uraian, :kejadian, :tindak_lanjut, :kondisi, :dokumentasi, 'Diajukan')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_petugas'     => $id_petugas,
            ':tanggal'        => $tanggal,
            ':waktu'          => $_POST['waktu'],
            ':jenis_kegiatan' => $_POST['jenis_kegiatan'],
            ':rincian'        => $_POST['rincian_pekerjaan'],
            ':lokasi'         => $_POST['lokasi'],
            ':uraian'         => $_POST['uraian'],
            ':kejadian'       => $_POST['kejadian'],
            ':tindak_lanjut'  => $_POST['tindak_lanjut'],
            ':kondisi'        => $_POST['kondisi'],
            ':dokumentasi'    => $dokumentasi,
        ]);
        $kegiatan = $_POST['jenis_kegiatan'];
    } else {
        $sql = "INSERT INTO laporan_kebersihan 
                (id_petugas, tanggal, area_kerja, jenis_pekerjaan, rincian_pekerjaan, kondisi, keterangan, dokumentasi, status) 
                VALUES 
                (:id_petugas, :tanggal, :area_kerja, :jenis_pekerjaan, :rincian, :kondisi, :keterangan, :dokumentasi, 'Diajukan')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_petugas'     => $id_petugas,
            ':tanggal'        => $tanggal,
            ':area_kerja'     => $_POST['area_kerja'],
            ':jenis_pekerjaan'=> $_POST['jenis_pekerjaan'],
            ':rincian'        => $_POST['rincian_pekerjaan'],
            ':kondisi'        => $_POST['kondisi'],
            ':keterangan'     => $_POST['keterangan'],
            ':dokumentasi'    => $dokumentasi,
        ]);
        $kegiatan = $_POST['jenis_pekerjaan'];
    }

    // ============= NOTIFIKASI =============
    $subjek = "Laporan Baru dari $nama_petugas";
    $pesan = "Laporan $jenis baru telah diajukan.\n\n"
           . "Petugas: $nama_petugas\n"
           . "Tanggal: $tanggal\n"
           . "Kegiatan: $kegiatan\n\n"
           . "Silakan login ke sistem untuk memeriksa laporan.";

    kirimEmail(ADMIN_EMAIL, $subjek, $pesan);
    kirimWhatsApp(ADMIN_WA, $pesan);

    header("Location: dashboard.php?sukses=1");
} catch (PDOException $e) {
    echo "Gagal menyimpan: " . $e->getMessage();
}
?>