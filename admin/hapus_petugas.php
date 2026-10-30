<?php
include '../includes/session.php';
cekLogin();
cekRole('admin');
include '../config/database.php';

$id = $_GET['id'];
try {
    $stmt = $pdo->prepare("DELETE FROM petugas WHERE id_petugas = ?");
    $stmt->execute([$id]);
    header("Location: kelola_petugas.php?sukses=1");
} catch (PDOException $e) {
    echo "Gagal hapus: " . $e->getMessage();
}
?>