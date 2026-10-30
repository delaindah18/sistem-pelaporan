<?php
// ============ DATABASE ============
if (!isset($pdo)) {
    $host = "localhost";
    $db   = "db_pelaporan";
    $user = "root";
    $pass = "";
    $charset = "utf8mb4";

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        die("Koneksi database gagal: " . $e->getMessage());
    }
}

// ============ KONFIGURASI NOTIFIKASI ============
// Cek dulu sebelum define, agar tidak error "already defined"

if (!defined('SMTP_HOST'))      define('SMTP_HOST', 'smtp.gmail.com');
if (!defined('SMTP_USER'))      define('SMTP_USER', 'emailanda@gmail.com');
if (!defined('SMTP_PASS'))      define('SMTP_PASS', 'password_app_anda');
if (!defined('SMTP_PORT'))      define('SMTP_PORT', 587);
if (!defined('SMTP_FROM_NAME')) define('SMTP_FROM_NAME', 'Sistem Pelaporan BPS');

if (!defined('FONNTE_TOKEN'))   define('FONNTE_TOKEN', 'token_fonnte_anda');

if (!defined('ADMIN_EMAIL'))    define('ADMIN_EMAIL', 'admin@bps.go.id');
if (!defined('ADMIN_WA'))       define('ADMIN_WA', '081234567890');
?>