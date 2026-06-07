<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../config.php';

// Check if user is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Handle Add User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $nim = trim($_POST['nim']);
    $name = trim($_POST['name']);
    $team = trim($_POST['team']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("INSERT INTO users (nim, name, team, password, role) VALUES (?, ?, ?, ?, 'peserta')");
    $stmt->bind_param("ssss", $nim, $name, $team, $password);

    if ($stmt->execute()) {
        $success = "User berhasil ditambahkan!";
    } else {
        $error = "Gagal menambahkan user. NIM mungkin sudah ada.";
    }
}

// Handle Delete User
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    header("Location: dashboard.php");
    exit;
}

// Get all users with their stats
$users_query = "SELECT u.*,
    (SELECT SUM(c.points) FROM submissions s JOIN challenges c ON s.challenge_id = c.id WHERE s.user_id = u.id AND s.is_correct = TRUE) as total_score,
    (SELECT COUNT(*) FROM submissions s WHERE s.user_id = u.id AND s.is_correct = TRUE) as solved_count
    FROM users u WHERE u.role = 'peserta' ORDER BY total_score DESC";
$users_result = $conn->query($users_query);

// Get overall stats
$total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'peserta'")->fetch_assoc()['count'];
$total_challenges = $conn->query("SELECT COUNT(*) as count FROM challenges")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CyberUAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <span class="font-bold text-xl text-gray-900">CyberUAS Admin</span>
                </div>
                <div class="flex items-center gap-4">
                    <a href="../scoreboard.php" class="text-gray-600 hover:text-gray-900 transition">Scoreboard</a>
                    <a href="../logout.php" class="text-red-500 hover:text-red-700 transition">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 001 5.996M12 15h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Peserta</p>
                        <p class="text-2xl font-bold text-gray-900"><?= $total_users ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Challenges</p>
                        <p class="text-2xl font-bold text-gray-900"><?= $total_challenges ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Avg. Score</p>
                        <?php
                        $avg = $conn->query("SELECT AVG(total_score) as avg FROM (SELECT SUM(c.points) as total_score FROM submissions s JOIN challenges c ON s.challenge_id = c.id WHERE s.is_correct = TRUE GROUP BY s.user_id) as scores")->fetch_assoc()['avg'];
                        ?>
                        <p class="text-2xl font-bold text-gray-900"><?= round($avg ?? 0) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add User Form -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-8">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Tambah Peserta Baru</h2>

            <?php if (isset($success)): ?>
            <div class="mb-4 p-4 bg-green-50 border border-green-100 rounded-xl text-green-700">
                <?= htmlspecialchars($success) ?>
            </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
            <div class="mb-4 p-4 bg-red-50 border border-red-100 rounded-xl text-red-700">
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="hidden" name="add_user" value="1">
                <input type="text" name="nim" required placeholder="NIM" class="px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all outline-none">
                <input type="text" name="name" required placeholder="Nama Lengkap" class="px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all outline-none">
                <input type="text" name="team" required placeholder="Team" class="px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all outline-none">
                <input type="password" name="password" required placeholder="Password" class="px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all outline-none">
                <button type="submit" class="md:col-span-4 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-all">
                    Tambah Peserta
                </button>
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Daftar Peserta & Progress</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">NIM</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Team</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Score</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Progress</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php while ($user = $users_result->fetch_assoc()): ?>
                        <?php $progress = ($user['solved_count'] / $total_challenges) * 100; ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($user['nim']) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($user['name']) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-600"><?= htmlspecialchars($user['team']) ?></td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-700 rounded-lg font-semibold text-sm">
                                    <?= $user['total_score'] ?? 0 ?> pts
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 w-24 bg-gray-100 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: <?= $progress ?>%"></div>
                                    </div>
                                    <span class="text-sm text-gray-500"><?= $user['solved_count'] ?>/<?= $total_challenges ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="?delete=<?= $user['id'] ?>" onclick="return confirm('Hapus user ini?')" class="text-red-500 hover:text-red-700 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</body>
</html>
