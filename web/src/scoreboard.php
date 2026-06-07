<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'config.php';

// Get scoreboard data
$scoreboard_query = "
    SELECT
        u.id,
        u.team,
        u.name,
        SUM(c.points) as total_score,
        COUNT(DISTINCT s.challenge_id) as solved_count
    FROM submissions s
    JOIN users u ON s.user_id = u.id
    JOIN challenges c ON s.challenge_id = c.id
    WHERE s.is_correct = TRUE
    GROUP BY u.id, u.team, u.name
    ORDER BY total_score DESC, s.submitted_at ASC
";
$scoreboard_result = $conn->query($scoreboard_query);

// Get top 3 for podium
$top_teams = [];
$rank = 1;
while ($row = $scoreboard_result->fetch_assoc()) {
    $row['rank'] = $rank++;
    $top_teams[] = $row;
}

// Reset result pointer
$scoreboard_result->data_seek(0);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoreboard - CyberUAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .podium-1 { height: 160px; }
        .podium-2 { height: 120px; }
        .podium-3 { height: 100px; }
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
                <div class="flex items-center gap-6">
                    <?php if (isset($_SESSION['user'])): ?>
                    <a href="index.php" class="text-gray-600 hover:text-gray-900 transition">Challenges</a>
                    <a href="scoreboard.php" class="text-blue-600 font-semibold">Scoreboard</a>
                    <a href="logout.php" class="text-gray-400 hover:text-red-500 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </a>
                    <?php else: ?>
                    <a href="login.php" class="px-4 py-2 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">🏆 Scoreboard</h1>
            <p class="text-gray-500">Lihat peringkat tim terbaik dalam menyelesaikan challenges</p>
        </div>

        <!-- Podium (Top 3) -->
        <?php if (count($top_teams) >= 1): ?>
        <div class="flex justify-center items-end gap-6 mb-12">
            <!-- 2nd Place -->
            <?php if (isset($top_teams[1])): ?>
            <div class="text-center">
                <div class="w-24 h-24 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center text-4xl">
                    🥈
                </div>
                <p class="font-bold text-gray-900"><?= htmlspecialchars($top_teams[1]['team']) ?></p>
                <p class="text-sm text-gray-500 mb-3"><?= htmlspecialchars($top_teams[1]['name']) ?></p>
                <div class="bg-gray-200 rounded-t-lg podium-2 flex items-end justify-center pb-4">
                    <span class="text-2xl font-bold text-gray-700"><?= $top_teams[1]['total_score'] ?></span>
                </div>
            </div>
            <?php endif; ?>

            <!-- 1st Place -->
            <div class="text-center">
                <div class="w-28 h-28 mx-auto mb-4 rounded-full bg-yellow-100 flex items-center justify-center text-5xl">
                    🥇
                </div>
                <p class="font-bold text-gray-900 text-lg"><?= htmlspecialchars($top_teams[0]['team']) ?></p>
                <p class="text-sm text-gray-500 mb-3"><?= htmlspecialchars($top_teams[0]['name']) ?></p>
                <div class="bg-yellow-400 rounded-t-lg podium-1 flex items-end justify-center pb-4">
                    <span class="text-3xl font-bold text-yellow-900"><?= $top_teams[0]['total_score'] ?></span>
                </div>
            </div>

            <!-- 3rd Place -->
            <?php if (isset($top_teams[2])): ?>
            <div class="text-center">
                <div class="w-24 h-24 mx-auto mb-4 rounded-full bg-orange-100 flex items-center justify-center text-4xl">
                    🥉
                </div>
                <p class="font-bold text-gray-900"><?= htmlspecialchars($top_teams[2]['team']) ?></p>
                <p class="text-sm text-gray-500 mb-3"><?= htmlspecialchars($top_teams[2]['name']) ?></p>
                <div class="bg-orange-200 rounded-t-lg podium-3 flex items-end justify-center pb-4">
                    <span class="text-2xl font-bold text-orange-700"><?= $top_teams[2]['total_score'] ?></span>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Full Scoreboard Table -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Rank</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Team</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Solved</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Score</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php
                        $rank = 1;
                        $scoreboard_result->data_seek(0);
                        while ($team = $scoreboard_result->fetch_assoc()):
                            $rank_class = '';
                            $trophy = '';
                            if ($rank === 1) {
                                $rank_class = 'bg-yellow-50';
                                $trophy = '🥇';
                            } elseif ($rank === 2) {
                                $rank_class = 'bg-gray-50';
                                $trophy = '🥈';
                            } elseif ($rank === 3) {
                                $rank_class = 'bg-orange-50';
                                $trophy = '🥉';
                            }
                        ?>
                        <tr class="hover:bg-gray-50 transition <?= $rank_class ?>">
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 font-bold text-gray-700">
                                    <?= $trophy ?: $rank ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-900"><?= htmlspecialchars($team['team']) ?></p>
                            </td>
                            <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($team['name']) ?></td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-sm font-medium">
                                    <?= $team['solved_count'] ?> solved
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-gray-900"><?= number_format($team['total_score']) ?></span>
                                <span class="text-sm text-gray-400 ml-1">pts</span>
                            </td>
                        </tr>
                        <?php $rank++; endwhile; ?>

                        <?php if ($rank === 1): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p>Belum ada skor yang tercatat.</p>
                                <p class="text-sm">Jadilah yang pertama menyelesaikan challenge!</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</body>
</html>
