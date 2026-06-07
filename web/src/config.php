<?php
$conn = new mysqli("db", "root", "root", "cyber");


$CTF_END = strtotime("2026-06-01 23:59:00");

$FLAGS = [
  "easy"   => 100,
  "medium" => 200,
  "hard"   => 300
];
?>