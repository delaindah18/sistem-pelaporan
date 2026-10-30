<?php
include '../includes/session.php';
cekLogin();
cekRole('admin');

$id = $_GET['id'] ?? 0;
$jenis = $_GET['jenis'] ?? 'satpam';

if ($jenis == 'satpam') {
    $stmt = $pdo->prepare("SELECT l.*, p.nama, p.email, p.no_hp, p.kategori_tugas,
                           a.nama AS nama_admin 
                           FROM laporan_satpam l 
                           JOIN petugas p ON l.id_petugas = p.id_petugas 
                           LEFT JOIN administrator a ON l.diperiksa_oleh = a.id_admin
                           WHERE l.id_laporan = ?");
} else {
    $stmt = $pdo->prepare("SELECT l.*, p.nama, p.email, p.no_hp, p.kategori_tugas,
                           a.nama AS nama_admin 
                           FROM laporan_kebersihan l 
                           JOIN petugas p ON l.id_petugas = p.id_petugas 
                           LEFT JOIN administrator a ON l.diperiksa_oleh = a.id_admin
                           WHERE l.id_laporan = ?");
}
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    die("<div style='padding:40px;text-align:center;'>
        <h2>❌ Laporan tidak ditemukan</h2>
        <a href='dashboard.php'>← Kembali ke Dashboard</a>
    </div>");
}

// Status badge color
$status_class = 'diajukan';
if ($data['status'] == 'Disetujui') $status_class = 'disetujui';
if ($data['status'] == 'Perlu Perbaikan') $status_class = 'perluperbaikan';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Detail Laporan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .detail-header {
            background: linear-gradient(135deg, #1a5276, #2874a6);
            color: #fff;
            padding: 25px 30px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .detail-header h2 {
            color: #fff;
            margin-bottom: 5px;
        }
        .detail-header p {
            opacity: 0.9;
            font-size: 14px;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 25px;
            margin-bottom: 25px;
        }
        @media (max-width: 900px) {
            .detail-grid { grid-template-columns: 1fr; }
        }
        .detail-card {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        .detail-card h3 {
            color: #1a5276;
            font-size: 15px;
            padding-bottom: 12px;
            margin-bottom: 18px;
            border-bottom: 2px solid #f0f4f8;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .info-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px dashed #f0f4f8;
            font-size: 14px;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            width: 160px;
            color: #777;
            font-weight: 600;
            flex-shrink: 0;
        }
        .info-value {
            color: #333;
            flex: 1;
        }
        .rincian-box {
            background: #f8fbfd;
            border-left: 4px solid #1a5276;
            padding: 15px 20px;
            border-radius: 6px;
            font-size: 14px;
            line-height: 1.8;
            white-space: pre-line;
            color: #333;
        }
        .foto-dokumentasi {
            max-width: 100%;
            border-radius: 10px;
            margin-top: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: transform 0.3s;
        }
        .foto-dokumentasi:hover {
            transform: scale(1.02);
        }
        .foto-empty {
            padding: 40px;
            text-align: center;
            background: #f8fbfd;
            border-radius: 10px;
            color: #999;
            border: 2px dashed #e1eaf2;
        }

        /* ============ TIMELINE ============ */
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 5px;
            bottom: 5px;
            width: 2px;
            background: #e1eaf2;
        }
        .timeline-item {
            position: relative;
            padding-bottom: 25px;
        }
        .timeline-item:last-child {
            padding-bottom: 0;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -25px;
            top: 3px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #1a5276;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px #1a5276;
        }
        .timeline-item.success::before { background: #27ae60; box-shadow: 0 0 0 2px #27ae60; }
        .timeline-item.warning::before { background: #f39c12; box-shadow: 0 0 0 2px #f39c12; }
        .timeline-item.danger::before  { background: #e74c3c; box-shadow: 0 0 0 2px #e74c3c; }
        .timeline-title {
            font-weight: 600;
            color: #1a5276;
            font-size: 14px;
            margin-bottom: 3px;
        }
        .timeline-desc {
            font-size: 13px;
            color: #666;
            margin-bottom: 3px;
        }
        .timeline-time {
            font-size: 11px;
            color: #999;
            font-style: italic;
        }

        /* STATUS CARD */
        .status-card {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            text-align: center;
        }
        .status-icon-big {
            font-size: 60px;
            margin-bottom: 10px;
        }
        .status-label-big {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .status-diajukan { background: #fff3cd; color: #856404; }
        .status-disetujui { background: #d4edda; color: #155724; }
        .status-perluperbaikan { background: #f8d7da; color: #721c24; }

        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn-secondary {
            background: #95a5a6;
            color: #fff;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }
        .btn-secondary:hover { background: #7f8c8d; }

        /* LIGHTBOX FOTO */
        .lightbox {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.9);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .lightbox.active { display: flex; }
        .lightbox img {
            max-width: 90%;
            max-height: 90%;
            border-radius: 10px;
        }
    </style>
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">

    <!-- HEADER DETAIL -->
    <div class="detail-header">
        <div>
            <h2>
                <?= $jenis == 'satpam' ? '🛡️' : '🧹' ?>
                Detail Laporan <?= ucfirst($jenis) ?>
            </h2>
            <p>ID Laporan: #<?= str_pad($data['id_laporan'], 5, '0', STR_PAD_LEFT) ?></p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="cetak_laporan.php?id=<?= $id ?>&jenis=<?= $jenis ?>" 
            target="_blank" 
            class="btn-primary" 
            style="background:#27ae60;">
                🖨️ Cetak Kop Surat
            </a>
            <a href="periksa_laporan.php?id=<?= $id ?>&jenis=<?= $jenis ?>" class="btn-primary">
                ✏️ Periksa & Ubah Status
            </a>
        </div>
    </div>

    <div class="detail-grid">

        <!-- KOLOM KIRI: DETAIL LAPORAN -->
        <div>
            <div class="detail-card">
                <h3>📋 Informasi Laporan</h3>
                <div class="info-row">
                    <div class="info-label">Tanggal</div>
                    <div class="info-value">
                        <?= date('d F Y', strtotime($data['tanggal'])) ?>
                    </div>
                </div>
                <?php if ($jenis == 'satpam'): ?>
                <div class="info-row">
                    <div class="info-label">Waktu</div>
                    <div class="info-value"><?= htmlspecialchars($data['waktu']) ?> WIB</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Jenis Kegiatan</div>
                    <div class="info-value"><b><?= htmlspecialchars($data['jenis_kegiatan']) ?></b></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Lokasi</div>
                    <div class="info-value"><?= htmlspecialchars($data['lokasi']) ?></div>
                </div>
                <?php else: ?>
                <div class="info-row">
                    <div class="info-label">Area Kerja</div>
                    <div class="info-value"><b><?= htmlspecialchars($data['area_kerja']) ?></b></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Jenis Pekerjaan</div>
                    <div class="info-value"><?= htmlspecialchars($data['jenis_pekerjaan']) ?></div>
                </div>
                <?php endif; ?>
                <div class="info-row">
                    <div class="info-label">Kondisi</div>
                    <div class="info-value"><?= htmlspecialchars($data['kondisi']) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Pelapor</div>
                    <div class="info-value">
                        <b><?= htmlspecialchars($data['nama']) ?></b>
                        <span style="color:#999;font-size:12px;">
                            (<?= ucfirst($data['kategori_tugas']) ?>)
                        </span>
                    </div>
                </div>
            </div>

            <?php if ($jenis == 'satpam'): ?>
                <?php if ($data['uraian']): ?>
                <div class="detail-card">
                    <h3>📝 Uraian Kegiatan</h3>
                    <div class="rincian-box"><?= htmlspecialchars($data['uraian']) ?></div>
                </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="detail-card">
                <h3>🔧 Rincian Pekerjaan</h3>
                <div class="rincian-box"><?= htmlspecialchars($data['rincian_pekerjaan']) ?></div>
            </div>

            <?php if ($jenis == 'satpam' && $data['kejadian']): ?>
            <div class="detail-card">
                <h3>⚠️ Kejadian yang Perlu Dilaporkan</h3>
                <div class="rincian-box"><?= htmlspecialchars($data['kejadian']) ?></div>
            </div>
            <?php endif; ?>

            <?php if ($jenis == 'satpam' && $data['tindak_lanjut']): ?>
            <div class="detail-card">
                <h3>✅ Tindak Lanjut</h3>
                <div class="rincian-box"><?= htmlspecialchars($data['tindak_lanjut']) ?></div>
            </div>
            <?php endif; ?>

            <?php if ($jenis == 'kebersihan' && $data['keterangan']): ?>
            <div class="detail-card">
                <h3>📝 Keterangan Tambahan</h3>
                <div class="rincian-box"><?= htmlspecialchars($data['keterangan']) ?></div>
            </div>
            <?php endif; ?>

            <!-- DOKUMENTASI FOTO -->
            <div class="detail-card">
                <h3>📸 Dokumentasi Foto</h3>
                <?php if ($data['dokumentasi']): ?>
                    <img src="../uploads/dokumentasi/<?= htmlspecialchars($data['dokumentasi']) ?>" 
                         class="foto-dokumentasi"
                         onclick="openLightbox(this.src)"
                         alt="Dokumentasi">
                    <p style="font-size:12px;color:#999;margin-top:8px;">
                        💡 Klik gambar untuk memperbesar
                    </p>
                <?php else: ?>
                    <div class="foto-empty">📷 Tidak ada dokumentasi foto</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- KOLOM KANAN: STATUS & TIMELINE -->
        <div>

            <!-- STATUS CARD -->
            <div class="status-card">
                <div class="status-icon-big">
                    <?php 
                        if ($data['status'] == 'Diajukan') echo '⏳';
                        elseif ($data['status'] == 'Disetujui') echo '✅';
                        else echo '⚠️';
                    ?>
                </div>
                <div class="status-label-big status-<?= $status_class ?>">
                    <?= $data['status'] ?>
                </div>

                <?php if ($data['catatan_admin']): ?>
                    <div style="text-align:left;margin-top:15px;padding:12px;background:#f8fbfd;border-radius:8px;font-size:13px;">
                        <b style="color:#1a5276;">Catatan Admin:</b>
                        <p style="margin-top:6px;color:#555;"><?= nl2br(htmlspecialchars($data['catatan_admin'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- TIMELINE -->
            <div class="detail-card" style="margin-top:20px;">
                <h3>🕐 Riwayat Laporan</h3>
                <div class="timeline">

                    <!-- 1. Laporan Dibuat -->
                    <div class="timeline-item">
                        <div class="timeline-title">📝 Laporan Dibuat</div>
                        <div class="timeline-desc">
                            Oleh <b><?= htmlspecialchars($data['nama']) ?></b>
                        </div>
                        <div class="timeline-time">
                            <?= date('d M Y, H:i', strtotime($data['created_at'])) ?>
                        </div>
                    </div>

                    <!-- 2. Diperiksa / Disetujui -->
                    <?php if ($data['diperiksa_pada']): ?>
                        <div class="timeline-item <?= $data['status'] == 'Disetujui' ? 'success' : 'danger' ?>">
                            <div class="timeline-title">
                                <?php if ($data['status'] == 'Disetujui'): ?>
                                    ✅ Laporan Disetujui
                                <?php else: ?>
                                    ⚠️ Perlu Perbaikan
                                <?php endif; ?>
                            </div>
                            <div class="timeline-desc">
                                Oleh <b><?= htmlspecialchars($data['nama_admin'] ?? 'Admin') ?></b>
                            </div>
                            <div class="timeline-time">
                                <?= date('d M Y, H:i', strtotime($data['diperiksa_pada'])) ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="timeline-item warning">
                            <div class="timeline-title">⏳ Menunggu Pemeriksaan</div>
                            <div class="timeline-desc">
                                Belum diperiksa oleh admin
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- 3. Update Terakhir -->
                    <?php if ($data['updated_at'] && $data['updated_at'] != $data['created_at']): ?>
                        <div class="timeline-item">
                            <div class="timeline-title">🔄 Terakhir Diupdate</div>
                            <div class="timeline-time">
                                <?= date('d M Y, H:i', strtotime($data['updated_at'])) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

            <!-- ACTION -->
            <div class="action-buttons" style="margin-top:20px;">
                <a href="dashboard.php" class="btn-secondary">← Kembali</a>
            </div>

        </div>
    </div>

</div>

<!-- LIGHTBOX FOTO -->
<div class="lightbox" id="lightbox" onclick="closeLightbox()">
    <img id="lightboxImg" src="" alt="Preview">
</div>

<script>
function openLightbox(src) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightbox').classList.add('active');
}
function closeLightbox() {
    document.getElementById('lightbox').classList.remove('active');
}
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>