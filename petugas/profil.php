<?php
include '../includes/session.php';
cekLogin();
cekRole('petugas');
include_once '../includes/profil.php';

$id = $_SESSION['id'];

// Ambil data profil
$user = getProfil($pdo, $id, 'petugas');

$pesan = '';
$tipe = '';

// ============ UPDATE DATA DIRI ============
if (isset($_POST['update_profil'])) {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $no_hp = trim($_POST['no_hp']);

    // Upload avatar jika ada
    $avatar = null;
    if (!empty($_FILES['avatar']['name'])) {
        $avatar = uploadAvatar($_FILES['avatar'], $user['avatar']);
    }

    try {
        if ($avatar) {
            $stmt = $pdo->prepare("UPDATE petugas SET nama=?, email=?, no_hp=?, avatar=? WHERE id_petugas=?");
            $stmt->execute([$nama, $email, $no_hp, $avatar, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE petugas SET nama=?, email=?, no_hp=? WHERE id_petugas=?");
            $stmt->execute([$nama, $email, $no_hp, $id]);
        }

        $_SESSION['nama'] = $nama;
        $_SESSION['email'] = $email;
        $_SESSION['no_hp'] = $no_hp;

        $pesan = "Profil berhasil diupdate!";
        $tipe = "success";
        $user = getProfil($pdo, $id, 'petugas'); // refresh
    } catch (PDOException $e) {
        $pesan = "Gagal: " . $e->getMessage();
        $tipe = "error";
    }
}

// ============ GANTI PASSWORD ============
if (isset($_POST['ganti_password'])) {
    $lama = $_POST['password_lama'];
    $baru = $_POST['password_baru'];
    $konfirmasi = $_POST['password_konfirmasi'];

    if (!password_verify($lama, $user['password'])) {
        $pesan = "Password lama salah!";
        $tipe = "error";
    } elseif ($baru != $konfirmasi) {
        $pesan = "Konfirmasi password tidak cocok!";
        $tipe = "error";
    } elseif (strlen($baru) < 6) {
        $pesan = "Password minimal 6 karakter!";
        $tipe = "error";
    } else {
        try {
            $hash = password_hash($baru, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE petugas SET password=? WHERE id_petugas=?");
            $stmt->execute([$hash, $id]);
            $pesan = "Password berhasil diubah!";
            $tipe = "success";
        } catch (PDOException $e) {
            $pesan = "Gagal: " . $e->getMessage();
            $tipe = "error";
        }
    }
}

$avatar_path = getAvatarPath($user['avatar']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Profil Saya</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .profil-header {
            background: linear-gradient(135deg, #1a5276, #2874a6);
            color: #fff;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 25px;
        }
        .avatar-big {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 44px;
            overflow: hidden;
            border: 4px solid rgba(255,255,255,0.3);
            flex-shrink: 0;
        }
        .avatar-big img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .profil-header h2 {
            color: #fff;
            margin-bottom: 5px;
            font-size: 22px;
        }
        .profil-header p {
            opacity: 0.9;
            font-size: 14px;
        }
        .badge-role {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-top: 8px;
        }
        .profil-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        @media (max-width: 768px) {
            .profil-grid { grid-template-columns: 1fr; }
            .profil-header { flex-direction: column; text-align: center; }
        }
        .profil-card {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        .profil-card h3 {
            color: #1a5276;
            font-size: 15px;
            padding-bottom: 12px;
            margin-bottom: 18px;
            border-bottom: 2px solid #f0f4f8;
        }
        .form-avatar-preview {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8fbfd;
            border-radius: 10px;
        }
        .form-avatar-preview img,
        .form-avatar-preview .placeholder {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
        }
        .form-avatar-preview .placeholder {
            background: linear-gradient(135deg, #1a5276, #2874a6);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }
        .form-avatar-preview .info {
            flex: 1;
        }
        .form-avatar-preview .info p {
            font-size: 12px;
            color: #999;
            margin-top: 3px;
        }
    </style>
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">

    <!-- HEADER PROFIL -->
    <div class="profil-header">
        <div class="avatar-big">
            <?php if ($avatar_path): ?>
                <img src="<?= $avatar_path ?>" alt="Avatar">
            <?php else: ?>
                <?= strtoupper(substr($user['nama'], 0, 1)) ?>
            <?php endif; ?>
        </div>
        <div>
            <h2><?= htmlspecialchars($user['nama']) ?></h2>
            <p>@<?= htmlspecialchars($user['username']) ?></p>
            <span class="badge-role">
                <?= ucfirst($user['kategori_tugas']) ?> - BPS Kota Medan
            </span>
        </div>
    </div>

    <?php if ($pesan): ?>
        <div class="alert-<?= $tipe ?>"><?= $pesan ?></div>
    <?php endif; ?>

    <div class="profil-grid">

        <!-- EDIT DATA DIRI -->
        <div class="profil-card">
            <h3>✏️ Edit Data Diri</h3>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-avatar-preview">
                    <?php if ($avatar_path): ?>
                        <img src="<?= $avatar_path ?>" id="previewAvatar" alt="Avatar">
                    <?php else: ?>
                        <div class="placeholder" id="previewPlaceholder">
                            <?= strtoupper(substr($user['nama'], 0, 1)) ?>
                        </div>
                        <img src="" id="previewAvatar" style="display:none;" alt="">
                    <?php endif; ?>
                    <div class="info">
                        <label style="margin:0;font-size:13px;">Ganti Foto Profil</label>
                        <input type="file" name="avatar" accept="image/*" onchange="previewImg(event)" style="margin-top:5px;">
                        <p>Format: JPG, PNG, GIF. Maks 2 MB.</p>
                    </div>
                </div>

                <label>Nama Lengkap</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>

                <label>Username</label>
                <input type="text" value="<?= htmlspecialchars($user['username']) ?>" disabled style="background:#f0f4f8;cursor:not-allowed;">

                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">

                <label>No HP (WhatsApp)</label>
                <input type="text" name="no_hp" value="<?= htmlspecialchars($user['no_hp']) ?>" placeholder="08xxxxxxxxxx">

                <button type="submit" name="update_profil">💾 Simpan Perubahan</button>
            </form>
        </div>

        <!-- GANTI PASSWORD -->
        <div class="profil-card">
            <h3>🔒 Ganti Password</h3>

            <form method="POST">
                <label>Password Lama</label>
                <input type="password" name="password_lama" required>

                <label>Password Baru</label>
                <input type="password" name="password_baru" required minlength="6">

                <label>Konfirmasi Password Baru</label>
                <input type="password" name="password_konfirmasi" required minlength="6">

                <button type="submit" name="ganti_password">🔑 Ubah Password</button>
            </form>

            <div style="margin-top:20px;padding:15px;background:#f8fbfd;border-radius:8px;font-size:13px;color:#666;">
                <b style="color:#1a5276;">💡 Tips Keamanan:</b>
                <ul style="margin-top:8px;padding-left:18px;line-height:1.8;">
                    <li>Minimal 6 karakter</li>
                    <li>Gunakan kombinasi huruf & angka</li>
                    <li>Jangan bagikan password ke orang lain</li>
                </ul>
            </div>
        </div>

    </div>

    <br>
    <a href="dashboard.php" class="btn-back">← Kembali ke Dashboard</a>
</div>

<script>
function previewImg(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('previewAvatar');
            const placeholder = document.getElementById('previewPlaceholder');
            img.src = e.target.result;
            img.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
}
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>