# 📋 Sistem Pelaporan PPPK Satpam & Kebersihan

Sistem informasi berbasis web untuk pelaporan kegiatan **PPPK Satpam dan Kebersihan** di **Badan Pusat Statistik (BPS) Kota Medan**. Dibangun dengan **PHP Native**, **PDO**, dan **MySQL**, dilengkapi fitur export Excel, PDF, grafik statistik, serta notifikasi Email & WhatsApp.

---

## 📌 Daftar Isi

- [Fitur Utama](#-fitur-utama)
- [Teknologi yang Digunakan](#-teknologi-yang-digunakan)
- [Struktur Folder](#-struktur-folder)
- [Persyaratan Sistem](#-persyaratan-sistem)
- [Instalasi](#-instalasi)
- [Konfigurasi](#-konfigurasi)
- [Penggunaan](#-penggunaan)
- [Akun Default](#-akun-default)
- [Screenshot](#-screenshot)
- [API & Library](#-api--library)
- [Troubleshooting](#-troubleshooting)
- [Lisensi](#-lisensi)
- [Kontributor](#-kontributor)

---

## ✨ Fitur Utama

### 🔐 Autentikasi
- Login dengan pembedaan hak akses (Petugas & Administrator)
- Password terenkripsi dengan `password_hash()` (bcrypt)
- Session management yang aman

### 👤 Modul Petugas (Satpam & Kebersihan)
- Dashboard dengan menu card
- Isi laporan kegiatan satpam (tanggal, waktu, kegiatan, rincian pekerjaan, kejadian, tindak lanjut, kondisi)
- Isi laporan kegiatan kebersihan (tanggal, area kerja, jenis pekerjaan, rincian pekerjaan, kondisi, keterangan)
- Upload foto dokumentasi
- Lihat riwayat laporan
- Melihat catatan dari admin

### 👨‍💼 Modul Administrator
- Dashboard dengan statistik (total laporan, total petugas)
- **Periksa & validasi laporan** → ubah status (Diajukan, Disetujui, Perlu Perbaikan)
- **Kelola Data Petugas** (CRUD: Create, Read, Update, Delete)
- **Cari & Filter Laporan** berdasarkan nama, jenis, dan rentang tanggal
- **Rekapitulasi Laporan** per bulan/tahun
- **Export Excel** (PhpSpreadsheet)
- **Export PDF** (DomPDF)
- **Grafik Statistik** interaktif (Chart.js)
- Cetak laporan (Print)
- Notifikasi Email & WhatsApp otomatis

### 📧 Notifikasi Otomatis
- **Email** (PHPMailer + Gmail SMTP) — saat laporan baru dikirim & saat status diubah
- **WhatsApp** (Fonnte API) — notifikasi ke admin & petugas

### 🎨 Desain
- UI/UX responsif dengan CSS murni
- Logo terintegrasi di halaman login & header
- Badge status berwarna (kuning: diajukan, hijau: disetujui, merah: perlu perbaikan)

---

## 🛠 Teknologi yang Digunakan

| Teknologi | Versi | Fungsi |
|:---|:---|:---|
| **PHP** | 7.4+ | Bahasa pemrograman server-side |
| **PDO** | - | Koneksi database (prepared statement) |
| **MySQL** | 5.7+ / 8.0 | Basis data |
| **HTML5 & CSS3** | - | Struktur & tampilan |
| **JavaScript** | - | Interaksi & Chart.js |
| **Composer** | 2.x | Manajemen dependensi |
| **PhpSpreadsheet** | ^1.29 | Export Excel |
| **DomPDF** | ^2.0 | Export PDF |
| **PHPMailer** | ^6.8 | Kirim email SMTP |
| **Fonnte API** | - | Kirim WhatsApp |
| **Chart.js** | 4.x | Grafik statistik |
| **XAMPP** | - | Web server lokal |

---

## 📁 Struktur Folder

```
sistem-pelaporan/
├── config/
│   └── database.php             # Konfigurasi database + notifikasi
├── assets/
│   ├── css/
│   │   └── style.css            # Styling utama
│   ├── js/
│   │   └── script.js            # JavaScript tambahan
│   └── img/
│       └── logo.png             # Logo aplikasi
├── includes/
│   ├── header.php               # Header aplikasi
│   ├── footer.php               # Footer aplikasi
│   └── session.php              # Session & autentikasi
├── auth/
│   ├── login.php                # Halaman login
│   ├── proses_login.php         # Proses validasi login
│   └── logout.php               # Logout
├── petugas/
│   ├── dashboard.php            # Dashboard petugas
│   ├── laporan_satpam.php       # Form laporan satpam
│   ├── laporan_kebersihan.php   # Form laporan kebersihan
│   ├── simpan_laporan.php       # Proses simpan laporan
│   └── riwayat.php              # Riwayat laporan petugas
├── admin/
│   ├── dashboard.php            # Dashboard admin
│   ├── kelola_petugas.php       # Daftar petugas
│   ├── tambah_petugas.php       # Form tambah petugas
│   ├── edit_petugas.php         # Form edit petugas
│   ├── hapus_petugas.php        # Hapus petugas
│   ├── periksa_laporan.php      # Periksa laporan
│   ├── update_status.php        # Update status laporan
│   ├── cari_laporan.php         # Cari laporan
│   ├── rekapitulasi.php         # Rekapitulasi laporan
│   ├── grafik.php               # Grafik statistik
│   ├── export_excel.php         # Export Excel
│   └── export_pdf.php           # Export PDF
├── notifikasi/
│   ├── email.php                # Fungsi kirim email
│   └── whatsapp.php             # Fungsi kirim WA
├── uploads/
│   └── dokumentasi/             # Folder upload foto
├── vendor/                      # Library Composer (auto-generate)
├── database/
│   └── db_pelaporan.sql         # Struktur & data awal database
├── composer.json                # Konfigurasi Composer
├── generate_hash.php            # Tool generate hash password
├── reset_password.php           # Tool reset password (opsional)
├── index.php                    # Entry point
└── README.md                    # Dokumentasi ini
```

---

## 💻 Persyaratan Sistem

- **XAMPP** (Apache + MySQL + PHP 7.4+)
- **Composer** (untuk install library)
- **Browser modern** (Chrome, Firefox, Edge)
- **Koneksi internet** (untuk notifikasi Email/WA & CDN Chart.js)

---

## 🚀 Instalasi

### 1. Clone / Download Proyek

Letakkan folder proyek di:
```
C:\xampp\htdocs\sistem-pelaporan
```

### 2. Install Composer Dependencies

Buka **CMD** di folder proyek, jalankan:
```bash
composer install
```

Jika muncul error karena antivirus mengunci file:
```bash
# Tutup XAMPP & VS Code dulu
composer clear-cache
composer install
```

Atau tambahkan folder proyek ke **exclusion antivirus**.

### 3. Import Database

1. Start **Apache** & **MySQL** di XAMPP Control Panel
2. Buka `http://localhost/phpmyadmin`
3. Buat database baru: **`db_pelaporan`**
4. Tab **Import** → pilih file `database/db_pelaporan.sql` → **Go**

### 4. Generate Password Hash

1. Buka `http://localhost/sistem-pelaporan/generate_hash.php`
2. Copy hash untuk admin & petugas
3. Update di phpMyAdmin:
   - Tabel `administrator` → kolom `password`
   - Tabel `petugas` → kolom `password`

Atau gunakan `reset_password.php` untuk auto-update.

### 5. Buat Folder Upload

Buat folder:
```
C:\xampp\htdocs\sistem-pelaporan\uploads\dokumentasi\
```

### 6. Siapkan Logo

Simpan logo Anda sebagai:
```
assets/img/logo.png
```

### 7. Jalankan

Buka browser:
```
http://localhost/sistem-pelaporan
```

---

## ⚙️ Konfigurasi

Edit file `config/database.php`:

```php
// Database
$host = "localhost";
$db   = "db_pelaporan";
$user = "root";
$pass = "";

// Email (Gmail SMTP)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'emailanda@gmail.com');     // Gmail Anda
define('SMTP_PASS', 'app_password_gmail');       // App Password
define('SMTP_PORT', 587);
define('SMTP_FROM_NAME', 'Sistem Pelaporan BPS');

// WhatsApp (Fonnte)
define('FONNTE_TOKEN', 'token_fonnte_anda');     // Token Fonnte

// Admin
define('ADMIN_EMAIL', 'admin@bps.go.id');
define('ADMIN_WA', '081234567890');
```

### 📧 Cara Dapat App Password Gmail:
1. Buka https://myaccount.google.com/apppasswords
2. Aktifkan **2-Step Verification** dulu
3. Generate **App Password** → copy 16 karakter
4. Paste ke `SMTP_PASS`

### 📱 Cara Dapat Token Fonnte:
1. Daftar di https://fonnte.com
2. Login → hubungkan WhatsApp dengan scan QR
3. Copy **Token** di menu **Device**
4. Paste ke `FONNTE_TOKEN`

> 💡 **Catatan**: Jika belum dikonfigurasi, sistem tetap jalan — hanya notifikasi tidak terkirim.

---

## 🎯 Penggunaan

### Sebagai Petugas (Satpam/Kebersihan)
1. **Login** dengan akun petugas
2. Pilih menu **Isi Laporan** (satpam/kebersihan)
3. Isi formulir + upload foto
4. Klik **Kirim Laporan**
5. Cek **Riwayat Laporan** untuk melihat status

### Sebagai Administrator
1. **Login** dengan akun admin
2. **Dashboard** → lihat statistik & laporan terbaru
3. **Periksa Laporan** → ubah status + catatan
4. **Kelola Petugas** → CRUD data petugas
5. **Cari Laporan** → filter berdasarkan nama, jenis, tanggal
6. **Rekapitulasi** → pilih bulan/tahun → Export Excel/PDF/Cetak
7. **Grafik Statistik** → lihat visualisasi data

---

## 🔑 Akun Default

| Role | Username | Password |
|:---|:---|:---|
| Administrator | `admin` | `admin123` |
| Petugas Satpam | `ahmad` | `petugas123` |
| Petugas Kebersihan | `siti` | `petugas123` |

> ⚠️ **Segera ganti password** setelah login pertama!

---

## 📸 Screenshot

> Tambahkan screenshot di folder `docs/` dan tampilkan di sini.

| Halaman | Deskripsi |
|:---|:---|
| `docs/login.png` | Halaman Login dengan logo |
| `docs/dashboard-admin.png` | Dashboard Administrator |
| `docs/dashboard-petugas.png` | Dashboard Petugas |
| `docs/form-laporan.png` | Form Laporan Satpam |
| `docs/periksa-laporan.png` | Halaman Periksa Laporan |
| `docs/rekapitulasi.png` | Rekapitulasi + Export |
| `docs/grafik.png` | Grafik Statistik Chart.js |

---

## 📚 API & Library

### Composer Packages
| Package | Versi | Fungsi |
|:---|:---|:---|
| `phpoffice/phpspreadsheet` | ^1.29 | Export Excel (.xlsx) |
| `dompdf/dompdf` | ^2.0 | Export PDF |
| `phpmailer/phpmailer` | ^6.8 | Kirim email SMTP |

### CDN
- **Chart.js** v4 — `https://cdn.jsdelivr.net/npm/chart.js`

### API Eksternal
- **Fonnte API** — `https://api.fonnte.com/send` (WhatsApp)

---

## 🐛 Troubleshooting

### ❌ Composer install gagal
**Pesan**: `Could not delete ... This can be due to an antivirus...`

**Solusi**:
1. Tutup XAMPP, VS Code, File Explorer
2. Hapus folder `vendor/` dan `composer.lock`
3. `composer clear-cache`
4. Jalankan CMD sebagai Administrator
5. `composer install`
6. Tambahkan folder ke exclusion antivirus

### ❌ Koneksi database gagal
- Pastikan MySQL sudah **Start** di XAMPP
- Cek nama database: `db_pelaporan`
- Cek user/pass di `config/database.php`

### ❌ Login gagal terus
- Pastikan hash password di database **60 karakter** dan diawali `$2y$10$`
- Cek tidak ada spasi di awal/akhir hash
- Jalankan `reset_password.php` untuk reset

### ❌ Export Excel/PDF error
- Pastikan `composer install` sukses
- Cek folder `vendor/` ada
- Cek `vendor/autoload.php` ada

### ❌ Notifikasi Email/WA tidak terkirim
- Cek kredensial SMTP/Fonnte di `config/database.php`
- Cek log: `C:\xampp\apache\logs\error.log`
- Untuk Gmail, gunakan **App Password** (bukan password biasa)

### ❌ Upload foto gagal
- Pastikan folder `uploads/dokumentasi/` ada
- Linux/Mac: `chmod 777 uploads/dokumentasi`
- Cek ukuran file (maks 2 MB)

### ❌ Grafik Chart.js tidak muncul
- Pastikan koneksi internet aktif (Chart.js via CDN)
- Cek Console browser (F12) untuk error

---

1. Password Hash
Password di SQL di atas menggunakan hash contoh. Setelah import, buka:

text
http://localhost/sistem-pelaporan/generate_hash.php
Copy hash untuk admin123 dan petugas123, lalu update di phpMyAdmin:

Update admin:

sql
UPDATE administrator 
SET password = '$2y$10$WjGyHoJkHhFSs29dkxM7GuP.auFhv.gQGoN/84bIgftgTbR86jsZO' 
WHERE username = 'admin';
Update petugas:

sql
UPDATE petugas 
SET password = '$2y$10$iLMw3j0iLZTyNIo.gBApM.kzGON89TpOQnEt2OZ7sT3n14K7qAJsW' 
WHERE username IN ('ahmad', 'siti');
Atau gunakan file reset_password.php yang sudah kita buat sebelumnya untuk auto-update semua hash.

## 📝 Lisensi

Proyek ini dibuat untuk keperluan **Praktek Kerja Lapangan (PKL)** di **Institut Teknologi dan Bisnis Indonesia (ITBI)** tahun **2026**.

© 2026 — **Dela Indah Melati** (NIM: 23040111)

---

## 👥 Kontributor

| Nama | Peran |
|:---|:---|
| **Dela Indah Melati** | Penulis & Pengembang |
| **Dr. Roberto Kaban, M.Kom** | Dosen Pembimbing |
| **BPS Kota Medan** | Instansi PKL |

---

## 📞 Kontak

- **Email**: [email Anda]
- **Instansi**: Badan Pusat Statistik Kota Medan
- **Alamat**: Jl. Gaperta/Brigjen H. Abdul Manaf Lubis No. 311, Medan Helvetia

---

## 🎓 Ucapan Terima Kasih

Terima kasih kepada:
- Tuhan Yang Maha Esa
- Kedua orang tua
- Dr. Roberto Kaban, M.Kom (Dosen Pembimbing)
- BPS Kota Medan (Instansi PKL)
- Institut Teknologi dan Bisnis Indonesia (ITBI)

---

**⭐ Jika proyek ini bermanfaat, jangan lupa beri bintang!** ⭐