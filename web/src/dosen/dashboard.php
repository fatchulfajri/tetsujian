<?php 
// Memastikan session sudah berjalan sebelum mengecek role
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../config.php';

// Proteksi halaman
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'dosen') {
    // Menggunakan tampilan error yang sedikit lebih rapi daripada sekadar die()
    echo "<div style='font-family:sans-serif; text-align:center; margin-top:50px; color:#e74c3c;'>
            <h2>⚠️ Akses Ditolak</h2>
            <p>Halaman ini hanya dapat diakses oleh Dosen/Admin.</p>
          </div>";
    die();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Dosen - Admin Control</title>
    <style>
        /* Base Reset */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f8fafc;
            padding: 20px;
        }

        /* Container Panel Admin */
        .admin-panel {
            background: #ffffff;
            max-width: 800px;
            margin: 0 auto;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            padding: 20px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-left: 5px solid #2c3e50;
        }

        /* Informasi Judul Panel */
        .panel-title {
            display: flex;
            flex-direction: column;
        }

        .panel-title h3 {
            color: #2c3e50;
            font-size: 1.1rem;
            font-weight: 700;
        }

        .panel-title p {
            color: #7f8c8d;
            font-size: 0.8rem;
            margin-top: 2px;
        }

        /* Grouping Tombol Aksi */
        .action-group {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        /* Base Button Styling */
        .btn {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            padding: 10px 18px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        /* Tombol Export CSV (Aksi Positif/Normal) */
        .btn-export {
            background-color: #e3f2fd;
            color: #1e88e5;
        }

        .btn-export:hover {
            background-color: #1e88e5;
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(30, 136, 229, 0.2);
        }

        /* Tombol Reset (Aksi Berbahaya/Danger) */
        .btn-reset {
            background-color: #ffebee;
            color: #e53935;
        }

        .btn-reset:hover {
            background-color: #e53935;
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(229, 57, 53, 0.2);
        }
    </style>
</head>
<body>

<div class="admin-panel">
    <!-- Bagian Kiri: Identitas Halaman -->
    <div class="panel-title">
        <h3>👨‍🏫 Panel Kontrol Dosen</h3>
        <p>Manajemen data dan sistem scoreboard</p>
    </div>

    <!-- Bagian Kanan: Menu Navigasi Utama -->
    <div class="action-group">
        <a href="export_csv.php" class="btn btn-export">
            📥 Export CSV
        </a>
        
        <!-- Diberi warna merah sebagai tanda "Peringatan/Bahaya" agar tidak salah klik -->
        <a href="reset.php" class="btn btn-reset" onclick="return confirm('Apakah Anda yakin ingin MERESET semua data? Tindakan ini tidak bisa dibatalkan!');">
            ⚠️ RESET DATA
        </a>
    </div>
</div>

</body>
</html>