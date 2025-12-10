<?php
session_start();
include "dbconnect.php";

// Check if buzzer should play
$row = $conn->query("SELECT * FROM fast_buzzer WHERE id=1")->fetch_assoc();
if ($row && $row['triggered'] == 1) {
    // reset trigger
    $conn->query("UPDATE fast_buzzer SET triggered=0 WHERE id=1");
    echo json_encode(['buzzer'=>true]);
} else {
    echo json_encode(['buzzer'=>false]);
}
?>
