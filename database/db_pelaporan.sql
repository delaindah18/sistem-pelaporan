-- ============================================
-- DATABASE: Sistem Pelaporan PPPK Satpam & Kebersihan
-- Instansi: BPS Kota Medan
-- Versi: Final (Lengkap)
-- ============================================

DROP DATABASE IF EXISTS db_pelaporan;
CREATE DATABASE db_pelaporan 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;
USE db_pelaporan;

-- ============================================
-- TABEL: administrator
-- ============================================
CREATE TABLE administrator (
    id_admin INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABEL: petugas
-- ============================================
CREATE TABLE petugas (
    id_petugas INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    kategori_tugas ENUM('satpam','kebersihan') NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) NULL,
    email VARCHAR(100),
    no_hp VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABEL: laporan_satpam
-- ============================================
CREATE TABLE laporan_satpam (
    id_laporan INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_petugas INT(11) NOT NULL,
    tanggal DATE NOT NULL,
    waktu TIME NOT NULL,
    jenis_kegiatan VARCHAR(100) NOT NULL,
    rincian_pekerjaan TEXT NOT NULL,
    lokasi VARCHAR(100) NOT NULL,
    uraian TEXT NOT NULL,
    kejadian TEXT,
    tindak_lanjut TEXT,
    kondisi VARCHAR(100) NOT NULL,
    dokumentasi VARCHAR(255),
    status ENUM('Diajukan','Disetujui','Perlu Perbaikan') DEFAULT 'Diajukan',
    catatan_admin TEXT,
    diperiksa_oleh INT(11) NULL,
    diperiksa_pada TIMESTAMP NULL,
    kode_verifikasi VARCHAR(20) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_petugas) REFERENCES petugas(id_petugas) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABEL: laporan_kebersihan
-- ============================================
CREATE TABLE laporan_kebersihan (
    id_laporan INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_petugas INT(11) NOT NULL,
    tanggal DATE NOT NULL,
    area_kerja VARCHAR(100) NOT NULL,
    jenis_pekerjaan VARCHAR(100) NOT NULL,
    rincian_pekerjaan TEXT NOT NULL,
    kondisi VARCHAR(100) NOT NULL,
    keterangan TEXT,
    dokumentasi VARCHAR(255),
    status ENUM('Diajukan','Disetujui','Perlu Perbaikan') DEFAULT 'Diajukan',
    catatan_admin TEXT,
    diperiksa_oleh INT(11) NULL,
    diperiksa_pada TIMESTAMP NULL,
    kode_verifikasi VARCHAR(20) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_petugas) REFERENCES petugas(id_petugas) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- INDEKS untuk performa
-- ============================================
CREATE INDEX idx_satpam_tanggal ON laporan_satpam(tanggal);
CREATE INDEX idx_satpam_status ON laporan_satpam(status);
CREATE INDEX idx_kebersihan_tanggal ON laporan_kebersihan(tanggal);
CREATE INDEX idx_kebersihan_status ON laporan_kebersihan(status);

-- ============================================
-- DATA DEFAULT: ADMINISTRATOR
-- Password: admin123 (hash bcrypt)
-- ============================================
INSERT INTO administrator (nama, username, password, email) VALUES
('Admin BPS Medan', 'admin', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1HlCS4bZJ18JuywdB6aV6Vv6q6q6q6q', 'admin@bps.go.id');

-- ============================================
-- DATA DEFAULT: PETUGAS
-- Password: petugas123 (hash bcrypt)
-- ============================================
INSERT INTO petugas (nama, kategori_tugas, username, password, email, no_hp) VALUES
('Ahmad', 'satpam', 'ahmad', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1HlCS4bZJ18JuywdB6aV6Vv6q6q6q6q', 'ahmad@bps.go.id', '081234567890'),
('Siti', 'kebersihan', 'siti', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1HlCS4bZJ18JuywdB6aV6Vv6q6q6q6q', 'siti@bps.go.id', '081234567891');

-- ============================================
-- DATA DUMMY: LAPORAN SATPAM
-- ============================================
INSERT INTO laporan_satpam 
(id_petugas, tanggal, waktu, jenis_kegiatan, rincian_pekerjaan, lokasi, uraian, kejadian, tindak_lanjut, kondisi, status) 
VALUES
(1, '2026-09-14', '08:00:00', 'Pemeriksaan Lingkungan', 
'1. Memeriksa seluruh ruangan kantor\n2. Mengawasi keluar-masuk tamu\n3. Patroli area kantor\n4. Memeriksa pintu dan jendela\n5. Mencatat kejadian penting', 
'Kantor BPS Kota Medan', 
'Melakukan pemeriksaan lingkungan kantor, mengawasi keluar-masuk tamu dan patroli area kantor.', 
'Tidak ada kejadian mencurigakan', 
'Kondisi aman dan terkendali', 
'Aman dan terkendali', 
'Disetujui'),

(1, '2026-09-15', '08:00:00', 'Patroli Area', 
'1. Patroli keliling gedung\n2. Memeriksa CCTV\n3. Memeriksa lampu penerangan', 
'Kantor BPS Kota Medan', 
'Patroli rutin area kantor', 
'-', '-', 
'Aman', 
'Disetujui'),

(1, '2026-09-16', '08:00:00', 'Pengawasan Tamu', 
'1. Mencatat identitas tamu\n2. Mengarahkan tamu ke bagian terkait\n3. Mengawasi keluar-masuk kendaraan', 
'Lobby Kantor BPS', 
'Mengawasi dan mencatat tamu yang datang ke kantor', 
'Tamu dari BPS Provinsi', 
'Diarahkan ke Kepala Bagian Umum', 
'Terkendali', 
'Diajukan'),

(1, '2026-10-14', '08:00:00', 'Pemeriksaan Rutin', 
'1. Memeriksa ruang kerja\n2. Memeriksa area parkir\n3. Memastikan pintu terkunci', 
'Kantor BPS Kota Medan', 
'Pemeriksaan rutin harian', 
'-', '-', 
'Aman dan terkendali', 
'Diajukan');

-- ============================================
-- DATA DUMMY: LAPORAN KEBERSIHAN
-- ============================================
INSERT INTO laporan_kebersihan 
(id_petugas, tanggal, area_kerja, jenis_pekerjaan, rincian_pekerjaan, kondisi, keterangan, status) 
VALUES
(2, '2026-09-14', 'Ruang Kerja, Toilet, Halaman, Area Parkir', 'Pembersihan Rutin',
'1. Membersihkan ruang kerja\n2. Membersihkan toilet\n3. Menyapu halaman\n4. Membersihkan area parkir\n5. Membuang sampah', 
'Lingkungan kantor bersih dan rapi', 
'Semua area telah dibersihkan sesuai jadwal', 
'Disetujui'),

(2, '2026-09-15', 'Ruang Rapat, Koridor, Musholla', 'Pembersihan Ruangan',
'1. Membersihkan ruang rapat\n2. Menyapu koridor\n3. Membersihkan musholla', 
'Bersih dan nyaman', 
'Ruang siap digunakan untuk rapat', 
'Disetujui'),

(2, '2026-09-16', 'Toilet Lantai 1 & 2', 'Pembersihan Toilet',
'1. Membersihkan lantai\n2. Membersihkan dinding\n3. Mengisi sabun dan tisu\n4. Membuang sampah', 
'Toilet bersih dan wangi', 
'Sudah dicek oleh koordinator', 
'Diajukan'),

(2, '2026-10-14', 'Ruang Kerja, Toilet, Halaman, Area Parkir', 'Pembersihan Rutin',
'1. Membersihkan ruang kerja\n2. Membersihkan toilet\n3. Menyapu halaman\n4. Membersihkan area parkir\n5. Membuang sampah', 
'Lingkungan kantor bersih dan rapi', 
'Semua area telah dibersihkan', 
'Diajukan');

-- ============================================
-- UPDATE: Set kode verifikasi untuk laporan yang sudah ada
-- ============================================
UPDATE laporan_satpam SET kode_verifikasi = UPPER(SUBSTRING(MD5(CONCAT(id_laporan, 'satpam', tanggal)), 1, 8));
UPDATE laporan_kebersihan SET kode_verifikasi = UPPER(SUBSTRING(MD5(CONCAT(id_laporan, 'kebersihan', tanggal)), 1, 8));

-- ============================================
-- SELESAI
-- ============================================