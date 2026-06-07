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

// Get user's solved challenges
$solved_query = $conn->prepare("SELECT challenge_id FROM submissions WHERE user_id = ? AND is_correct = TRUE");
$solved_query->bind_param("i", $user['id']);
$solved_query->execute();
$solved_result = $solved_query->get_result();
$solved_challenges = [];
while ($row = $solved_result->fetch_assoc()) {
    $solved_challenges[] = $row['challenge_id'];
}

// Get all challenges with statistics
$challenges_query = "SELECT * FROM challenge_stats ORDER BY points ASC";
$challenges_result = $conn->query($challenges_query);

// Calculate user score
$score_query = $conn->prepare("SELECT SUM(c.points) as total_score FROM submissions s JOIN challenges c ON s.challenge_id = c.id WHERE s.user_id = ? AND s.is_correct = TRUE");
$score_query->bind_param("i", $user['id']);
$score_query->execute();
$score_result = $score_query->get_result();
$user_score = $score_result->fetch_assoc()['total_score'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Challenges - CyberUAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white min-h-screen">

    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <span class="font-bold text-xl text-gray-900">CyberUAS</span>
                </div>

                <!-- Nav Links -->
                <div class="hidden md:flex items-center gap-6">
                    <a href="index.php" class="text-blue-600 font-semibold">Challenges</a>
                    <a href="scoreboard.php" class="text-gray-600 hover:text-gray-900 transition">Scoreboard</a>
                </div>

                <!-- User Menu -->
                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <p class="font-semibold text-gray-900"><?= htmlspecialchars($user['name']) ?></p>
                        <p class="text-sm text-gray-500"><?= htmlspecialchars($user['team']) ?></p>
                    </div>
                    <div class="flex items-center gap-2 bg-blue-50 px-4 py-2 rounded-xl">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <span class="font-bold text-blue-600"><?= $user_score ?></span>
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
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Challenges</h1>
            <p class="text-gray-500">Pilih tantangan dan kumpulkan flag untuk mendapatkan poin!</p>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap gap-3 mb-6">
            <button onclick="filterChallenges('all')" class="filter-btn active px-4 py-2 rounded-xl text-sm font-semibold bg-blue-600 text-white transition-all">
                All
            </button>
            <?php foreach ($CATEGORIES as $key => $cat): ?>
            <button onclick="filterChallenges('<?= $key ?>')" class="filter-btn px-4 py-2 rounded-xl text-sm font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition-all">
                <?= $cat['icon'] ?> <?= $cat['name'] ?>
            </button>
            <?php endforeach; ?>
        </div>

        <!-- Challenge Cards Grid -->
        <div id="challenges-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while ($challenge = $challenges_result->fetch_assoc()): ?>
            <?php
                $is_solved = in_array($challenge['id'], $solved_challenges);
                $diff = $DIFFICULTY[$challenge['difficulty']];
                $cat = $CATEGORIES[$challenge['category']];
                $solve_percentage = $challenge['solve_percentage'] ?? 0;
            ?>
            <div class="challenge-card group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden" data-category="<?= $challenge['category'] ?>">
                <!-- Card Header -->
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <!-- Category Badge -->
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-<?= $cat['color'] ?>-50 rounded-lg">
                            <span class="text-lg"><?= $cat['icon'] ?></span>
                            <span class="text-sm font-medium text-<?= $cat['color'] ?>-700"><?= $cat['name'] ?></span>
                        </div>

                        <!-- Solved Badge -->
                        <?php if ($is_solved): ?>
                        <div class="flex items-center gap-1 px-3 py-1.5 bg-green-50 rounded-lg">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm font-medium text-green-700">Solved</span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Challenge Title -->
                    <h3 class="text-lg font-bold text-gray-900 mb-2"><?= htmlspecialchars($challenge['title']) ?></h3>

                    <!-- Difficulty Badge -->
                    <div class="flex items-center gap-2 mb-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $diff['bg'] ?> <?= $diff['text'] ?>">
                            <?= $diff['name'] ?>
                        </span>
                        <span class="text-sm font-semibold text-blue-600"><?= $challenge['points'] ?> pts</span>
                    </div>

                    <!-- Description Preview -->
                    <p class="text-gray-500 text-sm mb-4 line-clamp-2"><?= htmlspecialchars(substr($challenge['description'], 0, 100)) ?>...</p>
                </div>

                <!-- Card Footer -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <div class="flex items-center justify-between text-sm text-gray-500 mb-3">
                        <span><?= $challenge['solve_count'] ?> solves</span>
                        <span><?= $solve_percentage ?>%</span>
                    </div>

                    <!-- Start Button -->
                    <a href="view.php?id=<?= $challenge['id'] ?>" class="block w-full py-3 rounded-xl font-semibold text-center transition-all
                        <?= $is_solved
                            ? 'bg-green-100 text-green-700 hover:bg-green-200'
                            : 'bg-blue-600 text-white hover:bg-blue-700'
                        ?> shadow-sm">
                        <?= $is_solved ? 'View Challenge' : 'Start Challenge' ?>
                    </a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

    </main>

    <script>
        function filterChallenges(category) {
            const cards = document.querySelectorAll('.challenge-card');
            const buttons = document.querySelectorAll('.filter-btn');

            // Update active button
            buttons.forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white');
                btn.classList.add('bg-gray-100', 'text-gray-700');
            });
            event.target.classList.remove('bg-gray-100', 'text-gray-700');
            event.target.classList.add('bg-blue-600', 'text-white');

            // Filter cards
            cards.forEach(card => {
                if (category === 'all' || card.dataset.category === category) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>

</body>
</html>
