<?php
include '../includes/session.php';
cekLogin();
cekRole('admin');
include '../config/database.php';

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $kategori = $_POST['kategori_tugas'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];

    try {
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE petugas SET nama=?, kategori_tugas=?, username=?, password=?, email=?, no_hp=? WHERE id_petugas=?");
            $stmt->execute([$nama, $kategori, $username, $password, $email, $no_hp, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE petugas SET nama=?, kategori_tugas=?, username=?, email=?, no_hp=? WHERE id_petugas=?");
            $stmt->execute([$nama, $kategori, $username, $email, $no_hp, $id]);
        }
        header("Location: kelola_petugas.php?sukses=1");
        exit;
    } catch (PDOException $e) {
        $error = "Gagal: " . $e->getMessage();
    }
}

$stmt = $pdo->prepare("SELECT * FROM petugas WHERE id_petugas = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) die("Petugas tidak ditemukan.");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Petugas</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
    <h2>Edit Data Petugas</h2>
    <?php if (isset($error)): ?><div class="alert-error"><?= $error ?></div><?php endif; ?>
    <form method="POST">
        <label>Nama Lengkap</label><input type="text" name="nama" value="<?= htmlspecialchars($p['nama']) ?>" required>
        <label>Kategori Tugas</label>
        <select name="kategori_tugas" required>
            <option value="satpam" <?= $p['kategori_tugas']=='satpam'?'selected':'' ?>>Satpam</option>
            <option value="kebersihan" <?= $p['kategori_tugas']=='kebersihan'?'selected':'' ?>>Kebersihan</option>
        </select>
        <label>Username</label><input type="text" name="username" value="<?= htmlspecialchars($p['username']) ?>" required>
        <label>Password Baru (kosongkan jika tidak diubah)</label><input type="password" name="password">
        <label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($p['email']) ?>">
        <label>No HP</label><input type="text" name="no_hp" value="<?= htmlspecialchars($p['no_hp']) ?>">
        <button type="submit">Simpan Perubahan</button>
    </form>
    <br><a href="kelola_petugas.php" class="btn-back">← Kembali</a>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>