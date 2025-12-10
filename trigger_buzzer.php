<?php
include "dbconnect.php";
$conn->query("UPDATE fast_buzzer SET triggered=1 WHERE id=1");
?>
