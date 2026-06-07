<?php
include "db.php";
$res = $conn->query("SELECT flag FROM flags");
$row = $res->fetch_assoc();
echo "Flag: " . $row['flag'];