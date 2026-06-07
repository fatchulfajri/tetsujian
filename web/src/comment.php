<?php
include "db.php";

if(isset($_POST['comment'])) {
    $c = $_POST['comment'];
    $conn->query("INSERT INTO comments VALUES ('$c')");
}

$res = $conn->query("SELECT comment FROM comments");
while($row = $res->fetch_assoc()) {
    echo $row['comment']."<br>";
}
?>

<form method="POST">
<textarea name="comment"></textarea><br>
<button>Send</button>
</form>