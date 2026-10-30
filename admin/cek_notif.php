<?php
include '../includes/session.php';
cekLogin();
cekRole('admin');
include '../config/database.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("
        SELECT 
            (SELECT COUNT(*) FROM laporan_satpam WHERE status='Diajukan') + 
            (SELECT COUNT(*) FROM laporan_kebersihan WHERE status='Diajukan')
    ");
    $total = (int)$stmt->fetchColumn();

    echo json_encode([
        'status' => 'success',
        'total' => $total,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'total' => 0,
        'message' => $e->getMessage()
    ]);
}
?>