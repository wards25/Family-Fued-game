<?php
header('Content-Type: application/json');
include "dbconnect.php";

$player = isset($_GET['player']) ? intval($_GET['player']) : 1;

$total = 0;

// Prepare and execute
$stmt = mysqli_prepare($conn, "SELECT total_points FROM fast_total_points WHERE player_id = ?");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $player);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $result);
    if (mysqli_stmt_fetch($stmt)) {
        $total = $result;
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);

echo json_encode(['total' => $total]);
exit;
