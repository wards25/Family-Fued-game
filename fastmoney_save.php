<?php
session_start();
include "dbconnect.php";

$qid = $_POST['qid'];
$answer = trim($_POST['answer']);
$player = $_POST['player'];

// Check if ANYONE else already used the SAME answer for the SAME question
$duplicate_check_stmt = $conn->prepare("
    SELECT COUNT(*) AS cnt 
    FROM fast_player_answers 
    WHERE question_id = ? 
      AND LOWER(answer_text) = LOWER(?) 
      AND player != ?
");
$duplicate_check_stmt->bind_param("isi", $qid, $answer, $player);
$duplicate_check_stmt->execute();
$duplicate_check_stmt->bind_result($cnt);
$duplicate_check_stmt->fetch();
$duplicate_check_stmt->close();

if ($cnt > 0) {
    echo "duplicate";
    exit;
}

// If not duplicate → save answer
$stmt = $conn->prepare("
    REPLACE INTO fast_player_answers(player, question_id, answer_text)
    VALUES(?, ?, ?)
");
$stmt->bind_param("iis", $player, $qid, $answer);
$stmt->execute();

if ($stmt->error) {
    echo "SQL ERROR: " . $stmt->error;
    exit;
}
echo "OK";
