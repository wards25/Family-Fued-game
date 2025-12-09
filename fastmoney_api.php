<?php
session_start();
include "dbconnect.php";

// Which player? (default = player 1)
$player = isset($_GET['player']) ? (int)$_GET['player'] : 1;

// Fetch 5 Fast Money questions
$sql = "
    SELECT 
        fq.id AS qid,
        fq.question_text,
        fa.answer_text,
        fa.points
    FROM fast_questions fq
    LEFT JOIN fast_player_answers fa
        ON fq.id = fa.question_id 
        AND fa.player = $player
    ORDER BY fq.id ASC
    LIMIT 5
";

$res = $conn->query($sql);

$output = [];
while ($row = $res->fetch_assoc()) {
    $output[] = [
        "question_id"  => $row["qid"],
        "question"     => $row["question_text"],
        "answer_text"  => $row["answer_text"],
        "points"       => $row["points"]
    ];
}

header("Content-Type: application/json");
echo json_encode($output);
?>
