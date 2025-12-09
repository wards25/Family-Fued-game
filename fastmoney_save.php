<?php
session_start();
include "dbconnect.php";

$qid = $_POST['qid'];
$answer = trim($_POST['answer']);

$stmt = $conn->prepare("
    REPLACE INTO fast_player_answers(player, question_id, answer_text)
    VALUES(?, ?, ?)
");
$stmt->bind_param("iis", $_POST['player'], $qid, $answer);
$stmt->execute();

echo "OK";
