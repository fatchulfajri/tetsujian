<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'config.php';

// if (time() > $CTF_END) die("CTF SUDAH BERAKHIR");
if (isset($CTF_END) && time() > $CTF_END) {
    die("CTF SUDAH BERAKHIR");
}

if (!isset($_SESSION['user'])) {
    die("Akses ditolak: Silakan login terlebih dahulu.");
}

$user = $_SESSION['user'];
$challenge = $_POST['challenge'];
$flag = $_POST['flag'];

$valid = "CTF{$challenge}-{$user['team']}";

if ($flag === $valid) {
  $score = $FLAGS[$challenge];
  $conn->query("INSERT INTO submissions 
    (user_id, challenge, score, submit_time)
    VALUES ({$user['id']}, '$challenge', $score, NOW())");
  echo "FLAG BENAR!";
} else {
  echo "FLAG SALAH";
}
?>