<?php
include '../includes/session.php';
cekLogin();
cekRole('admin');
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $kategori = $_POST['kategori_tugas'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];

    try {
        $stmt = $pdo->prepare("INSERT INTO petugas (nama, kategori_tugas, username, password, email, no_hp) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nama, $kategori, $username, $password, $email, $no_hp]);
        header("Location: kelola_petugas.php?sukses=1");
        exit;
    } catch (PDOException $e) {
        $error = "Gagal: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Petugas</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
    <h2>Tambah Data Petugas</h2>
    <?php if (isset($error)): ?><div class="alert-error"><?= $error ?></div><?php endif; ?>
    <form method="POST">
        <label>Nama Lengkap</label><input type="text" name="nama" required>
        <label>Kategori Tugas</label>
        <select name="kategori_tugas" required>
            <option value="satpam">Satpam</option>
            <option value="kebersihan">Kebersihan</option>
        </select>
        <label>Username</label><input type="text" name="username" required>
        <label>Password</label><input type="password" name="password" required>
        <label>Email</label><input type="email" name="email">
        <label>No HP (WhatsApp)</label><input type="text" name="no_hp" placeholder="08xxxxxxxxxx">
        <button type="submit">Simpan</button>
    </form>
    <br><a href="kelola_petugas.php" class="btn-back">← Kembali</a>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>