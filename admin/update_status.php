<?php
include '../includes/session.php';
cekLogin();
cekRole('admin');
include_once '../notifikasi/email.php';
include_once '../notifikasi/whatsapp.php';

$id = $_POST['id'];
$jenis = $_POST['jenis'];
$status = $_POST['status'];
$catatan = $_POST['catatan_admin'];

$email_petugas = $_POST['email_petugas'];
$no_hp_petugas = $_POST['no_hp_petugas'];
$nama_petugas = $_POST['nama_petugas'];

$id_admin = $_SESSION['id'];

try {
    if ($jenis == 'satpam') {
        $stmt = $pdo->prepare("UPDATE laporan_satpam 
                               SET status = ?, catatan_admin = ?, diperiksa_oleh = ?, diperiksa_pada = NOW() 
                               WHERE id_laporan = ?");
    } else {
        $stmt = $pdo->prepare("UPDATE laporan_kebersihan 
                               SET status = ?, catatan_admin = ?, diperiksa_oleh = ?, diperiksa_pada = NOW() 
                               WHERE id_laporan = ?");
    }
    $stmt->execute([$status, $catatan, $id_admin, $id]);

    // Notifikasi ke petugas
    $subjek = "Laporan Anda telah diperiksa: $status";
    $pesan = "Halo $nama_petugas,\n\n"
           . "Laporan $jenis Anda telah diperiksa.\n"
           . "Status: $status\n"
           . "Catatan Admin: $catatan\n\n"
           . "Silakan login ke sistem untuk melihat detailnya.";

    if (!empty($email_petugas)) kirimEmail($email_petugas, $subjek, $pesan);
    if (!empty($no_hp_petugas)) kirimWhatsApp($no_hp_petugas, $pesan);

    header("Location: dashboard.php?sukses=1");
} catch (PDOException $e) {
    echo "Gagal update: " . $e->getMessage();
}
?>