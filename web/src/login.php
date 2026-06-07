<?php
include 'config.php';
session_start();

$error = '';
$success = '';

// Handle Registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $nim = trim($_POST['nim']);
    $name = trim($_POST['name']);
    $team = trim($_POST['team']);
    $password = $_POST['password'];

    // Validate inputs
    if (empty($nim) || empty($name) || empty($team) || empty($password)) {
        $error = "Semua field harus diisi!";
    } else {
        // Check if NIM already exists
        $check = $conn->prepare("SELECT id FROM users WHERE nim = ?");
        $check->bind_param("s", $nim);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "NIM sudah terdaftar!";
        } else {
            // Register new user
            $stmt = $conn->prepare("INSERT INTO users (nim, name, team, password, role) VALUES (?, ?, ?, ?, 'peserta')");
            $stmt->bind_param("ssss", $nim, $name, $team, $password);

            if ($stmt->execute()) {
                $success = "Registrasi berhasil! Silakan login.";
            } else {
                $error = "Registrasi gagal. Silakan coba lagi.";
            }
        }
    }
}

// Handle Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $nim = trim($_POST['nim']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE nim = ?");
    $stmt->bind_param("s", $nim);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Check password (in production, use password_verify())
        if ($password === $user['password']) {
            $_SESSION['user'] = $user;

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "NIM tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CyberUAS - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-white min-h-screen flex items-center justify-center p-4">

    <!-- Main Container -->
    <div class="w-full max-w-md">
        <!-- Logo/Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-2xl mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">CyberUAS</h1>
            <p class="text-gray-500 mt-1">Platform Pembelajaran Cybersecurity</p>
        </div>

        <!-- Auth Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <!-- Tabs -->
            <div class="flex border-b border-gray-100">
                <button onclick="showTab('login')" id="login-tab" class="flex-1 py-3 text-sm font-semibold text-blue-600 border-b-2 border-blue-600 transition-all">
                    Login
                </button>
                <button onclick="showTab('register')" id="register-tab" class="flex-1 py-3 text-sm font-semibold text-gray-500 hover:text-gray-700 transition-all">
                    Daftar
                </button>
            </div>

            <div class="p-6">
                <!-- Error Message -->
                <?php if ($error): ?>
                <div class="mb-4 p-3 bg-red-50 border border-red-100 rounded-xl flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm text-red-600"><?php echo htmlspecialchars($error); ?></span>
                </div>
                <?php endif; ?>

                <!-- Success Message -->
                <?php if ($success): ?>
                <div class="mb-4 p-3 bg-green-50 border border-green-100 rounded-xl flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm text-green-600"><?php echo htmlspecialchars($success); ?></span>
                </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form id="login-form" method="POST">
                    <input type="hidden" name="login" value="1">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIM</label>
                            <input type="text" name="nim" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all outline-none"
                                placeholder="Masukkan NIM Anda">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input type="password" name="password" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all outline-none"
                                placeholder="Masukkan password">
                        </div>
                        <button type="submit"
                            class="w-full py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                            Masuk
                        </button>
                    </div>
                </form>

                <!-- Register Form -->
                <form id="register-form" method="POST" class="hidden">
                    <input type="hidden" name="register" value="1">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIM</label>
                            <input type="text" name="nim" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all outline-none"
                                placeholder="Masukkan NIM">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="name" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all outline-none"
                                placeholder="Masukkan nama lengkap">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Team</label>
                            <input type="text" name="team" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all outline-none"
                                placeholder="Nama team">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input type="password" name="password" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all outline-none"
                                placeholder="Buat password">
                        </div>
                        <button type="submit"
                            class="w-full py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                            Daftar Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-sm text-gray-400 mt-6">
            © 2026 CyberUAS. Platform Pembelajaran Cybersecurity
        </p>
    </div>

    <script>
        function showTab(tab) {
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            const loginTab = document.getElementById('login-tab');
            const registerTab = document.getElementById('register-tab');

            if (tab === 'login') {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
                loginTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
                loginTab.classList.remove('text-gray-500');
                registerTab.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
                registerTab.classList.add('text-gray-500');
            } else {
                loginForm.classList.add('hidden');
                registerForm.classList.remove('hidden');
                registerTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
                registerTab.classList.remove('text-gray-500');
                loginTab.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
                loginTab.classList.add('text-gray-500');
            }
        }
    </script>

</body>
</html>
