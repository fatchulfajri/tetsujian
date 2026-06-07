<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$challenge_id = $_GET['id'] ?? 0;

// Get challenge details
$stmt = $conn->prepare("SELECT * FROM challenges WHERE id = ?");
$stmt->bind_param("i", $challenge_id);
$stmt->execute();
$challenge_result = $stmt->get_result();

if ($challenge_result->num_rows === 0) {
    die("Challenge not found");
}

$challenge = $challenge_result->fetch_assoc();

// Check if user already solved this challenge
$solved_check = $conn->prepare("SELECT is_correct FROM submissions WHERE user_id = ? AND challenge_id = ?");
$solved_check->bind_param("ii", $user['id'], $challenge_id);
$solved_check->execute();
$solved_result = $solved_check->get_result();
$is_solved = false;
if ($solved_result->num_rows > 0) {
    $is_solved = $solved_result->fetch_assoc()['is_correct'];
}

// Handle flag submission
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['flag'])) {
    $submitted_flag = trim($_POST['flag']);

    if ($is_solved) {
        $message = 'Anda sudah menyelesaikan challenge ini!';
        $message_type = 'info';
    } else {
        if ($submitted_flag === $challenge['flag']) {
            // Correct flag!
            $insert = $conn->prepare("INSERT INTO submissions (user_id, challenge_id, flag, is_correct, submitted_at) VALUES (?, ?, ?, TRUE, NOW())");
            $insert->bind_param("iis", $user['id'], $challenge_id, $submitted_flag);

            if ($insert->execute()) {
                // Update solve count
                $conn->query("UPDATE challenge_solves SET solve_count = solve_count + 1 WHERE challenge_id = $challenge_id");
                $message = "🎉 Flag Benar! Anda mendapatkan {$challenge['points']} poin!";
                $message_type = 'success';
                $is_solved = true;
            }
        } else {
            // Wrong flag
            $insert = $conn->prepare("INSERT INTO submissions (user_id, challenge_id, flag, is_correct, submitted_at) VALUES (?, ?, ?, FALSE, NOW())");
            $insert->bind_param("iis", $user['id'], $challenge_id, $submitted_flag);
            $insert->execute();
            $message = "❌ Flag salah. Coba lagi!";
            $message_type = 'error';
        }
    }
}

$diff = $DIFFICULTY[$challenge['difficulty']];
$cat = $CATEGORIES[$challenge['category']];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($challenge['title']) ?> - CyberUAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .code-block {
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body class="bg-white min-h-screen">

    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <a href="index.php" class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </a>
                    <span class="font-bold text-xl text-gray-900">CyberUAS</span>
                </div>

                <!-- Nav Links -->
                <div class="hidden md:flex items-center gap-6">
                    <a href="index.php" class="text-gray-600 hover:text-gray-900 transition">Challenges</a>
                    <a href="scoreboard.php" class="text-gray-600 hover:text-gray-900 transition">Scoreboard</a>
                </div>

                <!-- User Menu -->
                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <p class="font-semibold text-gray-900"><?= htmlspecialchars($user['name']) ?></p>
                        <p class="text-sm text-gray-500"><?= htmlspecialchars($user['team']) ?></p>
                    </div>
                    <a href="logout.php" class="text-gray-400 hover:text-red-500 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Back Button -->
        <a href="index.php" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 mb-6 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Challenges
        </a>

        <!-- Challenge Header -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $diff['bg'] ?> <?= $diff['text'] ?>">
                            <?= $diff['name'] ?>
                        </span>
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-<?= $cat['color'] ?>-50 rounded-lg">
                            <span><?= $cat['icon'] ?></span>
                            <span class="text-sm font-medium text-<?= $cat['color'] ?>-700"><?= $cat['name'] ?></span>
                        </div>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($challenge['title']) ?></h1>
                </div>
                <div class="flex items-center gap-2 bg-blue-50 px-4 py-2 rounded-xl">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span class="font-bold text-blue-600"><?= $challenge['points'] ?> pts</span>
                </div>
            </div>

            <!-- Solved Badge -->
            <?php if ($is_solved): ?>
            <div class="flex items-center gap-2 w-fit px-4 py-2 bg-green-50 rounded-xl border border-green-100">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-semibold text-green-700">Challenge Selesai!</span>
            </div>
            <?php endif; ?>
        </div>

        <!-- Challenge Description -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Deskripsi</h2>
            <div class="prose prose-gray max-w-none">
                <p class="text-gray-600 whitespace-pre-line"><?= htmlspecialchars($challenge['description']) ?></p>
            </div>

            <!-- Hints -->
            <?php if ($challenge['hints']): ?>
            <div class="mt-6 p-4 bg-yellow-50 rounded-xl border border-yellow-100">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    <div>
                        <h3 class="font-semibold text-yellow-800 mb-1">Hint</h3>
                        <p class="text-sm text-yellow-700"><?= htmlspecialchars($challenge['hints']) ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Flag Submission -->
        <?php if (!$is_solved): ?>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Submit Flag</h2>

            <?php if ($message): ?>
            <div class="mb-4 p-4 rounded-xl flex items-center gap-3
                <?= $message_type === 'success' ? 'bg-green-50 border border-green-100 text-green-700' :
                   ($message_type === 'error' ? 'bg-red-50 border border-red-100 text-red-700' : 'bg-blue-50 border border-blue-100 text-blue-700') ?>">
                <?php if ($message_type === 'success'): ?>
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <?php elseif ($message_type === 'error'): ?>
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <?php else: ?>
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <?php endif; ?>
                <span class="font-medium"><?= htmlspecialchars($message) ?></span>
            </div>
            <?php endif; ?>

            <form method="POST">
                <div class="flex gap-3">
                    <input type="text" name="flag" required
                        class="flex-1 px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all outline-none code-block text-sm"
                        placeholder="CTF{...}" autocomplete="off">
                    <button type="submit"
                        class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                        Submit
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>

    </main>

</body>
</html>
