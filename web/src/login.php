<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim  = $_POST['nim'];
    $pass = $_POST['password'];

    $q = $conn->query(
        "SELECT * FROM users WHERE nim='$nim' AND password='$pass'"
    );

    if ($q && $q->num_rows > 0) {
        $_SESSION['user'] = $q->fetch_assoc();
        header("Location: index.php");
        exit;
    } else {
        $error = "Login gagal!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Scoreboard</title>
    <style>
        /* Reset dan Base Styling */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* Kontainer Form Login (Card) */
        .login-card {
            background: #ffffff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h2 {
            color: #2c3e50;
            font-size: 1.8rem;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .subtitle {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-bottom: 24px;
        }

        /* Notifikasi Error */
        .error-message {
            background-color: #fde8e8;
            color: #e74c3c;
            padding: 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            border-left: 4px solid #e74c3c;
            text-align: left;
        }

        /* Grouping Form Input */
        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group label {
            display: block;
            margin-bottom: 6px;
            color: #34495e;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-group input {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid #dcdde1;
            border-radius: 8px;
            font-size: 1rem;
            color: #2c3e50;
            outline: none;
            transition: all 0.3s ease;
        }

        /* Efek fokus saat input diklik */
        .input-group input:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.15);
        }

        /* Tombol Login */
        .btn-login {
            width: 100%;
            padding: 14px;
            background-color: #3498db;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.1s ease;
            margin-top: 10px;
        }

        .btn-login:hover {
            background-color: #2980b9;
        }

        .btn-login:active {
            transform: scale(0.98);
        }
    </style>
</head>
<body>

<div class="login-card">
    <h2>Selamat Datang</h2>
    <p class="subtitle">Silakan login untuk mengakses akun Anda</p>

    <?php if (!empty($error)): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <label for="nim">NIM</label>
            <input type="text" id="nim" name="nim" placeholder="Masukkan NIM Anda" required autocomplete="off">
        </div>
        
        <div class="input-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Masukkan Password" required>
        </div>
        
        <button type="submit" class="btn-login">Masuk Aplikasi</button>
    </form>
</div>

</body>
</html>