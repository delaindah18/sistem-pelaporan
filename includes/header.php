<?php
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
$is_petugas = isset($_SESSION['role']) && $_SESSION['role'] == 'petugas';

// Ambil avatar user
$avatar_user = null;
$link_profil = '#';
$link_dashboard = '#';

if ($is_admin) {
    $link_profil = '../admin/profil.php';
    $link_dashboard = '../admin/dashboard.php';
    if (isset($pdo)) {
        $stmt = $pdo->prepare("SELECT avatar FROM administrator WHERE id_admin = ?");
        $stmt->execute([$_SESSION['id']]);
        $av = $stmt->fetchColumn();
        if ($av && file_exists(__DIR__ . '/../uploads/avatar/' . $av)) {
            $avatar_user = '../uploads/avatar/' . $av;
        }
    }
} elseif ($is_petugas) {
    $link_profil = '../petugas/profil.php';
    $link_dashboard = '../petugas/dashboard.php';
    if (isset($pdo)) {
        $stmt = $pdo->prepare("SELECT avatar FROM petugas WHERE id_petugas = ?");
        $stmt->execute([$_SESSION['id']]);
        $av = $stmt->fetchColumn();
        if ($av && file_exists(__DIR__ . '/../uploads/avatar/' . $av)) {
            $avatar_user = '../uploads/avatar/' . $av;
        }
    }
}

// Notifikasi admin
$jumlah_notif = 0;
$daftar_notif = [];
if ($is_admin && isset($pdo)) {
    try {
        $stmt = $pdo->query("SELECT 
            (SELECT COUNT(*) FROM laporan_satpam WHERE status='Diajukan') + 
            (SELECT COUNT(*) FROM laporan_kebersihan WHERE status='Diajukan')");
        $jumlah_notif = (int)$stmt->fetchColumn();

        if ($jumlah_notif > 0) {
            $stmt = $pdo->query("
                (SELECT 'satpam' AS jenis, id_laporan, tanggal, jenis_kegiatan AS kegiatan, created_at
                 FROM laporan_satpam WHERE status='Diajukan')
                UNION ALL
                (SELECT 'kebersihan' AS jenis, id_laporan, tanggal, area_kerja AS kegiatan, created_at
                 FROM laporan_kebersihan WHERE status='Diajukan')
                ORDER BY created_at DESC LIMIT 5
            ");
            $daftar_notif = $stmt->fetchAll();
        }
    } catch (PDOException $e) { $jumlah_notif = 0; }
}
?>
<header class="main-header">
    <div class="header-left">
        <a href="<?= $link_dashboard ?>" style="display:flex;align-items:center;gap:12px;text-decoration:none;color:#fff;">
            <img src="../assets/img/logo.png" alt="Logo" class="logo-header">
            <div class="header-title">
                <b>Sistem Pelaporan PPPK</b>
                <span>BPS Kota Medan</span>
            </div>
        </a>
    </div>
    <div class="header-right">

        <?php if ($is_admin): ?>
        <!-- LONCENG NOTIFIKASI -->
        <div class="notif-wrapper" id="notifWrapper">
            <button class="notif-bell" id="notifBell" onclick="toggleNotif()">
                <span class="bell-icon">🔔</span>
                <?php if ($jumlah_notif > 0): ?>
                    <span class="notif-badge"><?= $jumlah_notif > 99 ? '99+' : $jumlah_notif ?></span>
                <?php endif; ?>
            </button>
            <div class="notif-dropdown" id="notifDropdown">
                <div class="notif-header">
                    <b>Notifikasi</b>
                    <span><?= $jumlah_notif ?> laporan baru</span>
                </div>
                <div class="notif-body">
                    <?php if (count($daftar_notif) > 0): ?>
                        <?php foreach ($daftar_notif as $n): ?>
                            <a href="../admin/periksa_laporan.php?id=<?= $n['id_laporan'] ?>&jenis=<?= $n['jenis'] ?>" class="notif-item">
                                <div class="notif-icon"><?= $n['jenis'] == 'satpam' ? '🛡️' : '🧹' ?></div>
                                <div class="notif-content">
                                    <div class="notif-title">Laporan <?= ucfirst($n['jenis']) ?></div>
                                    <div class="notif-desc"><?= htmlspecialchars($n['kegiatan']) ?></div>
                                    <div class="notif-time"><?= date('d M Y', strtotime($n['tanggal'])) ?></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="notif-empty">🎉 Tidak ada laporan baru</div>
                    <?php endif; ?>
                </div>
                <div class="notif-footer"><a href="../admin/dashboard.php">Lihat Semua</a></div>
            </div>
        </div>
        <?php endif; ?>

        <!-- PROFIL DROPDOWN -->
        <div class="profil-wrapper" id="profilWrapper">
            <button class="profil-btn" onclick="toggleProfil()">
                <div class="avatar-header">
                    <?php if ($avatar_user): ?>
                        <img src="<?= $avatar_user ?>" alt="Avatar">
                    <?php else: ?>
                        <?= strtoupper(substr($_SESSION['nama'] ?? 'G', 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <span class="user-name"><?= htmlspecialchars($_SESSION['nama'] ?? 'Guest') ?></span>
                <span class="arrow">▾</span>
            </button>

            <div class="profil-dropdown" id="profilDropdown">
                <div class="profil-dropdown-header">
                    <div class="avatar-dropdown">
                        <?php if ($avatar_user): ?>
                            <img src="<?= $avatar_user ?>" alt="Avatar">
                        <?php else: ?>
                            <?= strtoupper(substr($_SESSION['nama'] ?? 'G', 0, 1)) ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <b><?= htmlspecialchars($_SESSION['nama'] ?? 'Guest') ?></b>
                        <p><?= $is_admin ? 'Administrator' : 'Petugas ' . ucfirst($_SESSION['kategori'] ?? '') ?></p>
                    </div>
                </div>
                <a href="<?= $link_profil ?>" class="profil-dropdown-item">
                    👤 Profil Saya
                </a>
                <a href="<?= $link_dashboard ?>" class="profil-dropdown-item">
                    🏠 Dashboard
                </a>
                <a href="../auth/logout.php" class="profil-dropdown-item logout">
                    🚪 Logout
                </a>
            </div>
        </div>

    </div>
</header>

<script>
function toggleNotif() {
    document.getElementById('notifDropdown').classList.toggle('active');
    document.getElementById('profilDropdown').classList.remove('active');
}
function toggleProfil() {
    document.getElementById('profilDropdown').classList.toggle('active');
    document.getElementById('notifDropdown').classList.remove('active');
}
document.addEventListener('click', function(e) {
    const nw = document.getElementById('notifWrapper');
    const pw = document.getElementById('profilWrapper');
    if (nw && !nw.contains(e.target)) {
        document.getElementById('notifDropdown')?.classList.remove('active');
    }
    if (pw && !pw.contains(e.target)) {
        document.getElementById('profilDropdown')?.classList.remove('active');
    }
});
</script>