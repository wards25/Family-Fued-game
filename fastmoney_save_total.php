<?php
include_once "dbconnect.php";

$player = intval($_POST['player']);
$total  = intval($_POST['total']);

// Make sure player_id is PRIMARY KEY or UNIQUE in your table

$stmt = $conn->prepare("
    INSERT INTO fast_total_points (player_id, total_points)
    VALUES (?, ?)
    ON DUPLICATE KEY UPDATE total_points = VALUES(total_points)
");

$stmt->bind_param("ii", $player, $total);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "TOTAL SAVED";
} else {
    echo "NO CHANGE";
}

$stmt->close();
$conn->close();
?>
