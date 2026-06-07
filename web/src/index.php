<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'config.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
$user = $_SESSION['user'];

// Mengamankan output teks agar terhindar dari celah XSS
$team = htmlspecialchars($user['team']);
$nim = htmlspecialchars($user['nim']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard CTF</title>
    <style>
        /* Reset & Base Style */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f4f6f9;
            color: #333;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        /* Container Utama (Dashboard Card) */
        .dashboard-container {
            background: #ffffff;
            width: 100%;
            max-width: 500px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 35px;
            position: relative;
            overflow: hidden;
        }

        /* Header Dashboard & Tombol Logout */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 20px;
        }

        .welcome-text h2 {
            color: #2c3e50;
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .welcome-text p {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        /* Tombol Navigasi Kecil di Atas */
        .nav-links-top {
            display: flex;
            gap: 10px;
        }

        .btn-secondary {
            text-decoration: none;
            font-size: 0.85rem;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-logout {
            background-color: #fff0f0;
            color: #e74c3c;
        }

        .btn-logout:hover {
            background-color: #e74c3c;
            color: white;
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #34495e;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Mengubah Select Option Menjadi Lebih Elegan */
        .form-group select, .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid #dcdde1;
            border-radius: 8px;
            font-size: 1rem;
            color: #2c3e50;
            outline: none;
            background-color: #fff;
            transition: all 0.3s ease;
        }

        .form-group select:focus, .form-group input:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.15);
        }

        /* Flag Input Monospace Style (Khas Hacker/CTF) */
        .flag-input {
            font-family: 'Courier New', Courier, monospace !important;
            font-weight: bold;
            letter-spacing: 1px;
        }

        /* Tombol Submit Utama */
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.2);
            transition: all 0.2s ease;
            margin-top: 10px;
        }

        .btn-submit:hover {
            opacity: 0.95;
            box-shadow: 0 6px 15px rgba(52, 152, 219, 0.3);
            transform: translateY(-1px);
        }

        .btn-submit:active {
            transform: translateY(1px);
        }

        /* Menu Bawah (Menuju Scoreboard) */
        .dashboard-footer {
            margin-top: 25px;
            text-align: center;
            border-top: 1px dashed #e2e8f0;
            padding-top: 20px;
        }

        .btn-scoreboard {
            display: inline-block;
            text-decoration: none;
            color: #3498db;
            font-size: 0.95rem;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .btn-scoreboard:hover {
            color: #1a5276;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <div class="dashboard-header">
        <div class="welcome-text">
            <h2>Hii, Team <?= $team ?>!</h2>
            <p>NIM: <?= $nim ?></p>
        </div>
        <div class="nav-links-top">
            <a href="logout.php" class="btn-secondary btn-logout">Logout</a>
        </div>
    </div>

    <form method="POST" action="submit_flag.php">
        <div class="form-group">
            <label for="challenge">Pilih Tantangan (Challenge)</label>
            <select name="challenge" id="challenge">
                <option value="easy">🟢 Easy (Mudah)</option>
                <option value="medium">🟡 Medium (Sedang)</option>
                <option value="hard">🔴 Hard (Sulit)</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="flag">Masukkan Flag</label>
            <input type="text" name="flag" id="flag" class="flag-input" placeholder="FORMAT{fl4g_4nd4_d1s1n1}" required autocomplete="off">
        </div>
        
        <button type="submit" class="btn-submit">Submit Flag </button>
    </form>

    <div class="dashboard-footer">
        <a href="scoreboard.php" class="btn-scoreboard">Lihat Scoreboard Live</a>
    </div>

</div>

</body>
</html>