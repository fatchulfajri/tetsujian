<?php include '../config.php';
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=scoreboard.csv");

$q = $conn->query("
SELECT u.nim,u.team,SUM(s.score) score
FROM submissions s JOIN users u ON s.user_id=u.id
GROUP BY u.team");

echo "NIM,TEAM,SCORE\n";
while($r=$q->fetch_assoc()){
  echo "{$r['nim']},{$r['team']},{$r['score']}\n";
}