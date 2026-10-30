<?php
/**
 * Ambil data profil user (petugas/admin)
 */
function getProfil($pdo, $id, $role) {
    if ($role == 'petugas') {
        $stmt = $pdo->prepare("SELECT * FROM petugas WHERE id_petugas = ?");
    } else {
        $stmt = $pdo->prepare("SELECT * FROM administrator WHERE id_admin = ?");
    }
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Upload avatar, return nama file atau null
 */
function uploadAvatar($file, $old_avatar = null) {
    if (!isset($file) || $file['error'] != 0) return null;

    $folder = __DIR__ . '/../uploads/avatar/';
    if (!is_dir($folder)) mkdir($folder, 0777, true);

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowed)) return null;

    // Hapus file lama
    if ($old_avatar && file_exists($folder . $old_avatar)) {
        unlink($folder . $old_avatar);
    }

    $nama_file = 'avatar_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], $folder . $nama_file)) {
        return $nama_file;
    }
    return null;
}

/**
 * Cek apakah avatar ada, jika tidak return null
 */
function getAvatarPath($avatar) {
    if ($avatar && file_exists(__DIR__ . '/../uploads/avatar/' . $avatar)) {
        return '../uploads/avatar/' . $avatar;
    }
    return null;
}
?>