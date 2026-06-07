<?php
// Database Configuration
$conn = new mysqli("db", "root", "root", "cyber");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");

// Challenge Categories with Icons
$CATEGORIES = [
    'web' => ['name' => 'Web Exploitation', 'icon' => '🌐', 'color' => 'blue'],
    'crypto' => ['name' => 'Cryptography', 'icon' => '🔐', 'color' => 'purple'],
    'forensics' => ['name' => 'Forensics', 'icon' => '🔍', 'color' => 'green'],
    'reverse' => ['name' => 'Reverse Engineering', 'icon' => '🔧', 'color' => 'orange'],
    'network' => ['name' => 'Network', 'icon' => '📡', 'color' => 'cyan'],
    'linux' => ['name' => 'Linux', 'icon' => '🐧', 'color' => 'yellow']
];

// Difficulty Levels
$DIFFICULTY = [
    'easy' => ['name' => 'Easy', 'color' => 'green', 'bg' => 'bg-green-100', 'text' => 'text-green-700'],
    'medium' => ['name' => 'Medium', 'color' => 'yellow', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
    'hard' => ['name' => 'Hard', 'color' => 'red', 'bg' => 'bg-red-100', 'text' => 'text-red-700']
];
?>
